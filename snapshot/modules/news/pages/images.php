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
} else{
$id=intval($_REQUEST["id"]);
$this->ms_i($id);
?>
	<h2><?php echo get_lang('modify_images');?></h2>		  
	<div class="news-row goback"><a href="home.php?m=news&p=admin_news" class="news-btn news-btn-default pull-right"><?php echo get_lang('go_back');?></a></div>

	<div class="news-container" id="main_content">
	<?php
			$xml = simplexml_load_file($this->data_file);
			
			$current_images = trim($xml->listing[$id]->images);
			
			if(isset($_REQUEST["current"])&&isset($_REQUEST["new"]))
			{
				$current=str_replace("img","",$_REQUEST["current"]);
				$new=str_replace("img","",$_REQUEST["new"]);
				
				$pos_current=strpos($current_images,$current);
				$pos_new=strpos($current_images,$new);
				
				if
				(
					$pos_current!==false
					&&
					$pos_new!==false
				)
				{
				
					$current_images = str_replace($new,"###",$current_images);
					$current_images = str_replace($current,$new,$current_images);
					$current_images = str_replace("###",$current,$current_images);
					
					$xml->listing[$id]->images=$current_images;
					$xml->asXML($this->data_file); 
					
				}
			}
			else
			if(isset($_REQUEST["del"]))
			{
				$pos=strpos($current_images,$_REQUEST["del"]);
				
				if($pos!==false)
				{
				
				
					$current_images = str_replace($_REQUEST["del"],"",$current_images);
					$current_images = str_replace(",,",",",$current_images);
					$current_images = trim($current_images,",");
					
					$xml->listing[$id]->images=$current_images;
					$xml->asXML($this->data_file); 
					
					if(file_exists("modules/news/thumbnails/".$_REQUEST["del"].".jpg"))
						{
							unlink("modules/news/thumbnails/".$_REQUEST["del"].".jpg");
						}
						
					if(file_exists("modules/news/uploaded_images/".$_REQUEST["del"].".jpg"))
						{
							unlink("modules/news/uploaded_images/".$_REQUEST["del"].".jpg");
						}
				
				}
			}
			else
			if(isset($_POST["proceed_save"]))
			{
				///images processing
				$str_images_list = "";
				$limit_pictures=25;
				$path="modules/news/";
				$ini_array = parse_ini_file("modules/news/config.php",true);
				$image_quality=$ini_array["website"]["image_quality"];
				$max_image_width=$ini_array["website"]["max_image_width"];
				
				include("modules/news/include/images_processing.php");
				///end images processing
				
				if($current_images != "")
				{
					$str_images_list = $current_images.",".$str_images_list;
				}
				
				
				$xml->listing[$id]->images=$str_images_list;
				$xml->asXML($this->data_file); 
				$current_images=$str_images_list;
			}	
			
			?>
			
				<div class="news-row">
				
				<form action="home.php?m=news&p=admin_news" method="post"   enctype="multipart/form-data">
					<input type="hidden" name="page" value="images"/>
					<input type="hidden" name="proceed_save" value="1"/>
					<input type="hidden" name="id" value="<?php echo $id;?>"/>
					
						<script>
						function Dele(x)
						{
							document.location.href="home.php?m=news&p=admin_news&page=images&id=<?php echo $id;?>&del="+x;

						}
						</script>
						<div class="news-row">
						<div id="drag_images">
						<?php
						$iPicCounter = 0;
						$image_ids = explode(",",$current_images);
				

						for($i=0;$i<sizeof($image_ids);$i++)
						{
						
							if(isset($image_ids[$i]) && $image_ids[$i]!="")	
							{
							?>
								
										
								<div  ondragstart="javascript:img_drag_start(this)" class="drag_img" id="img<?php echo $image_ids[$i];?>">
								<a class="pull-right" href="javascript:Dele('<?php echo $image_ids[$i];?>')"><img src="modules/news/images/cancel.gif" alt="<?php echo get_lang('delete');?>" width="21" height="20" border="0"></a>
								<br>
								<img  src="modules/news/thumbnails/<?php echo $image_ids[$i];?>.jpg" alt="" height="125"/>
								
							
								</div>
							<?php
							}
							?>
							<span style="display:none">
								<input type="file" name="userfile<?php echo $i;?>">
							</span>
							<?php
							
							$iPicCounter++;
							
						}
						?>
						</div></div>
						<div class="clearfix"></div>
						
						<div class="news-row">
						<br>
						<?php if (extension_loaded('gd')) { ?>
						<h3><?php echo get_lang('upload_more_images');?></h3>
						<br/>
						<input  type="file" class="pull-left" name="images[]" id="images"  multiple="multiple"/>
						
						<button type="submit" class="news-btn news-btn-default pull-left"><?php echo get_lang('submit');?></button>
						<?php
						}else{
							echo "<h3><span class='failure'>".get_lang('gd_fail')."</span></h3>";
							}
						?></div>
						
						</form>
						
						<script>
						
						function init_drag() 
						{
							
							$('.drag_img').draggable( {
								containment: '#main_content',
								revert: true
							} );
							
							$('.drag_img').droppable( {
								drop: handle_drop
							} );
							
							
						}
						 
						function handle_drop( event, ui ) 
						{
							var id = $(this).attr('id');
							var draggable = ui.draggable;
						  
							document.location.href="home.php?m=news&p=admin_news&page=images&id=<?php echo $id;?>&current="+id+"&new="+draggable.attr('id');

						}
						var x_index=100;
						function img_drag_start(x)
						{
							x_index=x_index+100;
							x.style.zIndex=x_index;
						}

						$(init_drag);
						</script>
						

						<div class="clearfix"></div>
						<br/><br/>
				
				</div>
				
	</div>
	
<?php
}
?>