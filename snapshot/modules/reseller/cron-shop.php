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
 
chdir(realpath(dirname(__FILE__))); /* Change to the current file path */
chdir("../.."); /* Base path to ogp web files */
// Report all PHP errors
error_reporting(E_ALL);
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

//Remove outdated user homes, It must be a cron or in home.php
$user_homes = $db->resultQuery( "SELECT * 
								 FROM OGP_DB_PREFIXreseller_homes
								 WHERE end_date>0 AND end_date<NOW()" );

if (!is_array($user_homes))
{
	echo "Nothing to do at reseller homes.\r\n";
}
else
{
	foreach($user_homes as $user_home)
	{
		$user_id = $user_home['user_id'];
		$home_id = $user_home['home_id'];
		$home_info = $db->getGameHomeWithoutMods($home_id);
		$server_info = $db->getRemoteServerById($home_info['remote_server_id']);
		$remote = new OGPRemoteLibrary($server_info['agent_ip'], $server_info['agent_port'], $server_info['encryption_key']);
		$ftp_login = isset($home_info['ftp_login']) ? $home_info['ftp_login'] : $home_id;
		$remote->ftp_mgr("userdel", $ftp_login);
		$db->changeFtpStatus('disabled',$home_id);
		$addresses = $db->getHomeIpPorts($home_id);
		foreach($addresses as $address)
		{	
			$server_xml = read_server_config(SERVER_CONFIG_LOCATION."/".$home_info['home_cfg_file']);
			if(isset($server_xml->control_protocol_type))$control_type = $server_xml->control_protocol_type; else $control_type = "";
			$remote->remote_stop_server($home_id,$address['ip'],$address['port'],$server_xml->control_protocol,$home_info['control_password'],$control_type);
		}
		
		// Unassign Home to the current owner at DB.
		$db->unassignHomeFrom("user", $user_id, $home_id);
		
		// Remove the game home from DB
		$db->deleteGameHome($home_id);
		
		// Remove the game home files from remote server
		$remote->remove_home($home_info['home_path']);
		
		echo "Home ID $home_id removed successfully.\r\n";
		
		// Restore slots in the reseller account
		$qry_accounts = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE account_id=".$user_home['account_id']);
		$account = $qry_accounts[0];
		$update_available_slots = $account['available_slots'] + $user_home['assigned_slots'];
		$db->query("UPDATE OGP_DB_PREFIXreseller_accounts SET available_slots=".$update_available_slots." WHERE account_id=".$account['account_id']);
		
		// Delete the reseller home entry
		$db->query( "DELETE FROM " . $table_prefix . "reseller_homes
					 WHERE home_id=" . $home_id);

		echo "The amount of available slots available at the reseller account with ID ".$account['account_id']." has been updated,\r\nnow haves ".$update_available_slots." free slots.\r\n";
	}
}	
$reseller_accounts = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE end_date>0" );
if (!is_array($reseller_accounts))
{
	echo "Nothing to do at reseller accounts.\r\n";
}
else
{
	$changes = FALSE;
	foreach($reseller_accounts as $account)
	{
		$months_old_query = $db->resultQuery( "SELECT TIMESTAMPDIFF(MONTH,'".$account['payment_date']."',NOW());" );
		$months_old = $months_old_query[0]["TIMESTAMPDIFF(MONTH,'".$account['payment_date']."',NOW())"];
		if( $months_old > 0 )
		{
			$months = $account['invoice_duration'] == "month" ? 1 : 12;
			$total_months = $months * $account['qty'];
			$update_available_months = $total_months - $months_old;
			
			if( $update_available_months <= 0 )
			{
				$db->query("UPDATE OGP_DB_PREFIXreseller_accounts SET available_months=".$update_available_months." WHERE account_id=".$account['account_id']);
				$db->query("UPDATE OGP_DB_PREFIXreseller_accounts SET end_date=-1 WHERE account_id=".$account['account_id']);
				echo "The reseller account with ID ".$account['account_id']." has expired (0 months available).\r\n";
				$changes = TRUE;
			}
			else
			{
				if( $account['available_months'] != $update_available_months )
				{
					$db->query("UPDATE OGP_DB_PREFIXreseller_accounts SET available_months=".$update_available_months." WHERE account_id=".$account['account_id']);
					echo "The amount of available months at reseller account ID ".$account['account_id']." has been updated,\r\n".$update_available_months." months remaining to expire.\r\n";
					$changes = TRUE;
				}
			}
		}
	}
	if( ! $changes )
	{
		echo "Nothing to do at reseller accounts.\r\n";
	}
}


?>
