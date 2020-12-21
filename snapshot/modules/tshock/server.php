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
function exec_ogp_module()
{
	include "modules/tshock/shared.php";
	if($continue)
	{
		?>	
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item">
				<a href="?m=tshock&show=broadcast&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
					Broadcast
				</a>
			</li>
			<li class="nav-item">
				<a href="?m=tshock&show=motd&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
					Message Of The Day
				</a>
			</li>
			<li class="nav-item">
				<a href="?m=tshock&show=off&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
					Off
				</a>
			</li>
			<li class="nav-item">
				<a href="?m=tshock&show=rawcmd&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
					Rawcmd
				</a>
			</li>
			<li class="nav-item">
				<a href="?m=tshock&show=reload&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
					Reload
				</a>
			</li>
			<li class="nav-item">
				<a href="?m=tshock&show=restart&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
					Restart
				</a>
			</li>
			<li class="nav-item">
				<a href="?m=tshock&show=rules&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
					Rules
				</a>
			</li>
			<li class="nav-item">
				<a href="?m=tshock&show=status&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
					Status
				</a>
			</li>
		</ul><?php
		if($_GET['show'] == 'broadcast') //Broadcast a server wide message.
		{
		?>
		<div id="tab4" class="tab-pane" role="tabpanel">
			<div class="row">
				<div class="col-md-12">
					<p class="mbr-text py-5 mbr-fonts-style display-7"></p>
						<form action="?m=tshock&show=broadcast&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>" method="POST">
							Message: <input type="text" name="msg" placeholder="The message to broadcast" size="45"><br>
							<br><input type="submit" name="broadcast" value="Submit">
						</form><?php
			if(isset($_POST['broadcast'])){
				if($token){
					$params = array('token' => $token,
									'msg' => $_POST['msg']);
					$response = getResponse($ip, $port, '/v2/server/broadcast/', $params);
					if($response['status'] == '200')
						print_success($response['response']);
					else
						print_failure($response['error']);
				}
				else
					print_failure("No Token Found!");
			}?>
				</div>
			</div>
		</div>
		<?php
		}
		
		if($_GET['show'] == 'motd') //Returns the motd, if it exists.
		{
			if($token){
				$params = array('token' => $token);
				$response = getResponse($ip, $port, '/v3/server/motd/', $params);
				if($response['status'] == '200')
					print_success('Message of the day:<br>'.$response['response']);
				else
					print_failure($response['error']);
			}
			else
				print_failure("No Token Found!");
		}
		
		if($_GET['show'] == 'off') //Turn the server off.
		{?>
			<div id="tab4" class="tab-pane" role="tabpanel">
				<div class="row">
					<div class="col-md-12">
						<p class="mbr-text py-5 mbr-fonts-style display-7"></p>
							<form action="?m=tshock&show=off&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>" method="POST">
								Message: <input type="text" name="message" placeholder="The shutdown message" size="35"><br><br>
								Confrim (Required to confirm that you want to turn the server off):<input type="checkbox" name="confirm" value="confirm"><br><br>
								No save (Shutdown without saving):<input type="checkbox" name="nosave" value="nosave"><br>
								<br><input type="submit" name="off" value="Submit">
							</form>
					</div>
				</div>
			</div><?php
			if(isset($_POST['off'])){
				if($token){
					$confirm = isset($_POST['confirm'])?"true":"false";
					$nosave = isset($_POST['nosave'])?"true":"false";
					$params = array('token' => $token,
									'message' => $_POST['message'],
									'confirm' => $confirm,
									'nosave' => $nosave);
					$response = getResponse($ip, $port, '/v2/server/off/', $params);
					if($response['status'] == '200')
						print_success($response['response']);
					else
						print_failure($response['error']);
				}
				else
					print_failure("No Token Found!");
			}
		}
		
		if($_GET['show'] == 'rawcmd') //Executes a remote command on the server, and returns the output of the command.
		{
		?>
			<div id="tab4" class="tab-pane" role="tabpanel">
				<div class="row">
					<div class="col-md-12">
						<p class="mbr-text py-5 mbr-fonts-style display-7"></p>
							<form action="?m=tshock&show=rawcmd&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>" method="POST">
								cmd: <input type="text" name="cmd" placeholder="The command and arguments to execute" size="45"><br>
								<br><input type="submit" name="rawcmd" value="Submit">
							</form>
					</div>
				</div>
			</div>
			<?php
			if(isset($_POST['rawcmd'])){
				if($token){
					$params = array('token' => $token,
									'cmd' => $_POST['cmd']);
					$response = getResponse($ip, $port, '/v3/server/rawcmd/', $params);
					if($response['status'] == '200')
					{
						$response_table = "<pre>";
						foreach($response['response'] as $line)
							$response_table .= $line."<br>";
						$response_table .= "</pre>";
						echo $response_table;
					}
					else
						print_failure($response['error']);
				}
				else
					print_failure("No Token Found!");
			}
		}
		
		if($_GET['show'] == 'reload') //Reload config files for the server.
		{?>
			<div id="tab4" class="tab-pane" role="tabpanel">
				<div class="row">
					<div class="col-md-12">
						<p class="mbr-text py-5 mbr-fonts-style display-7"></p>
							<form action="?m=tshock&show=reload&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>" method="POST">
								<input type="submit" name="reload" value="Reload configuration, permissions and regions">
							</form>
					</div>
				</div>
			</div><?php
			if(isset($_POST['reload'])){
				if($token){
					$params = array('token' => $token);
					$response = getResponse($ip, $port, '/v3/server/reload/', $params);
					if($response['status'] == '200')
					{
						print_success($response['response']);
					}
					else
						print_failure($response['error']);
				}
				else
					print_failure("No Token Found!");
			}
		}
		
		if($_GET['show'] == 'restart') //Attempt to restart the server.
		{?>
			<div id="tab4" class="tab-pane" role="tabpanel">
				<div class="row">
					<div class="col-md-12">
						<p class="mbr-text py-5 mbr-fonts-style display-7"></p>
							<form action="?m=tshock&show=restart&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>" method="POST">
								Message: <input type="text" name="message" placeholder="The shutdown message" size="35"><br><br>
								Confrim (Required to confirm that you want to restart the server):<input type="checkbox" name="confirm" value="confirm"><br><br>
								No save (Shutdown without saving):<input type="checkbox" name="nosave" value="nosave"><br>
								<br><input type="submit" name="restart" value="Submit">
							</form>
					</div>
				</div>
			</div><?php
			if(isset($_POST['restart'])){
				if($token){
					$confirm = isset($_POST['confirm'])?"true":"false";
					$nosave = isset($_POST['nosave'])?"true":"false";
					$params = array('token' => $token,
									'message' => $_POST['message'],
									'confirm' => $confirm,
									'nosave' => $nosave);
					$response = getResponse($ip, $port, '/v3/server/restart/', $params);
					if($response['status'] == '200')
						print_success($response['response']);
					else
						print_failure($response['error']);
				}
				else
					print_failure("No Token Found!");
			}
		}
		
		if($_GET['show'] == 'rules') //Returns the rules, if they exist.
		{
		?>
			<div id="tab4" class="tab-pane" role="tabpanel">
				<div class="row">
					<div class="col-md-12">
						<p class="mbr-text py-5 mbr-fonts-style display-7"></p>
							<form action="?m=tshock&show=rules&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>" method="POST">
								<input type="submit" name="rules" value="Show Rules">
							</form>
					</div>
				</div>
			</div><?php
			if(isset($_POST['rules'])){
				if($token){
					$params = array('token' => $token);
					$response = getResponse($ip, $port, '/v3/server/rules/', $params);
					if($response['status'] == '200')
					{
						$rules_div = "<div><ul class='rules'>";
						foreach($response['rules'] as $line)
							$rules_div .= "<li>".$line."</li>";
						$rules_div .= "</ul></div>";
						echo $rules_div;
					}
					else
						print_failure($response['error']);
				}
				else
					print_failure("No Token Found!");
			}
		}
		
		if($_GET['show'] == 'status') //The status endpoint returns basic information about the server's status.
		{
		?>
			<div id="tab4" class="tab-pane" role="tabpanel">
				<div class="row">
					<div class="col-md-12">
						<p class="mbr-text py-5 mbr-fonts-style display-7"></p>
							<form action="?m=tshock&show=status&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>" method="POST">
								Show Players:<input type="checkbox" name="players" value="players"><br>
								Show Rules:<input type="checkbox" name="rules" value="rules"><br>
								<br><input type="submit" name="status" value="Submit">
							</form>
					</div>
				</div>
			</div>
		<?php
			if(isset($_POST['status'])){
				if($token){
					$players = isset($_POST['players'])?"true":"false";
					$rules = isset($_POST['rules'])?"true":"false";
					$params = array('token' => $token,
									'players' => $players,
									'rules' => $rules);
					$response = getResponse($ip, $port, '/v2/server/status/', $params);
					if($response['status'] == '200')
					{
						$status_table = "<table class='status'>";
						foreach($response as $key => $value)
						{
							if($key == "status")
								continue;
							if(is_array($value))
							{
								$status_table .= "<tr class='entry_t1'><td class='key'>".$key."</td><td><table class='status'>";
								foreach($value as $v_key => $v_value)
								{
									if(is_array($v_value))
									{
										$status_table .= "<tr><td class='key'>".$v_key."</td><td><table class='status'>";
										foreach($v_value as $v_subkey => $v_subvalue)
										{
											$status_table .= "<tr class='entry_t2'><td class='key'>".$v_subkey."</td><td class='value'>".$v_subvalue."</td></tr>";
										}
										$status_table .= "</table></td></tr>";
									}
									else
									{
										$status_table .= "<tr class='entry_t3'><td class='key'>".$v_key."</td><td class='value'>".$v_value."</td></tr>";
									}
									
								}
								$status_table .= "</table></td></tr>";
							}
							else
							{
								$status_table .= "<tr class='entry_t4'><td class='key'>".$key."</td><td class='value'>".$value."</td></tr>";
							}
						}
						$status_table .= "</table>";
						echo $status_table;
					}
					else
						print_failure($response['error']);
				}
				else
					print_failure("No Token Found!");
			}
		}
	}
}