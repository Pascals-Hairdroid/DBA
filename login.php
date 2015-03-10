<?php
include_once 'include_login.php';
if(!isset($_SESSION))
	session_start();
//test:
//$_SESSION[L_ANGEMELDET]=true;
//echt:
$success = true;
if(!isset($_SESSION[L_USERNAME]))
	if(!login($_POST[L_USERNAME], $_POST[L_PASSWORT]))
		$success = false;
echo serialize($success);