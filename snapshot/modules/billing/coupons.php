<?php
function exec_ogp_module()
{		
	global $db;

	//Querying UPDATE a service FROM DB
	if (isset($_POST['update_coupon']) )
	{		
		$new_code = $db->realEscapeSingle($_POST['new_code']);								  
		$new_name = $db->realEscapeSingle($_POST['new_name']);
		$new_discount = $db->realEscapeSingle($_POST['new_discount']);
		$new_count = $db->realEscapeSingle($_POST['new_count']);
		$new_expires = $db->realEscapeSingle($_POST['new_expires']);
		$id = $db->realEscapeSingle($_POST['id']);

		//Create INSERT query
		$qry_change_url = "UPDATE OGP_DB_PREFIXbilling_coupons
						   SET code ='".$new_code."', 
							   name = '".$new_name."',
							   discount ='".$new_discount."',
							   count = '".$new_count."',
							   expires = '".$new_expires."'
							   WHERE id=".$id;
		$db->query($qry_change_url);
	}

	//Querying INSERT new coupon INTO DB
	if(isset($_POST['add_coupon']))
	{
		$id = $_POST['id'];
		$code = $_POST['code'];
		$name = $_POST['name'];
		$discount = $_POST['discount'];
		$count= $_POST['count'];
		$expires = $_POST['expires'];


		$query = "INSERT INTO OGP_DB_PREFIXbilling_coupons(code, name, discount, count, expires) VALUES('".$code."', '".$name."', '".$discount."', '".$count."', '".$expires."')";
		$db->query($query);	
	}
	
	//Querying REMOVE coupon FROM DB
	if (isset($_POST['del_coupon']))
	{
		$db->query( "DELETE FROM OGP_DB_PREFIXbilling_coupons WHERE id=" . $db->realEscapeSingle($_POST['id']) );
	}
	?>
	

	<!-- Show Coupons on DB -->
	</table>
	<br>
	<?php  
	$result = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXbilling_coupons");
	if ($result > 0)
	{
		?>
		<h2><?php print_lang('current_coupons');?></h2>
		<table class="center" style='text-align:center;'>
		<tr>
			
			<th><?php print_lang('code');?></th>
			<th><?php print_lang('coupon_name');?></th>
			<th><?php print_lang('discount');?></th>
			<th><?php print_lang('count');?></th>
			<th><?php print_lang('expires');?></th>
		</tr>
		
		<?php
		foreach($result as $row)
		{ 
		?>
		<tr class="tr<?php  $i = 0; echo($i++%2);?>">
			<form method="post" action="">
			<input name="id" type="hidden" value="<?php echo $row['id'];?>"/></td>
			<td><input name="new_code" type="text" value="<?php echo $row['code'];?>"/></td>
			<td><input name="new_name" type="text" value="<?php echo $row['name'];?>" /></td>
			<td><input name="new_discount" type="text" value="<?php echo $row['discount'];?>"/></td>
			<td><input name="new_count"type="text" value="<?php echo $row['count'];?>"/></td>
            <td><input name="new_expires" type="text" value="<?php echo $row['expires'];?>"/></td>
			<td><input type="submit" name="update_coupon" value="<?php print_lang('update_settings');?>"/></td>
			<td><input type="submit" name="del_coupon" value="<?php print_lang('del_coupon');?>"/></td>
			
			</form>
		</tr><?php
			}
			//add new row to insert
			?>
			<form method="post" action="">
			<td><input name="code" type="text" value=""/></td>
			<td><input name="name" type="text" value="" /></td>
			<td><input name="discount" type="text" value="0"/></td>
			<td><input name="count"type="text" value="0"/></td>
            <td><input name="expires" type="datetime-local" data-date-format="YYYY MMMM DD"  value=""/></td>			
			<td><input type="submit" name="add_coupon" value="<?php print_lang('add_coupon');?>"/></td>
			</form></table>
			<?php
	}
}
?>



