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
function create_drop_box_from_array_onchange($input_array,$listname,$current_value = "")
{
	$only_one = count($input_array) == 1;
	$disabled = $only_one? "disabled=disabled":"";
	$retval = "<select id=\"$listname\" name=\"$listname\" style=\"max-width:330px;\" onchange=\"this.form.submit()\" $disabled>\n";
    foreach($input_array as $key => $value)
    {
		// Make sure we don't allow HTML or script
		$key = trim(strip_tags($key));
        $value = trim(strip_tags($value));
        
        // We want to print lines with zeros, but not empty lines.
        if ( empty($value) and $value !="0" )
        {
            continue;
        }
		
		$sel = "";
		
        if ( $key == $current_value )
        {
            $sel .= "selected='selected'";
        }

		$retval .= "<option value='$key' $sel>$value</option>\n";
    }
    $retval .= "</select>\n";
    return $retval;
}

function get_mod_names_list($mods_list, $xml_mods)
{
	$mod_names = "";
	foreach(explode(',', $mods_list) as $workshop_mod_id)
	{
		foreach($xml_mods as $mod)
		{
			if($mod['id'] == $workshop_mod_id)
			{
				if($mod_names != "")
					$mod_names .= ",";
				$mod_names .= $mod->name;
			}
		}
	}
	return $mod_names;
}

function get_mod_info($workshop_mod_id)
{
	$request = http_build_query(array('itemcount' => '1', 'publishedfileids[0]' => "$workshop_mod_id"));
	
	$context = stream_context_create
		(array('http' => array
		(
			'method' => "POST",
			'header' => "Content-type: application/x-www-form-urlencoded",
			'content' => $request,
			'timeout' => 5
		)));
	
	$json = @file_get_contents('http://api.steampowered.com/ISteamRemoteStorage/GetPublishedFileDetails/v1/', false, $context);
	$response_array = json_decode($json, true);
	$app_info = $response_array['response']['publishedfiledetails'][0];
	return array($app_info['title'], $app_info['description'], $app_info['preview_url'], $app_info['file_url'], basename($app_info['filename']), $app_info['file_size']);
}

function belongs_to_workshop($workshop_mod_id, $workshop_id)
{
	$request = http_build_query(array('itemcount' => '1', 'publishedfileids[0]' => "$workshop_mod_id"));
	
	$context = stream_context_create
		(array('http' => array
		(
			'method' => "POST",
			'header' => "Content-type: application/x-www-form-urlencoded",
			'content' => $request,
			'timeout' => 5
		)));
	
	$json = @file_get_contents('http://api.steampowered.com/ISteamRemoteStorage/GetPublishedFileDetails/v1/', false, $context);
	$response_array = json_decode($json, true);
	$app_info = $response_array['response']['publishedfiledetails'][0];
	if($app_info['creator_app_id'] == $workshop_id)
		return true;
	else
		return false;
}

function get_installed_mods($home_cfg, $remote, $xml)
{
	$workshop_id = $xml->workshop_id;
	$config = $xml->config;
	$regex = $config->regex;
	$mods_backreference_index = (int)$config->mods_backreference_index;
	$string_separator = stripcslashes($config->string_separator);
	$filepath = $config->filepath;
	$mods = $xml->mods->mod;
	
	$full_filepath = clean_path($home_cfg['home_path']."/$filepath");
	
	if($remote->rfile_exists($full_filepath) === 0)
		return False;
	
	if($remote->remote_readfile($full_filepath, $file_content) !== 1)
		return False;
	
	if(preg_match("/$regex/m", $file_content, $matches))
	{
		$full_regex_string = trim($matches[0]);
		$current_mods_string = trim($matches[$mods_backreference_index]);
		if($current_mods_string != '')
		{
			$retval = $remote->get_workshop_mods_info($mod_info_array);
			$current = explode($string_separator, $current_mods_string);
			$installed_mods = array();
			foreach($current as $c)
			{
				if($c != "")
				{			
					$mod_string = trim($c);
					if($retval == "1")
						$installed_mods["$mod_string"] = isset($mod_info_array["$mod_string"])?$mod_info_array["$mod_string"]:$mod_string;
					else
						$installed_mods["$mod_string"] = $mod_string;
				}
			}
			return $installed_mods;
		}
		else
			return False;
	}
	else
		return False;
}

function remove_mod($home_cfg, $remote, $xml, $mod_string)
{
	$config = $xml->config;
	$regex = $config->regex;
	$mods_backreference_index = (int)$config->mods_backreference_index;
	$variable = $config->variable;
	$string_separator = stripcslashes($config->string_separator);
	$filepath = $config->filepath;
		
	$full_filepath = $home_cfg['home_path']."/$filepath";
	$mods_full_path = clean_path($home_cfg['home_path'].'/'.$xml->mods_path);
	
	if($remote->rfile_exists($full_filepath) === 0)
		return False;
	
	$remote->remote_readfile($full_filepath, $file_content);
		
	if(preg_match("/$regex/m", $file_content, $matches))
	{
		$full_regex_string = trim($matches[0]);
		$current_mods_string = trim($matches[$mods_backreference_index]);
		if($current_mods_string != '')
		{
			$current = explode($string_separator, $current_mods_string);
			 
			foreach($current as $index => $c)
			{
				if(trim($c) == $mod_string)
					unset($current[$index]);
			}
			$current = array_filter($current);
			$new_mods_string = implode($string_separator, $current);
			
			$replacement = $variable.$new_mods_string;
			$file_content = str_replace($full_regex_string, $replacement, $file_content);
		}
		else
			return False;
	}
	else
		return False;
	
	$remote->remote_writefile($full_filepath, $file_content);
	$uninstall_filepath = clean_path($mods_full_path.'/postuninstall.sh');
	$uninstallcmd = str_replace('%mods_full_path%', $mods_full_path, $xml->uninstall);
	$uninstallcmd = str_replace('%mod_string%', $mod_string, $uninstallcmd);
	$uninstallcmd .= "\nrm -f $uninstall_filepath";
	$output = "";
	if($remote->remote_writefile($uninstall_filepath, $uninstallcmd) === 1)
		$output .= $remote->exec("bash $uninstall_filepath");
	return $output;
}

function get_blacklist()
{
	return array( "33900","33930","232330","90","294420","251570","17515","34120","302550","42750","489650","748090","232290",
				  "17585","739590","17555","232370","55280","17705","261140","222840","320850","317670","824360","381690",
				  "41005","208050","105600","556450","402370");
}
?>
