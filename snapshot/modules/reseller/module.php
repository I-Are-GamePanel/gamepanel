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

// Module general information
$module_title = "Reseller";
$module_version = "0.1";
$db_version = 0;
$module_required = FALSE;
$module_menus = array(
    array( 'subpage' => 'rs_packs_shop', 'name'=>'Reseller Packs', 'group'=>'user' ),
    array( 'subpage' => 'rs_accounts', 'name'=>'Reseller Accounts', 'group'=>'admin' ),
	array( 'subpage' => 'rs_services', 'name'=>'Reseller Services', 'group'=>'admin' ),
	array( 'subpage' => 'rs_settings', 'name'=>'Reseller Settings', 'group'=>'admin' )
);

$install_queries = array();
$install_queries[0] = array(
	"DROP TABLE IF EXISTS `".OGP_DB_PREFIX."reseller_services`;",
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."reseller_services` (
	`service_id` int(11) NOT NULL auto_increment,
	`service_name` varchar(60) NOT NULL,
	`slot_max_qty` int(11) NOT NULL,
	`price_per_month` float(15,4) NOT NULL,
	`price_per_year` float(15,4) NOT NULL,
	`description` varchar(1000) NOT NULL,
	`remote_server_id` int(11) NOT NULL,
	`start_port` int(11) NOT NULL,
	`end_port` int(11) NOT NULL,
	`max_access_rights` varchar(255) NOT NULL, 
	PRIMARY KEY  (`service_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=UTF8;",
	
    "DROP TABLE IF EXISTS `".OGP_DB_PREFIX."reseller_accounts`;",
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."reseller_accounts` (
	`account_id` int(11) NOT NULL auto_increment,
	`service_id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	`qty` int(11) NULL,
	`invoice_duration` varchar(7) NOT NULL,
	`discount` int(11) NOT NULL,
	`price` int(11) NOT NULL,
	`payment_date` varchar(20) NOT NULL DEFAULT '0',
	`cart_id` int(11) NOT NULL,	
	`end_date` varchar(16) NOT NULL DEFAULT '0',
	`available_months` int(11) NOT NULL DEFAULT '0',
	`available_slots` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY  (`account_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=UTF8;",
	
	"DROP TABLE IF EXISTS `".OGP_DB_PREFIX."reseller_carts`;",
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."reseller_carts` (
	`cart_id` int(11) NOT NULL auto_increment,
	`user_id` int(11) NOT NULL,
	`paid` int(11) NULL,
	`tax_amount` varchar(20) NOT NULL DEFAULT '0',
	`currency` varchar(3) NOT NULL DEFAULT '0',
	PRIMARY KEY  (`cart_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=UTF8;",
	
	"DROP TABLE IF EXISTS `".OGP_DB_PREFIX."reseller_discount_codes`;",
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."reseller_discount_codes` (
	`discount_id` int(11) NOT NULL auto_increment,
	`service_id` int(11) NOT NULL,
	`percentage` int(11) NOT NULL,
	`description` varchar(255) NOT NULL DEFAULT '0',
	`code` varchar(255) NOT NULL,
	PRIMARY KEY  (`discount_id`)
	) ENGINE=MyISAM;",

	"DROP TABLE IF EXISTS `".OGP_DB_PREFIX."reseller_homes`;",
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."reseller_homes` (
	`home_id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	`account_id` int(11) NOT NULL,
	`assigned_slots` int(11) NOT NULL,
	`end_date` varchar(16) NOT NULL DEFAULT '0',
	PRIMARY KEY  (`home_id`)
	) ENGINE=MyISAM;"
);

?>