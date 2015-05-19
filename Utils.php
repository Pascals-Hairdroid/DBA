<?php
include_once(dirname(__FILE__)."/conf/db_exception_const.php");
include_once(dirname(__FILE__)."/conf/db_const.php");
function file_upload($filename, $file_to_upload, $target_file, $overwrite = false, $width=NK_Werbung_Bild_Width, $height=NK_Werbung_Bild_Height ,$allowed_types=null, $maxSize=1000000000){
	//echo "test: ".$target_file;
	$success = false;
	if($allowed_types == null)
		$allowed_types = unserialize(NK_Bild_Formate);
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	$targetFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
		$check = getimagesize($file_to_upload);
		if($check !== false) {
			echo "File is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
		} else {
			echo "File is not an image.";
			$uploadOk = 0;
		}
	}
	// Check if file already exists
	if (file_exists($target_file)) {
		if(!$overwrite){
			echo "Sorry, file already exists.";
			$uploadOk = 0;
		}
	}
	// Check file size
	if ($_FILES["fileToUpload"]["size"] > $maxSize) {
		echo "Sorry, your file is too large.";
		$uploadOk = 0;
	}
	// Allow certain file formats
	if(!in_array($imageFileType, $allowed_types)) {
		echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
	} 
	else {
		// Image einlesen
		switch ($imageFileType){
			case "jpg":
			case "jpeg":
				$image = imageCreateFromJpeg($file_to_upload);
				break;
			case "png":
				$image = imageCreateFromPng($file_to_upload);
				break;
			case "bmp":
				$image = imageCreateFromBmp($file_to_upload);
				break;
			case "gif":
				$image = imageCreateFromGif($file_to_upload);
				break;
		}
		
		// Image resizen und speichern
		// Überflüssig für DBA
		// $image = resizeImage($image, $width, $height);
		switch ($targetFileType){
			case "jpg":
			case "jpeg":
				// echo "speichern";
				$success=imagejpeg($image, $target_file, 100)?true:false;
				//echo $success;
				break;
			case "png":
				$success=imagepng($image, $target_file, 100)?true:false;
				break;
			case "gif":
				$success=imagegif($image, $target_file, 100)?true:false;
				break;
		}
		
		if ($success) {
			return true;
		}
	}
	return false;
}
// Überflüssig für DBA
function resizeImage($image, $width, $height){
	list($w, $h) = getimagesize($image);
	//...
	
	$img = imagecreatetruecolor($width, $hieght);
	imagecopyresampled($img, $image, 0,0,0,0, $width, $height, $w, $h);
	return $img;
}

?>