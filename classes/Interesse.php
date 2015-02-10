<?php 
class Interesse{
	private $id;
	private $bezeichnung;
	
	function __construct($id, $bezeichnung){
		$this->setId($id);
		$this->setBezeichnung($bezeichnung);
	}
	
	
	function setId($id){
		if(is_int($id))
			$this->id = $id;
		else
			throw new Exception("Id ung�ltig!");
	}
	
	function setBezeichnung($bezeichnung){
		if(is_string($bezeichnung))
			$this->bezeichnung = $bezeichnung;
		else
			throw new Exception("Bezeichnung ung�ltig!");
	}
	
	
	function getId(){
			return $this->id;
	}
	
	function getBezeichnung(){
			return $this->bezeichnung;
	}
}
?>