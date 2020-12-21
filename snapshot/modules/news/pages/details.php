<?php
// News Lister 
// Copyright (c) All Rights Reserved, NetArt Media 2003-2016
// Check http://www.netartmedia.net/newslister for demos and information
// Released under the MIT license
if(!defined('IN_SCRIPT')) die("");
if (!empty($_SESSION['user_id'])) {$homex="home";} else {$homex="index";}
if(isset($_REQUEST["id"]) && $_REQUEST["id"]!="" && is_numeric($_REQUEST["id"])) {
	$id=intval($_REQUEST["id"]);
	$listings = simplexml_load_file($this->data_file);
	if (isset($listings->listing[$id])) { ?>
		<h2><?php echo strip_tags(html_entity_decode($listings->listing[$id]->title));?></h2>
		<div class="news-row">
			<div class="news-row">
				<br>
			<?php if($listings->listing[$id]->images!="" && $this->settings["website"]["images_bottom"]==0) { /// showing the listing images for right side setting
				echo'<div class="news-one-third pull-right">';
					$images=explode(",",trim($listings->listing[$id]->images,","));
					if(file_exists("modules/news/uploaded_images/".$images[0].".jpg")) {
						echo "<a href=\"modules/news/uploaded_images/".$images[0].".jpg\" rel=\"prettyPhoto[ad_gal]\">";
						echo "<img src=\"modules/news/uploaded_images/".$images[0].".jpg\" alt=\"".strip_tags(html_entity_decode($listings->listing[$id]->title))."\" class=\"final-image\"/>";
						echo "</a>";
					} ?>
			<br/><br/>
			<?php for($i=1;$i<sizeof($images);$i++) {
					if(trim($images[$i])=="") continue;
					if($i!=0) {
						echo "<a href=\"modules/news/uploaded_images/".$images[$i].".jpg\" rel=\"prettyPhoto[ad_gal]\">";
					}
					echo "<img class=\"thumbnail-detail\" src=\"modules/news/thumbnails/".$images[$i].".jpg\" width=\"85\" alt=\"\"/>";
					if($i!=0) {
						echo "</a>";
					}
			} ?>
			<link rel="stylesheet" href="modules/news/css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
			<script src="modules/news/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
			<script type="text/javascript" charset="utf-8">
				$(document).ready(function() {
					$("a[rel='prettyPhoto[ad_gal]']").prettyPhoto({
						<?php if($this->settings["website"]["gallery_theme"]!='default') { ?>theme: '<?php echo $this->settings["website"]["gallery_theme"]; ?>' <?php } ?>
					});
				});
			</script>
			</div>
			<?php
				/// end showing the listing images if right side setting
			}
			if($this->settings["website"]["safe_HTML"]==1) {
				require_once('modules/news/include/library/HTMLPurifier.auto.php');
				$purificateur = new HTMLPurifier();
				echo $purificateur->purify($listings->listing[$id]->description);
			} else {
				echo $listings->listing[$id]->description;
			}
			if($listings->listing[$id]->images!="" && $this->settings["website"]["images_bottom"]==1) { /// showing the listing images for bottom setting
				echo'<div class="news-row img-bottom">';
					$images=explode(",",trim($listings->listing[$id]->images,","));
					if(file_exists("modules/news/uploaded_images/".$images[0].".jpg")) {
						echo "<a href=\"modules/news/uploaded_images/".$images[0].".jpg\" rel=\"prettyPhoto[ad_gal]\">";
						echo "<img src=\"modules/news/uploaded_images/".$images[0].".jpg\" alt=\"".strip_tags(html_entity_decode($listings->listing[$id]->title))."\" class=\"final-image\"/>";
						echo "</a>";
					} ?>
			<br/><br/>
			<?php for($i=1;$i<sizeof($images);$i++) {
					if(trim($images[$i])=="") continue;
					if($i!=0) {
						echo "<a href=\"modules/news/uploaded_images/".$images[$i].".jpg\" rel=\"prettyPhoto[ad_gal]\">";
					}
					echo "<img class=\"thumbnail-detail\" src=\"modules/news/thumbnails/".$images[$i].".jpg\" width=\"85\" alt=\"\"/>";
					if($i!=0) {
						echo "</a>";
					}
			} ?>
			<link rel="stylesheet" href="modules/news/css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
			<script src="modules/news/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
			<script type="text/javascript" charset="utf-8">
				$(document).ready(function() {
					$("a[rel='prettyPhoto[ad_gal]']").prettyPhoto({
						<?php if($this->settings["website"]["gallery_theme"]!='default') { ?>theme: '<?php echo $this->settings["website"]["gallery_theme"]; ?>' <?php } ?>
					});
				});
			</script>
			</div>
			<?php
				/// end showing the listing images if bottom setting
			} ?>
			</div>
		</div>
		<div class="clearfix"></div>
		<br/>
		<div class="news-row">
			<div class="news-half pull-left">
				<br><strong><?php echo date($this->settings["website"]["date_format"],intval($listings->listing[$id]->time));?></strong> - <?php echo get_lang('written_by');?>: <strong><?php echo strip_tags(html_entity_decode(stripslashes($listings->listing[$id]->written_by)));?></strong>
			</div>
			<div class="news-half pull-right">
				<a id="go_back_button" class="news-btn news-btn-default pull-right" href="<?php echo $homex;?>.php?m=news&p=news"><?php echo get_lang('go_back');?></a>
			</div>
		</div>
		
		<?php
		} else {
			echo "<h2>".get_lang('latest_news')."</h2><br><h3><span class='failure'>".get_lang('id_invalid')."</span></h3>";
		}
}
else {
	echo "<h2>".get_lang('latest_news')."</h2><br><h3><span class='failure'>".get_lang('id_not_set')."</span></h3>";
}