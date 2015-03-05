<?php
include_once dirname(__FILE__)."/".'Arbeitsplatzausstattung.php';
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
		try{
			$nummer=(int)$nummer;
		}
		catch(Exception $e){}
		if(is_int($nummer))
			$this->nummer = $nummer;
		else
			throw new Exception("Nummer ungültig!");
	}
	
	function setName($name){
		if(is_string($name))
			$this->name = $name;
		else
			throw new Exception("Name ungültig!");
	}
	
	function setAusstattung(array $ausstattung){
		foreach($ausstattung as $arbeitsplatzausstattung)
			if(!($arbeitsplatzausstattung instanceof Arbeitsplatzausstattung))
				throw new Exception("Ausstattung ungültig!");
		$this->ausstattung = $ausstattung;
	}
	
	
	function getNummer(){
			return $this->nummer;
	}
	
	function getName(){
			return $this->name;
	}
	
	function getAusstattung(){
		return $this->ausstattung;
	}
}
?>