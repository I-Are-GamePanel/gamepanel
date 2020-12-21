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
	$circulars = get_circulars();
	if(isset($_GET['list']))
	{
		echo '<h2>'.get_lang('your_circulars').'</h2>';
		rsort($circulars);
		echo "<table id='circular_list'>\n".
			 "<thead><tr><th>".get_lang('status')."</th><th>".get_lang('subject')."</th><th>".get_lang('date')."</th></tr></thead><tbody>\n";
		foreach($circulars as $key => $circular)
		{
			echo '<tr><td><i class="status_'.$circular['status'].'"></i></td><td><a href="?m=circular&p=show_circular&read_circular='.$circular['circular_id'].'">'.$circular['subject']."</a></td><td>".$circular['timestamp']."</td></tr>\n";
		}
		echo "</tbody></table>\n";
	}
	elseif(isset($_GET['read_circular']) and is_numeric($_GET['read_circular']))
	{
		foreach($circulars as $circular)
			if($circular['circular_id'] == $_GET['read_circular'])
				break;
		echo '<div id="circular_message">'.$circular['message']."</div>\n".
			 '<p><a href="?m=circular&p=show_circular&list">&lt;&lt; '.get_lang('back')."</a></p>\n";
		if($circular['status'] == "0")
			set_circular_readed($circular['circular_id']);
	}
	else
	{
		foreach($circulars as $key => $circular)
			if($circular['status'] == "1")
				unset($circulars[$key]);
		sort($circulars);
		header('Content-Type: application/json');
		echo json_encode($circulars);
	}
}
?>