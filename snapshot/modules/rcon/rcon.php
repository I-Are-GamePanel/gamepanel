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

require_once("modules/config_games/server_config_parser.php");
require_once('includes/lib_remote.php');

function exec_ogp_module()
{
	global $db;

	$server_homes = $db->getIpPorts();

	if ( !$server_homes )
	{
		return;
	}

	$select_game = "<form method=POST >\n<table class=center >\n\n<tr>\n";

	$i = 0;
	$i2 = 0;
	$colspan = "";
	foreach ( $server_homes as $server_home )
	{
		$server_xml = read_server_config(SERVER_CONFIG_LOCATION."/".$server_home['home_cfg_file']);
		$remote = new OGPRemoteLibrary($server_home['agent_ip'],$server_home['agent_port'],$server_home['encryption_key'],$server_home['timeout']);
		$screen_running = $remote->is_screen_running(OGP_SCREEN_TYPE_HOME,$server_home['home_id']) === 1;
		if( ( $server_xml->control_protocol == 'rcon' OR $server_xml->control_protocol == 'rcon2' OR 
		   @$server_xml->gameq_query_name == "minecraft" OR $server_xml->control_protocol == 'lcon' ) AND $screen_running )
		{

			$i2++;
			if ( count( $server_homes ) == $i2 )
			{
				$i = 0;
			}
			$control = ( $i == 0 ) ?  "</td>\n" : "</td>\n</tr>\n<tr>\n";
			$display_ip = checkDisplayPublicIP($server_home['display_public_ip'],$server_home['ip'] != $server_home['agent_ip'] ? $server_home['ip'] : $server_home['agent_ip']);
			$select_game .= "<td class=left ><input type=checkbox name='action-". $server_home['ip'] . "-" . $server_home['port'] .
							"' value='". $server_home['home_id'] . "-" . $server_home['mod_id'] . "-" . $server_home['ip'] .
							"-" . $server_home['port'] . "' />" . $server_home['home_name'] . " - " . $display_ip .
							":" . $server_home['port'] . $control;

			$i = ( $control == "</td>\n" ) ? 1 : 0;
		}
	}
	$select_game .= '<input type="button" name="check-all" id="check-all" value="'.get_lang('check-all').'">'.
					'<input type="button" name="uncheck-all" id="uncheck-all" value="'.get_lang('uncheck-all').'">'.
					"</table>\n<table class='center rcon' ><tr><td>".get_lang('rcon_command_title').
					"</td>\n<td>\n<input class=rcon type=text name=rcon_command size=200 style='width:550px;' />\n</td>\n".
					"<td><input type=submit name=remote_send_rcon_command value='".get_lang('send_command')."' />\n</td>\n".
					"</table>\n</form>\n";
?>
<h2>
	<?php print_lang('rcon_command_title'); ?>
</h2>
<?php
	echo $select_game;
	
	if(isset($_POST['remote_send_rcon_command']) AND $_POST['rcon_command'] != "" )
	{
		$rconCommand = $_POST['rcon_command'];
		foreach($_POST as $key => $value)
		{
			$return = "";
			if( preg_match( "/^action/", $key ) )
			{
				list($home_id,$mod_id,$ip,$port) = explode("-", $value);
				$home_info = $db->getGameHome($home_id);
				$remote = new OGPRemoteLibrary($home_info['agent_ip'],$home_info['agent_port'],$home_info['encryption_key'],$home_info['timeout']);
				$server_xml = read_server_config(SERVER_CONFIG_LOCATION."/".$home_info['home_cfg_file']);
				$control_type = isset($server_xml->control_protocol_type) ? $server_xml->control_protocol_type : "";
				
				if ( isset($server_xml->gameq_query_name) and  $server_xml->gameq_query_name == "minecraft" )
				{
					require_once("modules/gamemanager/MinecraftRcon.class.php");
					$rcon_port = $port+10;
					$rcon = new MinecraftRcon;
					if( $rcon->Connect($ip, $rcon_port, $home_info['control_password']) )
					{
						$return = $rcon->Command($rconCommand);
						if ($return);
							echo "<div class='bloc' ><h4>".get_lang('rcon_command_title').": [".$rconCommand."] ".
								  get_lang('has_sent_to')." ". $home_info['home_name']."</h4><xmp style='overflow:scroll;' >$return</xmp></div>";
								  
						$rcon->Disconnect();
						
					}
					else
					{
						echo "".get_lang('need_set_remote_pass')." ".$home_info['home_name']." ".get_lang('before_sending_rcon_com')."<br>";
					}
				}
				else
				{
					$remote_retval = $remote->remote_send_rcon_command( $home_id, $ip, $port, $server_xml->control_protocol, $home_info['control_password'],$control_type,$rconCommand,$return);
					
					if ( $remote_retval === -1 )
					{
						print_failure(get_lang("agent_offline"));
					}
					elseif ( $remote_retval === 1 )
					{
							echo "<div class='bloc' ><h4>".get_lang('rcon_command_title').": [".$rconCommand."] ".
								  get_lang('has_sent_to')." ". $home_info['home_name']."</h4><xmp style='overflow:scroll;' >$return</xmp></div>";
					}
					elseif ( $remote_retval === -10 )
					{
						echo "".get_lang('need_set_remote_pass')." ".$home_info['home_name']." ".get_lang('before_sending_rcon_com')."<br>";
					}
				}
			}
		}
	}
?>
<script type="text/javascript">
$('#check-all').click(function() {
    $('input:checkbox').attr('checked', true).prop('checked', true);
    return false;
});
$('#uncheck-all').click(function() {
    $('input:checkbox').attr('checked', false).prop('checked', false);
    return false;
});
</script>
<?php
}
?>
