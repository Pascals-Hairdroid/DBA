<?php
include_once 'include_login.php';
include_once 'DB_Con.php';
if(!isset($_SESSION))
	session_start();
//test:
//$_SESSION[L_ANGEMELDET]=true;
//echt:

$success = true;
if(!isset($_SESSION[L_USERNAME])){
  try{
	if(!login($_POST[L_USERNAME], $_POST[L_PASSWORT]))
		$success = false;
	} catch (DB_Exception $e){
    echo json_encode($e);
    exit(1);
	}
}
else{
  echo json_encode(new DB_Exception(000, "Schon eingeloggt!", "Schon  eingeloggt!"));
  exit(1);
}
$kunde = null;
if ($success) {
	$db = new DB_Con(DB_DEFAULT_CONF_FILE, true);
	$success = $db->sessionEintragen(session_id(), $_POST[L_USERNAME]);
	$kunde = $db->getKunde($_POST[L_USERNAME]);
	$foto = NK_Pfad_Kunde_Bildupload_beginn.md5($kunde->getEmail()).NK_Pfad_Kunde_Bild_ende;
	$picdate = file_exists($foto)?filemtime($foto):0;
}
echo json_encode($success?array("sessionId"=>session_id(), "kunde"=>$kunde, "picdate"=> $picdate):array("success"=>$success));
session_write_close();