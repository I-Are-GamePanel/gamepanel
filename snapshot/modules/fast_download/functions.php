<?php
function createIndexString( $title, $charset )
{
	return "<html>\n".
		   " <head>\n".
		   "  <title>$title</title>\n".
		   "  <meta http-equiv='Content-Type' content='text/html; charset=$charset' />\n".
		   " </head>\n".
		   " <body>\n".
		   "  <h2>$title</h2>\n".
		   " </body>\n".
		   "</html>";
}

function in_array_match($needle, $haystack) {
	if (!is_array($haystack))
		trigger_error('Argument 2 must be array');
	$needle = "#".preg_quote($needle)."#";
	foreach ($haystack as $value) {
		$match = preg_match($needle, $value);
		if ($match === 1) {
			return true;
		}
	}
	return false;
}

function get_alias_by_home_path($home_path, $aliases) {
	if (!is_array($aliases))
		trigger_error('Argument 2 must be array');
	$home_path = "#".preg_quote($home_path)."#";
	$alias_info = false;
	foreach ($aliases as $alias => $info) {
		if( preg_match($home_path, $info['home']) )
		{
			$alias_info = $aliases[$alias];
			$alias_info['alias'] = $alias;
			break;
		}
	}
	return $alias_info;
}

function clean_string($string)
{
	$string = preg_replace("/[^0-9a-zA-Z_ ]/m", "", $string);
	return str_replace(" ", "_", $string);
}

function get_access_rules($home_cfg_id = "")
{
	global $db;
	if($home_cfg_id == "")
	{
		return $db->resultQuery("SELECT * FROM `".OGP_DB_PREFIX."fastdl_access_rules`;");
	}
	else
	{
		$result = $db->resultQuery("SELECT * FROM `".OGP_DB_PREFIX."fastdl_access_rules` 
									WHERE `home_cfg_id`='".$db->realEscapeSingle($home_cfg_id)."'");
		if($result === FALSE)
		{
			$result = $db->resultQuery("SELECT * FROM `".OGP_DB_PREFIX."fastdl_access_rules` 
										WHERE `home_cfg_id`='0'");
			if($result === FALSE)
				return array('match_file_extension' => NULL,'match_client_ip', NULL);
		}
		return $result[0];
	}
}

function set_access_rule($home_cfg_id, $match_file_extension, $match_client_ip)
{
	global $db;
	if (!$db->resultQuery("SELECT * FROM `".OGP_DB_PREFIX."fastdl_access_rules` 
								WHERE `home_cfg_id`='".$db->realEscapeSingle($home_cfg_id)."'"))
	{
		if($match_file_extension == "" and  $match_client_ip == "")
			return TRUE;
		return $db->query("INSERT INTO `".OGP_DB_PREFIX."fastdl_access_rules`
						  (`home_cfg_id`,`match_file_extension`,`match_client_ip`)
						  VALUES('" . $db->realEscapeSingle($home_cfg_id) . "','" . $db->realEscapeSingle($match_file_extension) . "','" . $db->realEscapeSingle($match_client_ip) . "');");
	}
	else
	{
		if($match_file_extension == "" and  $match_client_ip == "")
		{
			return $db->query("DELETE FROM `".OGP_DB_PREFIX."fastdl_access_rules` WHERE `home_cfg_id`='" . $db->realEscapeSingle($home_cfg_id) . "';");
		}
		else
		{
			return $db->query("UPDATE `".OGP_DB_PREFIX."fastdl_access_rules` 
							  SET `match_file_extension`='" . $db->realEscapeSingle($match_file_extension) . "',
								  `match_client_ip`='" . $db->realEscapeSingle($match_client_ip) . "'
							  WHERE `home_cfg_id`='" . $db->realEscapeSingle($home_cfg_id) . "';");
		}
	}
}

function del_access_rule($home_cfg_id)
{
	global $db;
	return $db->query("DELETE FROM `".OGP_DB_PREFIX."fastdl_access_rules` WHERE `home_cfg_id`='" . $db->realEscapeSingle($home_cfg_id) . "';");
}

function check_access_rules_entries()
{
	$match_file_extension = "";
	$match_client_ip = "";
	$ip_entry_fail = FALSE;
	$extension_entry_fail = FALSE;
	$failures = get_lang("invalid_entries_found").":<br>";
	if($_POST['match_file_extension'] != "")
	{
		$entries = explode(",",$_POST['match_file_extension']);
		$entries = array_unique(array_filter($entries));
		foreach($entries as $key => $entry)
		{
			$entry = trim($entry);
			if(!preg_match("/^[a-z0-9]+$/i",$entry))
			{
				$failures .= "$entry<br>";
				unset($entries[$key]);
				$extension_entry_fail = TRUE;
			}
			else
			{
				$entries[$key] = $entry;
			}
		}
		$match_file_extension = implode(",",$entries);
	}
	if($_POST['match_client_ip'] != "")
	{
		$entries = explode(",",$_POST['match_client_ip']);
		$entries = array_unique(array_filter($entries));
		foreach($entries as $key => $entry)
		{
			$entry = trim($entry);
			if( !preg_match('#(^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})$|'.
							'(^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/(\d{1,2})$|'.
							'(^\d{1,3}\.\d{1,3}\.\d{1,3}\.)(\d{1,3})-(\d{1,3})$|'.
							'^[\d\*]{1,3}\.[\d\*]{1,3}\.[\d\*]{1,3}\.[\d\*]{1,3}$#',$entry) )
			{
				$failures .= "$entry<br>";
				unset($entries[$key]);
				$ip_entry_fail = TRUE;
			}
			else
			{
				$entries[$key] = $entry;
			}
		}
		$match_client_ip = implode(",",$entries);
	}
	if($ip_entry_fail or $extension_entry_fail)
		print_failure($failures);
	return array('match_client_ip' => $match_client_ip, 
				 'match_file_extension' => $match_file_extension,
				 'ip_entry_fail' => $ip_entry_fail,
				 'extension_entry_fail' => $extension_entry_fail);
}

function get_fastdl_settings($remote_server_id)
{
	global $db;
	if( !is_numeric($remote_server_id) ) return FALSE;
	$result = $db->resultQuery("SELECT `setting`,`value` FROM `".OGP_DB_PREFIX."fastdl_settings` 
								WHERE `remote_server_id`='".$db->realEscapeSingle($remote_server_id)."'");
	if(!$result) return FALSE;
	$results = array();
	foreach($result as $row)
	{
		$results[$row['setting']] = $row['value'];
	}
	return $results;

}

function set_fastdl_settings($remote_server_id, $settings)
{
	global $db;
	if( !is_numeric($remote_server_id) ) return FALSE;
	if( !is_array($settings) ) return FALSE;
	foreach ( $settings as $s_key => $s_value )
	{
		$query = 'INSERT INTO `'.OGP_DB_PREFIX.'fastdl_settings` (`remote_server_id`,`setting`,`value`)
			VALUES(\''.$db->realEscapeSingle($remote_server_id).'\', \''.$db->realEscapeSingle($s_key).'\', \''.$db->realEscapeSingle($s_value).'\') ON DUPLICATE KEY
			UPDATE value=\''.$db->realEscapeSingle($s_value).'\'';
		$db->query($query);
	}
	return TRUE;
}
?>
