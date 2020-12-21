<?php
// News Lister 
// Copyright (c) All Rights Reserved, NetArt Media 2003-2016
// Check http://www.netartmedia.net/newslister for demos and information
// Released under the MIT license
if(!defined('IN_SCRIPT_ADMIN')) {
	global $db;
	echo '<h3>'.get_lang('no_access').'</h3>';
	$abuse_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$db->logger(get_lang('unauthorized_access').' '.$abuse_link);
}
else {
	?>
	
	<h2><?php echo get_lang('config_options');?></h2>
	<div class="news-row goback"><a href="home.php?m=news&p=admin_news" class="news-btn news-btn-default pull-right"><?php echo get_lang('go_back');?></a></div>
	
	<div class="news-container">
			<?php
			// Check if the file "modules/news/config.php" is writable
			$value = 'modules/news/config.php';
			if ( !is_writable($value) ) {
				echo "<h3>".$value." : <span class='failure'>".get_lang('write_permission_required')."</span> <a href=\"home.php?m=news&p=admin_news&page=permissions\">".get_lang('check_permissions')."</a></h3><br/><br/>";
			}
			
			$ini_array = parse_ini_file("modules/news/config.php",true);
			
			if(isset($_POST["proceed_save"]))
			{
				$ini_array["website"]["date_format"]=stripslashes($_POST["date_format"]);
				$ini_array["website"]["results_per_page"]=stripslashes($_POST["results_per_page"]);
				$ini_array["website"]["enable_search"]=stripslashes($_POST["enable_search"]);
				$ini_array["website"]["image_quality"]=stripslashes($_POST["image_quality"]);
				$ini_array["website"]["max_image_width"]=stripslashes($_POST["max_image_width"]);
				$ini_array["website"]["images_bottom"]=stripslashes($_POST["images_bottom"]);
				$ini_array["website"]["gallery_theme"]=stripslashes($_POST["gallery_theme"]);
				$ini_array["website"]["WYSIWYG"]=stripslashes($_POST["WYSIWYG"]);
				$ini_array["website"]["tinymce_lang"]=stripslashes($_POST["tinymce_lang"]);
				$ini_array["website"]["tinymce_skin"]=stripslashes($_POST["tinymce_skin"]);
				
				$this->write_ini_file("modules/news/config.php", $ini_array);
			}
			
			?>
			
			<div class="news-row">
				<div class="news-row">
				<br/>
				<form id="main" action="home.php?m=news&p=admin_news" method="post">
					<input type="hidden" name="page" value="settings"/>
					<input type="hidden" name="proceed_save" value="1"/>
						
						<fieldset>
							<ol>
							
								<script>
								  function handleChange(input) {
									if (input.value < 1) input.value = 1;
									if (input.value > 100) input.value = 100;
								  }
								</script>
								
								<li>
									<label><?php echo get_lang('date_format');?> (<a href="http://php.net/manual/function.date.php" title="<?php echo get_lang('help_date');?>" target="_blank"><?php echo get_lang('help');?></a>):</label>
									
									<input type="text" name="date_format" value="<?php echo $ini_array["website"]["date_format"];?>"/>
								</li>
								<li>
									<label><?php echo get_lang('results_per_page');?>:</label>
									
									<input type="number" name="results_per_page" value="<?php echo $ini_array["website"]["results_per_page"];?>" onchange="handleChange(this);"/>
								</li>
								<li>
									<label><?php echo get_lang('image_quality');?>:</label>
									
									<input type="number" name="image_quality" value="<?php echo $ini_array["website"]["image_quality"];?>" onchange="handleChange(this);"/>
								</li>
								<li>
									<label><?php echo get_lang('max_image_width');?>:</label>
									
									<input type="number" name="max_image_width" value="<?php echo $ini_array["website"]["max_image_width"];?>"/>
								</li>
								<li>
									<label><?php echo get_lang('enable_search');?>:</label>
									
									<select name="enable_search">
										<option value="0" <?php if($ini_array["website"]["enable_search"]=="0") echo "selected";?>><?php echo get_lang('no_word');?></option>
										<option value="1" <?php if($ini_array["website"]["enable_search"]=="1") echo "selected";?>><?php echo get_lang('yes_word');?></option>
									</select>
								</li>
								<li>
									<label><?php echo get_lang('wysiwyg');?>:</label>
									
									<select name="WYSIWYG">
										<option value="TinyMCE" <?php if($ini_array["website"]["WYSIWYG"]=="TinyMCE") echo "selected";?>>Tiny MCE</option>
										<option value="NicEdit" <?php if($ini_array["website"]["WYSIWYG"]=="NicEdit") echo "selected";?>>Nic Edit</option>
									</select>
								</li>
								<li>
									<label><?php echo get_lang('tinymce_lang');?>:</label>
									
									<select name="tinymce_lang">
										<option value="da" <?php if($ini_array["website"]["tinymce_lang"]=="da") echo "selected";?>><?php echo get_lang('da');?></option>
										<option value="de" <?php if($ini_array["website"]["tinymce_lang"]=="de") echo "selected";?>><?php echo get_lang('de');?></option>
										<option value="en_GB" <?php if($ini_array["website"]["tinymce_lang"]=="en_GB") echo "selected";?>><?php echo get_lang('en_GB');?></option>
										<option value="es" <?php if($ini_array["website"]["tinymce_lang"]=="es") echo "selected";?>><?php echo get_lang('es');?></option>
										<option value="fi" <?php if($ini_array["website"]["tinymce_lang"]=="fi") echo "selected";?>><?php echo get_lang('fi');?></option>
										<option value="fr_FR" <?php if($ini_array["website"]["tinymce_lang"]=="fr_FR") echo "selected";?>><?php echo get_lang('fr_FR');?></option>
										<option value="it" <?php if($ini_array["website"]["tinymce_lang"]=="it") echo "selected";?>><?php echo get_lang('it');?></option>
										<option value="pl" <?php if($ini_array["website"]["tinymce_lang"]=="pl") echo "selected";?>><?php echo get_lang('pl');?></option>
										<option value="pt_PT" <?php if($ini_array["website"]["tinymce_lang"]=="pt_PT") echo "selected";?>><?php echo get_lang('pt_PT');?></option>
										<option value="ru" <?php if($ini_array["website"]["tinymce_lang"]=="ru") echo "selected";?>><?php echo get_lang('ru');?></option>
									</select>
								</li>
								<li>
									<label><?php echo get_lang('tinymce_skin');?>:</label>
									
									<select name="tinymce_skin">
										<option value="lightgray" <?php if($ini_array["website"]["tinymce_skin"]=="lightgray") echo "selected";?>>lightgray</option>
										<option value="lightgray-gradient" <?php if($ini_array["website"]["tinymce_skin"]=="lightgray-gradient") echo "selected";?>>lightgray-gradient</option>
										<option value="charcoal" <?php if($ini_array["website"]["tinymce_skin"]=="charcoal") echo "selected";?>>charcoal</option>
										<option value="tundora" <?php if($ini_array["website"]["tinymce_skin"]=="tundora") echo "selected";?>>tundora</option>
										<option value="custom" <?php if($ini_array["website"]["tinymce_skin"]=="custom") echo "selected";?>>custom</option>
									</select>
									<?php if($ini_array["website"]["tinymce_skin"]=="custom") { ?><br><p><img src="modules/news/images/warning.png"> <?php echo get_lang('tinymce_skin_custom'); ?></p> <?php } ?>
								</li>
								<li>
									<label><?php echo get_lang('gallery_theme');?>:</label>
									
									<select name="gallery_theme">
										<option value="default" <?php if($ini_array["website"]["gallery_theme"]=="default") echo "selected";?>>Default</option>
										<option value="light_rounded" <?php if($ini_array["website"]["gallery_theme"]=="light_rounded") echo "selected";?>>Light Rounded</option>
										<option value="light_square" <?php if($ini_array["website"]["gallery_theme"]=="light_square") echo "selected";?>>Light Square</option>
										<option value="dark_rounded" <?php if($ini_array["website"]["gallery_theme"]=="dark_rounded") echo "selected";?>>Dark Rounded</option>
										<option value="dark_square" <?php if($ini_array["website"]["gallery_theme"]=="dark_square") echo "selected";?>>Dark Square</option>
										<option value="facebook" <?php if($ini_array["website"]["gallery_theme"]=="facebook") echo "selected";?>>Facebook</option>
									</select>
								</li>
								<li>
									<label><?php echo get_lang('images_bottom');?>:</label>
									
									<select name="images_bottom">
										<option value="0" <?php if($ini_array["website"]["images_bottom"]=="0") echo "selected";?>><?php echo get_lang('img_right');?></option>
										<option value="1" <?php if($ini_array["website"]["images_bottom"]=="1") echo "selected";?>><?php echo get_lang('img_bottom');?></option>
									</select>
								</li>
								<li>
									<label><?php echo get_lang('safe_HTML');?>:</label>
									<select name="enable_safe_HTML" disabled>
										<option value="0" <?php if($ini_array["website"]["safe_HTML"]=="0") echo "selected";?>><?php echo get_lang('safe_HTML_dis');?></option>
										<option value="1" <?php if($ini_array["website"]["safe_HTML"]=="1") echo "selected";?>><?php echo get_lang('safe_HTML_en');?></option>
									</select>
									<br><p><?php if($ini_array["website"]["safe_HTML"]=="1") { echo get_lang('safe_HTML_en_info');} else { echo get_lang('safe_HTML_dis_info');} ?></p>
								</li>
								
							<ol>
						</fieldset>
						
						<div class="clearfix"></div>
						<br/>
						<button type="submit" class="news-btn news-btn-default pull-right"><?php echo get_lang('save');?></button>
						<br/>
						<div class="clearfix"></div>
						<br/>
					</form>
				
				</div>
				
			</div>
			
	</div>

<?php
}
?>