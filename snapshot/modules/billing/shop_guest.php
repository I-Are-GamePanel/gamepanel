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

function exec_ogp_module()
{
	require('includes/config.inc.php');	
	global $db;
	$settings = $db->getSettings();
	?>


<div align="center"><h1><em>Available Servers</em></h1>
<br>
</font>

<br>
Join our <a href="https://discord.gg/tmY6HEJ" target="_blank">Discord Server</a><br>
Read the <a href="http://web.iaregamer.com/index.php/wiki" target="_blank">Wiki</a> for more Information<br><br>
Return to  <a href='index.php'>Login Page</a>
<br>
</div>
	<?php  
	   if(intval($_REQUEST['service_id']) !==0) $where_service_id = " WHERE enabled = 1 and service_id=".intval($_REQUEST['service_id']); else $where_service_id = " where enabled = 1";																																																											 
	$qry_services = "SELECT * FROM OGP_DB_PREFIXbilling_services".$where_service_id;
	$services = $db->resultQuery($qry_services);
	foreach ($services as $key => $row) {
		$service_id[$key] = $row['service_id'];
		$home_cfg_id[$key] = $row['home_cfg_id'];
		$mod_cfg_id[$key] = $row['mod_cfg_id'];
		$service_name[$key] = $row['service_name'];
		$remote_server_id[$key] = $row['remote_server_id'];
		$slot_max_qty[$key] = $row['slot_max_qty'];
		$slot_min_qty[$key] = $row['slot_min_qty'];
		$price_daily[$key] = $row['price_daily'];
		$price_monthly[$key] = $row['price_monthly'];
		$price_year[$key] = $row['price_year'];
		$description[$key] = $row['description'];
		$img_url[$key] = $row['img_url'];
		$ftp[$key] = $row['ftp'];
		$install_method[$key] = $row['install_method'];
		$manual_url[$key] = $row['manual_url'];
		$access_rights[$key] = $row['access_rights'];
	}
	//Sort by service name, the 1st position in this array multisort, service_name, defines the row that sorts the array, if there are two equal service names then the next row, service_id, will sort these rows.
	array_multisort($service_name,
					$service_id,
					$home_cfg_id,
					$mod_cfg_id,
					$remote_server_id,
					$slot_max_qty,
					$slot_min_qty,
					$price_daily,
					$price_monthly,
					$price_year,
					$description,
					$img_url,
					$ftp,
					$install_method,
					$manual_url,
					$access_rights, SORT_DESC, $services);
	
echo "<table>";
	foreach($services as $row)
	{ 
	?>
<tr>
<tr>
<td valign="middle">
	
<img src="<?php echo $row['img_url'] ;?>" width=192 height=64 border=0 alt="cheap <?php echo $row['service_name'];?> Game Server">
</td>
<td valign="middle">
</td>
<td width="20%"><font size="+1"><?php echo $row['service_name'];?>
</font>
<br>
</font></strong>
</td>
<td align="right" width="20%">
<strong style="text-align:center;color:yellow;"><?php echo "$" .  number_format(floatval($row['price_monthly']),2). " per slot Monthly";?></strong>
</td>
<td width="20%">
<?php echo "($" .  number_format(floatval($row['price_monthly'] * $row['slot_min_qty']),2 )  . "</b>&nbsp;" . $row['slot_min_qty'] . " " . get_lang('slots') . ")";?>

</td>
</tr>
<?php
}
?>
</table>
<?php
}
?>


