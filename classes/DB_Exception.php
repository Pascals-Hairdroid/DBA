<?php
class DB_Exception extends Exception{
	public $errc;	// Error Code
	public $msg; 	// Message
	public $viewmsg; 	// User View Message
	
	function __construct($errc, $msg, $viewmsg){
		$this->setErrc($errc);
		$this->setMsg($msg);
		$this->setViewmsg($viewmsg);
	}
	
	function setErrc($errc){
		try{
			$errc=(int)$errc;
		}
		catch(Exception $e){}
		if(is_int($errc))
			$this->errc = $errc;
		else
			throw new Exception("Errorcode ungültig!");
	}
	
	function setMsg($msg){
		try{
			$msg = (string)$msg;
		}catch (Exception $e){}
		if(!is_string($msg))
			throw new Exception("Message ungültig!");
		$this->msg = utf8_encode($msg);
		$this->message = $this->msg;
	}
	
	function setViewmsg($viewmsg){
		try{
			$viewmsg = (string)$viewmsg;
		}catch (Exception $e){}
		if(!is_string($viewmsg))
			throw new Exception("Viewmessage ungültig!");
		$this->viewmsg = utf8_encode($viewmsg);
	}
	
	
	function getErrc(){
		return $this->errc;
	}
	
	function getMsg(){
		return utf8_decode($this->msg);
	}
	
	function getViewmsg(){
		return utf8_decode($this->viewmsg);
	}
	
}
?>