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
echo '<script src="js/modules/util.js"></script>';
function exec_ogp_module() 
{
	global $db;
	include 'util_config.php';
	
	$userInfo = $db->getUserById($_SESSION['user_id']);
	$userRole = $userInfo['users_role'];
	$commands = array();
	
	foreach($availableCommands as $command){
		if($userRole == 'admin' && $command['admin'] === true){
			$commands[] = '<option value="'.$command['title'].'">'.get_lang($command['title']).'</option>';
		}
		
		if($userRole == 'user' && $command['user'] === true){
			$commands[] = '<option value="'.$command['title'].'">'.get_lang($command['title']).'</option>';
		}
		
		if($userRole == 'subuser' && $command['subuser'] === true){
			$commands[] = '<option value="'.$command['title'].'">'.get_lang($command['title']).'</option>';
		}
	}
?>
<h2><?php echo get_lang('module_name'); ?></h2>
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"><?php echo get_lang('network_tools'); ?></a></li>
		<li><a href="#tabs-2"><?php echo get_lang('sourcemod_admins'); ?></a></li>
		<li><a href="#tabs-3"><?php echo get_lang('amx_mod_admins'); ?></a></li>
		<li><a href="#tabs-4"><?php echo get_lang('steam_converter'); ?></a></li>
	</ul>
	
	<div id="tabs-1">
		<div>
			<span id="loading_agents" class="show"><?php echo get_lang('loading_agents'); ?></span>
			<span id="loading_failed" class="hide"><?php echo get_lang('loading_failed'); ?></span>
			<span id="no_commands" class="hide"><?php echo get_lang('no_commands'); ?></span>
			<span id="agents_offline" class="hide"><?php echo get_lang('agents_offline'); ?></span>
		</div>
		
		<div id="options" class="hide">
			<div><?php echo get_lang('your_ip'); ?> <span id="your-address"><?php echo getClientIPAddress(); ?></span></div>
			<form action="" method="POST" id="network_tools">
				<div id="select_agent"></div>
				
				<label for="command"><?php echo get_lang('command'); ?></label>
				<select id="command" name="command"><?php echo implode('', $commands); ?></select>
				
				<label for="remote_target"><?php echo get_lang('remote_target'); ?></label>
				<input type="text" id="remote_target" name="remote_target" required>
				<button type="submit"><?php echo get_lang('submit'); ?></button>
			</form>
		</div>
		
		<div id="loading" class="hide"></div>
		<div id="output" class="hide"></div>
	</div><!--/#tabs-1-->
	
	<div id="tabs-2">
		<div>
			<div>
				<span id="no_servers" class="hide"><?php echo get_lang('no_servers'); ?></span>
			</div>
			
			<div id="add_admin" class="hide">
				<form action="" method="POST" id="addadmin_form">
					<div id="games"></div>
					
					<label for="addSteamid"><?php echo get_lang('steamid'); ?></label>
					<input type="text" id="addSteamid" name="addSteamid" pattern="^STEAM_[01]:[01]:\d+$" required>
					
					
					<label for="immunity"><?php echo get_lang('immunity'); ?></label>
					<input type="number" id="immunity" name="immunity" min="1" max="99">
					
					<label for="sourcemod_perms"><?php echo get_lang('sourcemod_perms'); ?></label>
					<select id="sourcemod_perms" name="sourcemod_perms">
						<option value="root"><?php echo get_lang('sourcemod_perm_root'); ?></option>
						<option value="custom"><?php echo get_lang('sourcemod_perm_custom'); ?></option>
					</select>
					
					<div id="sourcemod_flagList" class="hide">
						<fieldset>
							<?php
								foreach(range('a', 't') as $flag){
							?>
							
							<div class="item">
								<input type="checkbox" id="flag_<?php echo $flag; ?>" name="flags[]" value="<?php echo $flag; ?>">
								<label for="flag_<?php echo $flag; ?>"><?php echo get_lang('sourcemod_flag_'.$flag); ?></label>
							</div>
							
							<?php
								}
							?>
						</fieldset>
					</div>
					
					<input type="hidden" name="remote_server_id" id="remote_server_id" value="">
					<input type="hidden" name="gameserver_name" id="gameserver_name" value="">
					<input type="hidden" name="gameserver_ip" id="gameserver_ip" value="">
					<input type="hidden" name="gameserver_port" id="gameserver_port" value="">
					
					<button type="submit"><?php echo get_lang('submit'); ?></button>
				</form>
			</div>
			
			<div id="invalid_server" class="hide"><?php echo get_lang('server_not_selected'); ?></div>
			<div id="invalid_steamid_admin" class="hide"><?php echo get_lang('invalid_steamid'); ?></div>
			<div id="invalid_response_admin" class="hide"><?php echo get_lang('post_failed'); ?></div>
			<div id="invalid_immunity" class="hide"><?php echo get_lang('invalid_immunity'); ?></div>
			
			<div id="all_servers_offline" class="hide"><?php echo get_lang('agents_offline'); ?></div>
			
			<div id="addadmin_response" class="hide"></div>
		</div>
	</div><!--/#tabs-2-->
	
	<div id="tabs-3">
		<div>
			<div>
				<span id="amx_no_servers" class="hide"><?php echo get_lang('no_servers'); ?></span>
			</div>
			
			<div id="amx_add_admin" class="hide">
				<form action="" method="POST" id="amx_addadmin_form">
					<div id="amx_games"></div>
					
					<label for="amx_login_type"><?php echo get_lang('amx_login_type'); ?></label>
					<select id="amx_login_type" name="amx_login_type">
						<option value="amx_login_steamid"><?php echo get_lang('amx_login_steamid'); ?></option>
						<option value="amx_login_nick_pass"><?php echo get_lang('amx_login_nick_pass'); ?></option>
					</select>
					
					<div id="amx_login_steamid">
						<label for="amx_Steamid"><?php echo get_lang('steamid'); ?></label>
						<input type="text" id="amx_Steamid" name="amx_Steamid" pattern="^STEAM_[01]:[01]:\d+$" required>
					</div>
					
					<div id="amx_login_nick_pass"  class="hide">
						<label for="amx_Nickname"><?php echo get_lang('nickname'); ?></label>
						<input type="text" id="amx_Nickname" name="amx_Nickname" pattern="^[^\s][A-zÀ-ÿ0-9 !@)(,}/|\.:?;{#$%&*+=-]{1,28}[^\s]$">
						<label for="amx_Password"><?php echo get_lang('password'); ?></label>
						<input type="text" id="amx_Password" name="amx_Password" pattern="^[^\s][A-zÀ-ÿ0-9 !@)(,}/|\.:?;{#$%&*+=-]{1,28}[^\s]$">
					</div>
					
					<label for="amx_mod_perms"><?php echo get_lang('amx_mod_perms'); ?></label>
					<select id="amx_mod_perms" name="amx_mod_perms">
						<option value="root"><?php echo get_lang('amx_mod_perm_root'); ?></option>
						<option value="custom"><?php echo get_lang('amx_mod_perm_custom'); ?></option>
					</select>
					
					<div id="amx_mod_flagList" class="hide">
						<fieldset>
							<?php
								foreach(range('a', 'u') as $flag){
							?>
							
							<div class="amx_item">
								<input type="checkbox" id="amx_flag_<?php echo $flag; ?>" name="amx_flags[]" value="<?php echo $flag; ?>">
								<label for="amx_flag_<?php echo $flag; ?>"><?php echo get_lang('amx_mod_flag_'.$flag); ?></label>
							</div>
							
							<?php
								}
							?>
						</fieldset>
					</div>
					
					<input type="hidden" name="remote_server_id" id="amx_remote_server_id" value="">
					<input type="hidden" name="gameserver_name" id="amx_gameserver_name" value="">
					<input type="hidden" name="gameserver_ip" id="amx_gameserver_ip" value="">
					<input type="hidden" name="gameserver_port" id="amx_gameserver_port" value="">
					
					<button type="submit"><?php echo get_lang('submit'); ?></button>
				</form>
			</div>
			
			<div id="amx_invalid_server" class="hide"><?php echo get_lang('server_not_selected'); ?></div>
			<div id="amx_invalid_steamid_admin" class="hide"><?php echo get_lang('invalid_steamid'); ?></div>
			<div id="amx_invalid_nickname_admin" class="hide"><?php echo get_lang('invalid_nickname'); ?></div>
			<div id="amx_invalid_password_admin" class="hide"><?php echo get_lang('invalid_password'); ?></div>
			<div id="amx_invalid_response_admin" class="hide"><?php echo get_lang('post_failed'); ?></div>
			
			<div id="amx_all_servers_offline" class="hide"><?php echo get_lang('agents_offline'); ?></div>
			
			<div id="amx_addadmin_response" class="hide"></div>
		</div>
	</div><!--/#tabs-3-->
	
		<div id="tabs-4">
		<div>
			<form action="" method="POST" id="steam_converter">
				<label for="steam_input">SteamID / SteamID3 / SteamID64 / CustomID:</label>
				<input type="text" name="steam_input" id="steam_input" required>
				
				<button type="submit"><?php echo get_lang('submit'); ?></button>
			</form>
		</div>
		
		<div id="invalid_steamid" class="hide"><?php echo get_lang('invalid_steamid'); ?></div>
		<div id="invalid_response" class="hide"><?php echo get_lang('post_failed'); ?></div>
		
		<div id="steam_info">
			<div id="steamLink"></div>
			<div id="steamId"></div>
			<div id="steamId3"></div>
			<div id="steamId64"></div>
		</div>
	</div><!--/#tabs-4-->
</div> <!--/#tabs-->
<div id="translation" 
data-target_empty="<?=get_lang('target_empty')?>"
data-command_empty="<?=get_lang('command_empty')?>"
data-agent_invalid="<?=get_lang('agent_invalid')?>"
data-post_failed="<?=get_lang('post_failed')?>"
data-select_server="<?=get_lang('select_server')?>"
data-select_server_option="<?=get_lang('select_server_option')?>"
data-select_agent="<?=get_lang('select_agent')?>" ></div>
<?php
}
?>
