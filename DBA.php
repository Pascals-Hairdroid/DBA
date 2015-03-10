<?php
include_once 'conf/login_const.php';
session_start();
if(!isset($_SESSION[L_ANGEMELDET]))
	die();
include_once "DB_Con.php";
include_once 'conf/dba_const.php';
if(isset($_POST[DBA_XML_PARAMS]))
	$post = $_POST[DBA_XML_PARAMS];
try{
	$vargs = new SimpleXMLElement($post);
} catch(Exception $e){
	echo "ERROR: XML-ERROR: ".$e->getMessage();
}
$args = array();
$function = $_GET[DBA_FUNCTION];
if(!in_array($function, $DBA_FUNCTIONS)){
	echo "ERROR: METHOD NOT SUPPORTED!";
	die;
}
$db = new DB_Con("conf/db.php", true);
switch ($function){
	case DBA_F_KUNDEEINTRAGEN:
		try {
			$interessen = array();
			if(isset($vargs->Kunde->Interessen))
				foreach($vargs->Kunde->Interessen->Interesse as $interesse){
					array_push($interessen, new Interesse($interesse->id, $interesse->bezeichnung));
				}
			array_push($args, new Kunde($vargs->Kunde->email, $vargs->Kunde->vorname, $vargs->Kunde->nachname, $vargs->Kunde->telNr, $_SESSION[L_ADMIN]?$vargs->Kunde->freischaltung:false, $vargs->Kunde->foto, $interessen));
		}catch(Exception $e){
			echo "ERROR: ".$e->getMessage();
			exit(1);	
		}
	break;
	case DBA_F_KUNDEPWUPDATEN:
		try {
			array_push($args, new Kunde($_SESSION[L_ADMIN]?$vargs->Kunde->email:$_SESSION[L_USERNAME], null, null, null, null, null, array()));
			array_push($args, $vargs->passwort);
		}catch(Exception $e){
			echo "ERROR: ".$e->getMessage();
			exit(1);
		}
		break;
}

$res = call_user_func_array(array($db,$function), $args);
echo serialize($res);
?>