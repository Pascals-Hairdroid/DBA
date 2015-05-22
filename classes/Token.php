<?php
include_once dirname(__FILE__)."/".'Interesse.php';
class Token {
	public $token;
	public $timestamp;

	function __construct($token, DateTime $timestamp){
		$this->setToken($token);
		$this->setTimestamp($timestamp);
	}
	
	
	function setToken($token){
		try{
			$token = (string)$token;
		}catch (Exception $e){}
		if(is_string($token))
			$this->token = utf8_encode($token);
		else
			throw new Exception("Token ungültig!");
	}
	
	function setTimestamp(DateTime $timestamp){
		$this->timestamp = $timestamp;
	}
	
	
	function getToken(){
		return utf8_decode($this->token);
	}
	
	function getTimestamp(){
		return $this->timestamp;
	}
}
?>