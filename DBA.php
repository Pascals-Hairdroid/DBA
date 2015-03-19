<?php
include_once 'include_login.php';
include_once 'classes/DB_Exception.php';
include_once 'conf/db_exception_const.php';
include_once 'conf/login_const.php';
include_once 'conf/dba_const.php';
include_once "DB_Con.php";

function objectToObject($instance, $className) {
	return unserialize(sprintf(
			'O:%d:"%s"%s',
			strlen($className),
			$className,
			strstr(strstr(serialize($instance), '"'), ':')
	));
}

function toKunde($kd){
	$kunde = objectToObject($kd, Kunde::NAME);
	foreach($kunde->getInteressen() as $interesse){
		array_push($interessen, objectToObject($interesse, Interesse::NAME));
	}
}
$db = new DB_Con("conf/db.php", true);
if(isset($_POST[DBA_SESSION_ID])){
	session_start($_POST[DBA_SESSION_ID]);
	try{
		$susr = $db->getKundeMailBySessionId($_POST[DBA_SESSION_ID]);
		$abf = $db->selectQuery(DB_TB_KUNDEN, DB_F_KUNDEN_PASSWORT, DB_F_KUNDEN_PK_EMAIL."=\"".$db->escape_string($susr)."\"");
		if($abf==false)
			throw new DB_Exception(404, "Kein Eintrag zu Session-ID gefunden. ID: ".$_POST[DBA_SESSION_ID], DB_ERR_VIEW_SESSION_NOT_FOUND);
		$row = mysqli_fetch_assoc($abf);
		login($susr, $row[DB_F_KUNDEN_PASSWORT]);
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
if(strcmp($function,DBA_F_KUNDEEINTRAGEN)!=0 and !isset($_SESSION[L_ANGEMELDET]))
	die();
if(isset($_POST[DBA_JSON_PARAMS]))
	$post = $_POST[DBA_JSON_PARAMS];
try{
	$vargs = json_decode($post);
} catch(Exception $e){
	echo json_encode(new DB_Exception(400, "Fehler beim json-decoden der Parameter! Fehlermessage: ".$e->getMessage(), DB_ERR_VIEW_DECODE_FAIL));
}
$args = array();
if(!in_array($function, $DBA_FUNCTIONS)){
	echo "ERROR: METHOD NOT SUPPORTED!";
	die;
}
switch ($function){
	case DBA_F_KUNDEEINTRAGEN:
		try {
			$interessen = array();
			if(isset($vargs[0]) and isset($vargs[1])){
				array_push($args, toKunde($vargs[0]));
				array_push($args, $vargs[1]);
				$db->kundeEintragen($args[0]);
				$function = DBA_F_KUNDEPWUPDATEN;
			}
		}catch(DB_Exception $e){
			echo json_encode($e);
			exit(1);
		}
	break;
	case DBA_F_KUNDEPWUPDATEN:
		try {
			array_push($args, $_SESSION[L_ADMIN]?toKunde($args[0]):new Kunde($_SESSION[L_USERNAME], null, null, null, false, null, array()));
			array_push($args, $vargs[1]);
		}catch (DB_Exception $e){
			
		}catch(DB_Exception $e){
			echo json_encode($e);
			exit(1);
		}
		break;
}

$res = call_user_func_array(array($db,$function), $args);
echo json_encode($res);
?>