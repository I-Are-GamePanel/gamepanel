<script type="text/javascript">
$(document).ready(function(){ 
	$( "#db_id" ).change(function() {
		this.form.submit();
	});
}); 
</script>
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

require_once("modules/mysql/functions.php");
require_once('includes/form_table_class.php');
require_once('includes/lib_remote.php');
if ( function_exists('mysqli_connect') )
	require_once("modules/mysql/mysqli_database.php");
else
	require_once("modules/mysql/mysql_database.php");

function exec_ogp_module() {

	$modDb = new MySQLModuleDatabase();
	require("includes/config.inc.php");
	$modDb->connect($db_host,$db_user,$db_pass,$db_name,$table_prefix);
	
	global $view,$db;
	
	$isAdmin = $db->isAdmin( $_SESSION['user_id'] );

	if( $isAdmin )
		$game_home = $db->getGameHome($_GET['home_id']);
	else
		$game_home = $db->getUserGameHome($_SESSION['user_id'],$_GET['home_id']);
	if ( ! $game_home and ! $isAdmin )
		return;
	
	echo "<h2>".get_lang_f('mysql_dbs_for',htmlentities($game_home['home_name']))."</h2>";
	
	$home_dbs = $modDb->getMysqlDBsbyHomeId($game_home['home_id']);
	
	if(empty($home_dbs))
	{
		print_failure(get_lang_f('there_are_no_databases_assigned_for',htmlentities($game_home['home_name'])));
		return;
	}

	$db_array["0"] = get_lang('select_db');
	foreach ( $home_dbs as $home_db )
	{
		$db_array["$home_db[db_id]"] = $home_db['db_name'];
	}
		
	$ft = new FormTable();
	$ft->start_form('');
	$ft->start_table();
	$ft->add_custom_field('select_db',
		create_drop_box_from_array($db_array,"db_id",isset($_REQUEST['db_id'])?$_REQUEST['db_id']:"0",false));
	$ft->end_table();
	$ft->end_form();
	
	$database_exists = FALSE;
	$server_online = FALSE;
	
	//if(isset($_REQUEST['db_id']) AND $_REQUEST['db_id'] != "0")
	if((isset($_REQUEST['db_id']) AND $_REQUEST['db_id'] != "0") OR isset($_POST['save_db_changes']) AND $_POST['db_id'] != "0")
	{
		if(isset($_REQUEST['db_id']))
			$db_id = $_REQUEST['db_id'];
		
		if (isset($_POST['save_db_changes']))
			$db_id = $_POST['db_id'];
		
		$mysql_db = $modDb->getMysqlHomeDBbyId($game_home['home_id'],$db_id);
		
		if(!$mysql_db)
			return;
			
		if($mysql_db['remote_server_id'] != "0")
		{
			$remote_server = $db->getRemoteServer($mysql_db['remote_server_id']);
			$remote = new OGPRemoteLibrary($remote_server['agent_ip'],$remote_server['agent_port'],$remote_server['encryption_key'],$remote_server['timeout']);
			$host_stat = $remote->status_chk();
			if($host_stat === 1 )
			{
				$command = "mysql -h localhost -P ".$mysql_db['mysql_port']." -u root -p".$mysql_db['mysql_root_passwd'].' -e exit; echo $?';
				$test_mysql_conn = $remote->exec($command);

				if($test_mysql_conn == 0)
				{
					$user_db = $remote->exec('mysqlshow --user='.$mysql_db['db_user'].' --password='.$mysql_db['db_passwd'].' '.$mysql_db['db_name']);

					if($user_db != "")
						$database_exists = TRUE;

					$server_online = TRUE;
				}
			}
		}
		else
		{
			if( function_exists('mysqli_connect') )
			{
				$link = mysqli_connect($mysql_db['mysql_ip'], $mysql_db['db_user'], $mysql_db['db_passwd'], $mysql_db['db_name'], $mysql_db['mysql_port']);
				if ( $link !== FALSE )
				{
					$server_online = TRUE;
					$database_exists = TRUE;
					$databases = mysqli_query($link, "SHOW TABLES;");
					$user_db = "Database: ".$mysql_db['db_name']."\nTables:\n";
					while ( $table = mysqli_fetch_array($databases) ) {
						$user_db .= $table[0] . "\n";
					}
					mysqli_close($link);
				}
			}
			else
			{
				@$link = mysql_connect($mysql_db['mysql_ip'].':'.$mysql_db['mysql_port'], $mysql_db['db_user'], $mysql_db['db_passwd']);
				
				if ( $link !== FALSE )
				{
					$server_online = TRUE;
					if ( mysql_select_db($mysql_db['db_name'],$link) !== FALSE )
					{		
						$databases = mysql_query("SHOW TABLES;");
						$user_db = "Database: ".$mysql_db['db_name']."\nTables:\n";
						while ( $table = mysql_fetch_array($databases) ) {
							$user_db .= $table[0] . "\n";
						}
						$database_exists = TRUE;
					}
					mysql_close($link);
				}
				
			}
		}
		
		if(isset($_POST['restore']))
		{
			$command = 'mysql --host='.$mysql_db['mysql_ip'].' --port='.$mysql_db['mysql_port'].' --user='.$mysql_db['db_user'].
					   ' --password='.$mysql_db['db_passwd'].' '.$mysql_db['db_name'].' < ';
			
			$local_tmp = sys_get_temp_dir()."/".$_FILES["file"]["name"];
			move_uploaded_file($_FILES["file"]["tmp_name"], $local_tmp);
			if($mysql_db['remote_server_id'] != "0")
			{
				$temp_dir = trim($remote->exec('mktemp -d'));
				$writefile = $temp_dir."/".$_FILES["file"]["name"];
				$content = file_get_contents($local_tmp);
				$command .= $writefile;
				if($remote->remote_writefile($writefile, $content) === 1)
					$remote->exec($command);
			}
			else
			{
				$command .= $local_tmp;
				system($command);
			}
			unlink($local_tmp);
			$view->refresh('?m=mysql&p=user_db&home_id='.$game_home['home_id'].'&db_id='.$db_id,0);
		}
		//voltar para o game monitor
		echo "<div align='center'><form action='' method='get'>
		  <input type='hidden' name='m' value='gamemanager' />
		  <input type='hidden' name='p' value='game_monitor' />
		  <input type='hidden' name='home_id' value='".$_GET['home_id']."' />
		  <input type='submit' value='<<&lt;Back to Game Monitor' />
		  </form></div>";
		if($server_online and $database_exists)
		{
			?>
			<form action="" method="post" class="form-group">
			<input type="hidden" name="db_id" value="<?php echo $db_id; ?>">
			<?php
			echo "<table class='database' ><tr><td>\n<div class='dragbox bloc rounded' ><h4>".get_lang('db_info')."</h4>\n".
				 "<table class='database_info' ><tr>".
				 "<td><b>Sign in to :<a href='http://".$mysql_db['mysql_ip'] ."/phpmyadmin' target='_blank'>&nbsp;&nbsp;Phpmyadmin</a></td><td></td></tr>".
				 "<td><b>Download do :<a href='https://www.heidisql.com/installers/HeidiSQL_11.0.0.5919_Setup.exe' target='_blank'>  &nbsp;&nbsp;HeidiSQL</a></td><td></td></tr>".
				 "<td><b>".get_lang('mysql_ip')." :</b></td><td>".$mysql_db['mysql_ip']."</td></tr>\n".
				 "<td><b>".get_lang('mysql_port')." :</b></td><td>".$mysql_db['mysql_port']."</td></tr>\n".
				 "<td><b>".get_lang('db_name')." :</b></td><td>".$mysql_db['db_name']."</td></tr>\n".
				 "<td><b>".get_lang('db_user')." :</b></td><td>".$mysql_db['db_user']."</td></tr>\n".
				 "<td><b>".get_lang('db_passwd')." :</b></td><td>".$mysql_db['db_passwd']."</td></tr>\n".
				 '<td><b>New database password :</b></td><td><input type="text" id="db_passwd" name="db_passwd" value="" size="25" class="form-control"></td></tr>'."\n".
				 '<td></td><td><input type="submit" name="save_db_changes" value="Save new password" class="btn btn-sm btn-primary"></td></tr>'."\n".
				 "<td><b>".get_lang('privilegies')." :</b></td><td>".$mysql_db['privilegies_str']."</td></tr></table></div>\n".
				 "<td><div class='dragbox bloc rounded' style='background:black;' ><h4>".get_lang('db_tables')."</h4>".
				 "<pre><xmp>".$user_db."</xmp></pre></div></td></tr></table>";
			?>
			</form>
			<?php

			/*
			echo "<table class='database' ><tr><td>\n<div class='dragbox bloc rounded' ><h4>".get_lang('db_info')."</h4>\n".
				 "<table class='database_info' ><tr>".
				 "<td><b>Fazer login no :<a href='http://".$mysql_db['mysql_ip'] ."/phpmyadmin' target='_blank'>&nbsp;&nbsp;Phpmyadmin</a></td><td></td></tr>".
				 "<td><b>Download do :<a href='https://www.heidisql.com/installers/HeidiSQL_11.0.0.5919_Setup.exe' target='_blank'>  &nbsp;&nbsp;HeidiSQL</a></td><td></td></tr>".
				 "<td><b>".get_lang('mysql_ip')." :</b></td><td>".$mysql_db['mysql_ip']."</td></tr>\n".
				 "<td><b>".get_lang('mysql_port')." :</b></td><td>".$mysql_db['mysql_port']."</td></tr>\n".
				 "<td><b>".get_lang('db_name')." :</b></td><td>".$mysql_db['db_name']."</td></tr>\n".
				 "<td><b>".get_lang('db_user')." :</b></td><td>".$mysql_db['db_user']."</td></tr>\n".
				 "<td><b>".get_lang('db_passwd')." :</b></td><td>".$mysql_db['db_passwd']."</td></tr>\n".
				 "<td><b>".get_lang('privilegies')." :</b></td><td>".$mysql_db['privilegies_str']."</td></tr></table></div>\n".
				 "<td><div class='dragbox bloc rounded' style='background:black;' ><h4>".get_lang('db_tables')."</h4>".
				 "<pre><xmp>".$user_db."</xmp></pre></div></td></tr></table>";
			*/
			
			if (isset($_POST['save_db_changes']))
			{
				$db_id = $db_id;
				$home_id = $game_home['home_id'];
				$post_db_user = trim($mysql_db['db_user']);
				$post_db_passwd = trim($_POST['db_passwd']);
				$post_db_name = trim($mysql_db['db_name']);
				$enabled = 1;
				
				if ( empty($post_db_passwd) ){
					//print_failure(get_lang('enter_db_password'));
					print_failure("Enter a new password!");
				}
				else
				{
					$mysql_db = $modDb->getMysqlDBbyId($db_id);
					
					if($post_db_passwd != $mysql_db['db_passwd'])
					{
						if($mysql_db['remote_server_id'] != "0")
						{
							$remote_server = $db->getRemoteServer($mysql_db['remote_server_id']);
							$remote = new OGPRemoteLibrary($remote_server['agent_ip'],$remote_server['agent_port'],$remote_server['encryption_key'],$remote_server['timeout']);
							$host_stat = $remote->status_chk();
							if($host_stat === 1 )
							{
								$SQL = "DROP USER '".$mysql_db['db_user']."'@'%';".
									   "GRANT ".$mysql_db['privilegies_str']." ON \\`".$mysql_db['db_name']."\\`.* TO '".$mysql_db['db_user']."'@'%' IDENTIFIED BY '".$post_db_passwd."';".
									   "FLUSH PRIVILEGES;";
									
								$command = "mysql --host=localhost --port=".$mysql_db['mysql_port']." -uroot -p".$mysql_db['mysql_root_passwd']." -e \"".$SQL."\"";
								$remote->exec($command);
							}
						}
						else
						{
							if( function_exists('mysqli_connect') )
							{
								@$link = mysqli_connect($mysql_db['mysql_ip'], 'root', $mysql_db['mysql_root_passwd'], "", $mysql_db['mysql_port']);
								
								if ( $link !== FALSE )
								{
									$queries = array("DROP USER '".$mysql_db['db_user']."'@'%';",
													 "GRANT ".$mysql_db['privilegies_str']." ON `".$mysql_db['db_name']."`.* TO '".$mysql_db['db_user']."'@'%' IDENTIFIED BY '".$post_db_passwd."';",
													 "FLUSH PRIVILEGES;");
									foreach( $queries as $query )
									{
										@$return = mysqli_query($link, $query);
										if(!$return)
											break;
									}
									mysqli_close($link);
									$modDb->connect($db_host,$db_user,$db_pass,$db_name,$table_prefix);
								}
							}
							else
							{
								@$link = mysql_connect($mysql_db['mysql_ip'].':'.$mysql_db['mysql_port'], 'root', $mysql_db['mysql_root_passwd']);
								
								if ( $link !== FALSE )
								{
									$queries = array("DROP USER '".$mysql_db['db_user']."'@'%';",
													 "GRANT ".$mysql_db['privilegies_str']." ON `".$mysql_db['db_name']."`.* TO '".$mysql_db['db_user']."'@'%' IDENTIFIED BY '".$post_db_passwd."';",
													 "FLUSH PRIVILEGES;");
									foreach( $queries as $query )
									{
										@$return = mysql_query($query);
										if(!$return)
											break;
									}
									mysql_close($link);
									$modDb->connect($db_host,$db_user,$db_pass,$db_name,$table_prefix);
								}
							}
						}
						
						if ( $modDb->editMysqlServerDB($db_id, $home_id, $post_db_user, $post_db_passwd, $post_db_name, $enabled) === FALSE )
						{       
							print_failure(get_lang('could_not_be_changed'));
						}
						else
						{
							print_success(get_lang_f('db_changed_successfully',$post_db_name));
						}
						$view->refresh('?m=mysql&p=user_db&home_id='.$game_home['home_id'].'&db_id='.$db_id."&amp;assign");
					}
				}
			}

			if(suhosin_function_exists('system') or $mysql_db['remote_server_id'] != "0")
			{
				echo "<h2>".get_lang('db_backup')."</h2>";
				
				?>
				<table class='administration-table'>
				 <tr>
				  <td>
				  <form method="POST" action="?m=mysql&p=get_dump&home_id=<?php echo $game_home['home_id']; ?>&db_id=<?php echo $db_id; ?>&type=cleared" >
				   <button name="download"><?php print_lang('download_db_backup'); ?></button>
				  </form>
				  <br>
				  <form method="POST" action="?m=mysql&p=user_db&home_id=<?php echo $game_home['home_id']; ?>&db_id=<?php echo $db_id; ?>" enctype="multipart/form-data">
				   <label for="file"><?php print_lang('sql_file'); ?>:</label>
				   <input type="file" name="file" id="file" />
				   <button name="restore"><?php print_lang('restore_db_backup'); ?></button>
				  </form>
				  </td>
				 </tr>
				</table>
				<?php
			}
		}
	}
}


