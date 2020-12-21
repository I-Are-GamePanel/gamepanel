<?php
function exec_ogp_module()
{
	if(isset($_POST['cart_id']))
	{
		//Include database connection details
		require('includes/config.inc.php');

		global $db,$view;
		if(isset($_GET['type']) && $_GET['type'] == 'cleared')
		{
			echo '<body onload="window.print()" >';
			$view->setCharset(get_lang('lang_charset'));
		}	
		$settings = $db->getSettings();

		$user_id = $_SESSION['user_id'];
		$cart_id = $_POST['cart_id'];
		
		$isAdmin = $db->isAdmin( $_SESSION['user_id'] );
		
		if ( $isAdmin )
			$accounts = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE cart_id=".$cart_id );
		else
			$accounts = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE cart_id=".$cart_id." AND user_id=".$user_id );
			
		$cart = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXreseller_carts WHERE cart_id=".$cart_id );
			
		if( !empty($accounts) )
		{
		?>
		<br><br>
		<table width="772" height="438" border="0" style="color:#000000" bgcolor="#FFFFFF">
			  <tr bgcolor="#000000">
				<td colspan="5" align="center"  style="color:white">
					<p style="font-size:18pt"><b><?php print_lang("invoice");?></b></p>
				</td>
			  </tr>
			  <tr>
				<td height="21" colspan="5">&nbsp;</td>
			  </tr>
			  <tr>
				<td width="150" height="21" align="left"><?php print_lang("business");?>:<br><b><?php  echo "<b>".$settings['panel_name']."</b>"; ?></td>
				<td colspan="2" rowspan="3">&nbsp;</td>
				<td colspan="2" rowspan="3"><img width="300" height="100" src="images/banner.gif"></td>
			  </tr>
			  <tr>
				<td width="150" height="21" align="left"><?php print_lang("business_email");?>:<br><?php  echo "<b>".$settings['paypal_email']."</b>"; ?></td>
			  </tr>
			  <tr>
				<td height="23" colspan="5">&nbsp;</td>
			  </tr>
			  <tr>
				<td style="border: 2px solid #000000" bgcolor="#CCCCCC" height="23" ><div align=center><strong><?php print_lang("service");?></strong></div></td>
				<td style="border: 2px solid #000000" bgcolor="#CCCCCC"><div align=center><strong><?php print_lang("invoice_duration");?></strong></div></td>
				<td style="border: 2px solid #000000" bgcolor="#CCCCCC"><div align=center><strong><?php print_lang("service_price");?></strong></div></td>
				<td style="border: 2px solid #000000" bgcolor="#CCCCCC"><div align=center><strong><?php print_lang("discount");?></strong></div></td>
				<td style="border: 2px solid #000000" bgcolor="#CCCCCC"><div align=center><strong><?php print_lang("account_price");?></strong></div></td>
			  </tr>
			<?php
			$subtotal = 0;
			foreach($accounts as $account)
			{
				$account_id = $account['account_id'];
				$user_id = $account['user_id'];
				$service_id = $account['service_id'];
				$service = $db->resultQuery( "SELECT * 
											   FROM OGP_DB_PREFIXreseller_services 
											   WHERE service_id=".$service_id );
											   
				$cart = $db->resultQuery( "SELECT * 
										   FROM OGP_DB_PREFIXreseller_carts 
										   WHERE cart_id=".$account['cart_id'] );
				
				$currency = $cart[0]['currency'];
				$service_name = $service[0]['service_name'];
				$slots_max_qty = $service[0]['slot_max_qty'];
				$qty = $account['qty'];
				$invoice_duration = $account['invoice_duration'];
				$discount = $account['discount'];
				$price = $account['price'];
				$subtotal += $price;
				
				//Calculating Costs
					
				if ($invoice_duration == "month")
				{
				$price_slot=$service[0]['price_per_month'];
				}
				elseif ($invoice_duration == "year")
				{
				$price_slot=$service[0]['price_per_year'];
				}

				?>			  
				  <tr>
					<td height="23"><?php  echo $service_name; ?></td>
					<td><?php  echo $qty." ".get_lang($invoice_duration."s"); ?></td>
					<td><?php  echo $price_slot.$currency." / ".get_lang($invoice_duration)." (&nbsp;".$qty*$price_slot.$currency."&nbsp;)"; ?></td>
					<td><?php  echo $discount; ?>%</td>
					<td><?php  echo $price.$currency; ?></td>
				  </tr><?php
			}
			
			$total = $subtotal+($cart[0]['tax_amount']/100*$subtotal);
			
			?>
			  <tr>
				<td height="24" colspan="5">&nbsp;</td>
			  </tr>
			  <tr>
				<td colspan="3" rowspan="4">&nbsp;</td>
				<td height="23" style="border: 2px solid #000000"><div align="right"><strong><?php print_lang("subtotal");?> : </strong></div></td>
				<td style="border: 2px solid #000000"><?php  echo $subtotal.$currency; ?></td>
			  </tr>
			  <tr>
				<td height="23" style="border: 2px solid #000000"><div align="right"><strong><?php print_lang("tax");?> : </strong></div></td>
				<td style="border: 2px solid #000000"><?php  echo $cart[0]['tax_amount']."%"; ?></td>
			  </tr>
			  <tr>
				<td height="23" style="border: 2px solid #000000" bgcolor="#CCCCCC"><div align="right"><strong><?php print_lang("total");?> : </strong></div></td>
				<td style="border: 2px solid #000000" bgcolor="#CCCCCC"><?php  echo $total.$currency; ?></td>
			  </tr>
			  <tr>
				<td height="23" style="border: 2px solid #000000" bgcolor="#CCCCCC"><div align="right"><strong><?php print_lang("cart_id");?> : </strong></div></td>
				<td style="border: 2px solid #000000" ><?php  echo $cart_id; ?></td>
			  </tr>
			  <tr>
				<td height="23" style="border: 2px solid #000000" bgcolor="#CCCCCC"><div align="right"><strong><?php print_lang("payment_date");?> : </strong></div></td>
				<td style="border: 2px solid #000000"><?php  echo $account['payment_date']; ?></td>
			  </tr>
			  <tr>
				<td height="21" colspan="2">&nbsp;</td>
			  </tr>
			</table>
			<br><br>
			<form method='post' action='?m=reseller&p=bill&bt=<?php echo $_GET['bt']; ?>&type=cleared' >
			<input type="hidden" name="cart_id" value="<?php echo $_POST['cart_id'];?>">
			<input type="submit" value="<?php print_lang('print_invoice') ?>" />
			</form>
			<?php
		}
	}
	echo create_back_button($_GET['m'], $_GET['bt']);
}	
?>