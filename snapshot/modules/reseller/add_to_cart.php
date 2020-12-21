<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2010 The OGP Development Team
 *
 * http://www.opengamepanel.org/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */

function exec_ogp_module()
{
	global $db;
	$settings = $db->getSettings();
		
	$service_id = $_REQUEST['service_id'];
	
	// Query for Selected service info.
	$services = $db->resultQuery("SELECT DISTINCT * FROM OGP_DB_PREFIXreseller_services WHERE service_id=".$service_id);
	$service = $services[0];
	//Compiling info about invoice to create an invoice order.

	$qty = $_POST['qty'];
	$invoice_duration = $_POST['invoice_duration'];
	$user_id = $_SESSION['user_id'];
	$tax_amount = $settings['tax_amount'];
	$currency = $settings['currency'];
	
	if ($invoice_duration == "month")
	{
		$price_pack = $service['price_per_month'];
	}
	elseif ($invoice_duration == "year")
	{
		$price_pack = $service['price_per_year'];
	}
	
	$price = $price_pack * $qty;
	
	if( isset( $_POST['code'] ) and $_POST['code'] != "" )
	{
		$discount_info = $db->resultQuery("SELECT DISTINCT percentage FROM OGP_DB_PREFIXreseller_discount_codes WHERE code='".$_POST['code']."'");
		if(!empty($discount_info))
		{
			$discount_percentage = $discount_info[0]['percentage'];
			$discount_qty = ( $price / 100 ) * $discount_percentage;
			if( $discount_qty > 0 )
				$price = $price - $discount_qty;
		}
		else
		{
			$discount_percentage = 0;
		}
	}
	else
	{
		$discount_percentage = 0;
	}
	
	global $view;
		
	if( isset( $_POST["add_to_cart"] ) )
	{
		if( isset( $_SESSION['CART'] ) )
		{
			$i = count( $_SESSION['CART'] );
			$i++;
		}
		else
		{
			$i = 0;
		}
		
		$_SESSION['CART'][$i] = array( "cart_id" => $i,
									   "service_id" => $service_id,
									   "user_id" => $_SESSION['user_id'], 
									   "qty" => $qty, 
									   "invoice_duration" => $invoice_duration, 
									   "discount" => $discount_percentage,
									   "price" => $price,
									   "tax_amount" => $tax_amount,
									   "currency" => $currency,
									   "paid" => 0,
									   "end_date" => 0);
		echo '<meta http-equiv="refresh" content="0;url=?m=reseller&p=cart">';
	}
}
?>