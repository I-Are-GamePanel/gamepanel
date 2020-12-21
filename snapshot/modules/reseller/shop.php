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
	global $db, $settings;
	
	if(!isset($settings['price_per_month']) and !isset($settings['price_per_year']))
	{
		print_failure("Configure the reseller settings");
		return;
	}
	
	if(isset($settings['price_per_month']) and $settings['price_per_month'] == 0 and isset($settings['price_per_year']) and $settings['price_per_year'] == 0)
	{
		print_failure("Atleast one invoice type must be checked in the reseller settings.");
		return;
	}
		
	if (isset($_POST['save']) AND !empty($_POST['description']))
	{
		$new_description = clean($_POST['description']);
		$service = clean($_POST['service_id']);
		
		$change_description = "UPDATE OGP_DB_PREFIXreseller_services
						       SET description ='".$new_description."'
						       WHERE service_id=".$service;
		$save = $db->query($change_description);
	}
	?>
	<table class="center">
	<tr>
	<td>
	<a href="?m=reseller&p=cart"><img SRC="images/cart.png" BORDER="0" WIDTH=22 HEIGHT=20/><?php print_lang('your_cart');?></a>
	</td>
	<td>
	<a href="?m=reseller&p=rs_assign_server"><b>[+]</b><?php print_lang('rs_assign_servers');?></a>
	</td>
	</tr>
	<tr>
	<td colspan=2>
	<?php 
	echo date('d-m-Y');
	?>
	</td>
	</tr>
	<tr>
	<td colspan=2>
	<?php 
	echo date('H:i');
	?>
	</td>
	</tr>
	</table>
	<?php 
	// Shop Form
	$isAdmin = $db->isAdmin($_SESSION['user_id'] );
	if(isset($_REQUEST['service_id'])) $where_service_id = " WHERE service_id=".$_REQUEST['service_id']; else $where_service_id = "";
	$qry_services = "SELECT * FROM OGP_DB_PREFIXreseller_services".$where_service_id;
	$services = $db->resultQuery($qry_services);
	if(empty($services))
	{
		if($isAdmin)
		{
		?>		
		<a href="?m=reseller&p=rs_services"><?php print_lang('add_some_services'); ?></a>
		<?php
		}
		return;
	}
	foreach ($services as $key => $row) {
		$service_id[$key] = $row['service_id'];
		$slot_max_qty[$key] = $row['slot_max_qty'];
		$price_per_month[$key] = $row['price_per_month'];
		$price_per_year[$key] = $row['price_per_year'];
		$description[$key] = $row['description'];
		$max_access_rights[$key] = $row['max_access_rights'];
	}
	array_multisort($service_id,
					$slot_max_qty,
					$price_per_month,
					$price_per_year,
					$description,
					$max_access_rights, SORT_DESC, $services);
	?>
	<div style="border-left:10px solid transparent;">
	<?php		
	foreach( $services as $row )
	{
		if(!isset($_REQUEST['service_id']))
		{
			?>
			<div style="float:left; border: 4px solid transparent;border-bottom: 25px solid transparent;">
			<form action="" method="POST">
				<input name="service_id" type="hidden" value="<?php echo $row['service_id'];?>" />
				<input type="image" src="modules/reseller/pack_image.png" width=280 height=132 border=0 alt="Bad Image" onsubmit="submit-form();" value="More Info" />
				<center><b><?php echo $row['service_name'];?></b></center>
				<?php
				if( isset( $settings['price_per_month'] ) and $settings['price_per_month'] == 1 )
				{
				?>
				<center><em style="text-align:center;background-color:orange;color:blue;"><?php echo "<b>" .
				floatval(round(($row['price_per_month']),2 )) . "</b>&nbsp;" . $settings['currency'] . "/" . get_lang('month') ;?></em></center>
				<?php
				}
				if( isset( $settings['price_per_year'] ) and $settings['price_per_year'] == 1 )
				{
				?>
				<center><em style="text-align:center;background-color:orange;color:blue;"><?php echo "<b>" .
				floatval(round(($row['price_per_year']),2 )) . "</b>&nbsp;" . $settings['currency'] . "/" . get_lang('year') ;?></em></center>
				<?php
				}
				?>
			</form>
			</div>
			<?php 
		}		else
		{	
			?>
			<div style="float:left; border: 4px solid transparent;border-bottom: 25px solid transparent;">
			<img src="modules/reseller/pack_image.png" width=280 height=132 border=0 alt="Bad Image">
			<center><b><?php echo $row['service_name']."</b></center>";

			if($isAdmin)
			{
				if(!isset($_POST['edit']))
				{
					echo "<p style='color:gray;width:280px;' >$row[description]<p>";
					echo "<form action='' method='post'>".
						 "<input type='hidden' name='service_id' value='$row[service_id]' />".
						 "<input type='submit' name='edit' value='" . get_lang('edit') . "' />".
						 "</form>";
				}
				else
				{
					echo "<form action='' method='post'>".
						 "<textarea style='resize:none;width:280px;height:132px;' name='description' >$row[description]</textarea><br>".
						 "<input type='hidden' name='service_id' value='$row[service_id]' />".
						 "<input type='submit' name='save' value='" . get_lang('save') . "' />".
						 "</form>";
				}
			}
			else
				echo "<p style='color:gray;width:280px;' >$row[description]<p>";
			?>
			</div>
			<table style="width:420px;float:left;">
			<form method="post" action="?m=reseller&p=add_to_cart<?php if(isset($_POST['service_id'])) echo "&service_id=".$_POST['service_id'];?>">
			<tr>
			<td align="right"><?php print_lang('service_name');?> ::</td>
			<td align="left">
			<?php echo $row['service_name'];?>
			</td>
			<tr>
			<td align="right"><?php print_lang('discount_code');?> ::</td>
			<td align="left">
			<input type="text" name="code" size="15" value="">
			</td>
			</tr>
			<tr> 
			  <td align="right"><?php print_lang('invoice_duration');?> ::</td>
			  <td align="left">
			  <select name="qty">
			  <?php 
			  $qty=1;
			  while($qty<=12)
			  {
			  echo "<option value='$qty'>$qty</option>";
			  $qty++;
			  }
			  ?>
			  </select>
			  <select name="invoice_duration">
			  <?php
			  if( $settings['price_per_month'] == 1) echo '<option value="month">'.get_lang('months').'</option>';
			  if( $settings['price_per_year'] == 1) echo '<option value="year">'.get_lang('years').'</option>';
			  ?>
			  </select>
			  </td>
			</tr>
			<tr>
			  <td align="left" colspan="2">
			  	<input name="service_id" type="hidden" value="<?php echo $row['service_id'];?>"/>
				<input type="submit" name="add_to_cart" value="<?php print_lang('add_to_cart');?>"/>
			  </form>
			  </td>
			</tr>
			<tr>
			<td align="left" colspan="2">
			<form action ="?m=reseller&p=rs_packs_shop" method="POST">
			  <button><< <?php print_lang('back_to_list');?></button>
			</form>
			</td>
			</tr>
			</table>
			<?php
		}
	}
	?>
	</div>
	<?php  
}
?>