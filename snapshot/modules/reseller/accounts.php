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
function exec_ogp_module()
{
	error_reporting(E_ALL);
	
	global $db;
		
	if(isset($_POST['remove']))
	{
		$query_delete_account = $db->query("DELETE FROM OGP_DB_PREFIXreseller_accounts WHERE cart_id=".$_POST['cart_id']);
		$query_delete_account = $db->query("DELETE FROM OGP_DB_PREFIXreseller_carts WHERE cart_id=".$_POST['cart_id']);
	}
	if(isset($_POST['paid']))
	{
		$query = "UPDATE OGP_DB_PREFIXreseller_carts
				  SET paid=1
				  WHERE cart_id=".$_POST['cart_id'];
		$db->query($query);
		
		$query = "UPDATE OGP_DB_PREFIXreseller_accounts
				  SET payment_date=NOW()
				  WHERE cart_id=".$_POST['cart_id'];
		$db->query($query);
		
		$cart_accounts = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE cart_id=".$_POST['cart_id']);
		foreach ( $cart_accounts as $account )
		{
			$months = $account['invoice_duration'] == "month" ? 1 : 12;
			$total_months = $months * $account['qty'];
			
			$db->query("UPDATE OGP_DB_PREFIXreseller_accounts
						SET available_months=".$total_months.
					   " WHERE account_id=".$account['account_id'] );
				   
			$db->query( "UPDATE OGP_DB_PREFIXreseller_accounts ".
						"SET end_date=ADDDATE(DATE(NOW() + INTERVAL ".$total_months." MONTH), 1) ".
						"WHERE account_id=".$account['account_id'] );
		}
	}
	$status_array = array ( "not_paid" => 0,
							"paid" => 1,
							"awaiting_payment" => 2,
							"paid_and_installed" => 3
						  );
	?>
	<h2><?php print_lang("reseller_accounts");?></h2>
	<?php
	foreach($status_array as $status => $paid_value)
	{
		$carts = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_carts WHERE paid=$paid_value");
		if( $carts > 0 )
		{
			?>
		<h4><?php print_lang($status);?></h4><?php
			foreach($carts as $cart) 
			{
			?>
		<center>
			<table style="width:100%;text-align:center;" class="center">
				<tr>
					<th><?php print_lang("login");?></th>
					<th><?php print_lang("cart_id");?></th>
					<th><?php print_lang("account_id");?></th>
					<th><?php print_lang("home_name");?></th>
					<th><?php print_lang("price");?></th>
				</tr>
				<?php  
				$accounts = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE cart_id=".$cart['cart_id']);
				$subtotal = 0;
				$i = 0;
				foreach($accounts as $account) 
				{
				?>
				<tr class="tr<?php echo($i++%2);?>">
					<td><a href="?m=user_admin&p=edit_user&user_id=<?php echo $account['user_id'];?>" ><?php $user = $db->getUserById($account['user_id']); echo $user['users_login'];?></a></td>
					<td><b class="success"><?php echo $account['cart_id'];?></b></td>
					<td>
					<?php
					if($account['account_id'] > 0)
					{
					?>
					 <b class="success"><?php echo $account['account_id'];?></b>
					 <a href="?m=reseller&bt=rs_accounts&p=account_details&account_id=<?php echo $account['account_id'];?>" >(<?php print_lang('account_details');?>)</a> 
					<?php
					}
					else
					{
					?>
					 <b class="success"><?php 
						echo $account_id = round(($account['account_id'] - $account['account_id'] - $account['account_id']) / 1000000);
					?></b>
					<?php
						print_lang('account_extended_to_new_cart');
					}
					?></td><td><?php 
					$qry_services = "SELECT * FROM OGP_DB_PREFIXreseller_services WHERE service_id=".$account['service_id'];
					$services = $db->resultQuery($qry_services);
					$service = $services[0];
					$invoice_duration = $account['qty'] > 1 ? $account['invoice_duration']."s" : $account['invoice_duration'];
					echo $service['service_name']." [ ".$service['slot_max_qty']." ".get_lang('slots').", ".$account['qty']." ".get_lang($invoice_duration)." ]";?></td>
					<td><?php echo $account['price'].$carts[0]['currency'];?></td>
			    </tr><?php 
				$subtotal += $account['price'];
				}
				$total = $subtotal+($cart['tax_amount']/100*$subtotal);
				?>
				<tr>
					<td>
				<?php
				if ($status == "not_paid" OR $status == "awaiting_payment" OR $account['end_date'] == -1 )
				{
					$months = $account['invoice_duration'] == "month" ? 1 : 12;
					$total_months = $months * $account['qty'];
					?>
					 <form method="post" action="">
					  <input type="hidden" name="cart_id" value="<?php echo $account['cart_id'];?>">
					  <input type="hidden" name="total_months" value="<?php echo $total_months;?>">
					  <input name="paid" type="submit" value="<?php print_lang("set_as_paid");?>">
					 </form>
					<?php
				}
				elseif($status == "paid" )
				{
					?>
					 <form method="post" action="?m=reseller&p=bill&bt=rs_accounts">
					  <input type="hidden" name="cart_id" value="<?php echo $account['cart_id'];?>">
					  <input name="paid" type="submit" value="<?php print_lang("see_invoice");?>">
					 </form>
					<?php
				}
				elseif($status == "paid_and_installed")
				{
					?>
					 <form method="post" action="?m=reseller&p=bill">
					  <input type="hidden" name="cart_id" value="<?php echo $account['cart_id'];?>">
					  <input name="paid" type="submit" value="<?php print_lang("see_invoice");?>">
					 </form>
					<?php
				}
				?>
					</td>
					<td>
					 <form method="post" action="">
					  <input type="hidden" name="cart_id" value="<?php echo $account['cart_id'];?>">
					  <input name="remove" type="submit" value="<?php print_lang("remove_cart");?>">
					 </form>
					</td>
					<td>
					 <?php echo get_lang('subtotal')." <b>".number_format( $subtotal , 2 ).$carts[0]['currency']."</b>"; ?>
					</td>
					<td>
					 <?php echo get_lang('tax')." <b>".$cart['tax_amount']."% (".number_format( $cart['tax_amount']/100*$subtotal, 2 ).$carts[0]['currency'].")</b>"; ?>
					</td>
					<td>
					 <?php echo get_lang('total')." <b>".number_format( $total , 2 ).$carts[0]['currency']."</b>"; ?>
					</td>
				</tr>
			</table>
		</center>
				<?php
			}
		}
	}
}
?>