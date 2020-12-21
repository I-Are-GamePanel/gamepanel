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
include 'includes/lib_remote.php';
function exec_ogp_module() 
{
	global $db;
	$remoteServers = $db->getRemoteServers();
	$servers = array();
	
	if(is_array($remoteServers)){
		foreach($remoteServers as $server){
			$remote = new OGPRemoteLibrary($server['agent_ip'], $server['agent_port'], $server['encryption_key'], 1);
			$status = (int)$remote->status_chk();
			
			$servers[] = array(
				'id'		=>	$server['remote_server_id'],
				'name'		=>	$server['remote_server_name'] .' '. (($status) === 0 ? '('.get_lang('offline').')' : '('.get_lang('online').')'),
				'status'	=>	$status,
			);
		}
	}
	echo json_encode($servers);
}
?>