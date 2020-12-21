<?php
function curPageName() 
{
	return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
}

function exec_ogp_module()
{		
	require('includes/config.inc.php');
	require_once('modules/settings/functions.php');
    	require_once('includes/form_table_class.php');
    	global $db,$view,$settings;
	
	$currencies = Array ( 
							'AUD'	=>	'Australian Dollar',
							'BRL'	=>	'Brazilian Real',
							'CAD'	=>	'Canadian Dollar',
							'CZK'	=>	'Czech Koruna',
							'DKK'	=>	'Danish Krone',
							'EUR'	=>	'Euro',
							'HKD'	=>	'Hong Kong Dollar',
							'HUF'	=>	'Hungarian Forint',
							'ILS'	=>	'Israeli New Sheqel',
							'JPY'	=>	'Japanese Yen',
							'MYR'	=>	'Malaysian Ringgit',
							'MXN'	=>	'Mexican Peso',
							'NOK'	=>	'Norwegian Krone',
							'NZD'	=>	'New Zealand Dollar',
							'PHP'	=>	'Philippine Peso',
							'PLN'	=>	'Polish Zloty',
							'GBP'	=>	'Pound Sterling',
							'RUB'	=>	'Russian Ruble',
							'SGD'	=>	'Singapore Dollar',
							'SEK'	=>	'Swedish Krona',
							'CHF'	=>	'Swiss Franc',
							'TWD'	=>	'Taiwan New Dollar',
							'THB'	=>	'Thai Baht',
							'TRY'	=>	'Turkish Lira',
							'USD'	=>	'U.S. Dollar'
						);

	asort($currencies);
	
	
	$settings['paypal'] = isset($settings['paypal']) ? $settings['paypal'] : "1";
	$settings['debug'] = isset($settings['debug']) ? $settings['debug'] : "1";
	$settings['sandbox'] = isset($settings['sandbox']) ? $settings['sandbox'] : "1";
	$settings['currency'] = isset($settings['currency']) ? $settings['currency'] : "EUR";
	$settings['daily'] = isset($settings['daily']) ? $settings['daily'] : 1;
	$settings['monthly'] = isset($settings['monthly']) ? $settings['monthly'] : 1;
	$settings['annually'] = isset($settings['annually']) ? $settings['annually'] : 1;
	$settings['tax_amount'] = isset($settings['tax_amount']) ? $settings['tax_amount'] : 7;
	$settings['webhookurl'] = isset($settings['webhookurl']) ? $settings['webhookurl'] : "https://discordapp.com/api/webhooks";
	$settings['checkbox'] = isset($settings['checkbox']) ? $settings['checkbox'] : "Terms and conditions";
    $settings['TOSpopup'] = isset($settings['TOSpopup']) ? $settings['TOSpopup'] : "Accept the TOS";
	$settings['display_free'] = isset($settings['display_free']) ? $settings['display_free'] : "1";


	$settings['paypal_email'] = isset($settings['paypal_email']) ? $settings['paypal_email'] : "Business@E-mail";
	function checked($value){
		global $settings;
		if( $settings[$value] == 1 )
			return 'checked="checked"';
	}
	

	if(isset($_POST['currency']))
	{
		$currency = $_REQUEST['currency'];
	}
	
    if ( isset($_REQUEST['update_settings']) )
    {
        $settings = array(
			"paypal" => $_REQUEST['paypal'],
			"debug" => $_REQUEST['debug'],
			"sandbox" => $_REQUEST['sandbox'],
			"currency" => $currency,
			"daily" => @$_REQUEST['daily'],
			"monthly" => @$_REQUEST['monthly'],
			"annually" => @$_REQUEST['annually'],
			"tax_amount" => $_REQUEST['tax_amount'],
			"webhookurl" => $_REQUEST['webhookurl'],
			"checkbox" => $_REQUEST['checkbox'],
			"TOSpopup" => $_REQUEST['TOSpopup'],
            "display_free" =>$_REQUEST['display_free'],
			"paypal_email" => $_REQUEST['paypal_email']);
			
        $db->setSettings($settings);
        print_success(get_lang('settings_updated'));
        $view->refresh("?m=billing&p=shop_settings");
        return;
    }
	
	$s = ( isset($_SERVER['HTTPS']) and  get_true_boolean($_SERVER['HTTPS']) ) ? "s" : "";
	$p = isset($_SERVER['SERVER_PORT']) & $_SERVER['SERVER_PORT'] != "80" ? ":".$_SERVER['SERVER_PORT'] : NULL ;
	$this_script = 'http'.$s.'://'.$_SERVER['SERVER_NAME'].$p.$_SERVER['SCRIPT_NAME'];
	$current_folder_url = str_replace( curPageName(), "", $this_script);
	
    echo "<h2>".get_lang('shop_settings')."</h2>";

    $ft = new FormTable();
?>
<form>
<tr>
<td></td>
</tr>
</form>
<?php
    $ft->start_form("?m=billing&p=shop_settings");
    $ft->start_table();
	echo "<tr><td colspan='2' ><h3>".get_lang('payment_gateway')."</h4></td></tr>";
	$ft->add_custom_field('paypal','<input type="checkbox" name="paypal" value="1" '.checked('paypal').'/>');
	$ft->add_custom_field('debug','<input type="checkbox" name="debug" value="1" '.checked('debug').'/>');
	$ft->add_custom_field('sandbox','<input type="checkbox" name="sandbox" value="1" '.checked('sandbox').'/>');
	$ft->add_field('string','paypal_email',$settings['paypal_email'],35);
	$ft->add_custom_field('currency',
        create_drop_box_from_array($currencies,"currency",$settings['currency'],false));
	echo "<tr><td colspan='2' ><h3>".get_lang('available_invoice_types')."</h4></td></tr>";
	$ft->add_custom_field('daily','<input type="checkbox" name="daily" value="1" '.checked('daily').'/>');
	$ft->add_custom_field('monthly','<input type="checkbox" name="monthly" value="1" '.checked('monthly').'/>');
	$ft->add_custom_field('annually','<input type="checkbox" name="annually" value="1" '.checked('annually').'/>');
	echo "<tr><td colspan='2' ><h3>Tax Amount</h4></td></tr>";
	$ft->add_field('string','tax_amount',$settings['tax_amount'],2);
	echo "<tr><td colspan='2' ><h3>Other Settings</h4></td></tr>";
	$ft->add_field('string','webhookurl',$settings['webhookurl'],2);
	$ft->add_field('string','checkbox',$settings['checkbox'],2);
    $ft->add_field('string','TOSpopup',$settings['TOSpopup'],2);
   $ft->add_custom_field('display_free','<input type="checkbox" name="display_free" value="1" '.checked('display_free').'/>');
	$ft->end_table();
	$ft->add_button("submit","update_settings",get_lang('update_settings'));
	$ft->end_form();
}
?>



