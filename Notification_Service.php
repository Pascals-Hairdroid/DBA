<?php
include_once 'DB_Con.php';
include_once 'conf/db_const.php';
include_once 'classes/DB_Exception.php';
include_once 'conf/db_exception_const.php';
include_once 'conf/notification_service_const.php';

// if(!isset($_GET[NS_DATE])){
// 	echo "test";
// 	echo json_encode(new DB_Exception(400, "Kein Datum gegeben.", DB_ERR_VIEW_PARAM_FAIL));
// 	exit(1);
// }
try{
	if(!isset($_GET[NS_DATE]))
		throw new DB_Exception(400, "Kein Datum angegeben.", utf8_encode(DB_ERR_VIEW_PARAM_FAIL));
	$date = new DateTime();
	$date->setTimestamp($_GET[NS_DATE]);
}catch(DB_Exception $e){
	echo json_encode($e);
	exit(1);
}catch(Exception $e){
	echo json_encode(new DB_Exception(400, "Falsches DateTime-Format. Message: ".$e->getMessage(), utf8_encode(DB_ERR_VIEW_PARAM_FAIL)));
	exit(1);
}
$db = new DB_Con(DB_DEFAULT_CONF_FILE, true, "utf8");
$res = array(false);
try{
	if(isset($_GET[NS_INTERESSEN])){
		$interessen = array();
		foreach($_GET[NS_INTERESSEN] as $id)
			array_push($interessen, new Interesse($id, ""));
		$res=$db->getAllWerbung($date, $interessen);
	}
	else
		$res=$db->getAllWerbung($date);
}catch(DB_Exception $e){
	echo json_encode($e);
	exit(1);
}catch(Exception $e){
	echo json_encode(new DB_Exception(500, "MESSAGE: ".$e->getMessage(), utf8_encode(DB_ERR_VIEW_UK_FAIL)));
	exit(1);
}
echo json_encode(array(werbungen => $res, lastSync => date('U')+""));
?>