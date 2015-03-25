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
	if(!login($_POST[L_USERNAME], $_POST[L_PASSWORT]))
		$success = false;
}
if ($success) {
	$db = new DB_Con(DB_DEFAULT_CONF_FILE, true);
	$success = $db->sessionEintragen(session_id(), $_POST[L_USERNAME]);
}
echo json_encode($success?session_id():$success);
session_write_close();