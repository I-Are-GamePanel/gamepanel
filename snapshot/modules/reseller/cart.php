<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) Copyright (C) 2008 - 2013 The OGP Development Team
 *
 * http://www.opengamepanel.org/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */
 
function clean($str){
	global $db;
	$str = @trim($str);
	if(get_magic_quotes_gpc()) 
	{
		$str = stripslashes($str);
	}
	return $db->real_escape_string($str);
}

function saveOrderToDb($account_id,$service_id,$user_id,$qty,$invoice_duration,$discount,$price,$cart_id,$available_slots,$payment_date){
	global $db, $view;
	if( $account_id == '' or $account_id <= -1000000)
	{
		$fields['service_id'] = $service_id;
		$fields['user_id'] = $user_id;
		$fields['qty'] = $qty;
		$fields['invoice_duration'] = $invoice_duration;
		$fields['discount'] = $discount;
		$fields['price'] = $price;
		$fields['cart_id'] = $cart_id;
		$fields['available_slots'] = $available_slots;
		$fields['payment_date'] = $payment_date;
		return $db->resultInsertId('reseller_accounts', $fields);
	}
	else
	{
		$query = sprintf("UPDATE 
						 `OGP_DB_PREFIXreseller_accounts` SET
						 `service_id` = '%d',
						 `user_id` = '%d', 
						 `qty` = '%s', 
						 `invoice_duration` = '%s', 
						 `discount` = '%s', 
						 `price` = '%s', 
						 `cart_id` = '%d', 
						 `available_slots` = '%d',
						 `payment_date` = '%s'
						 WHERE 
						 account_id=%d",
						 clean($service_id),
						 clean($user_id),
						 clean($qty),
						 clean($invoice_duration),
						 clean($discount),
						 clean($price),
						 clean($cart_id),
						 clean($available_slots),
						 clean($payment_date),
						 clean($account_id));
		if(!$db->query( $query ))	
			return false;
		return $accound_id;
	}
}

function assignOrdersToCart($user_id,$tax_amount,$currency)
{
	global $db;
	$fields['user_id'] = $user_id;
	$fields['tax_amount'] = $tax_amount;
	$fields['currency'] = $currency;
	return $db->resultInsertId('reseller_carts', $fields);
}

