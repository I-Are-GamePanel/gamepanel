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

//require("include/html_functions.php");
function create_selection($selection,$flag)
{
    return "<tr><td align='right'><label for='".clean_id_string($selection)."'>".get_lang($selection).":</label></td>
        <td align='left'><input id='".clean_id_string($selection)."' type='checkbox' name='".$selection."' value='1' checked='checked' /></td></tr><tr>
		<td align='left' class='info' colspan='2'>".get_lang($selection.'_info')."</td></tr>";
}
function exec_ogp_module()
{
    global $db,$view,$settings;
	echo "<h2>".get_lang('rs_assign_servers')."</h2>";
		
    $remote_servers = $db->getRemoteServers();
    if( $remote_servers === FALSE )
    {
		echo "<p class='note'>".get_lang('no_remote_servers_configured')."</p>
              <p><a href='?m=server'>".get_lang('add_remote_server')."</a></p>";

        return;
    }

    $game_cfgs = $db->getGameCfgs();
	$users = $db->getUserList();

    if ( $game_cfgs === FALSE )
    {
        echo "<p class='note'>".get_lang('no_game_configurations_found')." <a href='?m=config_games'>".get_lang('game_configurations')."</a></p>";
        return;
    }
	
	$selections = array( "allow_updates" => "u",
        "allow_file_management" => "f",
        "allow_parameter_usage" => "p",
        "allow_extra_params" => "e",
		"allow_ftp" => "t");
	
    if ( isset($_REQUEST['add_game_server']) )
    {
        $rserver_id = $_POST['rserver_id'];
		$home_cfg_id = $_POST['home_cfg_id'];
		$mod_cfg_id = $_POST['mod_cfg_id'];
		$max_players = $_POST['max_players'];
		$web_user_id = trim($_POST['web_user_id']);
		$qry_accounts = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE account_id=".$_POST['account_id']);
		$account = $qry_accounts[0];
		$qry_service = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_services WHERE service_id=".$account['service_id']);
		$service = $qry_service[0];
		$max_access_rights = $service['max_access_rights'];
		$start_port = $service['start_port'];
		$end_port = $service['end_port'];
		$post_months = $_POST['invoice_duration'] == "month" ? 1 : 12;
		$assigned_months = $post_months * $_POST['qty'];
		$account_months = $account['invoice_duration'] == "month" ? 1 : 12;
		$account_total_months = $account_months * $account['qty'];
			
		if( $account['available_months'] >= $assigned_months AND $account['available_slots'] >= $max_players )
		{
			$control_password = genRandomString(8);
			$access_rights = "";
			
			$ftp = FALSE;
			foreach ($selections as $selection => $flag)
			{
				if (isset($_POST[$selection]))
				{
					if( preg_match( "/$flag/", $max_access_rights ) )
					{
						$access_rights .= $flag;
						if ($flag == "t")
						{
							$ftp = TRUE;
						}
					}
				}
			}
			
			if ( empty( $web_user_id ) )
			{
				print_failure(get_lang('bad_user_name'));
			}
			else
			{
				foreach ( $game_cfgs as $cfg )
				{
					if($cfg['home_cfg_id'] == $home_cfg_id) $server_name = $cfg['game_name'];
				}
				foreach ( $remote_servers as $server )
				{
					if($server['remote_server_id'] == $rserver_id) $ogp_user = $server['ogp_user'];
				}
				foreach ( $users as $user )
				{
					if($user['user_id'] == $web_user_id) $web_user = $user['users_login'];
				}
				$ftppassword = genRandomString(8);
				$game_path = "/home/".$ogp_user."/";
				if ( ( $new_home_id = $db->addGameHome($rserver_id,$web_user_id,$home_cfg_id,
					clean_path($game_path),$server_name,$control_password,$ftppassword) )!== FALSE )
				{
					$db->assignHomeTo("user",$web_user_id,$new_home_id,$access_rights);
					$home_info = $db->getGameHomeWithoutMods($new_home_id);
					require_once('includes/lib_remote.php');
					$remote = new OGPRemoteLibrary($home_info['agent_ip'],$home_info['agent_port'],$home_info['encryption_key']);
					if($ftp)
					{
						$host_stat = $remote->status_chk();
						if( $host_stat === 1)
						{
							$remote->ftp_mgr("useradd", $home_info['home_id'], $home_info['ftp_password'], $home_info['home_path']);
							$db->changeFtpStatus('enabled',$home_info['home_id']);
						}
					}
					
					$home_id = $new_home_id;
					
					$remote_server_ips = $db->getRemoteServerIPs($rserver_id);
					
					$max_id = count($remote_server_ips) - 1;
					$ip_id = ( count($remote_server_ips) > 1 ) ? $remote_server_ips[rand(0,$max_id)]['ip_id'] : $remote_server_ips['0']['ip_id'];
					
					$add_port = $db->addGameIpPort( $home_id, $ip_id, $db->getNextAvailablePort($ip_id,$home_cfg_id) );

					$mod_id = $db->addModToGameHome($home_id,$mod_cfg_id);
					
					if ( $mod_id === FALSE )
					{
						print_failure(get_lang_f('failed_to_assing_mod_to_home',$mod_cfg_id));
						unset($_POST);
					}
					else
					{
						$cliopts = "";
						$cpus = "NA";
						$nice = "0";
						if ( $db->updateGameModParams($max_players,$cliopts,$cpus,$nice,$home_id,$mod_cfg_id) === FALSE )
						{
							print_failure(get_lang_f('failed_to_assing_mod_to_home',$mod_cfg_id));
							unset($_POST);
						}
						else
						{	
							$update_available_slots = $account['available_slots'] - $max_players;
							$db->query("UPDATE OGP_DB_PREFIXreseller_accounts SET available_slots=".$update_available_slots." WHERE account_id=".$account['account_id']);

							$end_date = "ADDDATE(DATE(NOW() + INTERVAL ".$assigned_months." MONTH), 1)";
														
							$query = sprintf('INSERT INTO `%1$sreseller_homes` (`home_id`, `user_id`, `account_id`, `assigned_slots`, `end_date`)
												VALUES(\'%2$s\', \'%3$s\', \'%4$s\', \'%5$s\', %6$s) 
												ON DUPLICATE KEY UPDATE 
												user_id=VALUES(user_id),
												account_id=VALUES(account_id),
												assigned_slots=VALUES(assigned_slots),
												end_date=VALUES(end_date);',
												'OGP_DB_PREFIX',
												$db->real_escape_string($home_id),
												$db->real_escape_string($web_user_id),
												$db->real_escape_string($account['account_id']),
												$db->real_escape_string($max_players),
												$db->real_escape_string($end_date));
							
							$db->query($query);
							
							if ($_POST['installation'] == "manual")
							{
								print_success(get_lang('server_added_successfully_needs_manual_install'));
								unset($_POST);
							}
							else
							{
								// Getting pre and post commands
								$game_mod_precmd = $db->resultQuery("SELECT DISTINCT precmd FROM OGP_DB_PREFIXgame_mods WHERE mod_id='$mod_id'");
								if($game_mod_precmd[0]['precmd'] === NULL OR empty($game_mod_precmd[0]['precmd']))
								{
									$config_mod_precmd = $db->resultQuery("SELECT DISTINCT def_precmd FROM OGP_DB_PREFIXconfig_mods WHERE mod_cfg_id='$mod_cfg_id'");
									if ($config_mod_precmd[0]['def_precmd'] === NULL OR empty($config_mod_precmd[0]['def_precmd']))
										$precmd = "";
									else
										$precmd = $config_mod_precmd[0]['def_precmd'];
								}
								else
									$precmd = $game_mod_precmd[0]['precmd'];
								

								$game_mod_postcmd = $db->resultQuery("SELECT DISTINCT postcmd FROM OGP_DB_PREFIXgame_mods WHERE mod_id='$mod_id'");								
								if($game_mod_postcmd[0]['postcmd'] === NULL OR empty($game_mod_postcmd[0]['postcmd']))
								{
									$config_mod_postcmd = $db->resultQuery("SELECT DISTINCT def_postcmd FROM OGP_DB_PREFIXconfig_mods WHERE mod_cfg_id='$mod_cfg_id'");
									if ($config_mod_postcmd[0]['def_postcmd'] === NULL OR empty($config_mod_postcmd[0]['def_postcmd']))
										$postcmd = "";
									else
										$postcmd = $config_mod_postcmd[0]['def_postcmd'];
								}
								else
									$postcmd = $game_mod_postcmd[0]['postcmd'];
								
								$home_info = $db->getGameHome($home_id);
								$server_xml = read_server_config(SERVER_CONFIG_LOCATION."/".$home_info['home_cfg_file']);	
								$exec_folder_path = clean_path($home_info['home_path'] . "/" . $server_xml->exe_location );
								$exec_path = clean_path($exec_folder_path . "/" . $server_xml->server_exec_name );
								// Starting Game server installation
								if( $_POST['installation'] == "steam" OR $_POST['installation'] == "steamcmd" )
								{
									$mod_xml = xml_get_mod($server_xml, $home_info['mods'][$mod_id]['mod_key']);
									$installer_name = $mod_xml->installer_name;
									$modkey = $home_info['mods'][$mod_id]['mod_key'];
									// Some games like L4D2 require anonymous login
									if($mod_xml->installer_login){
										$login = $mod_xml->installer_login;
										$pass = '';
									}else{
										$login = $settings['steam_user'];
										$pass = $settings['steam_pass'];
									}
									$modname = ( $installer_name == '90' and !preg_match("/(cstrike|valve)/", $modkey) ) ? $modkey : '';
									$betaname = isset($mod_xml->betaname) ? $mod_xml->betaname : '';
									$betapwd = isset($mod_xml->betapwd) ? $mod_xml->betapwd : '';
									preg_match("/(win|linux)(32|64)?$/", $server_xml->game_key, $matches);
									$os = strtolower($matches[1]) == 'linux'? 'linux':'windows';
									$arch = isset($matches[2])?$matches[2]:'32';
									
									if($precmd == "")
									{
										$preInstallCMD = "";
										if(isset($server_xml->post_install))
											$preInstallCMD .= $server_xml->pre_install;
									}
									else
										$preInstallCMD = $precmd;
									
									if($postcmd == "")
									{
										$postInstallCMD = "";
										if(isset($server_xml->post_install))
											$postInstallCMD .= $server_xml->post_install;
									}
									else
										$postInstallCMD = $postcmd;
									
									$postInstallCMD .= "\n{OGP_LOCK_FILE} " . $home_info['home_path'] . "/" . ($server_xml->exe_location ? $server_xml->exe_location . "/" : "") . $server_xml->server_exec_name;
									
									$remote->steam_cmd($home_info['home_id'],$home_info['home_path'],$installer_name,$modname,
													   $betaname,$betapwd,$login,$pass,$settings['steam_guard'],
													   $exec_folder_path,$exec_path,$preInstallCMD,$postInstallCMD,$os,'',$arch);
									print_success(get_lang('server_added_successfully_installing'));
									unset($_POST);
								}
								elseif($_POST['installation'] == "rsync")
								{
									if( isset($server_xml->lgsl_query_name) ) 
										$rs_name = $server_xml->lgsl_query_name;
									elseif( isset($server_xml->gameq_query_name) )
										$rs_name = $server_xml->gameq_query_name;
									elseif( isset($server_xml->protocol) )
										$rs_name = $server_xml->protocol;
									else
										$rs_name = $server_xml->mods->mod['key'];
									$url = "rsync.opengamepanel.org";
									
									if( preg_match("/win32/", $server_xml->game_key) OR preg_match("/win64/", $server_xml->game_key) ) 
										$os = "windows";
									elseif( preg_match("/linux/", $server_xml->game_key) )
										$os = "linux";
										
									$full_url = "$url/ogp_game_installer/$rs_name/$os/";
									$remote->start_rsync_install($home_id,$home_info['home_path'],"$full_url",$exec_folder_path,$exec_path,$precmd,$postcmd);
									print_success(get_lang('server_added_successfully_installing'));
									unset($_POST);
								}
							}
						}
						
					}
				}
				else
				{
					print_failure(get_lang_f("failed_to_add_home_to_db",$db->getError()));
					unset($_POST);
				}
			}
		}
		else
		{
			if( $account['available_months'] < $assigned_months )
				print_failure( get_lang_f('you_assigned_months_this_amount_exceeds_the_available_months_in_this_reseller_account', $assigned_months, $account['available_months']) );
			elseif( $account['available_slots'] < $max_players )
				print_failure( get_lang_f('you_assigned_max_players_this_amount_exceeds_the_available_slots_in_this_reseller_account', $max_players, $account['available_slots']) );
			$view->refresh('home.php?m=reseller&p=rs_assign_server', 5);
		}
    }

	
	if( isset($_POST['account_id']) )
	{
		$qry_accounts = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE user_id=".$_SESSION['user_id']." AND account_id=".$_POST['account_id']);
		$account = $qry_accounts[0];
		$qry_service = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_services WHERE service_id=".$account['service_id']);
		$service = $qry_service[0];
		$invoice_duration_string = $account['qty'] > 1 ? $account['invoice_duration']."s" : $account['invoice_duration'];
		echo "<h4>" . $service['service_name'] . "&nbsp;/&nbsp;" . $account['qty'] . " " . get_lang( $invoice_duration_string ) . "&nbsp;&nbsp;&nbsp;[".get_lang_f( 'available_slots', $account['available_slots'] ).
			 "&nbsp;/&nbsp;".get_lang_f( 'available_months', $account['available_months'] ).
			 "]&nbsp;<a href='?m=reseller&p=account_details&bt=rs_assign_server&account_id=".$account['account_id'].
			 "' >(".get_lang('account_details').")</a></h4>";
	}
    // View form to add more servers.
	echo "<form action='?m=reseller&amp;p=rs_assign_server' method='post'>";
	echo "<table class='center'>";
	if( !isset($_POST['account_id']) )
	{
		$accounts = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_accounts WHERE user_id=".$_SESSION['user_id']. "");
		if( ! empty( $accounts ) )
		{
			echo "<tr><td align=right><b>".get_lang('select_reseller_account')."</b></td><td align=left><select onchange=".'"this.form.submit()"'." name='account_id'>\n";
			echo "<option></option>\n";
			foreach( $accounts as $account )
			{
				$qry_service = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_services WHERE service_id=".$account['service_id']);
				$service = $qry_service[0];
					echo "<option value='".$account['account_id']."'>".
						$service['service_name']." (".get_lang_f('available_slots', $account['available_slots'])."&nbsp;/&nbsp;".get_lang_f('available_months', $account['available_months']).")</option>\n";
			}
			echo "</select>\n";
			echo "</td></tr>";
		}
		else
		{
			print_failure(get_lang('there_is_no_reseller_accounts_available_yet'));
			echo create_back_button($_GET['m'],"rs_packs_shop");
		}
	}
	else
	{
		if( $account['available_slots'] < 1 )
		{
			print_failure(get_lang('there_is_no_slots_available_in_this_reseller_account'));
			echo create_back_button($_GET['m'],"rs_packs_shop");
			return;
		}
		elseif( !isset( $_POST['home_cfg_id'] ) )
		{
			$rhost_id = $service['remote_server_id'];
			$remote_server = $db->getRemoteServer($rhost_id);
			require_once('includes/lib_remote.php');
			$remote = new OGPRemoteLibrary($remote_server['agent_ip'],$remote_server['agent_port'],$remote_server['encryption_key']);
			$host_stat = $remote->status_chk();
			if( $host_stat === 1)
				$os = $remote->what_os();
			else
			{
				print_failure(get_lang_f("caution_agent_offline_can_not_get_os_and_arch_showing_servers_for_all_platforms"));
				$os = "Unknown OS";
			}
			echo "<tr><td align=right><b>".get_lang('select_game')."</b></td><td align=left>\n".
				 "<select name='home_cfg_id' onchange='this.form.submit()' >\n".
				 "<option></option>\n";
			// Linux 64 bits + wine
			if( preg_match("/Linux/", $os) AND preg_match("/64/", $os) AND preg_match("/wine/", $os) )
			{
				foreach ( $game_cfgs as $row )
				{
					if ( preg_match("/linux/", $row['game_key']) )
					echo "<option value='".$row['home_cfg_id']."'>".$row['game_name'];
					if ( preg_match("/64/", $row['game_key']) ) echo " (64 bit)";
					echo "</option>\n";
				}
				echo "<option style='background:black;color:white;' value=''>".get_lang('wine_games').":</option>\n";
				foreach ( $game_cfgs as $row )
				{
					if ( preg_match("/win/", $row['game_key']) )
					echo "<option value='".$row['home_cfg_id']."'>".$row['game_name'];
					if ( preg_match("/64/", $row['game_key']) ) echo " (64 bit)";
					echo "</option>\n";
				}
			}
			// Linux 64 bits
			elseif( preg_match("/Linux/", $os) AND preg_match("/64/", $os) )
			{
				foreach ( $game_cfgs as $row )
				{
					if ( preg_match("/linux/", $row['game_key']))
					echo "<option value='".$row['home_cfg_id']."'>".$row['game_name'];
					if ( preg_match("/64/", $row['game_key']) ) echo " (64 bit)";
					echo "</option>\n";
				}
			}
			// Linux 32 bits + wine
			elseif( preg_match("/Linux/", $os) AND preg_match("/wine/", $os) )
			{ 
				foreach ( $game_cfgs as $row )
				{
					if ( preg_match("/linux32/", $row['game_key']) )
					echo "<option value='".$row['home_cfg_id']."'>".$row['game_name']."</option>\n";
				}
				echo "<option style='background:black;color:white;' value=''>".get_lang('wine_games')."</option>\n";
				foreach ( $game_cfgs as $row )
				{
					if ( preg_match("/win32/", $row['game_key']) )
					echo "<option value='".$row['home_cfg_id']."'>".$row['game_name']."</option>\n";
				}
			}
			// Linux 32 bits
			elseif( preg_match("/Linux/", $os) )
			{ 
				foreach ( $game_cfgs as $row )
				{
					if ( preg_match("/linux32/", $row['game_key']) )
					echo "<option value='".$row['home_cfg_id']."'>".$row['game_name']."</option>\n";
				}
			}
			// Windows 64 bits (CYGWIN)
			elseif( preg_match("/CYGWIN/", $os) AND preg_match("/64/", $os))
			{
				foreach ( $game_cfgs as $row )
				{
					if ( preg_match("/win/", $row['game_key']) )
					echo "<option value='".$row['home_cfg_id']."'>".$row['game_name'];
					if ( preg_match("/64/", $row['game_key']) ) echo " (64 bit)";
					echo "</option>\n";
				}
			}
			// Windows 32 bits (CYGWIN)
			elseif( preg_match("/CYGWIN/", $os))
			{
				foreach ( $game_cfgs as $row )
				{
					if ( preg_match("/win32/", $row['game_key']) )
					echo "<option value='".$row['home_cfg_id']."'>".$row['game_name']."</option>\n";
				}
			}
			elseif ( $os == "Unknown OS" )
			{
				foreach ( $game_cfgs as $row )
				{
					echo "<option value='".$row['home_cfg_id']."'>".$row['game_name'];
					if ( preg_match("/64/", $row['game_key']) ) echo " (64 bit)";
					echo "</option>\n";
				}
			}
			echo "</select>\n".
				 "<input type='hidden' name='account_id' value='".$account['account_id']."' />".
				 "<input type='hidden' name='rserver_id' value='".$rhost_id."' />".
				 "</td></tr>";
		}
		elseif( ! isset( $_POST['mod_cfg_id'] ) )
		{
			?>
			<tr>
			<td align=right><b><?php print_lang('select_mod'); ?></b></td><td align=left>
			<select name="mod_cfg_id" onchange="this.form.submit()" >
			<option></option>
			<?php
			$mod_qry = $db->resultQuery("SELECT DISTINCT mod_cfg_id, mod_name, game_name FROM OGP_DB_PREFIXconfig_mods NATURAL JOIN OGP_DB_PREFIXconfig_homes WHERE home_cfg_id=" . $_POST['home_cfg_id']);
			foreach($mod_qry as $array_mods) 
			{ 
				if($array_mods['mod_name'] == "none")$array_mods['mod_name']=$array_mods['game_name'];
			?>
				<option value="<?php echo $array_mods['mod_cfg_id'];?>"><?php  echo $array_mods['mod_name'];?></option>
			<?php 
				
			}
			?>
			</select>
			<input type="hidden" name="home_cfg_id" value="<?php echo $_POST['home_cfg_id']; ?>"/>
			<input type="hidden" name="account_id" value="<?php echo $account['account_id']; ?>" />
			<input type="hidden" name="rserver_id" value="<?php echo $_POST['rserver_id']; ?>" />
			</td>
			<tr>
			<?php 
		}
		elseif( ! isset( $_POST['max_players'] ) )
		{
			$cfg_info = $db->resultQuery("SELECT DISTINCT home_cfg_file FROM OGP_DB_PREFIXconfig_homes WHERE home_cfg_id=" . $_POST['home_cfg_id']);
			$server_xml = read_server_config(SERVER_CONFIG_LOCATION.$cfg_info[0]['home_cfg_file']);
			
			if( isset( $server_xml->installer )  )
			{
				$installation = $server_xml->installer;
			}
			else
			{
				if( isset($server_xml->lgsl_query_name) ) 
					$lgslname = $server_xml->lgsl_query_name;
				elseif( isset($server_xml->gameq_query_name) )
					$lgslname = $server_xml->gameq_query_name;
				elseif( isset($server_xml->protocol) )
					$lgslname = $server_xml->protocol;
				else
					$lgslname = $server_xml->mods->mod['key'];

				$sync_list = @file("modules/gamemanager/rsync.list", FILE_IGNORE_NEW_LINES);
				
				if ( in_array($lgslname, $sync_list) ) 
				{
					$installation = "rsync";
				}
				else
				{
					$installation = "manual";
				}
			}
			echo "<tr><td align=right>";
			if ( $server_xml->max_user_amount )
			{
				echo "<b>".get_lang('max_players')."</b></td>";
				$account['available_slots'];
				$max_selectable_players = $server_xml->max_user_amount <= $account['available_slots'] ? $server_xml->max_user_amount : $account['available_slots'];
				echo "<td align=left>".create_drop_box_from_array(range(0,$max_selectable_players),
					 'max_players" onchange="this.form.submit()',0,true).
					 '<input type="hidden" name="mod_cfg_id" value="'.$_POST['mod_cfg_id'].'"/>'.
					 '<input type="hidden" name="home_cfg_id" value="'.$_POST['home_cfg_id'].'"/>'.
					 '<input type="hidden" name="installation" value="'.$installation.'"/>'.
					 "<input type='hidden' name='account_id' value='".$account['account_id']."' />".					 "<input type='hidden' name='rserver_id' value='".$_POST['rserver_id']."' />";
			}
			echo "</td></tr>";
		}
		elseif( ! isset( $_POST['invoice_duration'] ) )
		{
			echo "<tr><td align=right>";
			echo "<b>".get_lang('invoice_duration')."</b></td>";
			?>
			<td  align=left>
			<select name="qty">
			<?php 
			$qty=1;
			while($qty<=12)
			{
				if( $qty > $account['available_months'] )
					break;
				echo "<option value='$qty'>$qty</option>";
				$qty++;
			}
			?>
			</select>
			<select name="invoice_duration">
			<?php
			if( $settings['price_per_month'] == 1 AND $account['available_months'] > 0 ) echo '<option value="month">'.get_lang('months').'</option>';
			if( $settings['price_per_year'] == 1 AND $account['available_months'] > 11 ) echo '<option value="year">'.get_lang('years').'</option>';
			?>
			</select>
			<?php
			$max_access_rights = $service['max_access_rights'];
			// Select user
			echo "<tr><td align=right><b>".get_lang('assign_to')."</b></td>\n".
				 "<td class='left'><select name='web_user_id'>\n";
			$users = $db->getUserList();
			foreach ( $users as $user )
				echo "<option value='".$user['user_id']."'>".$user['users_login']."</option>\n";
			echo "</select>\n</td></tr>";
			// Select permisions
			echo "<tr><td colspan=2><h4>".get_lang('access_rights')."</h4></td></tr>";
			foreach ( $selections as $selection => $flag)
			{
				if( preg_match( "/$flag/", $max_access_rights ) )
					echo create_selection($selection,$flag);
			}
			echo '<input type="hidden" name="mod_cfg_id" value="'.$_POST['mod_cfg_id'].'"/>'.
				 '<input type="hidden" name="home_cfg_id" value="'.$_POST['home_cfg_id'].'"/>'.
				 '<input type="hidden" name="installation" value="'.$_POST['installation'].'"/>'.
				 '<input type="hidden" name="max_players" value="'.$_POST['max_players'].'"/>'.
				 "<input type='hidden' name='account_id' value='".$account['account_id']."' />".
				 "<input type='hidden' name='rserver_id' value='".$_POST['rserver_id']."' />".
				 "</td><tr><td align='center' colspan='2'>".
				 "<input type='submit' name='add_game_server' value='".get_lang('add_game_server')."' />";
				 "</td></tr>";
		}
	}
	echo "</table></form>";
}
?>
