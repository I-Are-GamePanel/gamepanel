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
 
require 'modules/editconfigfiles/array_column.php';
require 'modules/editconfigfiles/functions.php';
require_once 'modules/config_games/server_config_parser.php';
require 'includes/lib_remote.php';

function exec_ogp_module()
{
    global $db, $view;
    
    $home_id = (int)$_GET['home_id'];
    $isAdmin = $db->isAdmin($_SESSION['user_id']);
    
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
    
    $file = ($_SERVER['REQUEST_METHOD'] === 'POST' ? rawurldecode($_POST['file']) : rawurldecode($_GET['file']));
    
    if (array_search($file, array_column($files, 'path')) === false) {
        print_failure(get_lang('invalid_file'));
        $view->refresh("?m=editconfigfiles&home_id=". (int)$server_home['home_id']);
        
        return;
    }
    
    $remote = new OGPRemoteLibrary($server_home['agent_ip'], $server_home['agent_port'], $server_home['encryption_key'], $server_home['timeout']);
    
    if ($remote->status_chk() === 0) {
        print_failure(get_lang('agent_offline'));
        $view->refresh("?m=gamemanager&p=game_monitor");
        
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $file_info = $remote->remote_writefile($server_home['home_path'] . '/' . $file, strip_real_escape_string($_POST['file_content']));
            
        if ($file_info === 1) {
            print_success(get_lang('wrote_changes'));
            $view->refresh("?m=editconfigfiles&home_id=". (int)$server_home['home_id']);
                
            return;
        } else {
            print_failure(get_lang('failed_write'));
            $view->refresh("?m=editconfigfiles&home_id=". (int)$server_home['home_id']);
                
            return;
        }
    } else {
        $newFile = ($remote->rfile_exists($server_home['home_path'] . '/' . $file) == 0 ? true : false);
        $file_info = $remote->remote_readfile($server_home['home_path'] . '/' . $file, $data);
        
        if ($file_info !== 1) {
            print_failure(get_lang('failed_read'));
            $view->refresh("?m=editconfigfiles");
                
            return;
        }
            
        echo '<h2>'.get_lang('editing_file').'</h2><p><b>'.($newFile ? get_lang('new_file').':' : '') .' '. htmlentities($file).'</b></p>';
        echo '<form action="?m=editconfigfiles&p=modify&home_id='.$server_home['home_id'].'" method="POST">';
        echo '<input type="hidden" name="file" value="'.rawurlencode($_GET['file']).'">';
        echo '<input type="hidden" name="action" value="save">';
        echo '<textarea name="file_content" style="width:98%;" rows="40">'. $data .'</textarea>';
        echo '<p><input type="submit" name="write" value="'. get_lang('save') . '" /></p>';
        echo '</form>';
        echo '<div><a href="?m=editconfigfiles&home_id='. (int)$server_home['home_id'].'">'.get_lang('go_back').'</a></div>';
    }
}
