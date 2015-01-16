<?php
include_once 'Arbeitsplatzausstattung.php';
class Arbeitsplatz{
	private $nummer;
	private $name;
	private $ausstattung = array();
	
	
	function __construct($nummer, $name, array $ausstattung){
		$this->setNummer($nummer);
		$this->setName($name);
		$this->setAusstattung($ausstattung);
	}
	
	
	function setNummer($nummer){
		if(is_int($nummer))
			$this->nummer = $nummer;
		else
			throw new Exception("Nummer ung�ltig!");
	}
	
	function setName($name){
		if(is_string($name))
			$this->name = $name;
		else
			throw new Exception("Name ung�ltig!");
	}
	
	function setAusstattung(array $ausstattung){
		foreach($ausstattung as $arbeitsplatzausstattung)
			if(!($arbeitsplatzausstattung instanceof Arbeitsplatzausstattung))
				throw new Exception("Ausstattung ung�ltig!");
	}
}
?>