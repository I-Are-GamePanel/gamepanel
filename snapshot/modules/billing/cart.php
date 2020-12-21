<?php
function saveOrderToDb($user_id,$service_id,$home_name,$ip,$max_players,$qty,$invoice_duration,$price,$remote_control_password,$ftp_password,$cart_id,$home_id = "0",$end_date,$finish_date,$extended = "0"){
	global $db;
	if(isset($_SESSION['coupon_id'])){
		$coupon_id = $_SESSION['coupon_id'];
		} else {
		$coupon_id = 0;
	}
	$fields['user_id'] = $user_id;
	$fields['service_id'] = $service_id;
	$fields['home_name'] = $home_name;
	$fields['ip'] = $ip;
	$fields['max_players'] = $max_players;
	$fields['qty'] = $qty;
	$fields['invoice_duration'] = $invoice_duration;
	$fields['price'] = $price;
	$fields['remote_control_password'] = $remote_control_password;
	$fields['ftp_password'] = $ftp_password;
	$fields['cart_id'] = $cart_id;
	$fields['home_id'] = $home_id;
	$fields['end_date'] = $end_date;
	$fields['finish_date'] = $finish_date;
	$fields['extended'] = $extended;
	$fields['coupon_id'] = $coupon_id;
	return $db->resultInsertId( 'billing_orders', $fields );
}



function assignOrdersToCart($user_id,$tax_amount,$currency,$coupon_id){
	global $db;
	$fields['user_id'] = $user_id;
	$fields['paid'] = '0';
	$fields['tax_amount'] = $tax_amount;
	$fields['currency'] = $currency;
	//discount coupon
	if (!isset($coupon_id)) $coupon_id = "0";
	$fields['coupon_id'] = $coupon_id;
	$check_expired = $db->resultquery("SELECT id from OGP_DB_PREFIXbilling_coupons WHERE id = $fields[coupon_id] AND count > 0 AND expires >= NOW()");
	if ($check_expired <= 0) $fields['coupon_id'] = 0;
	return $db->resultInsertId( 'billing_carts', $fields );
}

