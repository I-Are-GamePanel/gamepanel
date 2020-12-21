<?php


chdir("../../"); /* It just makes life easier */

/* Includes */
require_once("includes/helpers.php");
require_once("includes/config.inc.php");
require_once("includes/functions.php");
require_once("includes/lib_remote.php");
require_once("includes/lang.php");
require_once("modules/config_games/server_config_parser.php");
$db = createDatabaseConnection($db_type, $db_host, $db_user, $db_pass, $db_name, $table_prefix);
$settings = $db->getSettings();
$debug =  $settings['debug'];
$paypal_email = $settings['paypal_email'];  // your paypal email address



	 $cart_id = $_POST['item_number'];

	$fpx = fopen('modules/billing/ipnlog.txt', 'w');
	$header = "====================== CART ID " . $cart_id . " ========================\n";
	fwrite($fpx, $header);
	

// STEP 1: read POST data
// Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
// Instead, read raw POST data from the input stream.
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
  $keyval = explode ('=', $keyval);
  if (count($keyval) == 2)
    $myPost[$keyval[0]] = urldecode($keyval[1]);
}
// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
$req = 'cmd=_notify-validate';
if (function_exists('get_magic_quotes_gpc')) {
  $get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value) {
  if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
    $value = urlencode(stripslashes($value));
  } else {
    $value = urlencode($value);
  }
  $req .= "&$key=$value";
  fwrite($fpx, "$key=$value\n");

}
// Step 2: POST IPN data back to PayPal to validate
if ( $settings['sandbox'] == 1) {
	$ch = curl_init('https://ipnpb.sandbox.paypal.com/cgi-bin/webscr');
	}else {
	$ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr');
	}

curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
// In wamp-like environments that do not come bundled with root authority certificates,
// please download 'cacert.pem' from "https://curl.haxx.se/docs/caextract.html" and set
// the directory path of the certificate as shown below:
// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
if ( !($res = curl_exec($ch)) ) {
  // error_log("Got " . curl_error($ch) . " when processing IPN data");
  curl_close($ch);
  exit;
}
curl_close($ch);


	
// inspect IPN validation result and act accordingly
if (strcmp ($res, "VERIFIED") == 0) {
	fwrite($fpx, "VERIFIED\n");
	// assign posted variables to local variables
	 $item_name = $_POST['item_name'];
	 $item_number = $_POST['item_number'];
	 $payment_status = $_POST['payment_status'];
	 $payment_amount = $_POST['mc_gross'];
	 $payment_currency = $_POST['mc_currency'];
	 $txn_id = $_POST['txn_id'];
	 $receiver_email = $_POST['receiver_email'];
	 $payer_email = $_POST['payer_email'];	
	  
	$db->query("UPDATE OGP_DB_PREFIXbilling_carts
                                  SET paid=1
                                  WHERE cart_id=".$db->realEscapeSingle($cart_id));
	fwrite($fpx, "IPN Processed\n");
	

  // The IPN is verified, process it
	} else if (strcmp ($res, "INVALID") == 0) {
  // IPN invalid, log for manual investigation
	 echo "The response from IPN was: <b>" .$res ."</b>";
}

	fclose($fpx);

// Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
//header("HTTP/1.1 200 OK");
?>






