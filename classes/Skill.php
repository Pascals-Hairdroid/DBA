<?php 
class Skill {
	private $id;
	private $beschreibung;
	
	function __construct($id, $beschreibung){
		$this->setId($id);
		$this->setBeschreibung($beschreibung);
	}
	
	
	function setId($id){
		try{
			$id=(int)$id;
		}
		catch(Exception $e){}
		if(is_int($id))
			$this->id = $id;
		else 
			throw new Exception("Id ung�ltig!");
	}
	
	function setBeschreibung($beschreibung){
		if(is_string($beschreibung))
			$this->beschreibung = $beschreibung;
		else
			throw new Exception("Beschriebung ung�ltig!");
	}


	function getId(){
			return $this->id;
	}
	
	function getBeschreibung(){
			return $this->beschreibung;
	}
}
?>