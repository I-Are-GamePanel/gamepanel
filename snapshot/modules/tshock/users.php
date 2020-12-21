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
			<li class="nav-item"><a href="?m=tshock&p=users&show=create&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
					Create User
				</a></li>
			<li class="nav-item"><a href="?m=tshock&p=users&show=destroy&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
					Destroy User
				</a></li>
			<li class="nav-item"><a href="?m=tshock&p=users&show=activelist&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
					Active List
				</a></li>
			<li class="nav-item"><a href="?m=tshock&p=users&show=list&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
					List
				</a></li>
			<li class="nav-item"><a href="?m=tshock&p=users&show=read&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
					Read
				</a></li>
			<li class="nav-item"><a href="?m=tshock&p=users&show=update&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
					Update
				</a></li>
		</ul>			
		<?php
		if($_GET['show'] == 'create') //Create a new tshock user account.
		{
		?>
			<div id="tab4" class="tab-pane" role="tabpanel">
				<div class="row">
					<div class="col-md-12">
						<p class="mbr-text py-5 mbr-fonts-style display-7"></p>
						<form action="?m=tshock&p=users&show=create&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>" method="POST">
							Username: <input type="text" name="user" placeholder="The user account name for the new account" size="45"><br><br>
							Group: <input type="text" name="group" placeholder="The group the new account should be assigned" size="49"><br><br>											
							Password: <input type="text" name="password" placeholder="The password for the new account" size="46"><br><br>
							<input type="submit" name="create" value="Create Account">
						</form>
					</div>
				</div>
			</div><?php
			if(isset($_POST['create'])){
				if($token){
					$params = array('token' => $token,
									'user' => $_POST['user'],
									'group' => $_POST['group'],
									'password' => $_POST['password']);
					$response = getResponse($ip, $port, '/v2/users/create/', $params);
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
		
		if($_GET['show'] == 'destroy') //Destroy a tshock user account
		{
		?>
			<div id="tab4" class="tab-pane" role="tabpanel">
				<div class="row">
					<div class="col-md-12">
						<p class="mbr-text py-5 mbr-fonts-style display-7"></p>
						<form action="?m=tshock&p=users&show=destroy&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>" method="POST">
							Username/ID: <input type="text" name="user" placeholder="Name or id of account to lookup" size="35"><br><br>
							Lookup Type: <input type="radio" name="type" value="name" checked> Name
										 <input type="radio" name="type" value="id"> ID<br><br>
							<input type="submit" name="destroy" value="Remove Account">
						</form>
					</div>
				</div>
			</div><?php
			if(isset($_POST['destroy'])){
				if($token){
					$params = array('token' => $token,
									'user' => $_POST['user'],
									'type' => $_POST['type']);
					$response = getResponse($ip, $port, '/v2/users/destroy/', $params);
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
		
		if($_GET['show'] == 'activelist') //Returns the list of user accounts that are currently in use on the server.
		{
			if($token){
				$params = array('token' => $token);
				$response = getResponse($ip, $port, '/v2/users/activelist/', $params);
				if($response['status'] == '200')
				{
					if($response['activeusers'] != "")
						print_success($response['activeusers']);
					else
						print_failure("There are no active users online.");
				}
				else
					print_failure($response['error']);
			}
			else
				print_failure("No Token Found!");
		}
		
		if($_GET['show'] == 'list') //Lists all user accounts in the tshock database.
		{
			if($token){
				$params = array('token' => $token);
				$response = getResponse($ip, $port, '/v2/users/list/', $params);
				if($response['status'] == '200')
				{
					$users_table = "<table class='users_list'><theader><td>ID</td><td>Name</td><td>Group</td></theader>";
					foreach($response['users'] as $user)
						$users_table .= "<tr><td class='user_id'>".$user['id']."</td><td class='user_name'>".$user['name']."</td><td class='user_group'>".$user['group']."</td></tr>";
					$users_table .= "</table>";
					echo $users_table;
				}
				else
					print_failure($response['error']);
			}
			else
				print_failure("No Token Found!");
		}
		
		if($_GET['show'] == 'read') //List detailed information for a user account.
		{
		?>
			<div id="tab4" class="tab-pane" role="tabpanel">
				<div class="row">
					<div class="col-md-12">
						<p class="mbr-text py-5 mbr-fonts-style display-7"></p>
						<form action="?m=tshock&p=users&show=read&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>" method="POST">
							Username: <input type="text" name="user" placeholder="username or id of account to look up" size="35"><br><br>
							Lookup Type: <input type="radio" name="type" value="name" checked> Name
										 <input type="radio" name="type" value="id"> ID<br><br>
							<input type="submit" name="read" value="Submit">
						</form>
					</div>
				</div>
			</div><?php
			if(isset($_POST['read'])){
				if($token){
					$params = array('token' => $token,
									'user' => $_POST['user'],
									'type' => $_POST['type']);
					$response = getResponse($ip, $port, '/v2/users/read/', $params);
					if($response['status'] == '200')
					{
						print_success("ID: ".$response['id'].", Group: ".$response['group'].", Name: ".$response['name']);
					}
					else
						print_failure($response['error']);
				}
				else
					print_failure("No Token Found!");
			}
		}
		
		if($_GET['show'] == 'update') //Update a users information.
		{
		?>
			<div id="tab4" class="tab-pane" role="tabpanel">
				<div class="row">
					<div class="col-md-12">
						<p class="mbr-text py-5 mbr-fonts-style display-7"></p>
						<form action="?m=tshock&p=users&show=update&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>" method="POST">
							Username: <input type="text" name="user" placeholder="name or id of account to lookup" size="35"><br><br>
							Lookup Type: <input type="radio" name="type" value="name" checked> Name
										 <input type="radio" name="type" value="id"> ID<br><br>
							Password: <input type="text" name="password" placeholder="The users new password" size="35"><br><br>
							Group: <input type="text" name="group" placeholder="The new group for the user" size="38"><br><br>
							<input type="submit" name="update" value="Update">
						</form>
					</div>
				</div>
			</div><?php
			if(isset($_POST['update'])){
				if($token){
					$params = array('token' => $token,
									'user' => $_POST['user'],
									'type' => $_POST['type'],
									'password' => $_POST['password'],
									'group' => $_POST['group']);
					$response = getResponse($ip, $port, '/v2/users/update/', $params);
					if($response['status'] == '200')
					{
						foreach($response as $key => $resp)
						{
							if($key == "status")
								continue;
							print_success($resp);
						}
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
?>