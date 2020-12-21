<?php

function getResponse($ip, $port, $query, $params = array()){
	$api_request = 'http://'.$ip.':'.($port + 101).$query;
	if(!empty($params))
	{
		$api_request .= '?';
		foreach($params as $param_key => $param_value)
			$api_request .= $param_key."=".urlencode($param_value)."&";
	}
	return json_decode(file_get_contents($api_request), True);
}

function istshockAvailable($ip, $port){
	$api_url = 'http://'.$ip.':'.($port + 101);
	if(!file_get_contents($api_url)) 
	{
		print_failure("tshock is not available in the selected server, or the server is offline");
		return false;
	}
    return true;
}

function saveToken($ip, $port, $token){
	global $db;
	$query = sprintf("DELETE FROM `%stshock` WHERE `ip` = '%s' AND `port` = '%d'",
			$db->getTablePrefix(),
			$db->realEscapeSingle($ip),
			$db->realEscapeSingle($port));
	if($db->query($query))
	{
		$query = sprintf("INSERT INTO `%stshock` (`ip`,`port`,`token`) VALUES('%s', '%d', '%s')",
				$db->getTablePrefix(),
				$db->realEscapeSingle($ip),
				$db->realEscapeSingle($port),
				$db->realEscapeSingle($token));
		if($db->query($query))
		{
			return True;
		}
	}
	return False;
}

function getToken($ip, $port){
	global $db;
	$query = sprintf("SELECT `token` FROM `%stshock` WHERE `ip`='%s' AND `port`='%d';",
			$db->getTablePrefix(),
			$db->realEscapeSingle($ip),
			$db->realEscapeSingle($port));
	$result = $db->resultQuery($query);
	if($result != FALSE)
	{
		$last = end($result);
		return $last['token'];
	}
	return False;
}