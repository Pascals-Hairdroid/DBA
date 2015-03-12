<?php
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
session_start();
$function = $_GET[DBA_FUNCTION];
if(strcmp($function,DBA_F_KUNDEEINTRAGEN)!=0 and !isset($_SESSION[L_ANGEMELDET]))
	die();
if(isset($_POST[DBA_JSON_PARAMS]))
	$post = $_POST[DBA_JSON_PARAMS];
try{
	$vargs = json_decode($post);
} catch(Exception $e){
	echo "ERROR: JSON-ERROR: ".$e->getMessage();
}
$args = array();
if(!in_array($function, $DBA_FUNCTIONS)){
	echo "ERROR: METHOD NOT SUPPORTED!";
	die;
}
$db = new DB_Con("conf/db.php", true);
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
		}catch(Exception $e){
			echo "ERROR: ".$e->getMessage();
			exit(1);
		}
	break;
	case DBA_F_KUNDEPWUPDATEN:
		try {
			array_push($args, $_SESSION[L_ADMIN]?toKunde($args[0]):new Kunde($_SESSION[L_USERNAME], null, null, null, false, null, array()));
			array_push($args, $vargs[1]);
		}catch(Exception $e){
			echo "ERROR: ".$e->getMessage();
			exit(1);
		}
		break;
}

$res = call_user_func_array(array($db,$function), $args);
echo json_encode($res);
?>