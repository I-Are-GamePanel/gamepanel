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

require_once("modules/config_games/server_config_parser.php");
require_once('includes/lib_remote.php');
require_once('modules/fast_download/functions.php');
require('includes/form_table_class.php');
function exec_ogp_module()
{
	global $db,$view;
	echo "<h2>".get_lang("fast_dl")."</h2>\n";
	$remote_servers = $db->getRemoteServers();
	if ( !$remote_servers )
		return;
	if(isset($_GET['access_rules']))
	{
		echo "<h3>".get_lang("create_access_rules")."</h3>\n";
		echo print_failure(get_lang("warning_access_rules_applied_once_alias_created"));
		$game_cfgs = $db->getGameCfgs();
		$select_game = "<select onchange=".'"this.form.submit()"'." name='home_cfg_id' autofocus='autofocus'>\n".
					     "<option value='0'>" . get_lang("games_without_specified_rules") . "</option>\n";
		foreach ( $game_cfgs as $game_cfg )
		{
			$selected = ( isset( $_GET['home_cfg_id'] ) and 
						  $_GET['home_cfg_id'] == $game_cfg['home_cfg_id'] ) ?
						  "selected=selected" : "";
			$select_game .= "<option value='".$game_cfg['home_cfg_id']."' $selected>".$game_cfg['game_name'];
			if ( preg_match("/win(32|64)/i", $game_cfg['game_key']) )
				$select_game .= " (Windows)";
			else
				$select_game .= " (Linux)";
			if ( preg_match("/(win|linux)64/i", $game_cfg['game_key']) )
				$select_game .= " (64 Bit)";
			else
				$select_game .= " (32 Bit)";
			$select_game .= "</option>\n";
		}
		$select_game .= "</select>\n</td>\n</tr>\n</table>\n</form>\n";

		$ft1 = new FormTable();
		$ft1->start_form("home.php" , "get");
		$ft1->add_field_hidden("m", $_GET['m']);
		$ft1->add_field_hidden("access_rules", get_lang("access_rules"));
		$ft1->start_table();
		$ft1->add_custom_field('select_game',$select_game);
		$ft1->end_table();
		$ft1->end_form();
		
		if( !isset($_GET['home_cfg_id']) ) $_GET['home_cfg_id'] = '0';
		
		if(isset($_POST['remove']))
		{
			del_access_rule($_POST['remove']);
		}
		
		if(isset($_POST['save_access_rules']))
		{
			$check = check_access_rules_entries();
			if(($check['match_client_ip'] == "" and $check['ip_entry_fail']) == FALSE)
			{
				set_access_rule($_GET['home_cfg_id'], $check['match_file_extension'], $check['match_client_ip']);
			}
		}
		$rules = get_access_rules($_GET['home_cfg_id']);
		$ft2 = new FormTable();
		$ft2->start_form("?m=fast_download&home_cfg_id=".$_GET['home_cfg_id']."&access_rules=".$_GET['access_rules'] , "post");
		$ft2->start_table();
		$ft2->add_field('text','match_file_extension',@$rules['match_file_extension']);
		$ft2->add_field('text','match_client_ip',@$rules['match_client_ip']);
		$ft2->end_table();
		$ft2->add_button("submit","save_access_rules",get_lang("save_access_rules"));
		$ft2->end_form();
		
		$all_rules = get_access_rules();
		if(is_array($all_rules))
		{
			?>
			<script type="text/javascript" src="js/jquery/jquery-1.11.0.min.js"></script>
			<script type="text/javascript" src="js/jquery/plugins/jquery.tablesorter.collapsible.js"></script>
			<script type="text/javascript" src="js/jquery/plugins/jquery.tablesorter.mod.js"></script>
			<script>
			$(document).ready(function(){
				$("#servermonitor").tablesorter();
			});
			</script>
			<?php
			echo "<h3>".get_lang("current_access_rules")."</h3>\n";
			echo "<table id='servermonitor' class='tablesorter' style='width: 100%;'>\n<thead><tr>".
				 "<th class='header'>".get_lang("game_name")."</th><th class='header'>".get_lang("match_file_extension")."</th><th class='header'>".get_lang("match_client_ip")."</th></tr></thead>\n<tbody>";
			foreach($all_rules as $rule)
			{
				if($rule['home_cfg_id'] != '0')
				{
					$home_cfg = $db->getGameCfg($rule['home_cfg_id']);
					$game_name = $home_cfg['game_name'];
					if ( preg_match("/win(32|64)/i", $home_cfg['game_key']) )
						$game_name .= " (Windows)";
					else
						$game_name .= " (Linux)";
					if ( preg_match("/(win|linux)64/i", $home_cfg['game_key']) )
						$game_name .= " (64Bit)";
					else
						$game_name .= " (32Bit)";
				}
				else
				{
					$game_name = get_lang("games_without_specified_rules");
				}
				echo "<tr class='maintr' ><td><form method=POST style='display:block;float:left;' ><input type='image' src='modules/administration/images/remove.gif' name=remove value=$rule[home_cfg_id] onclick='submit-form();' ></form>$game_name</td><td>$rule[match_file_extension]</td><td>$rule[match_client_ip]</td><tr>\n";
			}
			echo "</tbody>\n</table>\n";
		}
		echo create_back_button($_GET['m']);
	}
	else
	{
		if(isset( $_GET['advanced'] ) and isset( $_POST['configuration'] ) and isset( $_GET['remote_server_id'] ) and $_GET['remote_server_id'] != "")
		{
			echo "<h3>".get_lang("fast_dl_advanced")."</h3>";
			$remote_server_id = $_GET['remote_server_id'];
			$remote_server = $db->getRemoteServer($remote_server_id);
			$rserver = $db->getRemoteServerById($remote_server_id);
			$remote = new OGPRemoteLibrary( $rserver['agent_ip'],
											$rserver['agent_port'],
											$rserver['encryption_key'],
											$rserver['timeout']);
			$fastdl_info = $remote->fastdl_get_info();
			$fastdl_settings = get_fastdl_settings($remote_server_id);
			if($fastdl_info !== -1)
			{
				if( preg_match("/^(127|0)/",$fastdl_info['ip']) )
					$fastdl_info['ip'] = $rserver['agent_ip'];
			}
			else
			{
				$fastdl_info = array();
				$fastdl_info['ip'] = $rserver['agent_ip'];
				$fastdl_info['port'] = '8080';
				$fastdl_info['listing'] = "1";
			}
			$ft1 = new FormTable();
			$ft1->start_form("?m=fast_download&remote_server_id=".$_GET['remote_server_id'] , "post");
			$ft1->add_field_hidden("stop_fastdl", '');
			$ft1->add_button("submit","stop_fastdl",get_lang("stop_fastdl"));
			$ft1->end_form();
			$ft1->start_form("?m=fast_download&remote_server_id=".$_GET['remote_server_id'] , "post");
			$ft1->start_table();
			$ft1->add_field('string','fastdl_ip',$fastdl_info['ip']);
			$ft1->add_field('string','fastdl_port',$fastdl_info['port']);
			$ft1->add_field('on_off','listing',$fastdl_info['listing']);
			$ft1->add_field('on_off','autostart_on_agent_startup',$fastdl_info['autostart_on_agent_startup']);
			$ft1->add_field('on_off','port_forwarded_to_80',@$fastdl_settings['port_forwarded_to_80']);
			$ft1->add_field_hidden("conf_fastdl", '');
			$ft1->end_table();
			$ft1->add_button("submit","conf_fastdl",get_lang("apply_settings_and_restart_fastdl"));
			$ft1->end_form();
			echo create_back_button($_GET['m']."&remote_server_id=".$_GET['remote_server_id']);
			?>
			<script>
			function disableSubmit(){
				var inputs = document.getElementsByTagName('input');
				var i;
				for (i = 0; i < inputs.length; i++) {
					if(inputs[i].type == 'submit')
					{
						inputs[i].disabled = true;
					}
				}
			}


			function init(){
				var forms = document.getElementsByTagName('form');
				var i;
				for (i = 0; i < forms.length; i++) {
					forms[i].onsubmit = disableSubmit;
				}
			}

			window.onload = init;
			</script>
			<?php
		}
		elseif(!isset($_GET['create_aliases']))
		{
			echo "<form method=GET >\n".
				 "<input type='hidden' name='m' value='". $_GET['m'] . "' />\n".
				 "<input type=submit value='" . get_lang("access_rules") . "' name='access_rules' />\n".
				 "<input type=submit value='" . get_lang("create_aliases") . "' name='create_aliases' />\n<br>\n".
				 get_lang("select_remote_server").": ".
				 "<select onchange=".'"this.form.submit()"'." name='remote_server_id'>\n".
				 "<option></option>\n";
			foreach ( $remote_servers as $server )
			{
				$selected = ( isset( $_GET['remote_server_id'] ) and 
							  $server['remote_server_id'] == $_GET['remote_server_id'] ) ? 
							  "selected=selected" : "";
				echo "<option $selected value='".$server['remote_server_id']."'>".
					 $server['remote_server_name']." (".$server['agent_ip'].")</option>\n";
			}
			echo "</select></form>\n";
		}
		if( isset( $_GET['remote_server_id'] ) and $_GET['remote_server_id'] != "" and !isset($_GET['access_rules']) and !isset( $_GET['advanced'] ) and !isset( $_POST['configuration'] ) and !isset( $_GET['create_aliases'] ))
		{
			$remote_server_id = $_GET['remote_server_id'];
			$rserver = $db->getRemoteServerById($remote_server_id);
			$remote = new OGPRemoteLibrary( $rserver['agent_ip'],
											$rserver['agent_port'],
											$rserver['encryption_key'],
											$rserver['timeout']);
			if($remote->status_chk() == 1)
			{
				$fd_status = $remote->fastdl_status();
				if(isset( $_POST['conf_fastdl'] ))
				{
					$fastdl_info = $remote->fastdl_get_info();
					$fastdl_ip = $_POST['fastdl_ip'];
					$fastdl_port = $_POST['fastdl_port'];
					$listing = $_POST['listing'];
					$autostart_on_agent_startup = $_POST['autostart_on_agent_startup'];
					unset($_POST['fastdl_ip'], $_POST['fastdl_port'], $_POST['listing'], $_POST['autostart_on_agent_startup'],$_POST['conf_fastdl']);
					set_fastdl_settings($remote_server_id, $_POST);
					if($remote->fastdl_create_config($fastdl_ip, $fastdl_port, $listing, $autostart_on_agent_startup) !== -1)
					{
						if($remote->restart_fastdl() !== 1)
						{
							echo get_lang("fastdl_could_not_be_restarted");
						}
					}
					else echo get_lang("configuration_file_could_not_be_written");
					$firewall_settings = $db->getFirewallSettings($remote_server_id);
					if ($firewall_settings['status'] == "enable")
					{
						if($fastdl_info !== -1)
							set_firewall($remote, $firewall_settings, 'deny', $fastdl_info['port'], $fastdl_info['ip']);
						set_firewall($remote, $firewall_settings, 'allow', $fastdl_port, $fastdl_ip);
					}
					$fd_status = $remote->fastdl_status();
				}
				elseif(isset( $_POST['stop_fastdl'] ))
				{
					$remote->stop_fastdl();
					$fd_status = $remote->fastdl_status();
				}
				$fastdl_info = $remote->fastdl_get_info();
				if($fastdl_info !== -1)
				{
					if( preg_match("/^(127|0)/",$fastdl_info['ip']) )
						$fastdl_info['ip'] = $rserver['agent_ip'];
					echo "<p>".get_lang("current_aliases_at_remote_server").":</p>";
					$aliases = $remote->fastdl_get_aliases();
					if($aliases !== -1)
					{
						if( isset( $_POST['delete'] ) )
						{
							$response_del = $remote->fastdl_del_alias($_POST['aliases']);
							foreach($_POST['aliases'] as $alias)
							{
								if( $response_del != -1 and
									$remote->rfile_exists($aliases[$alias]['home']) === 1 
									and isset($_POST['remove']))
									$remote->shell_action('remove_recursive', $aliases[$alias]['home']);
							}
							$aliases = $remote->fastdl_get_aliases();
						}
						if($aliases !== -1)
						{
							echo "<form method=POST >";
							$fastdl_settings = get_fastdl_settings($remote_server_id);
							$address = ($fastdl_info['port'] == '80' OR ($fastdl_settings and $fastdl_settings['port_forwarded_to_80'] == '1')) ? 
										$fastdl_info['ip'] : 
										$fastdl_info['ip'].":".$fastdl_info['port'];
							foreach( $aliases as $alias => $info )
							{
								echo "<input type=checkbox name='aliases[]' value='$alias'/>$alias".
									 "( <a href='http://$address/$alias' target='_blank' ".
									 "title='".$info['home']."' >http://$address/$alias</a> )<br>";
							}
							echo "<br><input type=checkbox name=remove />".get_lang("remove_folders")."<br>
								  <input type=submit name=delete value='".get_lang("delete_selected_aliases")."' />
								  </form>";
						}
					}
					if($aliases == -1)
						print_success( get_lang("no_aliases_defined") );
				}
				if($fd_status !== -1)
					print_success( get_lang("fast_download_daemon_running") );
				else
					print_failure( get_lang("fast_download_daemon_not_running") );
				echo "<form method=POST action='?m=fast_download&remote_server_id=".$_GET['remote_server_id'].
					 "&advanced' ><input type=submit name=configuration value='".get_lang("fast_dl_advanced")."'></form>";
			}
			else
			{
				print_failure( "Agent not running" );
			}
		}
		elseif( isset($_GET['create_aliases']) )
		{
			$server_homes = $db->getIpPorts();
			if( $server_homes === FALSE )
			{
				print_failure(get_lang("no_game_homes_assigned"));
				return;
			}
			echo "<table class='center' ><tr><td><b>".get_lang("create_alias_for").
				 ":</b></td><td>";
			$extra_inputs = array(array('type' => 'hidden','name' => 'create_aliases','value' => get_lang("create_aliases")));
			create_home_selector_address($_GET['m'], "", $server_homes, $extra_inputs);
			echo "</td></tr></table>";
			if( isset( $_GET['home_id-mod_id-ip-port'] ) and $_GET['home_id-mod_id-ip-port'] != "" )
			{
				list($home_id, $mod_id, $ip, $port) = explode( "-", $_GET['home_id-mod_id-ip-port'] );
				$server_home = $db->getGameHomeByIP($ip, $port);
				if ( !$server_home )
					return;
				$remote = new OGPRemoteLibrary($server_home['agent_ip'], 
											   $server_home['agent_port'], 
											   $server_home['encryption_key'], 
											   $server_home['timeout']);

				$fastdl_info = $remote->fastdl_get_info();
				$fastdl_settings = get_fastdl_settings($server_home['remote_server_id']);
				if( preg_match("/^(127|0)/",$fastdl_info['ip']) )
					$fastdl_info['ip'] = $server_home['agent_ip'];

				$fastdl_display_ip = checkDisplayPublicIP($server_home['display_public_ip'],$server_home['ip'] != $fastdl_info['ip'] ? $server_home['ip'] : $fastdl_info['ip']);

				$address = ($fastdl_info['port'] == '80' OR ($fastdl_settings and $fastdl_settings['port_forwarded_to_80'] == '1')) ? $fastdl_display_ip : $fastdl_display_ip.":".$fastdl_info['port'];
				$alias = isset( $_POST['alias'] ) ? $_POST['alias'] : str_replace(".","_",$ip)."_$port";
				$path = isset( $_POST['path'] ) ? $_POST['path'] : "fastdl";
				$aliases = $remote->fastdl_get_aliases();
				if( isset( $_POST['delete'] ) )
				{
					$rmalias = $_POST['rmalias'];
					if( $remote->fastdl_del_alias($rmalias) !== -1 and 
						$remote->rfile_exists($aliases[$rmalias]['home']) === 1 
						and isset($_POST['remove']) )
						$remote->shell_action('remove_recursive', $aliases[$rmalias]['home']);
					$aliases = $remote->fastdl_get_aliases();
				}
				$aliases = $aliases === -1 ? array() : $aliases;
				if( isset( $_POST['create'] ) )
				{
					$check = check_access_rules_entries();
					if(!$check['ip_entry_fail'] and !$check['extension_entry_fail'])
					{
						if(!array_key_exists($alias , $aliases))
						{
							$home = clean_path($server_home['home_path']."/".clean_string($_POST['path']));
							$alias = clean_string($_POST['alias']);
							if( $remote->rfile_exists( $home ) !== 1 )
							{
								$remote->shell_action('create_dir', $home);
							}
							$index = createIndexString( get_lang_f( 
									 "fast_download_service_for", 
									 $server_home["home_name"] ), 
									 $view->charset );
							$file = clean_path("$home/index.html");
							$remote->remote_writefile($file, $index);
							if( $remote->fastdl_add_alias($alias,$home,$_POST['match_file_extension'],$_POST['match_client_ip']) == 1 )
							{
								echo "<p class='success' style='text-align:center;' >".get_lang("success")."</p>";
							}
							else
							{
								echo "<p class='failure' style='text-align:center;' >".get_lang("failure")."</p>";
							}
						}
						else
							echo "<p class='failure' style='text-align:center;' >".get_lang_f('alias_already_exists',$alias)."</p>";
						$aliases = $remote->fastdl_get_aliases();
					}
				}

				$alias_info = get_alias_by_home_path($server_home['home_path'], $aliases);
				$rules = get_access_rules($server_home['home_cfg_id']);
				if(is_array($alias_info))
				{
					$url = "<a href='http://$address/".$alias_info['alias']."' target='_blank' >http://$address/".$alias_info['alias']."</a>";
					echo "<form method=POST >".
						 "<input type=hidden name=rmalias value='".$alias_info['alias']."'/>".
						 "<table class='center'>\n".
						 "<tr><td align=right >".get_lang("at_url").": </td><td align=left >$url</td></tr>\n".
						 "<tr><td align=right >".get_lang("to_the_path").": </td><td align=left >$alias_info[home]</td></tr>\n".
						 "<tr><td align=right >".get_lang("match_file_extension").": </td><td align=left >$alias_info[match_file_extension]</td></tr>\n".
						 "<tr><td align=right >".get_lang("match_client_ip").": </td><td align=left >$alias_info[match_client_ip]</td></tr>\n".
						 "<tr><td align=right ><input type=checkbox name=remove />".get_lang("remove_folder")."</td>".
						 "<td align=left ><input type=submit name=delete value='".get_lang("delete_alias")."' /></td></tr>\n".
						 "</table></form>";
				}
				else
				{
					$home_path = clean_path($server_home['home_path']."/");
					$ft3 = new FormTable();
					$ft3->start_form("" , "post");
					$ft3->start_table();
					$ft3->add_custom_field('at_url',"<b style='color:black;background:#FFF;padding:2.5px 0 1px 0;".
													"border:1.5px solid gray;' >http://$address/</b>".
													"<input type=text name=alias value='$alias' />");
					$ft3->add_custom_field('to_the_path',"<b style='color:black;background:#FFF;padding:2.5px 0 1px 0;".
														 "border:1.5px solid gray;' >$home_path</b>".
														 "<input type=text name=path value='$path' />");
					$ft3->add_field('text','match_file_extension',@$rules['match_file_extension']);
					$ft3->add_field('text','match_client_ip',@$rules['match_client_ip']);
					$ft3->end_table();
					$ft3->add_button("submit","create",get_lang("create_alias"));
					$ft3->end_form();
				}
			}
			echo create_back_button($_GET['m']);
		}
	}
}
?>
