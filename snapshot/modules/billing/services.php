<?php
function exec_ogp_module()
{		
	global $db;
	
	//Querying UPDATE a service FROM DB
	if (isset($_POST['service']) AND isset($_POST['new_enabled']))
	{
		$new_remote_server_id = $db->realEscapeSingle($_POST['new_remote_server_id']);
		$new_price_monthly = $db->realEscapeSingle($_POST['new_price_monthly']);
								  
		$new_url = $db->realEscapeSingle($_POST['new_url']);
		$new_enabled = $db->realEscapeSingle($_POST['new_enabled']);
		$service = $db->realEscapeSingle($_POST['service']);

		//Create INSERT query
		$qry_change_url = "UPDATE OGP_DB_PREFIXbilling_services
						   SET remote_server_id = '".$new_remote_server_id."',
							   price_monthly ='".$new_price_monthly."', 
							   remote_server_id = '".$new_remote_server_id."',
							   img_url ='".$new_url."',
							   enabled = '".$new_enabled."'
							   WHERE service_id=".$service;
		$db->query($qry_change_url);
	}

	//Querying INSERT new service INTO DB
	if(isset($_POST['mod_cfg_id']) AND isset($_POST['remote_server_id']) AND isset($_POST['slot_max_qty']) AND isset($_POST['price_daily']) AND isset($_POST['price_monthly']) AND isset($_POST['price_year']))
	{
		//Sanitize the POST values
		$home_cfg_id = $db->realEscapeSingle($_POST['home_cfg_id']);
		$mod_cfg_id = $db->realEscapeSingle($_POST['mod_cfg_id']);
		$service_name = $db->realEscapeSingle($_POST['service_name']);
		foreach ($_POST['remote_server_id'] as $remote)
			{
				$remote_server_id = $remote_server_id .  $remote . " ";
			}
		//echo $remote_servers_id;
		//$remote_server_id = $remote_servers_id;
		//$remote_server_id = $db->realEscapeSingle($_POST['remote_server_id']);
		$slot_max_qty = $db->realEscapeSingle($_POST['slot_max_qty']);
		$slot_min_qty = $db->realEscapeSingle($_POST['slot_min_qty']);
		$price_daily = $db->realEscapeSingle($_POST['price_daily']);
		$price_monthly = $db->realEscapeSingle($_POST['price_monthly']);
		$price_year = $db->realEscapeSingle($_POST['price_year']);
		$description = $db->realEscapeSingle($_POST['description']);
		$img_url = $db->realEscapeSingle($_POST['img_url']);
		$ftp = $db->realEscapeSingle($_POST['ftp']);
		$install_method = $db->realEscapeSingle($_POST['install_method']);
		$manual_url = $db->realEscapeSingle($_POST['manual_url']);
		$access_rights = "";
		$enabled = 1;
		if(isset($_POST['allow_updates']))$access_rights .= $db->realEscapeSingle($_POST['allow_updates']);
		if(isset($_POST['allow_file_management']))$access_rights .= $db->realEscapeSingle($_POST['allow_file_management']);
		if(isset($_POST['allow_parameter_usage']))$access_rights .= $db->realEscapeSingle($_POST['allow_parameter_usage']);
		if(isset($_POST['allow_extra_params']))$access_rights .= $db->realEscapeSingle($_POST['allow_extra_params']);
		if(isset($_POST['allow_ftp_usage']))$access_rights .= $db->realEscapeSingle($_POST['allow_ftp_usage']);
		if(isset($_POST['allow_custom_fields']))$access_rights .= $db->realEscapeSingle($_POST['allow_custom_fields']);
		
		$qry_add_service = "INSERT INTO OGP_DB_PREFIXbilling_services(service_id, home_cfg_id, mod_cfg_id, service_name, remote_server_id, slot_max_qty , slot_min_qty, price_daily, price_monthly, price_year, description, img_url, ftp, install_method, manual_url, access_rights,enabled) VALUES(NULL, '".$home_cfg_id."', '".$mod_cfg_id."', '".$service_name."', '".$remote_server_id."', '".$slot_max_qty."', '".$slot_min_qty."', '".$price_daily."', '".$price_monthly."', '".$price_year."', '".$description."', '".$img_url."', '".$ftp."', '".$install_method."', '".$manual_url."', '".$access_rights."', '" . $enabled . "')";
		$db->query($qry_add_service);	
	}
	
	//Querying REMOVE service FROM DB
	if (isset($_POST['service_id']))
	{
		$db->query( "DELETE FROM OGP_DB_PREFIXbilling_services WHERE service_id=" . $db->realEscapeSingle($_POST['service_id']) );
	}
	
	?>
	<h2><?php print_lang('add_service');?></h2>
	<form method="POST" action="">
	<table class="center">
	<!-- Part2 - Select  MOD	 -->		
	<?php 
	if(isset($_POST['home_cfg_id']))
	{
	?>
		<tr>
		<td>
		<select name="modcfgid">
		<?php
		$mod_qry = $db->resultQuery("SELECT DISTINCT mod_cfg_id, mod_name, game_name FROM OGP_DB_PREFIXconfig_mods NATURAL JOIN OGP_DB_PREFIXconfig_homes WHERE home_cfg_id=" . $db->realEscapeSingle($_POST['home_cfg_id']));
		foreach($mod_qry as $array_mods) 
		{ 
			if($array_mods['mod_name'] == "none")$array_mods['mod_name']=$array_mods['game_name'];
		?>
			<option value="<?php echo $array_mods['mod_cfg_id'];?>"><?php  echo $array_mods['mod_name'];?></option>
		<?php 
			
		}
		?>
		</select>
		</td>
		<input type="hidden" name="homecfgid" value="<?php echo $_POST['home_cfg_id'];?>"/>
		<tr>
		<?php 
	}
	else if (isset($_POST['modcfgid']) AND isset($_POST['homecfgid']))
	{
		?>
		</tr>
		<tr>
		<?php 
		$result3 = $db->resultQuery("SELECT DISTINCT remote_server_id, remote_server_name, agent_ip, ogp_user FROM OGP_DB_PREFIXremote_servers"); 
		?>
		<td><?php print_lang('remote_server');?></td>
		<td>
		<select name="remote_server_id[]" multiple size="5">
		<?php  
		foreach($result3 as $row3) 
		{
		?>
		<option value="<?php echo $row3['remote_server_id']; ?>">(<?php echo $row3['remote_server_id']; ?>) - IP[<?php echo $row3['agent_ip']; ?>]</option>
		<?php  
		} 
		?>
		</select>
		</td>
		</tr>
		<tr>
		<?php
		$mods = $db->resultQuery("SELECT DISTINCT mod_cfg_id, mod_name, game_name FROM OGP_DB_PREFIXconfig_mods NATURAL JOIN OGP_DB_PREFIXconfig_homes WHERE mod_cfg_id=" . $db->realEscapeSingle($_POST['modcfgid']));
		foreach($mods as $mod) 
		{ 
		?>
			<td><?php print_lang('service_name');?></td>
			<td><input name="service_name" type="text" size="61" value="<?php if($mod['mod_name']=="none")echo $mod['game_name']; else echo $mod['game_name']." - ".$mod['mod_name'];?>"/></td>
			<input name="mod_cfg_id" type="hidden" value="<?php echo $mod['mod_cfg_id'];}?>"/>
			<input name="home_cfg_id" type="hidden" value="<?php echo $_POST['homecfgid'];?>"/>
		</tr>
		<tr>
			<td><?php print_lang('min_slot_qty');?></td>
			<td><input name="slot_min_qty" type="text" size="8" value="16"/></td>
		</tr>
		<tr>
			<td><?php print_lang('max_slot_qty');?></td>
			<td><input name="slot_max_qty" type="text" size="8" value="64"/></td>
		</tr>
		<tr>
			<td>Price Daily</td>
			<td><input name="price_daily" type="text" size="8" value="0"/></td>
		</tr>
		<tr>
			<td><?php print_lang('price_monthly');?></td>
			<td><input name="price_monthly" type="text" size="8" value="0"/></td>
		</tr>
		<tr>
			<td><?php print_lang('price_year');?></td>
			<td><input name="price_year" type="text" size="8" value="0"/></td>
		</tr>
		<tr>
			<td><?php print_lang('ftp_account');?></td>
			<td>
			<select name="ftp">
			<option value="enabled"><?php print_lang('enabled');?></option>
			<option value="disabled"><?php print_lang('disabled');?></option>
			</td>
		</tr>
		<tr>
			<td><?php print_lang('select_install_method');?></td>
			<td>
			<select name="install_method">
			<option value="steam"><?php print_lang('steam');?></option>
			<option value="rsync"><?php print_lang('rsync');?></option>
			<option value="manual"><?php print_lang('manual_from_url');?></option>
			</td>
		</tr>
		<tr>
			<td><?php print_lang('url_for_manual_install');?></td>
			<td><input name="manual_url" type="text" size="61"/></td>
		</tr>
		<tr>
			<td><?php print_lang('description');?></td>
			<td><textarea name='description' cols='45' rows='5'></textarea></td>
		</tr>
		<tr>
			<td><?php print_lang('image_url');?></td>
			<td><textarea name='img_url' cols='45' rows='1'>images/games/unknown.png</textarea></td>
		</tr>
		<tr>
			<td><?php print_lang('access_rights');?></td>
			<td>
			<input name="allow_updates" type="checkbox" value="u" checked="checked"/><?php print_lang('allow_update');?><br>
			<input name="allow_file_management" type="checkbox" value="f" checked="checked"/><?php print_lang('allow_file_management');?><br>
			<input name="allow_parameter_usage" type="checkbox" value="p" checked="checked"/><?php print_lang('allow_parameter_usage');?><br>
			<input name="allow_extra_params" type="checkbox" value="e" checked="checked"/><?php print_lang('allow_extra_parameters_usage');?><br>
			<input name="allow_ftp_usage" type="checkbox" value="t" checked="checked"/><?php print_lang('allow_ftp_usage');?><br>
			<input name="allow_custom_fields" type="checkbox" value="c" checked="checked"/><?php print_lang('allow_custom_fields');?>
			</td>
		</tr>

		<tr>
			<td></td>
		<?php 
	}	
	else
	{
		?>
		<!-- Part 1 - Select GAME  -->
		<tr>
			<td><select name='home_cfg_id'>
			<?php
			global $db;
			$games = $db->getGameCfgs();
			foreach($games as $game) 
			{
				echo "<option value='".$game['home_cfg_id']."'>".$game['game_name'];
				if ( preg_match("/linux/", $game['game_key']) )
				echo " (Linux) ";
				if ( preg_match("/win/", $game['game_key']) )
				echo " (Windows) ";
				if ( preg_match("/64/", $game['game_key']) )
				echo " (64bit) ";
				echo "</option>";
				
			}
			?>
			</select></td>
		</tr>
		<?php 
	}
	?>
		<td><input type="submit" value="<?php print_lang('add_service');?>"/></td>
		</tr>
		</form>

	<!-- Show Services on DB -->
	</table>
	<br>
	<?php  
	$services = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXbilling_services  NATURAL JOIN OGP_DB_PREFIXremote_servers");
	if ($services > 0)
	{
		?>
		<h2><?php print_lang('current_services');?></h2>
		<table class="center" style='text-align:center;'>
		<tr>
			<th><?php print_lang('id');?></th>
			<th><?php print_lang('service_name');?></th>
			<th><?php print_lang('remote_server');?></th>
			<th><?php print_lang('price_monthly');?></th>
			<th><?php print_lang('service_image_url');?></th>
			<th>Enabled</th>
		</tr>
		<?php
		foreach($services as $row)
		{ 
		?>
		<tr class="tr<?php  $i = 0; echo($i++%2);?>">
			<td><b class="success" ><?php echo $row['service_id'];?></b></td>
			<td><?php echo $row['service_name'];?></td>
												
			<form method="post" action="">
			<input name="service" type="hidden" value="<?php echo $row['service_id'];?>"/>																	 
			<td><input name="new_remote_server_id" type="text" value="<?php echo $row['remote_server_id'];?>"/></td>
			<td><input name="new_price_monthly" type="text" value="<?php echo $row['price_monthly'];?>" size="6"/></td>
			<td><input name="new_url" type="text" value="<?php echo $row['img_url'];?>"/></td>
			<td><input name="new_enabled" type="text" value="<?php echo $row['enabled'];?>"/></td>

			<td><input type="submit" value="<?php print_lang('update_settings');?>"/></td>
			</form>
		</tr>
		<?php
if(isset($_POST['new_enabled']))
{
    $Enabled ='1';
}
else
{
    $Enabled ='0';
}
?>
		<?php 
		} 
		?>
		</tr>
		</table>
		<table class="center">
			<tr>
				<tr>
					<td>
						<form action="" method="post">
							<select name="service_id">
						<?php
						foreach($services as $service) 
						{ 
						?>
						<option value="<?php echo $service['service_id'];?>"><?php  echo $service['service_name'];?></option>				
						<?php 
						} 
						?>
						<input type="submit" value="<?php print_lang('remove_service');?>"/>
					</form>
				</td>
			</tr>
		</tr>
	</table>
	<?php
	}
}
?>

