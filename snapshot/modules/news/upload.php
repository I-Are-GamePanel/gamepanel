<?php
// News Lister 
// Copyright (c) All Rights Reserved, NetArt Media 2003-2016
// Check http://www.netartmedia.net/newslister for demos and information
// Released under the MIT license

session_name('opengamepanel_web');
session_start();

$output_dir = "uploads/";
if(isset($_FILES["myfile"]))
{
	$ret = array();

	// Is user logged in?
	if(isset($_SESSION['users_login'])) {

		$allowedExtensions = array('png', 'gif', 'jpg', 'jpeg');

		if(!is_array($_FILES["myfile"]["name"])) 
		{
			$fileName = $_FILES["myfile"]["name"];
			$ext = end(explode('.', $fileName));

			if (in_array($ext, $allowedExtensions)) {
				move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.$fileName);
				$ret[]= $fileName;

			} else {
				// something with a non-image extension was posted.
				// log?
			}

		} else {
			
			$fileCount = count($_FILES["myfile"]["name"]);

			for($i=0; $i < $fileCount; $i++) {

				$fileName = $_FILES["myfile"]["name"][$i];
				$ext = end(explode('.', $fileName));

				if (in_array($ext, $allowedExtensions)) {
					move_uploaded_file($_FILES["myfile"]["tmp_name"][$i], $output_dir.$fileName);
					$ret[]= $fileName;

				} else {
					// something with a non-image extension was posted.
					// log?
				}
			}
		}

	} else {
		// User isn't logged in.
		// Log something?
	}

	echo json_encode($ret);
 }
 ?>
