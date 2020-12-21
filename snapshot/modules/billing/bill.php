<?php
function exec_ogp_module()
{
	//Include database connection details
	require('includes/config.inc.php');

	global $db,$view,$settings;
	if(isset($_GET['type']) && $_GET['type'] == 'cleared')
	{
		echo '<body onload="window.print()" >';
		$view->setCharset(get_lang('lang_charset'));
	}	

	$user_id = $_SESSION['user_id'];
	$cart_id = $_POST['cart_id'];
	$cart_id = $db->realEscapeSingle($cart_id);
	$isAdmin = $db->isAdmin( $_SESSION['user_id'] );
	if ( $isAdmin )
		$orders = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_orders WHERE cart_id=".$db->realEscapeSingle($cart_id) );
	else
		$orders = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_orders WHERE cart_id=".$db->realEscapeSingle($cart_id)." AND user_id=".$db->realEscapeSingle($user_id) );
		
	$cart = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_carts WHERE cart_id=".$db->realEscapeSingle($cart_id) );
	$tempdate = date_create( $cart[0]['date']);
	$paid_date = date_format($tempdate,"d M Y H:m");

	
			
	if( !empty($orders) )
	{
		?>
		<br><br>
		<table width="772" height="438" border="0" style="color:#000000" bgcolor="#FFFFFF">
			  <tr bgcolor="#000000">
				<td colspan="7" align="center"  style="color:white">
					<p style="font-size:18pt"><b><?php print_lang("invoice");?></b></p>
				</td>
			  </tr>
			  <tr>
				<td align="left" >Paid: <?php  echo $paid_date; ?></td>
			  </tr>
			  <tr>
				<td width="150" height="21" align="left"><b><?php  echo "<b>I Are Gamer</b><br/>
				3400 Laurel Rd<br/>
				Brunswick, OH 44212	"; ?></td>
				<td colspan="4" rowspan="3">&nbsp;</td>
				<td align="center" colspan="2" rowspan="3" ><img src="images/iaregamer.png"><br>Thank you for your preference</td>
			  </tr>
			  <tr>
				<td width="150" height="21" align="left">Email: <?php  echo "<b>".$settings['panel_email_address']."</b>"; ?></td>
			  </tr>
			  <tr>
				<td height="23" colspan="7">&nbsp;</td>
			  </tr>
			  <tr>
				<td height="23" style="border: 2px solid #000000" bgcolor="#222222"><div align="center"><strong><?php print_lang("order");?></strong></div></td>
				<td height="23" style="border: 2px solid #000000" bgcolor="#222222"><div align="center"><strong>Server ID</strong></div></td>
				<td height="23" style="border: 2px solid #000000" bgcolor="#222222"><div align="center"><strong><?php print_lang("item");?></strong></div></td>
				<td  height="23" style="border: 2px solid #000000" bgcolor="#222222"><div align="center"><strong><?php print_lang("invoice_duration");?></strong></div></td>
				<td  height="23" style="border: 2px solid #000000" bgcolor="#222222"><div align="center"><strong><?php print_lang("slot_cost");?></strong></div></td>
				<td  height="23" style="border: 2px solid #000000" bgcolor="#222222"><div align="center"><strong><?php print_lang("slot_quantity");?></strong></div></td>
				<td  height="23" style="border: 2px solid #000000" bgcolor="#222222"><div align="center"><strong><?php print_lang("order_price");?></strong></div></td>
			  <hr/></tr>
		<?php
		$subtotal = 0;		
		foreach($orders as $order)
		{
			$order_id = $order['order_id'];
			$user_id = $order['user_id'];
			$service_id = $order['service_id'];
			$home_name = $order['home_name']." - ".$order_id;
			$ip = $order['ip'];
			$max_players = $order['max_players'];
			$qty = $order['qty'];
			$invoice_duration = $order['invoice_duration'];
			$price = $order['price'];
			$subtotal= $price * $max_players * $qty;
			$subtotal2 += $order['price'] * $max_players * $qty;
			$qry_service = "SELECT DISTINCT price_daily, price_monthly, price_year FROM ".$table_prefix."billing_services WHERE service_id=".$db->realEscapeSingle($service_id);
			$result_service = $db->resultQuery($qry_service);
			$row_service = $result_service[0];

				//Calculating Costs
				
			if ($invoice_duration == "day")
			{
			$price_slot=$row_service['price_daily'];
			}
			elseif ($invoice_duration == "month")
			{
			$price_slot=$row_service['price_monthly'];
			}
			elseif ($invoice_duration == "year")
			{
			$price_slot=$row_service['price_year']*12;
			}
			$duration = $invoice_duration > 1 ? $invoice_duration."s":$invoice_duration;
			
			?>			  
			  <tr>
				<td align="center" height="23"><?php  echo $order_id; ?></td>
				<td align="center" height="23"><?php echo $order['home_id']; ?></td>
				<td align="center" height="23"><?php echo $order['home_name']; ?></td>
				<td align="center"><?php  echo $qty." ".get_lang($duration); ?></td>
				<td align="center"><?php  echo "$" . number_format(floatval(round(($price_slot),2 )),2)." ".$settings['currency']."/".get_lang($invoice_duration); ?></td>
				<td align="center"><?php  echo $max_players; ?></td>
				<td align="center"><?php  echo "$" . number_format(floatval(round(($subtotal),2 )),2)." ".$settings['currency']; ?></td>
			  </tr><?php
		}

		$coupon_savings = 0;
		if($cart[0]['coupon_id']>0) {
			$result = $db->resultquery("SELECT discount from OGP_DB_PREFIXbilling_coupons WHERE id = '". $cart[0]['coupon_id'] . "'");
			foreach($result as $coupon){
				$coupon_savings = $subtotal2 * ($coupon['discount'] / 100);
			}
		}

		//$subtotal2 += $order['price'] * $max_players * $qty;
		//$total = $subtotal2+($cart[0]['tax_amount']/100*$subtotal2);
		$total = ($subtotal2 - $coupon_savings) * ($cart[0]['tax_amount'] / 100 + 1);
		?>
			  <tr>
				<td height="24" colspan="5">&nbsp;</td>
			  </tr>
			  <tr>
				<td colspan="3" rowspan="5">&nbsp;</td>
				<td height="23" style="border: 2px solid #000000"><div align="right"><strong><?php print_lang("subtotal");?> : </strong></div></td>
				<td style="border: 2px solid #000000"><?php echo "$" . number_format(floatval(round(($subtotal2),2 )),2) . " ".$settings['currency']; ?></td>
			  </tr>
			  <?php
			  if($cart[0]['coupon_id']>0) {
				 echo '
			  <tr>
				<td height="23" style="border: 2px solid #000000"><div align="right"><strong>Discount : </strong></div></td>
				<td style="border: 2px solid #000000">'. "$" . number_format(floatval(round((($subtotal2-$coupon_savings)-$subtotal2),2 )),2) . " ".$settings['currency'] .'</td>
			  </tr>';
			  }
			  ?>
			  <tr>
				<td height="23" style="border: 2px solid #000000"><div align="right"><strong><?php print_lang("tax");?> : </strong></div></td>
				<td style="border: 2px solid #000000"><?php  echo $cart[0]['tax_amount']."%"; ?></td>
			  </tr>
			  <tr>
				<td height="23" style="border: 2px solid #000000" bgcolor="#222222"><div align="right"><strong><?php print_lang("total");?> : </strong></div></td>
				<td style="border: 2px solid #000000" bgcolor="#222222"><?php  echo  "$" . number_format(floatval(round(($total),2 )),2)  ." ".$settings['currency']; ?></td>
			  </tr>
			  <tr>
				<td height="23" style="border: 2px solid #000000"><div align="right"><strong></strong></div></td>
				<td style="border: 2px solid #000000"></td>
			  </tr>
			</table>
			<br><br>
			<form method='post' action='?m=billing&p=bill&type=cleared' >
			<input type="hidden" name="cart_id" value="<?php echo $_POST['cart_id'];?>">
			<input type="submit" value="<?php print_lang('print_invoice') ?>" />
			</form>
			<form method='post' action='?m=billing&p=<?php 
			$isAdmin = $db->isAdmin($_SESSION['user_id']);
			if ($isAdmin)
			{
				echo 'orders';
			}
			else
			{
				echo 'cart';
			}
			echo "'><input type='submit' value='";
			print_lang('back');
			?>'/>
			</form>
			<br><br><?php
	}
}	
?>


