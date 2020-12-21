<?php
// News Lister, http://www.netartmedia.net/newslister
// A software product of NetArt Media, All Rights Reserved
// Find out more about our products and services on:
// http://www.netartmedia.net
// Released under the MIT license
?><?php

if(!defined('IN_SCRIPT_ADMIN')) die("");

if(isset($_POST["list_images"])&&$_POST["list_images"]!="")
{
	$list_files=explode(",",$_POST["list_images"]);
	for($i=0;$i<sizeof($list_files);++$i)
	{
		$file_name="modules/news/uploads/".$list_files[$i];
		if(!file_exists($file_name)) continue;
		
		$size	= getimagesize($file_name);
		$file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
		
		
		$mime_type="image/jpg";
		if($file_ext=="gif") $mime_type="image/gif";
		else
		if($file_ext=="png") $mime_type="image/png";
		
		
		$files[]=array
		(
			'name'    =>$file_name,
			'type'  => "",
			'tmp_name'=>$file_name,
			'mime' => $mime_type, 
			'size'  => getimagesize($file_name) 
		);
	}
}
else
if(!isset($_FILES[isset($input_field)?$input_field:'images']))
{

}
else
if(isset($_FILES))
{

	$files=array();
	$fdata=$_FILES[isset($input_field)?$input_field:'images'];
	
	
	if(is_array($fdata['name']))
	{
		for($i=0;$i<count($fdata['name']);++$i)
		{
			if(trim($fdata['name'][$i])==""||trim($fdata['tmp_name'][$i])=="") continue;
			
			$size	= getimagesize($fdata['tmp_name'][$i]);
			$mime	= $size['mime'];
			
			if (substr($mime, 0, 6) != 'image/') continue;


			$files[]=array
			(
				'name'    =>$fdata['name'][$i],
				'type'  => $fdata['type'][$i],
				'tmp_name'=>$fdata['tmp_name'][$i],
				'mime' => $mime, 
				'size'  => $fdata['size'][$i]  
			);
		}
	}else $files[]=$fdata;
}

if(isset($files))
{
	
	$is_first_image = true;
	
	foreach ($files as $file) 
	{ 
	
		if(trim($file['tmp_name'])=="") continue;
		
		$i_random=rand(200,100000000);

		$save_file_name = (isset($path)?$path:"")."uploaded_images/" .$i_random.".jpg";
	
		$uploaded_file = $file['tmp_name'];
		
		if($uploaded_file == "") continue;
	
		 list($width, $height) = getimagesize($uploaded_file) ; 

		if(!isset($max_image_width)) $max_image_width=600;
		
		if($width < $max_image_width)
		{
			$modwidth = $width;
		}
		else
		{
			$modwidth = $max_image_width; 
		}
		
		
		$diff =  $modwidth / $width;
		
		$modheight = $height * $diff; 
		
		
		$tn = imagecreatetruecolor($modwidth, $modheight) ; 
		
		
		$mime_type = "";
		
		if(isset($file['mime'])&&trim($file['mime'])!="")
		{
			$mime_type = $file['mime'];
		}
	
		if(isset($file['type'])&&trim($file['type'])!="")
		{
			$mime_type = $file['type'];
		}
	
		switch ($mime_type)
		{
			case 'image/gif':
				$creationFunction	= 'ImageCreateFromGif';
				$outputFunction		= 'ImagePng';
				$mime				= 'image/png'; // We need to convert GIFs to PNGs
			break;
			
			case 'image/x-png':
			case 'image/png':
				$creationFunction	= 'ImageCreateFromPng';
				$outputFunction		= 'ImagePng';
			break;
			
			default:
				$creationFunction	= 'ImageCreateFromJpeg';
				$outputFunction	 	= 'ImageJpeg';
			
			break;
		}

		$image	= $creationFunction($uploaded_file);

		if(!isset($image_quality)) $image_quality=90;
		imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height) ; 
		imagejpeg($tn, $save_file_name, $image_quality) ; 
		
		//thumbnails generation
			if($width < 250)
			{
				$thumb_width = $width;
			}
			else
			{
				$thumb_width = 250; 
			}
			$thumb_diff = $thumb_width / $width;
			$thumb_height = $height * $thumb_diff; 
			$thumb = imagecreatetruecolor($thumb_width, $thumb_height) ; 
			
			imagecopyresampled($thumb, $image, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height) ; 
			imagejpeg($thumb, (isset($path)?$path:"")."thumbnails/" .$i_random.".jpg", $image_quality) ; 
			
		//end thumbnails
	
		
		if($str_images_list!="")
		{
			$str_images_list.=",";
		}
		
		$str_images_list.=$i_random;
		
		$is_first_image = false;
	}
}


if(isset($_POST["list_images"])&&trim($_POST["list_images"])!="")
{
	$list_files=explode(",",$_POST["list_images"]);
	for($i=0;$i<sizeof($list_files);++$i)
	{
		$file_name="modules/news/uploads/".$list_files[$i];
		if(!file_exists($file_name)) continue;
		unlink($file_name);
	}
}
?>