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

// Module general information
$module_title = "Fast Download";
$module_version = "2.1";
$db_version = 4;
$module_required = TRUE;
$module_menus = array( array( 'subpage' => '', 'name'=>'Fast Download', 'group'=>'admin' ) );
$module_access_rights = array('d' => 'allow_fast_download');

$install_queries[0] = array("SELECT NOW();");
$install_queries[1] = array("DROP TABLE IF EXISTS `".OGP_DB_PREFIX."fastdl`;");
$install_queries[2] = array("SELECT NOW();");
$install_queries[3] = array(
	"CREATE TABLE ".OGP_DB_PREFIX."fastdl_access_rules (
		`home_cfg_id` varchar(32) NOT NULL,
		`match_file_extension` TEXT,
		`match_client_ip` TEXT,
		UNIQUE KEY (`home_cfg_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;");
$install_queries[4] = array(
	"CREATE TABLE ".OGP_DB_PREFIX."fastdl_settings (
		`remote_server_id` int(11) NOT NULL,
		`setting` varchar(63) NOT NULL,
        `value` varchar(255) NOT NULL,
		UNIQUE KEY remote_server_id (remote_server_id,setting)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;");
$uninstall_queries = array("DROP TABLE IF EXISTS `".OGP_DB_PREFIX."fastdl_access_rules`;",
						   "DROP TABLE IF EXISTS `".OGP_DB_PREFIX."fastdl_settings`;");
?>
