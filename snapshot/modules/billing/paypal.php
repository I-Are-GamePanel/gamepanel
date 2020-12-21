<?php
function exec_ogp_module()
{
global $db,$view;
$settings = $db->getSettings();
function curPageName() 
				{
					return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
				}


if ( $settings['sandbox'] == 1) {
	$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	$paypal_ipn_url = "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr";
}
else {
	$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
	$paypal_ipn_url = "https://ipnpb.paypal.com/cgi-bin/webscr";
} 

$s = ( isset($_SERVER['HTTPS']) and  get_true_boolean($_SERVER['HTTPS']) ) ? "s" : "";
$port = isset($_SERVER['SERVER_PORT']) & $_SERVER['SERVER_PORT'] != "80" ? ":".$_SERVER['SERVER_PORT'] : NULL ;
$this_script = 'http'.$s.'://'.$_SERVER['SERVER_NAME'].$port.$_SERVER['SCRIPT_NAME'];
$current_folder_url = str_replace( curPageName(), "", $this_script);
$cart_id = $_GET['cart_id'];
$debug =  $settings['debug'];


if(!empty($cart_id))
	{		
		$orders = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_orders WHERE cart_id=".$db->realEscapeSingle($cart_id));
		//get couponID then discount for this cart
		$result= $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_carts WHERE cart_id=".$db->realEscapeSingle($cart_id));
			foreach ($result as $cartDB){
				$coupon_id = $cartDB['id'];
			}

		$coupon_discount = 0;
		$result = $db->resultQuery( "SELECT discount FROM ogp_billing_coupons WHERE id=".$db->realEscapeSingle($cartDB['coupon_id']));
		foreach ($result as $couponDB){
			$coupon_discount=$couponDB['discount'];
		}

		$coupon_discount = $coupon_discount / 100;
		
		if( !empty( $orders ) )
		{
			$cart['price'] = 0;
			foreach($orders as $order) 
			{
				if( $order['qty'] > 1 )
					$order['invoice_duration'] = $order['invoice_duration']."s";				
				$cart['price'] += ($order['price']*$order['max_players']*$order['qty']);

				
				if( !isset( $cart['name'] ) )
					$cart['name'] = $order['home_name']."(".$order['qty'].get_lang($order['invoice_duration']).",".$order['max_players'].get_lang('slots').")";
				else
					$cart['name'] .= ' + '.$order['home_name']."(".$order['qty'].get_lang($order['invoice_duration']).",".$order['max_players'].get_lang('slots').")";
			}
			//price minus coupon discount
			$cart['price'] = $cart['price'] - $cart['price']*$coupon_discount;
			$total = $cart['price']+($settings['tax_amount']/100*$cart['price']);
			if ($total === 0)
			{
				$db->query("UPDATE " . $table_prefix . "billing_carts
												SET paid=1
												WHERE cart_id=".$db->realEscapeSingle($cart_id));
				$view->refresh("home.php?m=billing&p=cart",0);
			}
			$total = number_format( $total , 2 );
		}
	}

// -- GENERATING THE PAYPAL ORDER BUTTON -- 
?>
<html><body <?php if ( $debug != 1) { ?>onload="form1.submit()"<?php } ?>>
<form name="form1" action="<?php echo $paypal_url ?>" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="<?php echo $settings['paypal_email']; ?>">
<input type="hidden" name="item_name" value="<?php echo $cart['name']; ?>">
<input type="hidden" name="item_number" value="<?php echo $cart_id; ?>">
<input type="hidden" name="invoice" value="<?php echo $cart_id; ?>">
<input type="hidden" name="amount" value="<?php echo $total; ?>">
<input type="hidden" name="return" value="<?php echo  $current_folder_url.'modules/billing/bounce.php';?>">
<input type="hidden" name="cancel_return" value="<?php echo $this_script.'?m=billing&p=cart';?>">
<input type="hidden" name="notify_url" value="<?php echo $current_folder_url.'modules/billing/ipn.php';?>">
<input type="hidden" name="currency_code" value="<?php echo $settings['currency'];?>">
<input type="hidden" name="rm" value="2">
<?php 
	if ( $debug == 1) { ?>
	<h3 align="center">Debug Mode<br>
	Post Data being sent to Paypal</h3>
        <?php
	echo "<br>Sandbox Enabled = " .$settings['sandbox'];
	echo "<br>Paypal Url = " .$paypal_url;
	echo "<br>";
	echo "<br>Paypal Email = ".$settings['paypal_email'];
	echo "<br>Item Name = ".$cart['name'];
	echo "<br>Item Number = ".$cart_id;
	echo "<br>Invoice ID = ".$cart_id;
	echo "<br>Amount = ".$total;
	echo "<br>Return Url = ". $current_folder_url."modules/billing/bounce.php";
	echo "<br>Cancel Url = ". $this_script."?m=billing&p=cart";
	echo "<br>Notify Url = ". $current_folder_url."modules/billing/ipn.php";
	echo "<br>Currency Code =". $settings['currency'];
	echo "<br><br>";
	echo "<input type='submit' value='Click To Proceed To Paypal'>";
	}
	echo "After payment, you must return to this site to CREATE YOUR SERVER<br>"; 
	

}
?>