function exec_ogp_module()
{	
	global $db,$view,$settings;
	
	if( isset( $_POST["buy"] ) or isset( $_POST["pay"] ) )
	{
		if( isset( $_SESSION['CART'] ) )
		{
			$accounts = $_SESSION['CART'];
			// Create a new cart on DB
			$cart_id = assignOrdersToCart($_SESSION['user_id'],$settings['tax_amount'],$settings['currency']);
			foreach($accounts as $account) 
			{
				$service_id = $account['service_id'];
				$user_id = $account['user_id'];
				$qty = $account['qty'];
				$invoice_duration = $account['invoice_duration'];
				$discount = $account['discount'];
				$price = $account['price'];
				$paid = $account['paid'];
				$service_info = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXreseller_services WHERE service_id=".$service_id );
				$available_slots = $service_info[0]['slot_max_qty'];
				//Save account to DB
				if(!saveOrderToDb('',$service_id,$user_id,$qty,$invoice_duration,$discount,$price,$cart_id,$available_slots,"0"))
					print_failure("A service could not be added to the database");
			}
			// Remove Cart From Session
			unset($_SESSION['CART']);
			$db->query( "UPDATE OGP_DB_PREFIXreseller_carts
						 SET paid=2
						 WHERE cart_id=".$cart_id);
		}
		else
		{
			$cart_id = $_POST['cart_id'];
		}
	}
		
	if( isset( $_POST["extend"] ) or isset( $_POST["extend_and_pay"] ) )
	{
		$accounts = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE account_id=".$_POST['account_id']);
		// Create a new cart on DB
		$cart_id = assignOrdersToCart($_SESSION['user_id'],$settings['tax_amount'],$settings['currency']);
		$account = $accounts[0];
		$service_id = $account['service_id'];
		$account_id = $account['account_id'];
		$available_slots = $account['available_slots'];
		$old_qty = $account['qty'];
		$old_invoice_duration = $account['invoice_duration'];
		$old_discount = $account['discount'];
		$old_price = $account['price'];
		$old_payment_date = $account['payment_date'];
		// Get new invoice duration
		$qty = $_POST['qty'];
		$invoice_duration = $_POST['invoice_duration'];
		
		//Calculating New Price
		$services = $db->resultQuery( "SELECT * 
									   FROM OGP_DB_PREFIXreseller_services 
									   WHERE service_id=".$service_id );
		$service = $services[0];
		if ($invoice_duration == "month")
		{
			$price_pack = $service['price_per_month'];
		}
		elseif ($invoice_duration == "year")
		{
			$price_pack = $service['price_per_year'];
		}
		$price = $price_pack*$qty;
		
		//Save the old account information in the old cart with a negative signed(-) int for billing purposses
		$old_cart_id = $account['cart_id'];
		$ext_account_id = ( 0 - $account['account_id'] ) * 1000000;
		
		do {
			$test_account_query = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE account_id=".$ext_account_id);
			if( empty( $test_account_query[0] ) ) break;
			--$ext_account_id;
		} while( ! empty( $test_account_query[0] ) );
		
		saveOrderToDb("$ext_account_id",$service_id,$_SESSION['user_id'],$old_qty,$old_invoice_duration,$old_discount,$old_price,$old_cart_id,$available_slots,$old_payment_date);

		//Save the old account in to the new cart.
		saveOrderToDb("$account_id",$service_id,$_SESSION['user_id'],$qty,$invoice_duration,"0",$price,$cart_id,$available_slots,"0");
		
		//Set end_date to -2 at the old account information so it's known as an extended account.
		$db->query( "UPDATE OGP_DB_PREFIXreseller_accounts
					 SET end_date=-2
					 WHERE account_id=$ext_account_id");
		
		//Set end_date to 0 at the account information at the new cart, waiting for account extension payment.
		$db->query( "UPDATE OGP_DB_PREFIXreseller_accounts
					 SET end_date=0
					 WHERE account_id=$account_id");
		
		//Set end_date to 0 at the account information at the new cart, awaiting payment.
		$db->query( "UPDATE OGP_DB_PREFIXreseller_carts
						 SET paid=2
						 WHERE cart_id=".$cart_id);
	}
		
	if(isset($_POST['remove']))
	{
		$cart_id = $_POST['cart_id'];
		if( isset( $_SESSION['CART'][$cart_id] ) )
		{
			unset($_SESSION['CART'][$cart_id]);
		}
		$account_id = $_POST['account_id'];
		$db->query( "DELETE FROM OGP_DB_PREFIXreseller_accounts WHERE account_id=".$account_id );
		$accounts_in_cart = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE cart_id=".$cart_id );
		if( !$accounts_in_cart )
		{
			$db->query( "DELETE FROM OGP_DB_PREFIXreseller_carts WHERE cart_id=".$cart_id );
		}
	}
	
	if ( isset( $_POST["cart_id"] ) AND ( isset( $_POST["pay"] ) or isset( $_POST["extend_and_pay"] ) ) )
	{
		$view->refresh('home.php?m=reseller&p=paypal&cart_id='.$_POST["cart_id"], 0);
	}
	
	?><h2><?php print_lang("your_cart");?></h2><?php
	if( isset($_SESSION['CART']) and !empty($_SESSION['CART']) )
	{
		$carts[0] = $_SESSION['CART'];
	}

	$user_carts = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXreseller_carts WHERE user_id=".$_SESSION['user_id'] );
	
	if( $user_carts >=1 )
	{
		foreach ( $user_carts as $user_cart )
		{
			$cart_id = $user_cart['cart_id'];
			$carts[$cart_id] = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXreseller_carts AS cart JOIN
																OGP_DB_PREFIXreseller_accounts AS account 
																ON account.cart_id=cart.cart_id
																WHERE cart.cart_id=".$cart_id );
		}
	}
		
	if( empty( $carts ) )
	{
		print_failure( get_lang('there_are_no_accounts_in_cart') );
		?>		
		<a href="?m=reseller&p=rs_packs_shop"><?php print_lang('back'); ?></a>
		<?php
		return;
	}
	foreach ( $carts as $accounts )
	{
		if( !empty( $accounts ) )
		{
			?>
	<center>
		<table style="width:95%;text-align:center;" class="center">
			<tr>
			 <th>
			<?php print_lang("service");?></th>
			 <th>
			<?php print_lang("service_price");?>
			 </th>
			 <th>
			<?php print_lang("discount");?>
			 </th>
			 <th>
			<?php print_lang("price");?>
			 </th>
			 <th>
			<?php print_lang("account_actions");?>
			 </th>
			</tr>
			<?php 
			$subtotal = 0;
			$i = 0;
			foreach($accounts as $account)
			{
				$invoice_duration = ( $account['qty'] > 1 ) ? $account['invoice_duration']."s" : $account['invoice_duration'];

				$subtotal += $account['price'];
				$service_info = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXreseller_services WHERE service_id=".$account['service_id'] );
				?>
			<tr class="tr<?php echo($i++%2);?>">
			 <td>
				<?php 
				echo "<b>".$service_info[0]['service_name']."</b> [".$account['qty']." ".get_lang($invoice_duration).", ".$service_info[0]['slot_max_qty']." ".get_lang('slots')."]" ;
				?>
			 </td>
			 <td>
				<?php 
				echo ($service_info[0]['price_per_'.$account['invoice_duration']] * $account['qty']).$account['currency'];
				?>
			 </td>
			 <td>
				<?php 
				echo $account['discount']."% (" .( ( $service_info[0]['price_per_'.$account['invoice_duration']] / 100) * $account['discount'] ) * $account['qty'] . $account['currency'] . ")";
				?>
			 </td>
			 <td>
				<?php 
				echo $account['price'].$account['currency'];
				?>
			 </td>
			 <td style="text-align:center;">
				<?php
				if($account['paid'] == 0 or $account['paid'] == 2)
				{
					?>
			  <form method="post" action="">
			   <input type="hidden" name="cart_id" value="<?php echo $account['cart_id'];?>">
			   <input type="hidden" name="account_id" value="<?php echo $account['account_id'];?>">
			   <input type="submit" name="remove" value="<?php print_lang("remove_from_cart");?>">
			  </form>
				<?php
				}
				if($account['paid'] == 1 and $account['end_date'] == "-2")
				{
					print_lang('account_extended_to_new_cart');
				}
				if($account['end_date'] == "-1")
				{
					?>
			  <form method="post" action="">
			   <input type="hidden" name="cart_id" value="<?php echo $account['cart_id'];?>">
			   <input type="hidden" name="account_id" value="<?php echo $account['account_id'];?>">
			   <select name="qty">
					<?php 
					$qty=1;
					while($qty<=12)
					{
					echo "<option value='$qty'>$qty</option>";
					$qty++;
					}
					?>
			   </select>
			   <select name="invoice_duration">
					<?php
					
					if( $settings['price_per_month'] == 1) echo '<option value="month">'.get_lang('months').'</option>';
					if( $settings['price_per_year'] == 1) echo '<option value="year">'.get_lang('years').'</option>';
					?>
			   </select>
			   <input type="submit" name="extend" value="<?php print_lang("extend");?>">
			  </form>
				<?php
				}
				elseif( $account['end_date'] > 0 )
				{
				?>
			  <form method="post" action="home.php?m=reseller&p=rs_assign_server">
			   <input type="hidden" name="account_id" value="<?php echo $account['account_id'];?>">
			   <input type="submit" name="remove" value="<?php print_lang("rs_assign_servers");?>">
			  </form>
				<?php
				}
				?>
				</td>
			</tr>
			<?php
			}
			?>
		</table>
		<table style="width:95%;text-align:left;" class="center">
			<tr>
			 <td>
			<?php print_lang("subtotal");?></td>
			 <td>
			<?php 
			echo $subtotal.$account['currency'];?>
			 </td>
			</tr>
			<tr>
			 <td>
			<?php print_lang("tax");?></td>
			 <td>
			<?php echo $account['tax_amount'];?>%
			 </td>
			</tr>
			<tr>
			 <td>
			<?php print_lang("total");?>
			 </td>
			 <td>
			<?php 
			  $total = $subtotal+($account['tax_amount']/100*$subtotal);
			  echo number_format( $total , 2 ).$account['currency'];
			?>
			 </td>
			 <td>
			  <?php
			  if($account['paid'] == 1)
			  {
			  ?>
			 <form method="post" action="?m=reseller&p=bill&bt=cart">
			  <input type="hidden" name="cart_id" value="<?php echo $account['cart_id'];?>">
			  <input name="paid" type="submit" value="<?php print_lang("see_invoice");?>">
			 </form>
			  <?php
			  }
			  elseif($account['paid'] == 2)
			  {
			  ?>
			 <form method="post" action="">
			  <input type="hidden" name="cart_id" value="<?php echo $account['cart_id'];?>">
			  <input name="pay" type="submit" value="<?php print_lang("pay_from_paypal");?>">
			 </form>
			  <?php
			  }
			  elseif($account['paid'] == 3)
			  {
			  ?>
			 <form method="post" action="?m=reseller&p=bill&bt=cart">
			  <input type="hidden" name="cart_id" value="<?php echo $account['cart_id'];?>">
			  <input name="paid" type="submit" value="<?php print_lang("see_invoice");?>">
			 </form>
			  <?php
			  }
			  else
			  {
			  ?>
			 <form method="post" action="">
			  <input type="hidden" name="cart_id" value="<?php echo $account['cart_id'];?>">
			  <input name="buy" type="submit" value="<?php print_lang("buy");?>">
			 </form>
			  <?php
			  }
			  ?>
			  </form>
			 </td>
			</tr>
		</table>
	</center>
			<?php
		}
	}
	?>		
	<a href="?m=reseller&p=rs_packs_shop"><?php print_lang('back'); ?></a>
	<?php
}
?>