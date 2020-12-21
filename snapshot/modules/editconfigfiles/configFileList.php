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

require_once('modules/editconfigfiles/functions.php');
require_once("modules/config_games/server_config_parser.php");

function exec_ogp_module()
{
    global $db, $view;
    
    $home_id = (int)$_GET['home_id'];
    $isAdmin = $db->isAdmin($_SESSION['user_id']);

    if (empty($home_id) || $home_id === 0) {
        print_failure(get_lang('no_server_specfied'));
        $view->refresh("?m=gamemanager&p=game_monitor");

        return;
    }
    
    if ($isAdmin) {
        $server_home = $db->getGameHome($home_id);
    } else {
        $server_home = $db->getUserGameHome($_SESSION['user_id'], $home_id);
    }
    
    if ($server_home === false) {
        print_failure(get_lang('no_home'));
        $view->refresh("?m=gamemanager&p=game_monitor");
        
        return;
    }
    
    $server_xml = read_server_config(SERVER_CONFIG_LOCATION .'/'. $server_home['home_cfg_file']);

    $files = getFilesInXML($server_xml->configuration_files);
    
    if (empty($files)) {
        print_failure(get_lang('no_configs_for_game'));
        $view->refresh("?m=gamemanager&p=game_monitor");
    } else {
        echo '<h2>'.get_lang('configuration_files').'</h2>';
        
        echo '<table width="100%">
				<tr>
					<th>'.get_lang('name').'</th>
					<th>'.get_lang('description').'</th>
					<th>'.get_lang('actions').'</th>
				</tr>';
        
        foreach ($files as $file) {
            echo '<tr>
					<td>'. $file['name'] .'</td>
					<td>'. ($file['description'] ?: '<i>'.get_lang('no_description').'</i>') .'</td>
					<td><a href="?m=editconfigfiles&p=modify&home_id='.$server_home['home_id'].'&file='.rawurlencode($file['path']).'">[ '.get_lang('edit').' ]</a></td>
				</tr>';
        }
        
        echo '</table>';
    }

    $modKey = key($server_home['mods']);
    $IpPorts = $db->getHomeIpPorts($home_id);
    
    echo '<div style="margin-top:12px">
            <a href="?m=gamemanager&p=game_monitor&home_id-mod_id-ip-port='. $home_id .'-'. $server_home['mods'][$modKey]['mod_id'] .'-'. $IpPorts[0]['ip'] .'-'. $IpPorts[0]['port'] .'">'.get_lang('go_back').'</a>
    </div>';
}