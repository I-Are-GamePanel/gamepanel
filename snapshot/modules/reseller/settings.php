<?php
function exec_ogp_module()
{		
    require_once('includes/form_table_class.php');
    global $db,$view,$settings;
	
	$currencies = Array ( 
							0 => "EUR",
							1 => "USD",
							2 => "AUD",
							3 => "BRL",
							4 => "CAD",
							5 => "CZK",
							6 => "DKK",
							8 => "HKD",
							9 => "HUF",
							10 => "ILS",
							11 => "JPY",
							12 => "MYR",
							13 => "MXN",
							14 => "NOK",
							15 => "NZD",
							16 => "PHP",
							17 => "PLN",
							18 => "GBP",
							19 => "SGD",
							20 => "SEK",
							21 => "CHF",
							22 => "TWD",
							23 => "THB",
							24 => "TRY"
						);
	
	$settings = $db->getSettings();
	$settings['currency'] = isset($settings['currency']) ? $settings['currency'] : "EUR";
	$settings['price_per_month'] = isset($settings['price_per_month']) ? $settings['price_per_month'] : 1;
	$settings['price_per_year'] = isset($settings['price_per_year']) ? $settings['price_per_year'] : 1;
	$settings['tax_amount'] = isset($settings['tax_amount']) ? $settings['tax_amount'] : 21;
	$settings['paypal_email'] = isset($settings['paypal_email']) ? $settings['paypal_email'] : "Business@E-mail";
	function checked($value){
		global $settings;
		if( $settings[$value] == 1 )
			return 'checked="checked"';
	}
	
    if ( isset($_REQUEST['update_settings']) )
    {
        $settings = array("currency" => $_REQUEST['currency'],
			"price_per_month" => @$_REQUEST['price_per_month'],
			"price_per_year" => @$_REQUEST['price_per_year'],
			"tax_amount" => $_REQUEST['tax_amount'],
			"paypal_email" => $_REQUEST['paypal_email']);
        $db->setSettings($settings);
        print_success(get_lang('settings_updated'));
        $view->refresh("?m=reseller&p=rs_settings");
        return;
    }
	
    echo "<h2>".get_lang('reseller_settings')."</h2>";
    $ft = new FormTable();
    $ft->start_form("?m=reseller&p=rs_settings");
	echo "<h4>".get_lang('currency')."</h4>";
    $ft->start_table();
	$ft->add_custom_field('currency',
        create_drop_box_from_array($currencies,"currency",$settings['currency']));
	$ft->end_table();
	echo "<h4>".get_lang('available_invoice_types')."</h4>";
	$ft->start_table();
	$ft->add_custom_field('price_per_month','<input type="checkbox" name="price_per_month" value="1" '.checked('price_per_month').'/>');
	$ft->add_custom_field('price_per_year','<input type="checkbox" name="price_per_year" value="1" '.checked('price_per_year').'/>');
	$ft->end_table();
	echo "<h4>".get_lang('tax_amount')."</h4>";
	$ft->start_table();
	$ft->add_field('string','tax_amount',$settings['tax_amount'],2);
	$ft->end_table();
	echo "<h4>".get_lang('paypal_email')."</h4>";
	$ft->start_table();
	$ft->add_field('string','paypal_email',$settings['paypal_email'],35);
	$ft->end_table();
	$ft->add_button("submit","update_settings",get_lang('update_settings'));
	$ft->end_form();
}
?>
