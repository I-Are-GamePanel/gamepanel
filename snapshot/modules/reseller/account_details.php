<style>
form table.center tr td{
	width:50%;
	width:50%;
}

table.center{
	color:#333333;
	clear:both;
	width:100%;
	height:35px;
	margin-top:-6px;
	padding-top:10px;
	text-align: center;
	color:#FFFF;
}

table.center tr td{
	border:1px solid #cfcfcf;
	background:#e5e5e5;
}

table.center th{
	border:1px solid #cfcfcf;
	background:#c5c5c5;
}

table.center tr.first_row td{
	border:1px solid #cfcfcf;
	background:#c5c5c5;
}
</style>
<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) Copyright (C) 2008 - 2013 The OGP Development Team
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
	error_reporting(E_ALL);
	
	global $db,$settings;
	$isAdmin = $db->isAdmin($_SESSION['user_id']);
	
	echo "<h2>".get_lang('account_details')."</h2>";
	
	if( isset( $_POST['remove'] ) )
	{
		require_once("modules/config_games/server_config_parser.php");
		require_once("includes/lib_remote.php");
		$user_homes = $db->resultQuery( "SELECT * 
								 FROM OGP_DB_PREFIXreseller_homes
								 WHERE home_id=" . $_POST['home_id'] );
		$user_home = $user_homes[0];
		$user_id = $user_home['user_id'];
		$home_id = $user_home['home_id'];
		$home_info = $db->getGameHomeWithoutMods($home_id);
		$server_info = $db->getRemoteServerById($home_info['remote_server_id']);
		$remote = new OGPRemoteLibrary($server_info['agent_ip'], $server_info['agent_port'], $server_info['encryption_key']);
		$update_ftp_users = "pure-pw userdel ".$home_id." && pure-pw mkdb";
		$remote->sudo_exec( $update_ftp_users );
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
		
		print_success(get_lang_f('home_id_deleted_successfully', $home_id) );
		
		// Restore slots in the reseller account
		$qry_accounts = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE account_id=".$user_home['account_id']);
		$account = $qry_accounts[0];
		$update_available_slots = $account['available_slots'] + $user_home['assigned_slots'];
		$db->query("UPDATE OGP_DB_PREFIXreseller_accounts SET available_slots=".$update_available_slots." WHERE account_id=".$account['account_id']);
		
		// Delete the reseller home entry
		$db->query( "DELETE FROM OGP_DB_PREFIXreseller_homes
					 WHERE home_id=" . $home_id);

		print_success(get_lang_f('the_amount_of_available_slots_has_been_incremented_to', $update_available_slots) );
	}
	
	$filter = isset($_GET['account_id']) ? "account_id=".$_GET['account_id'] : "";
	$filter_and = isset($_GET['account_id']) ? $filter." AND" : "";
	$user_account = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE $filter_and user_id=".$_SESSION['user_id']);
	$where_filter = isset($_GET['account_id']) ? " WHERE ".$filter : "";
	$reseller_homes = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_homes".$where_filter);
    
	if( ! empty($reseller_homes) AND ( !empty($user_account[0]) or $isAdmin ) )
	{
		echo "<table class='center'>";
		echo "<tr><th>".get_lang('home_id')."</th><th>".get_lang('game_server')."</th>".
			 "<th>".get_lang('owner_user_name')."</th>";
		if($isAdmin)
		{
			echo "<th>".get_lang('reseller_user_name')."</th>";
		}
		echo "<th>".get_lang('assigned_slots')."</th>".
			 "<th>".get_lang('end_date')."</th>".
			 "<th>".get_lang('remove_server')."</th></tr>";
		$i = 0;
		foreach($reseller_homes as $user_home) 
		{
			// reseller home data
			$account_id = $user_home['account_id'];
			$assigned_slots = $user_home['assigned_slots'];
			$end_date = $user_home['end_date'];
			$user_id = $user_home['user_id'];
			$home_id = $user_home['home_id'];

			// reseller account data
			$rs_account = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE account_id=".$account_id);
			// reseller user info
			$rs_user_info = $db->getUserById($rs_account[0]['user_id']);
			$reseller_username = $rs_user_info['users_login'];
			// owner user info
			$ow_user_info = $db->getUserById($user_id);
			$owner_username = $ow_user_info['users_login'];
			
			$row = $db->getUserGameHome($user_id,$home_id);
			echo "<tr class='tr".($i++%2)."'><td>$home_id</td><td class='tdh'>$row[game_name]";
			echo empty($row['home_name']) ? get_lang('not_available') : " (".$row['home_name'].")";
			echo "</td><td class='tdh'>";
			if($isAdmin)
			{
				?>
				<a href="?m=user_admin&p=edit_user&user_id=<?php echo $user_id;?>" ><?php echo $owner_username;?></a>
				<?php
				echo "</td>\n";
				echo "</td><td class='tdh'>";
				?>
				<a href="?m=user_admin&p=edit_user&user_id=<?php echo $rs_account[0]['user_id'];?>" ><?php echo $reseller_username;?></a>
				<?php
			}
			else
			{
				echo $owner_username;
			}
			echo "</td><td>$assigned_slots ".get_lang('slots').
				 "</td><td class='tdh'>\n".$end_date."</td>\n";
			echo "</td><td class='tdh'>\n".
				 "<form method=POST><input type=hidden name=home_id value=".$home_id.
				 " />\n<input type=submit name=remove value='".get_lang('remove')."' />\n</form>\n".
				 "</td></tr>\n";
		}
		echo "</table>\n";
	}
	elseif( $isAdmin AND !isset( $_GET['account_id'] ) )
		print_failure(get_lang('there_is_no_reseller_homes_related_to_any_reseller_account'));
	else
	{
		print_failure(get_lang('there_are_no_game_servers_related_to_the_selected_reseller_account'));
	}
	echo create_back_button($_GET['m'], $_GET['bt']);
}
?>