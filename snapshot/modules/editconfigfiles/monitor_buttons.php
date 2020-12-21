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

if(!empty($server_xml->configuration_files)) {
	$module_buttons = array(
		"<a href=\"?m=editconfigfiles&home_id=".(int)$server_home['home_id']."\" class=\"monitorbutton\">
			<img src='" . check_theme_image("images/editconfig.png") . "' title='". get_lang("edit_configuration_files") ."'>
			<span>". get_lang("edit_configuration_files") ."</span>
		</a>"
	);
}
else
	$module_buttons = array();
?>