function exec_ogp_module()
{
	error_reporting(E_ALL);
	
	global $db,$view,$settings;
	 $discounted_price = 0;
	
	$user_id = $_SESSION['user_id'];
 
	if( isset($_POST["update_cart"] )) {
		//print_r($_POST);
		$db->query( "UPDATE OGP_DB_PREFIXbilling_orders SET max_players= ".$_POST['slots']." WHERE order_id=".$db->realEscapeSingle($_POST['order_id']));
		$db->query( "UPDATE OGP_DB_PREFIXbilling_orders SET qty= ".$_POST['qty']." WHERE order_id=".$db->realEscapeSingle($_POST['order_id']));
		$db->query( "UPDATE OGP_DB_PREFIXbilling_orders SET invoice_duration = 'month' WHERE order_id=".$db->realEscapeSingle($_POST['order_id']));
		$db->query( "UPDATE OGP_DB_PREFIXgame_mods SET max_players= ".$_POST['slots']." WHERE home_id=".$db->realEscapeSingle($_POST['homeid']));
	
	}
		
	//discount coupon
	if( isset($_POST["coupon_code"] ) && $_POST["coupon_code"] != "") {
	    $coupon_id = 0;
		$coupon_code = "";
		$result = $db->resultquery("SELECT * from OGP_DB_PREFIXbilling_coupons WHERE code= '". $_POST['coupon_code'] . "'");
		$coupon_name = "<b style='color:red'>NON-EXISTING COUPON</b>";
		$coupon_discount = 0;
		foreach($result as $couponDB){
			$_SESSION['coupon_id'] = $couponDB['id'];
			$coupon_id = $couponDB['id'];
			$coupon_code = $couponDB['code'];
			$coupon_discount = $couponDB['discount'];
			$coupon_name = $couponDB['name'];
            $coupon_recurring = $couponDB['recurring'];
			$coupon_expires = $couponDB['expires'];
			$coupon_count = $couponDB['count'];
			$today = date("Y-m-d H:i:s", time());
			if($coupon_expires < $today || $coupon_count == 0){
				$coupon_id = 0;
				$coupon_discount = 0;
				$coupon_name = "<b style='color:red'>EXPIRED COUPON</b>";
			}

			if ($coupon_count > 0) {
				$coupon_count--;
				$db->resultquery("UPDATE ogp_billing_coupons SET count = $coupon_count WHERE code = '$_POST[coupon_code]'");
			}
        }
	}
	
		
		
	if( isset( $_POST["buy"] ) or isset( $_POST["pay_paypal"] ) )
	{
		if( isset( $_SESSION['CART'] ) )
		{
			$orders = $_SESSION['CART'];
			if(isset($_SESSION['coupon_id'])){
				$coupon_id = $_SESSION['coupon_id'];
			} else {
				$coupon_id = 0;
			}
			// Fill The Cart on DB
			$cart_id = assignOrdersToCart($user_id,$settings['tax_amount'],$settings['currency'],$coupon_id);
			foreach($orders as $order) 
			{
				$service_id = $order['service_id'];
				$home_name = $order['home_name'];
				$ip = $order['ip'];
				$max_players = $order['max_players'];

				//They pushed the "buy" button. 
				//So set the quantity and invoice_duration
				
				if(isset($_POST["buy"]))
					{
					$invoice_duration = "month";
					$qty = 1;
					}
					else{
					$invoice_duration = $order['invoice_duration'];
					$qty = $order['qty'];
					}
				$price = $order['price'];
				$remote_control_password = $order['remote_control_password'];
				$ftp_password = $order['ftp_password'];
				//Save order to DB 
				saveOrderToDb($user_id,$service_id,$home_name,$ip,$max_players,$qty,$invoice_duration,$price,$remote_control_password,$ftp_password,$cart_id);
				if( isset( $_POST["buy"] )) {
				echo '<meta http-equiv="refresh" content="0;url=home.php?m=billing&p=create_servers&cart_id='.$cart_id.'" >';
				}
			}
			// Remove Cart From Session
			unset($_SESSION['CART']);
            unset($_SESSION['coupon_id']);
		}
		else
		{
			$cart_id = $_POST['cart_id'];
		}
		
		if ( !empty( $cart_id ) and isset( $_POST["pay_paypal"] ) and $settings['paypal'] == "1" )
		{
			echo '<meta http-equiv="refresh" content="0;url=home.php?m=billing&p=paypal&cart_id='.$cart_id.'" >';
		}
 
	}
	
	if( isset( $_POST["extend"] ) or isset( $_POST["extend_and_pay_paypal"] ))
	{
		
		$orders = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXbilling_orders WHERE order_id=".$db->realEscapeSingle($_POST['order_id']));
		
		// *****************************************
			//FIGURE OUT IF THIS IS ALREADY BEEN UPDATED 
			//RENEWAL IN DB SO 
			//WE DONT CREATE MULTIPLE INVOICES
		// *****************************************
		foreach($orders as $order) 
		{
		$cart_id = $order['cart_id'];
		if($order['end_date'] < 0) 
		{
			$cart_id = assignOrdersToCart($user_id,$settings['tax_amount'],$settings['currency'],$_SESSION['coupon_id']);
			$service_id = $order['service_id'];
			$home_name = $order['home_name'];
			$ip = $order['ip'];
			$max_players = $order['max_players'];
			$qty = $_POST['qty'];
			$invoice_duration = $_POST['invoice_duration'];
			$remote_control_password = $order['remote_control_password'];
			$ftp_password = $order['ftp_password'];
			$home_id = $order['home_id'];
			$end_date = 0;
			$finish_date = $order['finish_date'];
			$services = $db->resultQuery( "SELECT * 
										   FROM OGP_DB_PREFIXbilling_services 
										   WHERE service_id=".$db->realEscapeSingle($service_id) );
			$service = $services[0];
			//Calculating Price
			switch ($_POST['invoice_duration']) 
			{
				case "day":
					$price = $service['price_monthly']/30;
					break;
				case "month":
					$price = $service['price_monthly'];
					break;
				case "year":
					$price = $service['price_monthly']*12;
					break;
			}
			
			//Save order to DB
			//save the EXPIRED finish date into NEW finish date. Then check if FINISH DATE !=0 and move that + 1 month into end_date
			$order_id = saveOrderToDb($user_id,$service_id,$home_name,$ip,$max_players,$qty,$invoice_duration,$price,$remote_control_password,$ftp_password,$cart_id,$home_id,$end_date,$finish_date,"1");
			//Change the old order expiration to -3 so it can not be extended, since there is a new order managing the same game home.
			$db->query( "UPDATE OGP_DB_PREFIXbilling_orders
						 SET end_date=-3
						 WHERE order_id=".$db->realEscapeSingle($_POST['order_id']));
	   
		  
		 }

		}
		
		if ( !empty( $cart_id ) and isset( $_POST["extend_and_pay_paypal"] ) and $settings['paypal'] == "1" )
		{
			echo '<meta http-equiv="refresh" content="0;url=home.php?m=billing&p=paypal&cart_id='.$cart_id.'" >';
		}
		
	}
	
	if(isset($_POST['remove']))
	{
		$cart_id = $_POST['cart_id'];
		if( isset( $_SESSION['CART'][$cart_id] ) )
		{
			unset($_SESSION['CART'][$cart_id]);
            unset($_SESSION['coupon_id']);
		}
		$order_id = $_POST['order_id'];
		$db->query( "DELETE FROM OGP_DB_PREFIXbilling_orders WHERE order_id=".$db->realEscapeSingle($order_id) );
		$orders_in_cart = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_orders WHERE cart_id=".$db->realEscapeSingle($cart_id) );
		if( !$orders_in_cart )
		{
			$db->query( "DELETE FROM OGP_DB_PREFIXbilling_carts WHERE cart_id=".$db->realEscapeSingle($cart_id) );
		}

	}
		
	?>
	<style>
	h4 {
		width:250px;
		height:25px;
		background:#f5f5f5;
		border-top-style:solid;
		border-top-color:#afafaf;
		border-top-width:1px;
		border-style: solid;
		border-color: #CFCFCF;
		border-width: 1px;
		padding-top:8px;
		text-align: center;
		font-family:"Trebuchet MS";
	}
	</style>
	<h2>Cart</h2>
	<!--
	SHOW ALL THE INVOICES FOR USER
	<form method="post" action="?m=billing&p=orders">
	<input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
	<input type="submit" value="All Orders">
	</form>
	-->
	<?php
	if( isset($_SESSION['CART']) and !empty($_SESSION['CART']) )
	{
		$carts[0] = $_SESSION['CART'];
	}

	$user_carts = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_carts WHERE user_id=".$db->realEscapeSingle($user_id) ." order by cart_id desc" );
	

	if( $user_carts >=1 )
	{

	// SELECT WHAT KIND OF OLD INVOICES TO DISPLAY. WE NEED A BUTTON?	
		foreach ( $user_carts as $user_cart )
		{
			$cart_id = $user_cart['cart_id'];

			$carts[$cart_id] = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_carts AS cart JOIN
																OGP_DB_PREFIXbilling_orders AS orders  
																ON orders.cart_id=cart.cart_id
																WHERE orders.end_date IN (0, -1 , -2) AND (cart.cart_id=".$db->realEscapeSingle($cart_id). ") order by order_id asc");
		}
	}
	
	if( empty( $carts ) )
	{
		print_failure( get_lang('there_are_no_orders_in_cart') );
		?>		
		<a href="?m=billing&p=shop"><?php print_lang('back'); ?></a>
		<?php
		return;
	}
	foreach ( $carts as $orders )
	{
		if( !empty( $orders ) )
		{
			?>
	<center>
		<table style="width:95%;text-align:left;" class="center">
			<tr>
			<hr />
                        
			
			 <th>
			<?php print_lang("order_desc");?></th>
			 <th>
			<?php print_lang("price");?>
			 </th>
			 <?php
			 if(isset($orders[0]['paid']) and $orders[0]['paid'] == 3)
			 {
			 ?>
			 <th>
			 <?php print_lang('expiration_date');?>
			 </th>
	 
			 <th>Status
			 </th>
			 <?php
			 }
			 ?>
			 <th>
			 </th>
			</tr>
			<?php 
			$subtotal = 0;
			$total_orders = count($orders);
			$order_counter = 0;
			foreach($orders as $order)
			{
				$order_counter++;
				if ( $order['qty'] > 1 ) 
					$order['invoice_duration'] = $order['invoice_duration']."s";

				$subtotal += ($order['price']* $order['max_players'] * $order['qty']);
				
				?>
			<tr class="tr">
			
			 <td>
				<?php 
				$rserver = $db->getRemoteServer($order['ip']);
				echo "Order# ".$order['order_id'] . " <b>".$order['home_name']."</b> Server ID ".$order['home_id'] ;
				?>
			 </td>
			 <td>
				<?php 
				echo "$" . number_format( $order['price'], 2 ). "  " .$order['currency'] . " per slot<br>"
				
				. $order['max_players'] . " Slots<br>"
				. $order['qty'] . " " . $order['invoice_duration'] ;
				?>
			 </td>
				<?php
				if($order['paid'] == 0 and ($order['extended'] == 0))
				{
					?>
			 <td align="center">
			  <form method="post" action="">
			   <input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
			   <input type="hidden" name="order_id" value="<?php echo @$order['order_id'];?>">
	 
			   <input type="submit" name="remove" value="<?php print_lang("remove_from_cart");?>">
			  </form>
			  <?php if ($total_orders == $order_counter) { ?>
			 <!--checkbox -->
			   <form method="post" action="" onsubmit="if(document.getElementById('agree').checked) { return true; } else { alert('You must Agree to the TOS'); return false; }">
			    <input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
			   <?php     

				//see if user is a new customer, 
				//check number of orders they have had or if user is an admin (to be able to create server)
				$isAdmin = $db->isAdmin( $_SESSION['user_id'] );
				$result = $db->resultQuery("SELECT * FROM ogp_billing_orders WHERE user_id=".$user_id);
				$server_price =  number_format( $order['price'], 2 );
				if(isset($settings['display_free'])) {
						$display_free = $settings['display_free'];
				}else {
					$display_free = false;
				}
			    if((($server_price == 0 )||($isAdmin)) && ($display_free))
				//if($display_free)   
				{
	 			echo '<input name="buy" type="submit" value="Create FREE Server" ><br>';
                		echo "This server will be FREE to Install<br>Pay the invoice to continue this Demo Server<br>Otherwise it will be deleted when it expires</br>";
				}

				else{			

			   	if($settings['paypal'] == "1")
					echo '<input name="pay_paypal" type="submit"   value="'.get_lang_f("pay_from", get_lang('paypal')).'">';
			   	}
				
				?>
								   
																												 <!--checkbox do regulamento -->
			  <br><br><input type="checkbox" name="checkbox" value="check" id="agree" /><?php echo $settings['checkbox'];?>     
             </form>
             <?php } ?>
             </td><?php
                }
	 
				
				if($order['paid'] == 3)
				{
					$today=time();
                    $formated_finish_date = date('d/M/Y H:i A',$order['finish_date']);

					//end_date has a date for invoice
					if($order['end_date'] > 0)
					{
					$status = "<b style='color:green;'>Active</b>" ;
					}
															 
	  
														  
	  
					//end_date is -1, invoice has been created
					elseif($order['end_date'] == -1)
					{
					$status = "<b style='color:yellow;'>Invoice Due</b>";
					}
					//invoice was not paid, server is expired and suspended
					elseif($order['end_date'] == -2)
					{
					$status = "<b style='color:red;'>Suspended</b>";
					}
										
					//display the expiration date and invoice button.
					if($order['end_date'] > 0){$warning_end_date = "<b style='color:green;'>". $formated_finish_date ."</b>";}
					if($order['end_date'] == -1){$warning_end_date ="<b style='color:yellow;'>". $formated_finish_date ."</b>";}
					if($order['end_date'] == -2){$warning_end_date ="<b style='color:red;'>". $formated_finish_date ."</b>" ;}

				?>
			 <td>
				<?php echo "$warning_end_date";?>
			 </td>
			 <td>
				<?php echo "$status";


?>
			 </td>
			<?php
				}
				
				if( isset( $order['end_date'] ) and $order['end_date'] == "0" or $order['end_date'] == "-1" or $order['end_date'] == "-2")
				{
					?>
			 			 <td></td></tr><tr><td>

			  <form method="post" action="">
			   <input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
			   <input type="hidden" name="order_id" value="<?php echo $order['order_id'];?>">
			   <input type="hidden" name="homeid" value="<?php echo $order['home_id'];?>">
			   
			   <select name="slots">
				<?php 
				//allow to change the amount of max  players and invoice time when renewing server
				//get max_slots and min_slots from the billing_services for this game.
				
					$services = $db->resultQuery( "SELECT * 
										FROM OGP_DB_PREFIXbilling_services 
										   WHERE service_id=".$db->realEscapeSingle($order['service_id']) );
					$service = $services[0];
					$min = $service['slot_min_qty'];
					$max = $service['slot_max_qty'];
					$slots=$min;
					while($slots<= $max)
					{
					if($slots == $order['max_players'])
					{
						echo "<option value='$slots' selected>$slots slots</option>";
					}else{
						echo "<option value='$slots' >$slots slots</option>";
					}
					$slots++;
					}
					?>
			   </select>
			   
			      
			  
			   <select name="qty">
					<?php 
														  
					
											  
		
						  
		
																																																																							
					$qty=1;
					while($qty<=12)
					{
					if($qty == $order['qty'])
					{
						echo "<option value='$qty' selected>$qty months</option>";
					}else{
						echo "<option value='$qty'>$qty months</option>";

					}
					$qty++;
					}
					?>
			  </select>
             <input type="hidden" name="invoice_duration" value="month">
			   <!--
			   <input type="submit" name="extend" value="<?php print_lang("extend");?>">
			   -->
			   <?php
			   if($settings['paypal'] == "1")
				echo '<button name="update_cart" type="submit" value="update_cart">Update Invoice</button>';

				echo '<button name="extend_and_pay_paypal" type="submit" value="extend_and_pay_paypal">Renew Service</button>';
	
			   ?>

																														   
																																										

			  </form>
			 </td><?php
				}
				?>
			</tr><?php
			}
			?>
		</table>
		<table style="width:95%;text-align:left;" class="center">
			<tr>
			 <td>Amount</td>
									   
			 <td>
			<?php
			echo "$" . number_format( $subtotal , 2 ). " " .$order['currency'];?>
			 </td>
			</tr>
			<tr>
				<td><b><?php echo $coupon_name;?></b></td>
				<td>
				<?php
			//APPLY COUPON CODE HERE 
			$coupon_discount_amt = $subtotal * ($coupon_discount / 100);
			echo "-$" . number_format($coupon_discount_amt,2);
			?></td><td>
			 <table><tr>
			 <form method="post" action="">
						<td class="child">
						<input type="text" name="coupon_code"size="5" value="<?php echo $coupon_code ?>"></input>
					</td>
					<td>
					<input type="submit" name="Apply Code" value="Apply Code"></input>
					</td>
				   </tr></table>
			</form>
			</td>
			</tr>
			
			<tr>
				<td>Discounted Subtotal</td>
				<td><?php $subtotal = $subtotal-$coupon_discount_amt;echo "$" . number_format( $subtotal , 2 ). " " .$order['currency'];?></td>
			</tr>
			
			<tr>
			 <td>
			Tax Amount</td>
			 <td>
			<?php echo "$" . number_format($order['tax_amount']/100 * $subtotal,2);?>
			 </td>
			</tr>
			<tr>
			 <td>
			<?php print_lang("total");?>
			 </td>
			 <td>
			<?php 
			  $total = $subtotal+($order['tax_amount']/100*$subtotal);
			  echo "$" . number_format( $total , 2 ). " " .$order['currency'];
			?>
			 </td>
			 <td>
			  <?php
		if($order['paid'] == 1)
			  {
			  ?>
			 <form method="post" action="home.php?m=billing&p=create_servers">
			  <input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
			  <?php
			 if($order['extended'] == "1")
			 {
			 ?>
			  <input name="enable_server" type="submit" value="<?php print_lang("enable_server");?>">
			 <?php 
			 }
			 else
			 {
			 ?>
			  <input name="create_server" type="submit" value="<?php print_lang("create_server");?>">
			 <?php 
			 }
			?>
			 </form>
			  <?php
			  }
			  elseif($order['paid'] == 2)
			  {
			  echo get_lang_f("payment_is_pending_of_approval");
			  }
			  elseif($order['paid'] == 3)
			  {
			  ?>
			 <form method="post" action="?m=billing&p=bill">
			  <input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
			  <input name="paid" type="submit" value="<?php print_lang("see_invoice");?>">
			 </form>
			  <?php
			  }
			  else
			  {
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
											  
	<a href="?m=billing&p=shop"><?php print_lang('back'); ?></a>
	<?php
}
?>














