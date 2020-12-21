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
include 'modules/circular/functions.php';
function exec_ogp_module()
{
	global $db;
	if(isset($_POST['send_circular']))
	{
		send_circular($_POST);
		return;
	}
	
	if(isset($_GET['get_circulars']))
	{
		$circulars = get_circulars(true);
		header('Content-Type: application/json');
		echo json_encode($circulars);
		return;
	}
	
	if(isset($_POST['remove_circulars']))
	{
		if(isset($_POST['circulars_ids']) and is_array($_POST['circulars_ids']) and !empty($_POST['circulars_ids']))
		{
			foreach($_POST['circulars_ids'] as $circular_id)
			{
				remove_circular($circular_id, true);
			}
		}
		return;
	}
	
	echo '<link rel="stylesheet" href="css/quill/quill.snow.css">'."\n".
		 '<script type="text/javascript" src="js/quill/quill.js"></script>'."\n".
		 '<script type="text/javascript" src="js/modules/circular.js"></script>';
	
	if(isset($_GET['list']))
	{
		$circulars = get_circulars(true);
		if($circulars)
		{
			rsort($circulars);
			echo "<table id='circular_admin_list'><thead><tr>\n".
				 "<th><input type=\"checkbox\" onclick=\"swap_all_checkboxes(this)\"></th>\n".
				 "<th>".get_lang('subject')."</th>\n".
				 "<th>".get_lang('users_not_read_circular')."</th>".
				 "<th>".get_lang('date')."</th></thead>\n";
			
			foreach($circulars as $key => $circular)
			{
				$users_not_readed = get_usernames_not_read_circular($circular['circular_id']);
				$users_not_readed = $users_not_readed ? $users_not_readed: "";
				echo '<tr><td><input type="checkbox" class="circular_checkbox" name="remove_circular[]" value="'.$circular['circular_id'].'"></td>'."\n".
					 '<td><b>'.$circular['subject']."</b></td><td>$users_not_readed</td><td>".$circular['timestamp']."</td></tr>\n";
			}
			echo '<tr><td colspan=4><button onclick="remove_circulars()">'.get_lang('remove_selected_circulars')."</button></td></tr>\n".
				 "</table>\n";
		}
		else
		{
			print_failure(get_lang('there_are_no_circulars'));
		}
		echo '<p><a href="?m=circular">&lt;&lt; '.get_lang('back')."</a></p>\n";
		return;
	}
	
	$subusers_installed = $db->isModuleInstalled('subusers');
	echo "<h2>".get_lang('Circular')."</h2>";
		
	//[]admins []users []subusers[of user] []groups[group]
	echo '<table id="circular"><tr>'."\n".
		 '<td rowspan=2>'.get_lang('send_to').':</td>'."\n".
		 '<td><input type="checkbox" onclick="toggle_all(this, \'select_admins\')" id="admins"><label for="admins">'.get_lang('admins').'</label></td>'."\n".
		 '<td><input type="checkbox" onclick="toggle_all(this, \'select_users\')" id="users" ><label for="users" >'.get_lang('users') .'</label></td>'."\n".
		 '<td><input type="checkbox" onclick="toggle_all(this, \'select_groups\')" id="groups"><label for="groups">'.get_lang('groups').'</label></td>'."\n";
	
	if($subusers_installed){
		echo '<td><input type="checkbox" onclick="toggle_all(this, \'select_subusers_of_users\')" id="subusers_of_user"><label for="subusers_of_user">'.get_lang('subusers_of_user').'</label></td>'."\n";
	}
	
	$users = $db->getUserList();
	echo '</tr><tr>'."\n".
		 '<td><select id="select_admins" multiple>'."\n";
	if(!empty($users))
	{
		foreach($users as $user)
		{
			if($user['users_role'] == 'admin')
				echo '<option value="'.$user['user_id'].'">'.$user['users_login'].'</option>'."\n";
		}
	}
	echo '</select></td>'."\n".
		 '<td><select id="select_users" multiple>'."\n";
	if(!empty($users))
	{
		foreach($users as $user)
		{
			if($user['users_role'] == 'user')
				echo '<option value="'.$user['user_id'].'">'.$user['users_login'].'</option>'."\n";
		}
	}
	echo '</select></td>'."\n".
		 '<td><select id="select_groups" multiple>'."\n";
	$groups = $db->getGroupList();
	if(!empty($groups))
	{
		foreach($groups as $group)
		{
			if($db->listUsersInGroup($group['group_id']))
				echo '<option value="'.$group['group_id'].'">'.$group['group_name'].'</option>';
		}
	}
	echo '</select></td>'."\n";
	if($subusers_installed){
		echo '<td><select id="select_subusers_of_users" multiple>'."\n";
		if(!empty($users))
		{
			foreach($users as $user)
			{
				$sub_users_ids = $db->getUsersSubUsersIds($user['user_id']);
				if($user['users_role'] == 'user' and $sub_users_ids)	
					echo '<option value="'.$user['user_id'].'">'.$user['users_login'].'</option>'."\n";
			}
		}
		echo '</select></td>'."\n";
	}
	$colspan = $subusers_installed ? '5':'4';
	echo '</tr>'."\n".
		 '<tr><td>'.get_lang('subject').'</td><td colspan=4><input type="text" id="subject"></td></tr>'."\n".
		 '<tr><td colspan='.$colspan.'>'.get_lang('message').'</td></td></tr>'."\n".
		 '<tr><td colspan='.$colspan.'><div id="editor"></div></td></tr>'."\n".
		 '<tr><td colspan='.$colspan.'><button onclick="send_circular()" >'.get_lang('send_circular').'</button></td></tr>'."\n".
		 '</table>';
}
?>