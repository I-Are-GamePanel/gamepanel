<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2017 The OGP Development Team
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

function exec_ogp_module() {

	global $db, $settings;

	$isAdmin = $db->isAdmin($_SESSION['user_id']);
	if ( $isAdmin )
		$server_homes = $db->getIpPorts();
	else
		$server_homes = $db->getIpPortsForUser($_SESSION['user_id']);
		
	$user = $db->getUserById($_SESSION['user_id']);

	if(isset($_POST["submit"])){
		
		$email = $_POST["email"];
		$gameserver = $_POST['gameserver'];
		$subject = get_lang('support').": ".$_POST["subject"];
		$message = $_POST["message"];
		
//TICKET SUBMITTED, POST ON DISCORD and log
//logger
	//$db->logger( "SUPPORT TICKET SUBMITTED ");
	$db->logger( "TICKET SUBMITTED by " . $_SESSION['user_id']);


// URL FROM DISCORD WEBHOOK SETUP
$webhook = "https://discordapp.com/api/webhooks/710275918274363412/g5Tr-EUdEnLfFryOlscxJ6FuPiSJuE6EMKRYmh9UGMiqTUxU5-y9CQrBlDJW7znr0Tol";
$msg = json_decode('
{
    "username":"I Are Gamer",
    "content":"SUPPORT TICKET CREATED: Login with the userid and password http://privateemail.com" 

}
', true);

discordmsg($msg, $webhook);
//end discord

		$content = get_lang_f('support_email_content', $user['users_login'], $email, $gameserver, $message);				
		if( mymail($email, $subject, $content, $settings, $user['users_login']) == TRUE )
		{
			?>
			<script type="text/javascript">
			$( document ).ready(function() {
				$('#dialog').html('<p><img src="modules/support/images/info.png" ><?php print_lang('message_has_been_sent'); ?></p>').dialog();
			});
			</script>
			<?php
		}
	} // End else
	echo "<h2>".get_lang('support')."</h2>";
	echo '<center><form class="contactForm" name="contactForm" action="" method="post"><p style="font-size:12px;text-align:center;">'.get_lang('please_describe_your_issue_below').'</p>';
	echo get_lang('select_server').":<br /><select name='gameserver' id='gameserver'>";
	foreach($server_homes as $server_home)
	{
		echo "<option value='".$server_home['home_name']."'>".$server_home['home_name']."</option>";
	}
	echo "</select><br /><br />";
		
	if(!isset($user['users_email']) or $user['users_email'] == "")
	{
		echo get_lang('email_address').':
			<br />
			<input type="text" name="email" id="email" style="width: 250px;" />
			<br />
			<br />';
	}
	else
	{
		echo '<input type="hidden" name="email" id="email" value="'.$user['users_email'].'" />';
	}
	
	echo get_lang('subject').':
	<br />
	<input type="text" name="subject" id="subject" style="width: 250px;" />
	<br />
	<br />
	'.get_lang('message').':
	<br />
	<textarea name="message" id="message" style="width:400px; height:200px;"></textarea>
	<br />
	<br />
	<input type="submit" name="submit" value="'.get_lang('send').'" style="width:100px; height:30px; font-size:18px;" onclick="return validateForm()" />
	</form></center><br><br>';
	echo '<div id="dialog" title="'.get_lang('info').'"></div>';
	?>
	<script type="text/javascript">
	function validateForm()
	{
		var $email=document.forms["contactForm"]["email"].value;
		var $subject=document.forms["contactForm"]["subject"].value;
		var $message=document.forms["contactForm"]["message"].value;
		if ($email==null || $email=="")
		{
			$('#dialog').html('<p><img src="modules/support/images/error.png" ><?php print_lang('email_must_be_filled_out'); ?></p>').attr('title', '<?php print_lang('error'); ?>').dialog();
			return false;
		}
		else if ($subject==null || $subject=="")
		{
			$('#dialog').html('<p><img src="modules/support/images/error.png" ><?php print_lang('subject_must_be_filled_out'); ?></p>').attr('title', '<?php print_lang('error'); ?>').dialog();
			return false;
		}
		else if ($message==null || $message=="")
		{
			$('#dialog').html('<p><img src="modules/support/images/error.png" ><?php print_lang('message_must_be_filled_out'); ?></p>').attr('title', '<?php print_lang('error'); ?>').dialog();
			return false;
		}
	}
	</script>
	<?php 
} // End function
