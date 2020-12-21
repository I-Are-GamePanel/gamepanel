<?php
function exec_ogp_module()
{
	require('includes/config.inc.php');

	global $db,$view;
	
	$settings = $db->getSettings();
	
	$cart_id = $_GET['cart_id'];

	if(!empty($cart_id))
	{		
		$accounts = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE cart_id=".$cart_id );
		$carts = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXreseller_carts WHERE cart_id=".$cart_id );
		$cart = $carts[0];
		if( !empty( $accounts ) )
		{
			$cart['price'] = 0;
			foreach($accounts as $account) 
			{
				if( $account['qty'] > 1 )
					$account['invoice_duration'] = $account['invoice_duration']."s";
				
				$cart['price'] += $account['price'];
				
				if( !isset( $cart['name'] ) )
					$cart['name'] = $account['qty'].get_lang($account['invoice_duration']).",".$account['available_slots'].get_lang('slots');
				else
					$cart['name'] .= ' + '.$account['qty'].get_lang($account['invoice_duration']).",".$account['available_slots'].get_lang('slots');
			}
				
			$total = $cart['price']+($cart['tax_amount']/100*$cart['price']);
			if ($total === 0)
			{
				$db->query("UPDATE " . $table_prefix . "reseller_carts
												SET paid=1
												WHERE cart_id=".$cart_id);
				$view->refresh("home.php?m=reseller&p=cart",0);
			}
			else
			{
				// Setup class
				require_once('paypal.class.php');  // include the class file
				
				$receiver_email = $settings['paypal_email'];
				
				$p = new paypal_class;             // initiate an instance of the class
				//$p->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';   // Paypal Sandbox URL for developers (https://developer.paypal.com)
				$p->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';     // PayPal url
					
				// setup a variable for this script (ie: 'http://www.micahcarrick.com/paypal.php')
				$this_script = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
				
				function curPageName() 
				{
					return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
				}
				
				$current_folder_url = str_replace( curPageName(), "", $this_script);
				
				$p->add_field('business', $receiver_email);
				$p->add_field('currency_code', $settings['currency']);
				$p->add_field('return', $this_script.'?m=reseller&p=paid');
				$p->add_field('cancel_return', $this_script.'?m=reseller&p=cart');
				$p->add_field('notify_url', $current_folder_url.'modules/reseller/paid-ipn.php');
				$p->add_field('item_name', $cart['name']);
				$p->add_field('item_number', $cart_id);
				$p->add_field('amount', number_format( $total , 2 ));
				echo "<h2>".get_lang('redirecting_to_paypal')."</h2>";
				echo "<center><img style='border:4px dotted white;background:black' src='modules/addonsmanager/loading.gif' width='180' height='180' /img></center>";
				$p->submit_paypal_post(); // submit the fields to paypal
				//$p->dump_fields();      // for debugging, output a table of all the fields
			}
		}
	}
}
?>