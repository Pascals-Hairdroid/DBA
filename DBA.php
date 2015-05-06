<?php
include_once 'include_login.php';
include_once 'classes/DB_Exception.php';
include_once 'conf/db_exception_const.php';
include_once 'conf/login_const.php';
include_once 'conf/dba_const.php';
include_once "DB_Con.php";

// Test:
// echo json_encode("Hallo");
// $res=true;
// echo json_encode(array("res" => $res));
// ---
/**
 * 
 * @throws DB_Exception
 * @return Kunde
 */
function readKunde(){
	if(!isset($_POST[DBA_P_KUNDE_EMAIL]) && !isset($_SESSION[L_USERNAME])){
		//var_dump($_POST);
		throw new DB_Exception(400, "Kundenmail nicht gesetzt! ".var_export($_POST,true), DB_ERR_VIEW_PARAM_FAIL);

	}
	if(isset($_SESSION[L_USERNAME]))
		$_POST[DBA_P_KUNDE_EMAIL] = $_SESSION[L_USERNAME];
	$interessen_p = isset($_POST[DBA_P_KUNDE_INTERESSEN])?$_POST[DBA_P_KUNDE_INTERESSEN]:array();
	$interessen = array();
	foreach ($interessen_p as $id)
		array_push($interessen, new Interesse($id, null));

	return new Kunde($_POST[DBA_P_KUNDE_EMAIL], isset($_POST[DBA_P_KUNDE_VORNAME])?$_POST[DBA_P_KUNDE_VORNAME]:null, isset($_POST[DBA_P_KUNDE_NACHNAME])?$_POST[DBA_P_KUNDE_NACHNAME]:null, isset($_POST[DBA_P_KUNDE_TELNR])?$_POST[DBA_P_KUNDE_TELNR]:null, isset($_POST[DBA_P_KUNDE_FREISCHALTUNG])?$_POST[DBA_P_KUNDE_FREISCHALTUNG]:null, isset($_POST[DBA_P_KUNDE_FOTO])?$_POST[DBA_P_KUNDE_FOTO]:null, $interessen);
}

$db = new DB_Con(DB_DEFAULT_CONF_FILE, true);
if(isset($_POST[DBA_SESSION_ID])){
	session_start($_POST[DBA_SESSION_ID]);
	try{
		$susr = $db->getKundeMailBySessionId($_POST[DBA_SESSION_ID]);
		$abf = $db->selectQuery(DB_TB_KUNDEN, DB_F_KUNDEN_PASSWORT, DB_F_KUNDEN_PK_EMAIL."=\"".$db->escape_string($susr)."\"");
		if($abf==false)
			throw new DB_Exception(404, "Kein Eintrag zu Session-ID gefunden. ID: ".$_POST[DBA_SESSION_ID], DB_ERR_VIEW_SESSION_NOT_FOUND);
		$row = mysqli_fetch_assoc($abf);
		if(!login($susr, $row[DB_F_KUNDEN_PASSWORT]))
			throw new DB_Exception(401, "Login fehlgeschlagen!", DB_ERR_VIEW_BAD_LOGIN);
	}catch(DB_Exception $e){
		echo json_encode($e);
		exit(1);
	}catch(Exception $e){
		echo json_encode(new DB_Exception(500, "Unbekannter Fehler bei relogin via Session-ID! Fehlermessage: ".$e->getMessage(), DB_ERR_VIEW_UK_FAIL));
		exit(1);
	}
}

if(!isset($_GET[DBA_FUNCTION])){
	echo json_encode(new DB_Exception(400, "Keine Funktion angegeben!", DB_ERR_VIEW_NO_FUNCTION));
	exit(1);
}
$function = $_GET[DBA_FUNCTION];
if(strcmp($function,DBA_F_KUNDEEINTRAGEN)!=0 and !isset($_SESSION[L_ANGEMELDET])){
	echo json_encode(new DB_Exception(401, "Erweiterter Funktionsaufruf ohne Login!", DB_ERR_VIEW_UNAUTHORIZED));
	die();
}
$args = array();
if(!in_array($function, $DBA_FUNCTIONS)){
	echo json_encode(new DB_Exception(501, "Übergebene Funktion ist nicht in der Liste unterstützter funktionen! Gesuchte Funktion: ".$function, DB_ERR_VIEW_METHOD_NOT_SUPPORTED));
	die;
}
$res = true;
switch ($function){
	case DBA_F_KUNDEEINTRAGEN:
		try {
			$kunde = readKunde();
			$kunde->setFreischaltung(false);
			$res = $db->kundeEintragen($kunde);
			if (!$res)
				throw new DB_Exception(500, "Registrierung fehlgeschlagen!".var_export($res,true), "Registrierung fehlgeschlagen!");
			array_push($args, $kunde);
			array_push($args, $_POST[DBA_P_PASSWORT]);
			$function = DBA_F_KUNDEPWUPDATEN;
		}catch(DB_Exception $e){
			echo json_encode($e);
			exit(1);
		}
		break;
	case DBA_F_KUNDEPWUPDATEN:
		try {
			array_push($args, readKunde());
			array_push($args, $_POST[DBA_P_PASSWORT]);
		}catch(DB_Exception $e){
			echo json_encode($e);
			exit(1);
		}
		break;
	case DBA_F_KUNDEUPDATEN:
		try{
			$kunde_neu = readKunde();
			$kunde = $db->getKunde($kunde_neu->getEmail());
			if($kunde_neu->getVorname() != null) 
				$kunde->setVorname($kunde_neu->getVorname());
			if($kunde_neu->getNachname() != null) 
				$kunde->setNachname($kunde_neu->getNachname());
			if($kunde_neu->getTelNr() != null) 
				$kunde->setTelNr($kunde_neu->getTelNr());
			if($kunde_neu->getFoto() != null) 
				$kunde->setFoto($kunde_neu->getFoto());
			
			if(isset($_POST[DBA_P_KUNDE_INTERESSEN]))
				$kunde->setInteressen($kunde_neu->getInteressen());
			$args = array($kunde);
		}catch (DB_Exception $e){
			echo json_encode($e);
			exit(1);
		}
			break;
	default:
		echo json_encode(new DB_Exception(501, "Übergebene Funktion wird nicht unterstützt! Gesuchte Funktion: ".$function, DB_ERR_VIEW_METHOD_NOT_SUPPORTED));
		die;
		break;
}
$res = call_user_func_array(array($db,$function), $args);

if($function == DBA_F_KUNDEUPDATEN && isset($_POST[DBA_P_PASSWORT]) && $res){
	try {
		$res = $db->kundePwUpdaten(readKunde(), $_POST[DBA_P_PASSWORT]);
	}catch(DB_Exception $e){
		echo json_encode($e);
		exit(1);
	}
}
if($_GET[DBA_FUNCTION]==DBA_F_KUNDEEINTRAGEN){
	if($res){
		$_POST[L_USERNAME] = readKunde()->getEmail();
		$_POST[L_PASSWORT] = $_POST[DBA_P_PASSWORT];
		include("login.php");
		exit(0);
	}
}
echo json_encode(array("res" => $res));
?>