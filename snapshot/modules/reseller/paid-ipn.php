<?php
ini_set('log_errors', true);
ini_set('error_log', dirname(__FILE__).'/ipn_errors.log');

// instantiate the IpnListener class
include('ipnlistener.php');
$listener = new IpnListener();

// Enable sandbox for developers (https://developer.paypal.com)
//$listener->use_sandbox = true;

try {
    $listener->requirePostMethod();
    $verified = $listener->processIpn();
} catch (Exception $e) {
    error_log($e->getMessage());
}

chdir("../../"); /* It just makes life easier */

set_include_path(get_include_path() . PATH_SEPARATOR . "includes/");

/* Includes */
require_once("helpers.php");
require_once("config.inc.php");
require_once("functions.php");
require_once("lib_remote.php");
require_once("lang.php");
require_once("modules/config_games/server_config_parser.php");
ogpLang();

/* Query DB */
$db = createDatabaseConnection($db_type, $db_host, $db_user, $db_pass, $db_name, $table_prefix);

$panel_settings	= $db->getSettings();

$this_script = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
				
function curPageName()
{
	return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
}

$current_folder_url = str_replace( curPageName(), "", $this_script);

if( empty( $panel_settings['panel_name'] ) )
	$panel_name = "Open Game Panel";
else
	$panel_name = $panel_settings['panel_name'];

$ipn = $_POST;

if(empty($ipn))
{
	exit(0);
}

$to = $ipn['receiver_email'] . ', ' . $ipn['payer_email'];

$body = "<b>PayPal Payment For <a href='".
		$current_folder_url.
		"../../index.php?m=reseller&p=shop_guest' >".
		$panel_name."</a></b><br><br>".
		"<h2>Order</h2>".
		"- Item: ".$ipn['item_name']."<br>".
		"- Item number: ".$ipn['item_number']."<br>".
		"- Quantity: ".$ipn['quantity']."<br>".
		"- Shipping: ".$ipn['shipping']."<br>".
		"- Tax: ".$ipn['tax']."<br>".
		"- Currency: ".$ipn['mc_currency']."<br>".
		"- Currency fee: ".$ipn['mc_fee']."<br>".
		"- Currency gross: ".$ipn['mc_gross']."<br>".
		"- Transaction type: ".$ipn['txn_type']."<br>".
		"- Transaction ID: ".$ipn['txn_id']."<br>".
		"- Notify version: ".$ipn['notify_version']."<br><br>".
		"<h2>Payer Info</h2>".
		"- ID: ".$ipn['payer_id']."<br>".
		"- First name: ".$ipn['first_name']."<br>".
		"- Last name: ".$ipn['last_name']."<br>".	
		"- Email: ".$ipn['payer_email']."<br>".
		"- Email status: ".$ipn['payer_status']."<br><br>".
		"<h2>Address</h2>".
		"- Name: ".$ipn['address_name']."<br>".
		"- Street: ".$ipn['address_street']."<br>".
		"- City: ".$ipn['address_city']."<br>".
		"- State: ".$ipn['address_state']."<br>".
		"- Zip: ".$ipn['address_zip']."<br>".
		"- Country code: ".$ipn['address_country_code']."<br>".
		"- Country: ".$ipn['address_country']."<br>".
		"- Residence country code: ".$ipn['residence_country']."<br>".
		"- Address status: ".$ipn['address_status']."<br><br>".
		"<h2>Payment Receiver Info</h2>".
		"- Email: ".$ipn['receiver_email']."<br>".
		"- ID: ".$ipn['receiver_id']."<br><br>".
		"<h2>Payment</h2>".
		"- Type: ".$ipn['payment_type']."<br>".
		"- Date: ".$ipn['payment_date']."<br>".
		"- Status: ".$ipn['payment_status']."<br>";
/*
The processIpn() method returned true if the IPN was "VERIFIED" and false if it
was "INVALID".
*/
if ($verified AND isset( $ipn['payment_status'] ) ) 
{
	if( $ipn['payment_status']=="Completed" OR $ipn['payment_status']=="Canceled_Reversal" )
	{  
		$query = "UPDATE " . $table_prefix . "reseller_carts
				  SET paid=1
				  WHERE cart_id=".$ipn['item_number'];
		$db->query($query);
		
		$query = "UPDATE " . $table_prefix . "reseller_accounts
				  SET payment_date=NOW()
				  WHERE cart_id=".$ipn['item_number'];
		$db->query($query);
		
		$cart_accounts = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE cart_id=".$ipn['item_number']);
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
	elseif( $ipn['payment_status']=="Pending" OR $ipn['payment_status']=="In-Progress" )
	{
		$query = "UPDATE " . $table_prefix . "reseller_carts
				  SET paid=2
				  WHERE cart_id=".$ipn['item_number'];
		$db->query($query);
	}
	elseif( $ipn['payment_status']=="Reversed" OR $ipn['payment_status']=="Refunded" OR $ipn['payment_status']=="Denied" OR $ipn['payment_status']=="Expired" OR $ipn['payment_status']=="Failed" OR $ipn['payment_status']=="Voided" OR $ipn['payment_status']=="Partially_Refunded" )
	{
		$body .= "- Reason code: ".$ipn['reason_code']; 
		$query = "UPDATE " . $table_prefix . "reseller_carts
				  SET paid=2
				  WHERE cart_id=".$ipn['item_number'];
		$db->query($query);
		
		$cart_accounts = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE cart_id=".$ipn['item_number']);
		foreach ( $cart_accounts as $account )
		{
			$months = $account['invoice_duration'] == "month" ? 1 : 12;
			$total_months = $months * $account['qty'];
			
			$db->query("UPDATE OGP_DB_PREFIXreseller_accounts
						SET available_months=0 ".
					   "WHERE account_id=".$account['account_id'] );
				   
			$db->query( "UPDATE OGP_DB_PREFIXreseller_accounts ".
						"SET end_date=0 ".
						"WHERE account_id=".$account['account_id'] );
		}
	}		  
	$subject = "Payment ".$ipn['payment_status'];
	mymail($to, $subject, $body, $panel_settings);
}

?>