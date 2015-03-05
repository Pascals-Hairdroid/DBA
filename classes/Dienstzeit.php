<?php
include_once dirname(__FILE__)."/".'Wochentag.php';
class Dienstzeit{
	private $wochentag;
	private $beginn;
	private $ende;

	function __construct(Wochentag $wochentag, DateTime $beginn, DateTime $ende){
		$this->setBeginn($beginn);
		$this->setEnde($ende);
		$this->setWochentag($wochentag);
	}

	function setWochentag(Wochentag $wochentag){
		$this->wochentag = $wochentag;
	}
	
	function setBeginn(DateTime $beginn){
		$this->beginn = $beginn;
	}
	
	function setEnde(DateTime $ende){
		$this->ende = $ende;
	}
	

	function getWochentag(){
		return $this->wochentag;
	}
	
	function getBeginn(){
		return $this->beginn;
	}
	
	function getEnde(){
		return $this->ende;
	}
	
}
?>