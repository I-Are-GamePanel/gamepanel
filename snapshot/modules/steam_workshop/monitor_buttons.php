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

if(isset($server_xml->installer) and $server_xml->installer == "steamcmd")
{
	$mod_xml = xml_get_mod($server_xml, $server_home['mod_key']);
	require_once("modules/steam_workshop/functions.php");
	if(isset($mod_xml->installer_name) and !in_array((string)$mod_xml->installer_name, get_blacklist()))
	{
		$module_buttons = array(
			"<a class='monitorbutton' href='?m=steam_workshop&p=main&home_id-mod_id-ip-port=".$server_home['home_id']."-".$server_home['mod_id']."-".$server_home['ip']."-".$server_home['port']."'>
				<img src='" . check_theme_image("images/steam_workshop.png") . "' title='Steam Workshop'>
				<span>Steam Workshop</span>
			</a>"
		);
	}
	else
		$module_buttons = array();
}
else
	$module_buttons = array();
?>