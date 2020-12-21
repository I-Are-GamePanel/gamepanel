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
require_once('includes/form_table_class.php');
require_once("modules/steam_workshop/functions.php");
function exec_ogp_module()
{
	
	Global $db,$view;
	echo '<h2>Steam Workshop</h2>';
	define('CONFIGS', "modules/steam_workshop/game_configs/");
	
	if(isset($_REQUEST['home_cfg_id-mod_cfg_id-os']))
		list($home_cfg_id, $mod_cfg_id, $os) = explode('-', $_REQUEST['home_cfg_id-mod_cfg_id-os']);
			
	if(isset($home_cfg_id) and isset($mod_cfg_id))
	{
		$gameCfg = $db->getGameCfg($home_cfg_id);
		$cfgMods = $db->getCfgMods($home_cfg_id);
		
		foreach($cfgMods as $cfgMod)
		{
			if($cfgMod['mod_cfg_id'] == $mod_cfg_id)
				$modkey = $cfgMod['mod_key'];
		}
		
		$server_xml = read_server_config(SERVER_CONFIG_LOCATION."/".$gameCfg['home_cfg_file']);
		
		$mod_xml = xml_get_mod($server_xml, $modkey);
		
		if (!$mod_xml)
		{
			print_failure(get_lang_f('mod_key_not_found_from_xml',$modkey));
			return;
		}
		$xml_file = CONFIGS.$mod_xml->installer_name."_".$os.".xml";
		
		if(isset($_POST['save_config']))
		{
			$xml = new SimpleXMLElement('<workshop_settings/>');
			$xml->addChild('workshop_id', $_POST['workshop_id']);
			$xml->addChild('download_method', $_POST['download_method']);
			$xml->addChild('anonymous_login', $_POST['anonymous_login']);
			$xml->addChild('mods_path', $_POST['mods_path']);
			$mods = $xml->addChild('mods');
			if(file_exists($xml_file))
			{
				$file_xml = simplexml_load_file($xml_file);
				foreach($file_xml->mods->mod as $xml_mod)
				{
					$mod = $mods->addChild('mod');
					$mod->addAttribute('id', $xml_mod['id']);
					$mod->addChild('name', $xml_mod->name);
					$mod->addChild('description', $xml_mod->description);
					$mod->addChild('image_url', $xml_mod->image_url);
					$mod->addChild('download_url', $xml_mod->download_url);
					$mod->addChild('filename', $xml_mod->filename);
					$mod->addChild('file_size', $xml_mod->file_size);
				}
			}
			
			$config = $xml->addChild('config');
			$config->addChild('regex', $_POST['regex']);
			$config->addChild('mods_backreference_index', $_POST['mods_backreference_index']);
			$config->addChild('variable', $_POST['variable']);
			$config->addChild('place_after', $_POST['place_after']);
			$config->addChild('mod_string', $_POST['mod_string']);
			$config->addChild('string_separator', $_POST['string_separator']);
			$config->addChild('filepath', $_POST['filepath']);
			$xml->addChild('post_install', str_replace('&','&amp;',$_POST['post_install']));
			$xml->addChild('uninstall', str_replace('&','&amp;',$_POST['uninstall']));
			
			$dom = dom_import_simplexml($xml)->ownerDocument;
			$dom->formatOutput = true;
			file_put_contents($xml_file, $dom->saveXML());
		}
		
		if(isset($_POST['remove_mods']))
		{
			$xml = new SimpleXMLElement('<workshop_settings/>');
			$xml->addChild('workshop_id', $_POST['workshop_id']);
			$xml->addChild('download_method', $_POST['download_method']);
			$xml->addChild('anonymous_login', $_POST['anonymous_login']);
			$xml->addChild('mods_path', $_POST['mods_path']);
			$mods = $xml->addChild('mods');
			if(file_exists($xml_file))
			{
				$file_xml = simplexml_load_file($xml_file);
				foreach($file_xml->mods->mod as $xml_mod)
				{
					if(in_array($xml_mod['id'],$_POST['workshop_mod_id']))
						continue;
					$mod = $mods->addChild('mod');
					$mod->addAttribute('id', $xml_mod['id']);
					$mod->addChild('name', $xml_mod->name);
					$mod->addChild('description', $xml_mod->description);
					$mod->addChild('image_url', $xml_mod->image_url);
					$mod->addChild('download_url', $xml_mod->download_url);
					$mod->addChild('filename', $xml_mod->filename);
					$mod->addChild('file_size', $xml_mod->file_size);
				}
			}
			$config = $xml->addChild('config');
			$config->addChild('regex', $_POST['regex']);
			$config->addChild('mods_backreference_index', $_POST['mods_backreference_index']);
			$config->addChild('variable', $_POST['variable']);
			$config->addChild('place_after', $_POST['place_after']);
			$config->addChild('mod_string', $_POST['mod_string']);
			$config->addChild('string_separator', $_POST['string_separator']);
			$config->addChild('filepath', $_POST['filepath']);
			$xml->addChild('post_install', $_POST['post_install']);
			$xml->addChild('uninstall', $_POST['uninstall']);
			
			$dom = dom_import_simplexml($xml)->ownerDocument;
			$dom->formatOutput = true;
			file_put_contents($xml_file, $dom->saveXML());
		}
		
		if(file_exists($xml_file))
		{
			$xml = simplexml_load_file($xml_file);
			
			$workshop_id = $xml->workshop_id;
			$download_method = $xml->download_method;
			$anonymous_login = $xml->anonymous_login;
			$mods_path = $xml->mods_path;
			$regex = $xml->config->regex;
			$mods_backreference_index = $xml->config->mods_backreference_index;
			$variable = $xml->config->variable;
			$place_after = $xml->config->place_after;
			$mod_string = $xml->config->mod_string;
			$string_separator = $xml->config->string_separator;
			$filepath = $xml->config->filepath;
			$post_install = $xml->post_install;
			$uninstall = $xml->uninstall;
		}
		
		
	}
	
	$game_cfgs = $db->getGameCfgs();
	$games[0] = get_lang('select_game');
	foreach($game_cfgs as $game_cfg)
	{
		$server_xml = read_server_config(SERVER_CONFIG_LOCATION."/".$game_cfg['home_cfg_file']);
		if(isset($server_xml->installer) and $server_xml->installer == "steamcmd")
		{
			$cfgMods = $db->getCfgMods($game_cfg['home_cfg_id']);
			foreach($cfgMods as $cfgMod)
			{
				$mod_xml = xml_get_mod($server_xml, $cfgMod['mod_key']);
				if(isset($mod_xml->installer_name) and !in_array((string)$mod_xml->installer_name, get_blacklist()))
				{
					preg_match('/(linux|win)(32|64)?/i', $game_cfg['game_key'], $matches);
			
					if(strtolower($matches[1]) == 'linux')
						$os = "Linux";
					elseif(strtolower($matches[1]) == 'win')
						$os = "Windows";
					if(isset($matches[2]) and strtolower($matches[2]) == '64')
						$arch = "64";
					else
						$arch = "32";
					
					$modname = strtolower($cfgMod['mod_name']) == "none"? "":" [MOD:" . $cfgMod['mod_name']."]";
					
					$games[$game_cfg['home_cfg_id'].'-'.$cfgMod['mod_cfg_id'].'-'.$os] = $game_cfg['game_name'] . " (" . $os . " " . $arch . "bits)$modname";
				}
			}
		}
	}
	
	$download_methods = array("steamcmd", "steamapi");
	
	$ft = new FormTable();
	$ft->start_form("?m=steam_workshop&p=workshop_admin", "post", "autocomplete=\"off\"");
	$ft->start_table();
	$ft->add_custom_field('game', create_drop_box_from_array_onchange($games, "home_cfg_id-mod_cfg_id-os", @$_REQUEST['home_cfg_id-mod_cfg_id-os']));
	if(isset($home_cfg_id) and isset($mod_cfg_id))
	{
		$ft->add_field('string','workshop_id',@$workshop_id);
		$ft->add_custom_field('download_method',create_drop_box_from_array($download_methods, "download_method", @$download_method));
		$ft->add_field('on_off','anonymous_login',@$anonymous_login);
		$ft->add_field('string','mods_path',@$mods_path);
		$ft->add_field('string','regex',@$regex);
		$ft->add_field('string','mods_backreference_index',@$mods_backreference_index);
		$ft->add_field('string','variable',@$variable);
		$ft->add_field('string','place_after',@$place_after);
		$ft->add_field('string','mod_string',@$mod_string);
		$ft->add_field('string','string_separator',@$string_separator);	
		$ft->add_field('string','filepath',@$filepath);
		$ft->add_field('text','post_install',@$post_install);
		$ft->add_field('text','uninstall',@$uninstall);
		
		
		$ft->end_table();
		$ft->add_button("submit","save_config",get_lang('save_config'));
		$ft->end_form();
	}
	else
	{
		$ft->end_table();
		$ft->end_form();
	}
	
	if(isset($xml) and count($xml->mods->mod) > 0)
	{
		$ft = new FormTable();
		$ft->start_form("?m=steam_workshop&p=workshop_admin&home_cfg_id-mod_cfg_id-os=".$_REQUEST['home_cfg_id-mod_cfg_id-os'], "post", "autocomplete=\"off\"");
		$ft->start_table();
		echo "<tr><td>";
		$ft->add_field_hidden('workshop_id',$workshop_id);
		$ft->add_field_hidden('download_method',$download_method);
		$ft->add_field_hidden('anonymous_login',$anonymous_login);
		$ft->add_field_hidden('mods_path',$mods_path);
		$ft->add_field_hidden('regex',$regex);
		$ft->add_field_hidden('mods_backreference_index',$mods_backreference_index);
		$ft->add_field_hidden('variable',$variable);
		$ft->add_field_hidden('place_after',$place_after);
		$ft->add_field_hidden('mod_string',$mod_string);
		$ft->add_field_hidden('string_separator',$string_separator);	
		$ft->add_field_hidden('filepath',$filepath);
		$ft->add_field_hidden('post_install',$post_install);
		$ft->add_field_hidden('uninstall',$uninstall);
		echo "</td></tr>".
			 '<tr><td colspan=2><div id="scrolling_checkbox">';
		foreach($xml->mods->mod as $mod)
			echo "<input type='checkbox' id='select_mod_$mod[id]' name='workshop_mod_id[]' value='$mod[id]'><label for='select_mod_$mod[id]'>".$mod->name."</label><br>";
		echo '</div></td></tr>';
		$ft->end_table();
		$ft->add_button("submit","remove_mods",get_lang('remove_mods'));
		$ft->end_form();
	}
}
?>