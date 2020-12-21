<h2 class="mbr-section-title align-center pb-5 mbr-fonts-style display-2">
	tshock API Module
</h2>
<?php
global $db, $view;
$continue = True;
if(isset($_GET['home_id-mod_id-ip-port']))
	list($home_id, $mod_id, $ip, $port) = explode("-", $_GET['home_id-mod_id-ip-port']);

$home_cfg_ids = array();
foreach($db->getGameCfgs() as $cfg)
{
	if(preg_match('/terraria/i', $cfg['home_cfg_file']))
		$home_cfg_ids[] = $cfg['home_cfg_id'];
}

if(!empty($home_cfg_ids))
{
	$server_homes = array();
	$isAdmin = $db->isAdmin($_SESSION['user_id']);
	foreach($home_cfg_ids as $home_cfg_id)
	{
		if($isAdmin)
			$server_homes = array_merge($server_homes, $db->getHomesFor_limit('admin', $_SESSION['user_id'], 1, 9999, $home_cfg_id,''));
		else	
			$server_homes = array_merge($server_homes, $db->getHomesFor_limit('user_and_group', $_SESSION['user_id'], 1, 9999, $home_cfg_id,''));
	}
	
	if(empty($server_homes))
	{
		print_failure(get_lang('no_game_homes_assigned'));
		$continue = False;
		return;
	}
	else
	{
		create_home_selector_address($_GET['m'], 'default', $server_homes);
		
		if(isset($_GET['home_id-mod_id-ip-port']) && $_GET['home_id-mod_id-ip-port'] != "")
			list($home_id, $mod_id, $ip, $port) = explode("-", $_GET['home_id-mod_id-ip-port']);
		else
		{
			print_failure("No server selected.");
			$continue = False;
			return;
		}
	}
}
else
{
	print_failure("No Terraria XML found");
	$continue = False;
	return;
}


include 'modules/tshock/functions.php';

if($_GET['p'] != 'create_token')
{
	$token = getToken($ip, $port);

	if(!$token)
	{
		$view->refresh('home.php?m=tshock&p=create_token&home_id-mod_id-ip-port='.$_GET['home_id-mod_id-ip-port'], 0);
		$continue = False;
		return;
	}
		
	$token_test = getResponse($ip, $port, '/tokentest/', array('token' => $token));

	if($token_test['status'] != '200')
	{
		$view->refresh('home.php?m=tshock&p=create_token&home_id-mod_id-ip-port='.$_GET['home_id-mod_id-ip-port'], 0);
		$continue = False;
		return;
	}

	$game_home = $db->getGameHomeByIP($ip, $port);

	if ($game_home === FALSE)
	{
		print_failure( get_lang("no_access_to_home") );
		$continue = False;
		return;
	}

	if($game_home['home_cfg_file'] != 'terraria_win64.xml')
	{
		print_failure( "The given address is not for Terraria" );
		$continue = False;
		return;
	}
}
else
{
	if(!istshockAvailable($ip, $port))
		$continue = False;
	return;
}
?>
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a href="?m=tshock&p=bans&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">Bans</a>
	</li>
	<li class="nav-item">
		<a href="?m=tshock&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">Server</a>
	</li>
	<li class="nav-item">
		<a href="?m=tshock&p=users&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">Users</a>
	</li>
</ul>
