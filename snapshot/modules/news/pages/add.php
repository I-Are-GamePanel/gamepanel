<?php
// News Lister, http://www.netartmedia.net/newslister
// A software product of NetArt Media, All Rights Reserved
// Find out more about our products and services on:
// http://www.netartmedia.net
// Released under the MIT license
if(!defined('IN_SCRIPT_ADMIN')) {
	global $db;
	echo '<h3>'.get_lang('no_access').'</h3>';
	$abuse_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$db->logger(get_lang('unauthorized_access').' '.$abuse_link);
}
else{
?>
	<h2><?php echo get_lang('add_new_listing');?></h2>
	<div class="news-row goback"><a href="home.php?m=news&p=admin_news" class="news-btn news-btn-default pull-right"><?php echo get_lang('go_back');?></a></div>
	<div class="news-container">
			<?php
			$show_add_form=true;
			
			class SimpleXMLExtended extends SimpleXMLElement 
			{
			  public function addChildWithCDATA($name, $value = NULL) {
				$new_child = $this->addChild($name);

				if ($new_child !== NULL) {
				  $node = dom_import_simplexml($new_child);
				  $no   = $node->ownerDocument;
				  $node->appendChild($no->createCDATASection($value));
				}

				return $new_child;
			  }
			}

			if(isset($_REQUEST["proceed_save"]))
			{
				///server side check if fields are not empty in case user modified the page with 'inspect element' to remove required option
				$fields_ok=true;
				if (empty(trim($_POST["title"]," "))) {
					echo "<h3><span class='failure'>".get_lang('empty_title')."</span></h3>";
					$fields_ok=false;
				}
				if (empty(trim(strip_tags($_POST["description"]),"/&nbsp;/ "))) {
					echo "<h3><span class='failure'>".get_lang('empty_description')."</span></h3>";
					$fields_ok=false;
				}
				if (empty(trim($_POST["written_by"]," "))) {
					echo "<h3><span class='failure'>".get_lang('empty_author')."</span></h3>";
					$fields_ok=false;
				}
				if ($fields_ok) {
					///images processing
					$str_images_list = "";
					$limit_pictures=25;
					$path="modules/news/";
					$ini_array = parse_ini_file("modules/news/config.php",true);
					$image_quality=$ini_array["website"]["image_quality"];
					$max_image_width=$ini_array["website"]["max_image_width"];
					
					include("modules/news/include/images_processing.php");
					///end images processing
					$listings = simplexml_load_file($this->data_file,'SimpleXMLExtended', LIBXML_NOCDATA);
					$listing = $listings->addChild('listing');
					$listing->addChild('time', time());
					$listing->addChild('title', stripslashes($_POST["title"]));
					$article_content=stripslashes(str_replace('\r\n', '',$_POST["description"]));
					$article_content=str_replace("&nbsp;"," ",$article_content);
					
					$listing->addChildWithCDATA('description', $article_content);
					$listing->addChild('images', $str_images_list);
					$listing->addChild('written_by', stripslashes($_POST["written_by"]));
					$listings->asXML($this->data_file); 
					?>
					<h3><?php echo get_lang('new_added_success');?></h3>
					<br/>
					<a href="home.php?m=news&p=admin_news&page=add" class="underline-link"><?php echo get_lang('add_another');?></a>
					<?php echo get_lang('or_message');?>
					<a href="home.php?m=news&p=admin_news&page=home" class="underline-link"><?php echo get_lang('manage_listings');?></a>
					<br/>
					<br/>
					<br/>
					<?php
					$show_add_form=false;
				}
			}
			
			if($show_add_form)
			{
			?>
					<br/>
					
				<?php 
				if($this->settings["website"]["WYSIWYG"]=="TinyMCE") { ?>
					<script src="modules/news/js/tinymce/tinymce.min.js"></script>
					<script type="text/javascript">
					tinymce.init({
						selector: '#description',
						inline: true,
						menubar: false,
						skin_url: 'modules/news/js/tinymce/skins/<?php echo $this->settings["website"]["tinymce_skin"]; ?>',
						language: '<?php echo $this->settings["website"]["tinymce_lang"]; ?>',
						plugins: [
						'advlist autolink lists link image charmap print preview hr anchor pagebreak',
						'searchreplace wordcount visualblocks visualchars code fullscreen',
						'insertdatetime media nonbreaking save table contextmenu directionality',
						'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc help'
						],
						toolbar1: 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image media | link unlink | removeformat',
						toolbar2: 'styleselect | fontselect forecolor backcolor fontsizeselect | code',
						image_advtab: true
					});
					</script>
				<?php } else if($this->settings["website"]["WYSIWYG"]=="NicEdit") { ?>
					<script src="modules/news/js/nicEdit.js" type="text/javascript"></script>
					<script type="text/javascript">
					bkLib.onDomLoaded(function() {
						new nicEditor({fullPanel : true,iconsPath : 'modules/news/js/nicEditorIcons.gif'}).panelInstance('description');
					});
					</script>
				<?php } ?>
					<form  action="home.php?m=news&p=admin_news" method="post"   enctype="multipart/form-data">
					<input type="hidden" name="page" value="add"/>
					<input type="hidden" name="proceed_save" value="1"/>
				
					<div class="news-row">
						<div class="one-sixth pull-left">
							<?php echo get_lang('title');?>:
						</div>
						<div class="eight-tenth pull-right">
									<input class="news-form-control" type="text" name="title" required value="<?php echo $_REQUEST["title"];?>"/>
						</div>
					</div>
					<br/>
					<div class="news-row">
						<div class="one-sixth pull-left">
							<?php echo get_lang('description');?>:
						</div>
						<div class="eight-tenth pull-right">
							<?php 
							if($this->settings["website"]["WYSIWYG"]=="TinyMCE") { ?>
							<div id="description" class="news-form-control news-form-control-mce"></div>
							<?php }
							if($this->settings["website"]["WYSIWYG"]=="NicEdit") { ?>
							<textarea class="news-form-control news-form-control-mce" id="description" name="description" cols="40" rows="10"><?php echo $_POST["description"];?></textarea>
							<?php } ?>
						</div>
					</div>
					
					<br/>
					
					<div class="news-row">
						<div class="one-sixth pull-left">			
							<?php echo get_lang('images');?>:
						</div>
						<div class="eight-tenth pull-right">		
						<?php if (extension_loaded('gd')) { ?>
							<!--images upload-->
							<script src="modules/news/js/jquery.uploadfile.js"></script>

							
								<div id="mulitplefileuploader"><?php echo get_lang('please_select');?></div>
								
								
								<div id="status"><i>
									
								</i>
								
								</div>
								<script>
								var uploaded_files="";
								$(document).ready(function()
								{
								var settings = {
									url: "modules/news/upload.php",
									dragDrop:true,
									fileName: "myfile",
									maxFileCount:25,
									allowedTypes:"jpg,png,gif",	
									returnType:"json",
									 onSuccess:function(files,data,xhr)
									{
										if(uploaded_files!="") uploaded_files+=",";
										uploaded_files+=data;
										
									},
									afterUploadAll:function()
									{
										var preview_code="";
										var imgs = uploaded_files.split(",")
										for (var i = 0; i < imgs.length; i++)
										{
											preview_code+='<div class="img-wrap"><img width="120" src="modules/news/uploads/'+imgs[i]+'"/></div>';
										}
										
										document.getElementById("status").innerHTML=preview_code;
										document.getElementById("list_images").value=uploaded_files;
									},
									showDelete:false,
									
									showProgress:true,
									showFileCounter:false,
									showDone:false
								}
								
								

								var uploadObj = $("#mulitplefileuploader").uploadFile(settings);


								});
								</script>
										
							<!--end images upload-->
							<?php
							}else{
								echo "<h3><span class='failure'>".get_lang('gd_fail')."</span></h3>";
								}
							?>
						</div>
					</div>
					
					<br/>
					<div class="news-row">
						<div class="one-sixth pull-left">
							<?php echo get_lang('written_by');?>:
						</div>
						<div class="eight-tenth pull-right">
							<input class="news-form-control" type="text" name="written_by" required value="<?php echo isset($_REQUEST["written_by"]) ? $_REQUEST["written_by"] : $_SESSION['users_login']; ?>"/>
						</div>
					</div>
					
					<input type="hidden" name="list_images" value="<?php if(isset($_POST["list_images"])) echo $_POST["list_images"];?>" id="list_images"/>
					
					<div class="clearfix"></div>
					
				<div class="clearfix"></div>
				<br/>
				<button type="submit" class="news-btn news-btn-default pull-right"> <?php echo get_lang('submit');?> </button>
				<div class="clearfix"></div>
			</form>
			<?php } ?>
	</div>
<?php } ?>