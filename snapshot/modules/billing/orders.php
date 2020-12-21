<?php 
function exec_ogp_module()
{
	error_reporting(E_ALL);
	
	global $db,$settings;
	
	if(isset($_POST['remove']))
	{
		$query_delete_order = $db->query("DELETE FROM OGP_DB_PREFIXbilling_orders WHERE cart_id=".$db->realEscapeSingle($_POST['cart_id']));
		$query_delete_order = $db->query("DELETE FROM OGP_DB_PREFIXbilling_carts WHERE cart_id=".$db->realEscapeSingle($_POST['cart_id']));
	}
	if(isset($_POST['paid']))
	{
		$query_set_as_paid =  $db->query("UPDATE OGP_DB_PREFIXbilling_carts
										  SET paid=1
										  WHERE cart_id=".$db->realEscapeSingle($_POST['cart_id']));
	}
	$status_array = array ( "not_paid" => 0,
							"paid" => 1,
							"procesing_payment" => 2,
							"paid_and_installed" => 3
						  );
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
	<h2><?php print_lang("orders");?></h2>
	 <form method="post" action="?m=billing&p=shop">
	<input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
	<input type="submit" value="<?php print_lang("shop");?>">
	</form>	 
	<?php

	$isAdmin = $db->isAdmin( $_SESSION['user_id'] );
	$user_id = $_SESSION['user_id'];							 
	foreach($status_array as $status => $paid_value)
	if($isAdmin or $status == "paid_and_installed") 
	{											 
	{
         if ($isAdmin){
        $carts = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXbilling_carts WHERE paid =" . $db->realEscapeSingle($paid_value) ." order by cart_id DESC");
         }else{
        $carts = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXbilling_carts WHERE paid=3 AND user_id = " . $user_id ." order by cart_id DESC");
         }
		if( $carts > 0 )
		{
			?>
		<h2><?php print_lang($status);?></h2><?php
			foreach($carts as $cart) 
			{
			?>
		<center>
			<table style="width:100%;text-align:center;" class="center">
				<tr>
					<th style="width:25%"><?php print_lang("login");?></th>
					<th><?php print_lang("cart_id");?></th>
					<th><?php print_lang("order_id");?></th>
					<th>slot price</th>
					<th>Payday</th>
				<?php
				if($status == "paid_and_installed")
				{?>
					<th>Expiration dates</th>
				<?php
				}?>
				</tr>
				<?php  
				$orders = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXbilling_orders WHERE cart_id=".$db->realEscapeSingle($cart['cart_id'])." order by order_id DESC" );
				$subtotal = 0;
				foreach($orders as $order) 
				{
				if($order['qty'] > 1)
					$order['invoice_duration'] = $order['invoice_duration']."s";
				?>
				 <tr class="tr">
					<td><a href="?m=user_admin&p=edit_user&user_id=<?php echo $order['user_id'];?>" ><?php $user = $db->getUserById($order['user_id']); echo $user['users_login'];?></a></td>
					<td><b class="success"><?php echo $order['cart_id'];?></b></td>
					<td><b class="success"><?php echo $order['order_id'];?></b></td>
					<td><?php echo "$".$order['price'].$cart['currency'];?></td>
					<td><?php echo $cart['date'];?></td>
					<?php
					if($status == "paid_and_installed")
					{
						$today = time();
						$order_status = "Unknown";
						$order_status = $order['end_date'] > '0' ? "<b style='color:green;'>".get_lang('active')."</b>":$order_status;
						$order_status = $order['end_date'] == '0' ? "<b style='color:yellow;'>".get_lang('unpaid')."</b>":$order_status;																			   
						$order_status = $order['end_date'] == '-1' ? "<b style='color:yellow;'>".get_lang('invoice_due')."</b>":$order_status;
						$order_status = $order['end_date'] == '-2' ? "<b style='color:red;'>".get_lang('suspended')."</b>":$order_status;
						$order_status = $order['end_date'] == '-3' ? "<b style='color:yellow;'>".get_lang('renewed')."</b>":$order_status;
						$order_status = $order['end_date'] == '-99' ? "<b style='color:red;'>".get_lang('expired')."</b>":$order_status;
						$finish_date = date('d/M/Y H:i',$order['finish_date']);
						echo "<td>Status: <b>$order_status</b>";
						echo "<br>Expiration: <b>$finish_date</b></td>";
					}
					?>
					
			    </tr>

					 <tr class="tr">
                                         <td><?php echo $order['home_name']?></td>
                                         <td><?php echo " [ ".$order['max_players']." ".get_lang('slots').", ".$order['qty']." ".get_lang($order['invoice_duration'])." ]";?>
										 
					</td></tr>

				<?php
				 $max_players = $order['max_players'];
			     $qty = $order['qty']; 
			     $price = $order['price'];
				 $subtotal += $order['price'] * $max_players * $qty;

                 }

				 
				?>
				<tr>
					<td>
				<?php
				if ($status == "not_paid")
				{
					?>
					 <form method="post" action="">
					  <input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
					  <input name="paid" type="submit" value="<?php print_lang("set_as_paid");?>">
					 </form>
					<?php
				}
				elseif($status == "paid")
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
				elseif($status == "procesing_payment")
				{
					?>
					 <form method="post" action="">
					  <input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
					  <input name="paid" type="submit" value="<?php print_lang("set_as_paid");?>">
					 </form>
					<?php
				}
				elseif($status == "paid_and_installed")
				{
					?>
					 <form method="post" action="?m=billing&p=bill">
					  <input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
					  <input name="paid" type="submit" value="<?php print_lang("see_invoice");?>">
					 </form>
					<?php
				}
				?>
				</tr><tr>
				<td>
				<?php 
                    
					echo get_lang('subtotal')." <b>$".number_format( $subtotal , 2 ). " " .$cart['currency']."</b></br>";
				?>
				</td>
				<td>
                <?php				
					//obter as informações de cupom usadas neste pedido
					$coupon_savings = 0;
					if($cart['coupon_id']>0) {
						$result = $db->resultquery("SELECT * from OGP_DB_PREFIXbilling_coupons WHERE id = '". $cart['coupon_id'] . "'");
						foreach($result as $coupon){
							$coupon_savings = $subtotal * ($coupon['discount']/ 100);
							echo "Sub-total c/discount <b>$" .number_format( ($subtotal - $coupon_savings) , 2 ).$cart['currency']."</b></br><td>"; 
							echo "Coupon (".$coupon['code'].") <b>- $" .number_format( $coupon_savings , 2 ).$cart['currency']."</b></br>"; 
							}
					}
				?>
				</td>
				<td>
				<?php
					if ($settings['tax_amount'] > 0){
						echo get_lang('tax')."<b>(".$settings['tax_amount']."%) + $".number_format( $settings['tax_amount']/100*$subtotal, 2 ).$cart['currency']."</b></br>";
					}
				?>
				</td>
				<td>
				<?php	
					//$total = $subtotal-$coupon_savings+($settings['tax_amount']/100*$subtotal);
					$total = ($subtotal - $coupon_savings) * ($settings['tax_amount'] / 100 + 1);
					echo get_lang('total')." <b>$".number_format( $total , 2 ). " " .$cart['currency']."</b>"; 
				?>
				</td>
				<?php
					if($status == "paid_and_installed")
					{
				?>
				</tr>
				<?php
				}
				?>
					
				</tr>
			</table>
		</center>
				<?php
			}
		}
	}
    }//end foreach
}
?>

