<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2017 The OGP Development Team
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

chdir(realpath(dirname(__FILE__))); /* Change to the current file path */
chdir("../.."); /* Base path to ogp web files */
// Report all PHP errors
//error_reporting(E_ALL);
// Path definitions
define("CONFIG_FILE","includes/config.inc.php");
//Requiere
require_once("includes/functions.php");
require_once("includes/helpers.php");
require_once("includes/html_functions.php");
require_once("modules/config_games/server_config_parser.php");
require_once("includes/lib_remote.php");
require_once CONFIG_FILE;
// Connect to the database server and select database.
$db = createDatabaseConnection($db_type, $db_host, $db_user, $db_pass, $db_name, $table_prefix);

$panel_settings = $db->getSettings();
if( isset($panel_settings['time_zone']) && $panel_settings['time_zone'] != "" )
        date_default_timezone_set($panel_settings['time_zone']);


//set the dates to compare to db end_dates
//end_date is when we create an invoice
//invoice created = -1
//server suspended (past finish_date) -2
//server removed ( 7 days past finish date) -99
//server renewed (not used in this file) -3
//finish_date we suspend the server
//finish_date + 7 days we delete the server
$today=time();
$rundate = date('d/M/y G:i',$today);
//did the server expire(finish_date) 7 days ago?
$removal_date = strtotime('- 7 days');
echo $removal_date;
//Server has expired, end_date not -1
//Set the finish date = to end_date
//Set end_date = -1 which enabled invoice
$user_homes = $db->resultQuery( "SELECT *
                                                                 FROM " . $table_prefix .  "billing_orders
                                                                 WHERE end_date > 0 AND end_date < ".$today );

if (!is_array($user_homes))
{
}
else
{
        foreach($user_homes as $user_home)
        {

				$user_id = $user_home['user_id'];
                $home_id = $user_home['home_id'];
				
               
                // Reset the invoice end date -1 so cart.php will create an invoice
				$db->query( "UPDATE " . $table_prefix . "billing_orders
                                         SET end_date=-1
                                         WHERE order_id=".$db->realEscapeSingle($user_home['order_id']));

				// SEND EMAIL
					$settings = $db->getSettings();
					$subject = "You have an INVOICE at ". $panel_settings['panel_name'];
				    $email = $db->resultQuery("   SELECT DISTINCT users_email
									   FROM " . $table_prefix .  "users, " . $table_prefix .  "billing_orders
									   WHERE " . $table_prefix .  "users.user_id = $user_id")[0]["users_email"];
				    $message = "Your server with ID ". $home_id . " will expire soon. Please log in and VIEW INVOICES on the Dashboard to renew your server.<br><br><br>~<br>Thanks!<br>I Are Gamer Support";
				    $mail = mymail($email, $subject, $message, $settings);
					//logger
					$db->logger( "INVOICE created for server " . $home_id);

				 if (!$mail)
                                                  $db->logger( "Email FAILED - Server Invoiced " . $home_id);

				// END EMAIL 
				
				
        }
}

//Order has expired Suspend the server
//end date = 0 OR -1 and finish_date  < today 
//set end date -2 so we know its suspended
$user_homes = $db->resultQuery( "SELECT *
                                                                 FROM " . $table_prefix .  "billing_orders
                                                                 WHERE (end_date = -1 OR end_date = 0) AND finish_date < ".$today);

if (!is_array($user_homes))
{
}
else
{
        foreach($user_homes as $user_home)
        {
                $user_id = $user_home['user_id'];
                $home_id = $user_home['home_id'];
                $home_info = $db->getGameHomeWithoutMods($home_id);
                $server_info = $db->getRemoteServerById($home_info['remote_server_id']);
                $remote = new OGPRemoteLibrary($server_info['agent_ip'], $server_info['agent_port'], $server_info['encryption_key'],$server_info['timeout']);
                $ftp_login = isset($home_info['ftp_login']) ? $home_info['ftp_login'] : $home_id;
                $remote->ftp_mgr("userdel", $ftp_login);
                $db->changeFtpStatus('disabled',$home_id);
                $server_xml = read_server_config(SERVER_CONFIG_LOCATION."/".$home_info['home_cfg_file']);
                if(isset($server_xml->control_protocol_type))$control_type = $server_xml->control_protocol_type; else $control_type = "";
                $addresses = $db->getHomeIpPorts($home_id);
                foreach($addresses as $address)
                {
                        $remote->remote_stop_server($home_id,$address['ip'],$address['port'],$server_xml->control_protocol,$home_info['control_password'],$control_type,$home_info['home_path']);
                }
                $db->unassignHomeFrom("user", $user_id, $home_id);

                // Reset the invoice end date to -2
				// User can still RENEW server
                $db->query( "UPDATE " . $table_prefix . "billing_orders
                                         SET end_date=-2
                                         WHERE order_id=".$db->realEscapeSingle($user_home['order_id']));

			//logger
				$db->logger( "SUSPENDED server " . $home_id);

 				// SEND EMAIL
					$settings = $db->getSettings();
					$subject = "GameServer Suspended at ". $panel_settings['panel_name'];
				    $email = $db->resultQuery("   SELECT DISTINCT users_email
									   FROM " . $table_prefix .  "users, " . $table_prefix .  "billing_orders
									   WHERE " . $table_prefix .  "users.user_id = $user_id")[0]["users_email"];
				    $message = "Your server with ID ". $home_id . " has expired and has been suspended. Please log in and VIEW INVOICES on the Dashboard to renew your server.<br>~<br>Thanks!<br>I Are Gamer Support";
				    $mail = mymail($email, $subject, $message, $settings);
					if (!$mail)
                                                  $db->logger( "Email FAILED - Server Suspended " . $home_id);
				// END EMAIL 

        }
}

// end date = -2 (suspended) and its been suspended for $removal_date days
//set removed servers as -99
$user_homes = $db->resultQuery( "SELECT *
                                                                 FROM " . $table_prefix .  "billing_orders
                                                                 WHERE end_date = -2 AND finish_date < ".$removal_date );

if (!is_array($user_homes))
{
}
else
{
        foreach($user_homes as $user_home)
        {
                $user_id = $user_home['user_id'];
                $home_id = $user_home['home_id'];
                $home_info = $db->getGameHomeWithoutMods($home_id);
                $server_info = $db->getRemoteServerById($home_info['remote_server_id']);
                $remote = new OGPRemoteLibrary($server_info['agent_ip'], $server_info['agent_port'], $server_info['encryption_key'],$server_info['timeout']);

                // Remove the game home from db
                $db->deleteGameHome($home_id);

                // Remove the game home files from remote server
                $remote->remove_home($home_info['home_path']);

                

                // Reset the invoice end date
                $db->query( "UPDATE " . $table_prefix . "billing_orders
                                         SET end_date=-99
                                         WHERE order_id=".$db->realEscapeSingle($user_home['order_id']));

                						
				// Set order as not installed
                $db->query( "UPDATE " . $table_prefix . "billing_orders
                                         SET home_id=0
                                         WHERE cart_id=".$db->realEscapeSingle($user_home['cart_id']));
										 
				//logger
				$db->logger( "DELETED server " . $home_id);

				
				// SEND EMAIL
				    					$settings = $db->getSettings();
					$settings = $db->getSettings();
					$subject = "GameServer DELETED at ". $panel_settings['panel_name'];
				    $email = $db->resultQuery("   SELECT DISTINCT users_email
									   FROM " . $table_prefix .  "users, " . $table_prefix .  "billing_orders
									   WHERE " . $table_prefix .  "users.user_id = $user_id")[0]["users_email"];
				    $message = "Your server with ID ". $home_id . " has been deleted<br><br>You did not renew the service and it was PERMANENTLY REMOVED today. If this was an error, if you contact us immediately we may be able to restore your server.<br>Thanks for being a customer and we hope we can provide a server for you again.<br><br>I Are Gamer Support";
				    $mail = mymail($email, $subject, $message, $settings);
					if (!$mail)
                                                  $db->logger( "Email FAILED - Server Deleted " . $home_id);
				// END EMAIL 


        }
}
?>



