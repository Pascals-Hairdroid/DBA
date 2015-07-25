<?php
include_once(dirname(__FILE__)."/conf/db_exception_const.php");
include_once(dirname(__FILE__)."/conf/db_const.php");
function file_upload($filename, $file_to_upload, $target_file, $overwrite = false, $width=null, $height=null ,$allowed_types=null, $maxSize=1000000000){
	try{
		$success = false;
		if($allowed_types == null)
			$allowed_types = unserialize(NK_Bild_Formate);
		$imageFileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		$targetFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$imgSize = getimagesize($file_to_upload);
		
		// Validation
		if($imgSize === false)
			throw new DB_Exception(400, "Datei ist kein Bild!", DB_ERR_VIEW_PARAM_FAIL);
		
		if (!$overwrite && file_exists($target_file))
				throw  new DB_Exception(500, "Datei existiert bereits!", DB_ERR_VIEW_FILE_EXISTS);
		
		if ($_FILES["fileToUpload"]["size"] > $maxSize)
			throw  new DB_Exception(400, "Maximale Dateigröße überschritten!", DB_ERR_VIEW_PARAM_FAIL);
		
		if(!in_array($imageFileType, $allowed_types)) {
			$formats = "";
			foreach ($allowed_types as $type)
				$formats = $formats.", ".$type;
			$formats = substr($formats, 2);
			throw  new DB_Exception(400, "Dateiformat unzulässig! Nur ".$formats." sind zulässig!", DB_ERR_VIEW_PARAM_FAIL);
		}
		
		// Einlesen
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
		
		// Resizen
		// Überflüssig für DBA
		if($width!=null & $height != null)
			$image = resizeImage($image, $width, $height);
		
		// Speichern
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
			default:
				throw new DB_Exception(500, "Dateityp der Zieldatei nicht unterstützt!", DB_ERR_VIEW_FILETYPE_NOT_SUPPORTED);
		}
		if(!$success)
			throw new DB_Exception(500, "Datei konnte nicht gespeichert werden!", DB_ERR_VIEW_UK_FAIL);
		
		return true;
	}catch(DB_Exception $e){
		throw $e;
	}catch(Exception $e){
		throw new DB_Exception(500, "Ein Fehler ist aufgetreten! Message: ".$e->getMessage(), DB_ERR_VIEW_UK_FAIL);
	}
	
}

// Überflüssig für DBA
function resizeImage($image, $width, $height){
	list($w, $h) = getimagesize($image);
	//...
	
	$img = imagecreatetruecolor($width, $hieght);
	imagecopyresampled($img, $image, 0,0,0,0, $width, $height, $w, $h);
	return $img;
}

function exists($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	
	curl_setopt($ch, CURLOPT_NOBODY, 1);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if(curl_exec($ch)!==FALSE)
		return true;
	return false;
}

function umlaute_encode($str){
	$a = array("ä", "ö", "ü", "Ä", "Ö", "Ü", "\n");
	$b = array("&auml;", "&ouml;", "&uuml;", "&Auml;", "&Ouml;", "&Uuml;", "<br/>\n");
	return str_replace($a,$b,$str);
}
?>