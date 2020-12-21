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
	include 'modules/util/util_config.php';
	$userInfo = $db->getUserById($_SESSION['user_id']);
	$userRole = $userInfo['users_role'];
	
	$command = trim($_POST['command']);
	$target = trim($_POST['remote_target']);
	
	// Check if the specified agent exists. If it does, assign it to $servers. Otherwise, return that it's an invalid agent.
	if(($server = $db->getRemoteServerById($_POST['agent'])) === false){
		die(get_lang('agent_invalid'));
	}
	
	$remote = new OGPRemoteLibrary($server['agent_ip'], $server['agent_port'], $server['encryption_key'], 60);
	
	if($remote->status_chk() === 0){
		echo get_lang('networktools_agent_offline');
	}elseif(empty($target)){
		echo get_lang('target_empty');
	}elseif(empty($command)){
		echo get_lang('command_empty');
	}else{
		$os = preg_match("/CYGWIN/", $remote->what_os()) ? 'windows' : 'linux';
		
		// Loop over $availableCommands from util_config.php
		// Assign a variable, $allowAccess based on the current user's role and if the config file states the user's role is allowed access to this command.
		for($x = 0; $x < count($availableCommands); ++$x){
			if($availableCommands[$x]['title'] == $command){
				$command = $availableCommands[$x][$os];
				$allowAccess  = $availableCommands[$x][$userRole];
			}
		}
		
		if(isset($allowAccess) && $allowAccess === true){
			// Check the command is available to us. If it's not, echo command_unavilable
			$which = $remote->exec('which '.$command);
			if(empty($which)){
				echo get_lang('command_unavilable');
			}else{
				// Not completely necessary - gethostbyaddr(gethostbyname()) will return false if it's anything that's not valid.
				// This is mostly for logging attempted arbitrary commands.
				if(strpbrk($target, $blockedCharacters)){
					if($logMaliciousUsage){
						$db->logger(get_lang_f('command_bad_characters', $command, htmlentities($target)));
					}
					echo get_lang('command_hacking_attempt');
				}else{
					$target = gethostbyaddr(gethostbyname($target));
					if(!$target){
						echo get_lang('target_invalid');
					}else{
						$exec = $remote->exec($command.' '.$target);
						echo ($exec === null) ? get_lang('exec_failed') : htmlentities(trim($exec));
						if($logAllUsage){
							$db->logger(get_lang_f('command_executed', $command, htmlentities($target)));
						}
					}
				}
			}
		}else{
			// If the user isn't allowed access but they've somehow got this far then they've changed the value="" attr.
			// return with command_no_permissions and log the event.
			echo get_lang('command_no_access');
			if($logMaliciousUsage){
				$db->logger(get_lang_f('command_no_permissions', $command, htmlentities($target)));
			}
		}	//else allowAccess
	}
}
?>