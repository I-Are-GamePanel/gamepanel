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

// Define commands, user permissions (via *role* => true/false), and what they're referred to on each OS.
// Include arguements here. They cannot be passed in the text input field.
$availableCommands	=	array(
	// 'title' also needs to be specified in the translation files with the same values - as this is what shows on the select list. ex:
	array(
		'title'		=>	'ping',
		'linux'		=>	'ping -c 4',	// By default, ping runs indefinitely on Linux - so set the count to 4; same as Windows default.
		'windows'	=>	'ping',
		
		// Default: Ping is available to all user roles.
		'subuser'	=>	true,
		'user'		=>	true,
		'admin'		=>	true,
	),
	
	array(
		'title'		=>	'traceroute',
		'linux'		=>	'traceroute',
		'windows'	=>	'tracert',
		
		// Default: Traceroute is only available to admins.
		'subuser'	=>	false,
		'user'		=>	false,
		'admin'		=>	true,
	),
);

// An array of characters which should never be passed to exec()
$blockedCharacters	= '"#$%^&*()+=[]\';,\\/{}|:<>?~';

// Should we log attempted form manipulation (ie, editing the form to execute a command the user doesn't have access to) ...
// ... and attempted multi-command input? (ie, ;cd /;ls)
$logMaliciousUsage = true;

// Should we log successfully executed commands...?
// Could spam the logs - so probably set this to false.
$logAllUsage = false;

// Games which Sourcemod supports.
// Needs to be exactly what OGP reports as the game_name.
$supportedGames = array(
	'Counter Strike Global Offensive',
	'Counter Strike Source',
	'Day of Defeat Source',
	'Dystopia',
	'Garrys Mod',
	'Half-Life 2: Deathmatch',
	'Hidden: Source',
	'Pirates, Vikings and Knights II',
	'Team Fortress 2',
	'Team Fortress 2 Beta',
	'Left 4 Dead',
	'Left 4 Dead 2'
);

// Games which amx mod supports.
// Needs to be exactly what OGP reports as the game_name.
$amx_supportedGames = array(
	'Counter-Strike',
	'Counter-Strike Condition Zero',
	'Day of Defeat',
	'Death Match Classic',
	'Team Fortress Classic'
);

// Simple array of where admins are stored depending on the admin mod.
// mod.name => file.location
$adminFiles = array(
	'sourcemod'	=>	'addons/sourcemod/configs/admins_simple.ini',
	'amx_mod'	=>	'addons/amxmodx/configs/users.ini',
);

// Flags that sub-users need to add admins to owned parent game-servers.
// This should just be the same as allow_file_management and allow_ftp - as they'll be able to add admins anyway with those flags.
$subuserAdminManagement = 'ft';