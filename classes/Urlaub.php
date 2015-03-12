<?php
class Urlaub{
	public $beginn;
	public $ende;
	
	function __construct(DateTime $beginn, DateTime $ende){
		$this->setBeginn($beginn);
		$this->setEnde($ende);
	}
	
	
	function setBeginn(DateTime $beginn){
		$this->beginn = $beginn;
	}
	
	function setEnde(DateTime $ende){
		$this->ende = $ende;
	}
	
	
	function getBeginn(){
		return $this->beginn;
	}
	
	function getEnde(){
		return $this->ende;
	}
	
}
?>