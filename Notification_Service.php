<?php
include_once 'DB_Con.php';
include_once 'conf/db_const.php';
include_once 'classes/DB_Exception.php';
include_once 'conf/db_exception_const.php';
include_once 'conf/notification_service_const.php';
// Datumsparameter einlesen
try{
	$date = new DateTime();
	if(isset($_GET[NS_DATE]))
		$date->setTimestamp($_GET[NS_DATE]);
	elseif(isset($_POST[NS_DATE]))
      	$date->setTimestamp($_POST[NS_DATE]);
	else
		$date->setTimestamp(date('U'));
    
}catch(DB_Exception $e){
	echo json_encode($e);
	exit(1);
}catch(Exception $e){
	echo json_encode(new DB_Exception(400, "Falsches DateTime-Format. Message: ".$e->getMessage(), utf8_encode(DB_ERR_VIEW_PARAM_FAIL)));
	exit(1);
}
$db = new DB_Con(DB_DEFAULT_CONF_FILE, true, "utf8");
$res = array(false);
// Werbung holen
try{
	if(isset($_POST[NS_INTERESSEN])||isset($_POST[NS_EMAIL])){
		$interessen = array();
		if(isset($_POST[NS_INTERESSEN]))
			foreach($_POST[NS_INTERESSEN] as $id)
				if($id != "")
        			array_push($interessen, new Interesse($id, ""));
		else 
			$interessen = $db->getKunde($_POST[NS_EMAIL])->getInteressen();
		
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
// Antworten
echo json_encode(array(werbungen => $res, lastSync => date('U').""));
?>