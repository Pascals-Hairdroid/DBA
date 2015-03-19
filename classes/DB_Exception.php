<?php
class DB_Exception extends Exception{
	public $errc;	// Error Code
	public $msg; 	// Message
	public $viewmsg; 	// User View Message
	
	function __construct($errc, $msg, $viewmsg){
		$this->errc = $errc;
		$this->msg = $msg;
		$this->viewmsg = $viewmsg;
	}
}
?>