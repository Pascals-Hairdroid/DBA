<<<<<<< HEAD
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
echo json_encode($success?array("sessioId"=>session_id()):array("success"=>$success));
=======
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
if ($success) {
	$db = new DB_Con(DB_DEFAULT_CONF_FILE, true);
	$success = $db->sessionEintragen(session_id(), $_POST[L_USERNAME]);
}
echo json_encode($success?session_id():$success);
>>>>>>> 5f6764d91509fdd92174bf26627fded60f20412d
session_write_close();