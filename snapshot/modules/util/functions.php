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

function toCommunityID($id){
	if(preg_match('/^STEAM_/', $id)){
		$parts = explode(':', $id);
		return bcadd(bcadd(bcmul($parts[2], '2'), '76561197960265728'), $parts[1]);
	}elseif(is_numeric($id) && strlen($id) < 16){
		return bcadd($id, '76561197960265728');
	}else{
		return $id;
	}
}

function toSteamID($id){
	if(is_numeric($id) && strlen($id) >= 16){
		$z = bcdiv(bcsub($id, '76561197960265728'), '2');
	}elseif(is_numeric($id)){
		$z = bcdiv($id, '2');
	}else{
		return $id;
	}
	
	$y = bcmod($id, '2');
	return 'STEAM_0:'.$y.':'.floor($z);
}

function toUserID($id){
	if(preg_match('/^STEAM_/', $id)){
		$split = explode(':', $id);
		return $split[2] * 2 + $split[1];
	}elseif(preg_match('/^765/', $id) && strlen($id) > 15){
		return bcsub($id, '76561197960265728');
	}else{
		return $id;
	}
}

function getSteamId($input){
	$xml = simplexml_load_file('http://steamcommunity.com/id/'.$input.'?xml=1');
	$steamId64 = $xml->steamID64;
	
	if(preg_match('/^765/', $steamId64) === 0){
		return false;
	}else{
		return (string)$steamId64;	// Cast it to a string, otherwise an SimpleXMLElement Object will be returned.
	}
}

function convert($id){
	if(preg_match('/^\[U:1:[0-9]+\]/', $id)){
		$id = substr($id, 5, -1);
	}
	
	// Check if the input is in legacyId, communityId, or SteamId3 format.
	// If it's not, assume it's a custom Id and attempt to get the communityId from what the user entered.
	if(preg_match('/^STEAM_[01]:[01]:\d+$/', $id) === 0 && preg_match('/^[0-9]/', $id) === 0 && preg_match('/^765/', $id) === 0){
		if(($steamId = getSteamId($id)) === false){
			return json_encode(array());
		}
		
		$id = $steamId;
	}
	
	echo json_encode(array(
		'communityId'	=>	toCommunityID($id),
		'steamId'		=>	toSteamID($id),
		'steamId3'		=>	'[U:1:'.toUserID($id).']',
		'steamProfile'	=>	'<a href="http://steamcommunity.com/profiles/'.toCommunityID($id).'">Steam Profile</a>',
	));
}

// Get gameservers belonging to each user with matching permissions if they're not an admin.
function getUserServers($servers, $flags, $supportedGames){
	global $db;
	$info = array();
	$userInfo = $db->getUserById($_SESSION['user_id']);
	$userRole = $userInfo['users_role'];
	
	if(!empty($servers)){
		foreach($servers as $server){
			$gamehome = $db->getUserGameHome($_SESSION['user_id'], $server['home_id']);
			
			if(in_array($gamehome['game_name'], $supportedGames) === true){
				if($userRole !== 'admin'){
					if(strpbrk($gamehome['access_rights'], $flags) !== false){
						$info[] = $server;
					}
				}else{
					$info[] = $server;
				}
			}
		}
	}
	
	return $info;
}
?>