<style>
form table.center tr td{
	width:50%;
	width:50%;
}

table.center{
	color:#333333;
	clear:both;
	width:100%;
	height:35px;
	margin-top:-6px;
	padding-top:10px;
	text-align: center;
	color:#FFFF;
}

table.center tr td{
	border:1px solid #cfcfcf;
	background:#e5e5e5;
}

table.center th{
	border:1px solid #cfcfcf;
	background:#c5c5c5;
}

table.center tr.first_row td{
	border:1px solid #cfcfcf;
	background:#c5c5c5;
}
</style>
<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) Copyright (C) 2008 - 2013 The OGP Development Team
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

//Function to sanitize values received from the form. Prevents SQL injection
function clean($str){
	global $db;
	$str = @trim($str);
	if(get_magic_quotes_gpc()) 
	{
		$str = stripslashes($str);
	}
	return $db->real_escape_string($str);
}

function exec_ogp_module()
{
	global $db;
	//Querying UPDATE a service FROM DB
	if (isset($_POST['service']) AND isset($_POST['edit_service']))
	{
		//Sanitize the POST values
		$new_price_per_month = clean($_POST['new_price_per_month']);
		$new_price_per_year = clean($_POST['new_price_per_year']);
		$remote_server_id = clean($_POST['remote_server_id']);
		$start_port = clean($_POST['start_port']);
		$end_port = clean($_POST['end_port']);
		$service = clean($_POST['service']);

		$change_service_settings = "UPDATE OGP_DB_PREFIXreseller_services SET
									price_per_month='".$new_price_per_month."', 
									price_per_year='".$new_price_per_year."',
									remote_server_id='".$remote_server_id."',
									start_port='".$start_port."',
									end_port='".$end_port."'
									WHERE service_id=".$service;
		$db->query($change_service_settings);
	}

	//Querying INSERT new service INTO DB
	if(isset($_POST['slot_max_qty']) AND isset($_POST['price_per_month']) AND isset($_POST['price_per_year']))
	{
		//Sanitize the POST values
		$service_name = clean($_POST['service_name']);
		$slot_max_qty = clean($_POST['slot_max_qty']);
		$price_per_month = clean($_POST['price_per_month']);
		$price_per_year = clean($_POST['price_per_year']);
		$description = clean($_POST['description']);
		$remote_server_id = clean($_POST['remote_server_id']);
		$start_port = clean($_POST['start_port']);
		$end_port = clean($_POST['end_port']);
		$max_access_rights = "";
		if(isset($_POST['allow_updates']))$max_access_rights .= clean($_POST['allow_updates']);
		if(isset($_POST['allow_file_management']))$max_access_rights .= clean($_POST['allow_file_management']);
		if(isset($_POST['allow_parameter_usage']))$max_access_rights .= clean($_POST['allow_parameter_usage']);
		if(isset($_POST['allow_extra_params']))$max_access_rights .= clean($_POST['allow_extra_params']);
		if(isset($_POST['allow_ftp_usage']))$max_access_rights .= clean($_POST['allow_ftp_usage']);
		
		$qry_add_service = "INSERT INTO OGP_DB_PREFIXreseller_services(service_id, 
																	   service_name, 
																	   slot_max_qty , 
																	   price_per_month, 
																	   price_per_year, 
																	   description, 
																	   remote_server_id,
																	   start_port,
																	   end_port,
																	   max_access_rights) VALUES(NULL, '".$service_name.
																								   "', '".$slot_max_qty.
																								   "', '".$price_per_month.
																								   "', '".$price_per_year.
																								   "', '".$description.
																								   "', '".$remote_server_id.
																								   "', '".$start_port.
																								   "', '".$end_port.
																								   "', '".$max_access_rights."')";
		$db->query($qry_add_service);	
	}
	
	//Querying DELETE service FROM DB
	if (isset($_POST['remove_service']) AND isset($_POST['service_id']))
	{
		$db->query( "DELETE FROM OGP_DB_PREFIXreseller_services WHERE service_id=" . $_POST['service_id'] );
		$db->query( "DELETE FROM OGP_DB_PREFIXreseller_discount_codes WHERE service_id=" . $_POST['service_id'] );
	}
	
	if( isset( $_POST['add_discount_code'] ) )
	{
		//Sanitize the POST values
		$service_id = clean($_POST['service_id']);
		echo $service_id;
		$percentage = clean($_POST['percentage']);
		$description = clean($_POST['description']);
		$code = clean($_POST['code']);
		$add_code = "INSERT INTO OGP_DB_PREFIXreseller_discount_codes(discount_id, service_id, percentage, description, code) VALUES(NULL, '".$service_id."', '".$percentage."', '".$description."', '".$code."')";
		$db->query($add_code);	
	}
	
	if (isset($_POST['remove_code']) AND isset($_POST['discount_id']))
	{
		$db->query( "DELETE FROM OGP_DB_PREFIXreseller_discount_codes WHERE discount_id=" . $_POST['discount_id'] );
	}
	
	global $settings;
	?>
	<h2><?php print_lang('add_service');?></h2>
	<form method="POST" action="">
	<table class="center">
	<tr>
			<td align=right><?php print_lang('service_name');?></td>
			<td align=left><input name="service_name" type="text" size="60" value="100 Slot Pack"/></td>
		</tr>
		<tr>
			<td align=right><?php print_lang('max_slot_qty');?></td>
			<td align=left><input name="slot_max_qty" type="text" size="8" value="100"/><?php print_lang('slots');?></td>
		</tr>
		<tr>
			<td align=right><?php print_lang('price_per_month');?></td>
			<td align=left><input name="price_per_month" type="text" size="8" value="0"/><?php if(isset($settings['currency']))echo $settings['currency'];?></td>
		</tr>
		<tr>
			<td align=right><?php print_lang('price_per_year');?></td>
			<td align=left><input name="price_per_year" type="text" size="8" value="0"/><?php if(isset($settings['currency']))echo $settings['currency'];?></td>
		</tr>
		<tr>
			<td align=right><?php print_lang('description');?></td>
			<td align=left><textarea name='description' cols='45' rows='5'></textarea></td>
		</tr>
		<tr>
			<td align=right><?php print_lang('remote_server');?></td>
			<td align=left>
			<select name="remote_server_id">
			<?php
			$remote_servers = $db->getRemoteServers();
			foreach ( $remote_servers as $server )
			{
				echo "<option value='".$server['remote_server_id']."'>".
					$server['remote_server_name']." (".$server['agent_ip'].")</option>\n";
			}
			?>
			</select>
			</td>
		</tr>
		<tr>
			<td align=right><?php print_lang('ports_range');?></td>
			<td align=left>
			  <input name="start_port" type="text" size="10" value="27000"/> - 
			  <input name="end_port" type="text" size="10" value="27300"/>
			</td>
		</tr>
		<tr>
			<td align=right><?php print_lang('max_access_rights');?></td>
			<td align=left>
			<input name="allow_updates" type="checkbox" value="u" checked="checked"/><?php print_lang('allow_update');?><br>
			<input name="allow_file_management" type="checkbox" value="f" checked="checked"/><?php print_lang('allow_file_management');?><br>
			<input name="allow_parameter_usage" type="checkbox" value="p" checked="checked"/><?php print_lang('allow_parameter_usage');?><br>
			<input name="allow_extra_params" type="checkbox" value="e" checked="checked"/><?php print_lang('allow_extra_parameters_usage');?><br>
			<input name="allow_ftp_usage" type="checkbox" value="t" checked="checked"/><?php print_lang('allow_ftp_usage');?>
			</td>
		</tr>
		<tr>
		<td colspan=2><input type="submit" value="<?php print_lang('add_service');?>"/></td>
		</tr>
		</form>

	<!-- Show Services on DB -->
	</table>
	<br>
	<?php  
	$services = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_services");
	if ($services > 0)
	{
		?>
		<h2><?php print_lang('reseller_services');?></h2>
		<table class="center" style='text-align:center;'>
		<tr>
			<th><?php print_lang('id');?></th>
			<th><?php print_lang('service_name');?></th>
			<th><?php print_lang('remote_server');?></th>
			<th><?php print_lang('ports_range');?></th>
			<th><?php print_lang('price_per_month');?></th>
			<th><?php print_lang('price_per_year');?></th>
		</tr>
		<?php
		foreach($services as $row)
		{ 
		?>
		<tr class="tr<?php  $i = 0; echo($i++%2);?>">
			<td style="width:10px;"><b class="failure" ><?php echo $row['service_id'];?></b></td>
			<td><?php echo $row['service_name'];?></td>
			<form method="post" action="">
			<td align=left>
			<select name="remote_server_id">
			<?php
			$remote_servers = $db->getRemoteServers();
			foreach ( $remote_servers as $server )
			{
				$selected = $server['remote_server_id'] == $row['remote_server_id'] ? "selected='selected'":"";
				echo "<option value='".$server['remote_server_id']."' $selected >".
					$server['remote_server_name']." (".$server['agent_ip'].")</option>\n";
			}
			?>
			</select>
			</td>
			<input name="service" type="hidden" value="<?php echo $row['service_id'];?>"/>
			<input name="edit_service" type="hidden" />
			<td style="width:15%;" ><input name="start_port" type="text" value="<?php echo $row['start_port'];?>" size="6"/><input name="end_port" type="text" value="<?php echo $row['end_port'];?>" size="6"/></td>
			<td style="width:15%;" ><input name="new_price_per_month" type="text" value="<?php echo $row['price_per_month'];?>" size="6"/><?php if(isset($settings['currency']))echo $settings['currency'];?></td>
			<td style="width:15%;" ><input name="new_price_per_year" type="text" value="<?php echo $row['price_per_year'];?>" size="6"/><?php if(isset($settings['currency']))echo $settings['currency'];?></td>
			<td><input type="submit" value="<?php print_lang('edit');?>"/></td>
			</form>
		</tr>
		<?php 
		} 
		?>
		</tr>
		</table>
		<br>
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
							</select>
							<input type="submit" name="remove_service" value="<?php print_lang('remove_service');?>"/>
						</form>
					</td>
				</tr>
			</tr>
		</table>
		<br>
		<h2><?php print_lang('add_discount');?></h2>
		<form method="POST" action="">
		<table class="center">
		<tr>
			<td align=right><?php print_lang('discount_name');?></td>
			<td align=left><input name="description" type="text" size="60" value="10% off at all orders"/></td>
		</tr>
		<tr>
			<td align=right><?php print_lang('applies_to');?></td>
			<td align=left>
			<select name="service_id">
			<option value="0"><?php print_lang('all_services');?></option>
			<?php
			foreach($services as $service) 
			{ 
			?>
			<option value="<?php echo $service['service_id'];?>"><?php  echo $service['service_name'];?></option>				
			<?php 
			} 
			?>
			</select>
			</td>
		</tr>
		<tr>
			<td align=right><?php print_lang('percentage');?></td>
			<td align=left><input name="percentage" type="text" size="8" value="10"/>%</td>
		</tr>
		<tr>
			<td align=right><?php print_lang('code');?></td>
			<td align=left><input type=text name='code' size='50' value="<?php echo genRandomString('8'); ?>" /></td>
		</tr>
		<tr>
		<td colspan=2><input type="submit" name="add_discount_code" value="<?php print_lang('add_discount_code');?>"/></td>
		</tr>
		</table>
		</form>
		<br>
		<?php
		$discount_codes = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXreseller_discount_codes");
		if ($discount_codes > 0)
		{
			?>
		<h2><?php print_lang('reseller_discount_codes');?></h2>
		<table class="center" style='text-align:center;'>
		<tr>
			<th><?php print_lang('description');?></th>
			<th><?php print_lang('percentage');?></th>
			<th><?php print_lang('code');?></th>
			<th><?php print_lang('applies_to');?></th>
		</tr>
			<?php
			foreach($discount_codes as $row)
			{ 
				if( $row['service_id'] != "0" )
				{
					$service = $db->resultQuery("SELECT service_name FROM OGP_DB_PREFIXreseller_services WHERE service_id=".$row['service_id']);
					$service_name = $service[0]['service_name'];
				}
				else
				{
					$service_name = get_lang('all_services');
				}
					
				?>
			
		<tr class="tr<?php  $i = 0; echo($i++%2);?>">
			<td style="width:50%;"><b class="failure" ><?php echo $row['description'];?></b></td>
			<td style="width:10%;"><?php echo $row['percentage'];?>%</td>
			<td style="width:10%;"><?php echo $row['code'];?></td>
			<td><?php echo $service_name;?></td>
		</tr>
				<?php 
			} 
			?>
		</tr>
		</table>
		<br>
		<table class="center">
			<tr>
				<tr>
					<td>
						<form action="" method="post">
							<select name="discount_id">
			<?php
			foreach($discount_codes as $code) 
			{ 
				?>
							<option value="<?php echo $code['discount_id'];?>"><?php  echo $code['description'];?></option>				
				<?php 
			} 
			?>
							<input type="submit" name="remove_code" value="<?php print_lang('remove_code');?>"/>
						</form>
					</td>
				</tr>
			</tr>
		</table>
		<br>
		<?php
		}
	}
}
?>