<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2018 The OGP Development Team
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
require 'includes/lib_remote.php';
function exec_ogp_module() 
{
	global $db;
	include 'modules/util/functions.php';
	include 'modules/util/util_config.php';
	$servers = getUserServers($db->getIpPortsForUser($_SESSION['user_id']), $subuserAdminManagement, $amx_supportedGames);
	
	// If it's a post request and the user is signed in - process it.
	// Otherwise, remove some sensitive info (such as encryption_key) from $servers - only keeping what the user needs to see and json_encode it for JS to process.
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		$serverInfo = array();
		$flags = '';
		$amx_modFlags = range('a', 'u');
		
		// Don't use isset here because they're always going to be set if the form is submitted - we only want to process the data if the following isn't empty.
		if(!empty($_POST['amx_gameserver_id']) && !empty($_POST['remote_server_id']) && !empty($_POST['gameserver_name'])
		&& !empty($_POST['gameserver_ip']) && !empty($_POST['gameserver_port']) && !empty($_POST['amx_mod_perms'])
		&& !empty($_POST['amx_login_type']) && (!empty($_POST['amx_Steamid']) || !empty($_POST['amx_Nickname']) && !empty($_POST['amx_Password']))){
				
			foreach($servers as $server){
				// Try to check if hidden form values have been manually edited. If not, process.
				if($server['remote_server_id'] == $_POST['remote_server_id'] && $server['home_id'] == $_POST['amx_gameserver_id']
				&& $server['game_name'] == $_POST['gameserver_name'] && $server['ip'] == $_POST['gameserver_ip'] && $server['port'] == $_POST['gameserver_port']){	
					$serverInfo = $server;
					break;
				}
			}
			
			if(!empty($serverInfo)){
				$remote = new OGPRemoteLibrary($serverInfo['agent_ip'], $serverInfo['agent_port'], $serverInfo['encryption_key'], $serverInfo['timeout']);
				
				if($remote->status_chk() === 1){
					if(($_POST['amx_login_type'] == 'amx_login_steamid' && preg_match('/^STEAM_[01]:[01]:\d+$/', $_POST['amx_Steamid']))
					|| ($_POST['amx_login_type'] == 'amx_login_nick_pass'
					&& preg_match('/^[^\s][A-zÀ-ÿ0-9 !@)(,}\/|\.:?;{#$%&*+=-]{1,28}[^\s]$/', $_POST['amx_Nickname'])
					&& preg_match('/^[^\s][A-zÀ-ÿ0-9 !@)(,}\/|\.:?;{#$%&*+=-]{1,28}[^\s]$/', $_POST['amx_Password']))){
						
						if($_POST['amx_mod_perms'] == 'root'){
							$flags = 'abcdefghijklmnopqrstu';
						}elseif($_POST['amx_mod_perms'] == 'custom'){
							if(!empty($_POST['amx_flags']) && is_array($_POST['amx_flags'])){
								$x = array_intersect(array_values($_POST['amx_flags']), $amx_modFlags);
								$flags = implode('', ($x));
							}
						}
						
						if(!empty($flags)){
							$adminFile = $serverInfo['home_path'].'/'.$serverInfo['mod_key'].'/'.$adminFiles['amx_mod'];
							// Build up what the new line will be.
							if($_POST['amx_login_type'] == 'amx_login_steamid')
							{
								$newLine = "\"{$_POST['amx_Steamid']}\" \"\" \"${flags}\" \"ce\"";
							}
							elseif($_POST['amx_login_type'] == 'amx_login_nick_pass')
							{
								$newLine = "\"{$_POST['amx_Nickname']}\" \"{$_POST['amx_Password']}\" \"${flags}\" \"a\"";
							}
														
							// Only process if the $adminFile exists;
							if($remote->rfile_exists($adminFile) === 1){
								$remote->remote_readfile($adminFile, $file_content);
								
								// Decide if it's replacing an existing line or is a new line.
								if($_POST['amx_login_type'] == 'amx_login_steamid')
								{
									if(preg_match('/'.preg_quote($_POST['amx_Steamid']).'/i', $file_content)){
										$file_content = preg_replace('/.*'.preg_quote($_POST['amx_Steamid']).'.*/i', $newLine, $file_content);
									}else{
										$file_content .= $newLine."\r\n";
									}
								}
								elseif($_POST['amx_login_type'] == 'amx_login_nick_pass')
								{
									if(preg_match('/'.preg_quote($_POST['amx_Nickname']).'/i', $file_content)){
										$file_content = preg_replace('/.*'.preg_quote($_POST['amx_Nickname']).'.*/i', $newLine, $file_content);
									}else{
										$file_content .= $newLine."\r\n";
									}
								}
								
								if($remote->remote_writefile($adminFile, $file_content) === 1){
									if(!empty($serverInfo['control_password'])){
										$reloadAdmins = $remote->remote_send_rcon_command($serverInfo['home_id'], $serverInfo['ip'], $serverInfo['port'], 'rcon', $serverInfo['control_password'], '', 'amx_reloadadmins', $return);
										
										if($reloadAdmins === -1){
											echo get_lang('rcon_reload_admins_failed');
										}elseif($reloadAdmins === 1){
											echo $return;
										}
									}else{
										// No rcon password stored - can't reload admins
										echo get_lang_f('add_success_no_rcon', $_POST['amx_Steamid']);
									}
								}else{
									// There was a problem writing to the admin file.
									echo get_lang_f('writefile_error', $adminFile);
								}
							}else{
								// The remote admin file doesn't exist.
								echo get_lang_f('remotefile_nonexistent', $adminFiles['amx_mod']);
							}
						}else{
							// There wasn't any flags specified.
							echo get_lang('empty_flag_list');
						}
					}else{
						// invalid steam_id format given.
						echo get_lang('invalid_steam_format');
					}
				}else{
					// Agent is offline. We can't add any admins here.
					echo get_lang('selected_server_offline');
				}
			}else{
				// the hidden input values don't exist in our servers array. however, they should exist.
				// if we're here: 1) the hidden variables have either been manually changed, or 2) the user was removed from accessing the selected server while still on the page.
				echo get_lang('malformed_form');
			}
		}else{
			// An empty form was submitted.
			echo get_lang('empty_form_data');
		}
	}else{
		$return = array();
		for($x = 0; $x < count($servers); ++$x){
			$return[] = array(
				'remote_server_id'		=>	$servers[$x]['remote_server_id'],
				'ip'					=>	$servers[$x]['ip'],
				'port'					=>	$servers[$x]['port'],
				'home_id'				=>	$servers[$x]['home_id'],
				'home_name'				=>	$servers[$x]['home_name'],
				'game_name'				=>	$servers[$x]['game_name'],
			);
		}
		
		echo json_encode($return);
	}
}
?>