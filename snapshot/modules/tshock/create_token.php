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
		<div id="tab4" class="tab-pane" role="tabpanel">
			<div class="row">
				<div class="col-md-12">
					<p class="mbr-text py-5 mbr-fonts-style display-7"></p>
					<form action="?m=tshock&p=create_token&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>" method="POST">
						Username: <input type="text" name="username" placeholder="Superadmin Username"><br>
						Password: <input type="text" name="password" placeholder="Superadmin Password"><br>
						<input type="submit" name="create_token" value="Create Token">
					</form>
				</div>
			</div>
		</div>
		<?php
		If(isset($_POST['create_token'])){
			$response = getResponse($ip, $port, '/v2/token/create', array('username' => $_POST['username'], 'password' => $_POST['password']));
			
			If($response['status'] == '200'){
				if(saveToken($ip, $port, $response['token']))
				{
					print_success("Token Saved!");
					$view->refresh('home.php?m=tshock&home_id-mod_id-ip-port='.$_GET['home_id-mod_id-ip-port'], 2);
				}
				else
				{
					print_failure("Failed saving the token.");
				}
			}
			else
			{
				print_failure($response['error']);
			}
		}
	}
}
	?>