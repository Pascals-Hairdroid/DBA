<?php 
class Interesse{
	const NAME = "Interesse";
	public $id;
	public $bezeichnung;
	
	function __construct($id, $bezeichnung){
		$this->setId($id);
		$this->setBezeichnung($bezeichnung);
	}
	
	
	function setId($id){
		try{
			$id=(int)$id;
		}
		catch(Exception $e){}
		if(is_int($id))
			$this->id = $id;
		else
			throw new Exception("Id ungültig!");
	}
	
	function setBezeichnung($bezeichnung){
		try{
			$bezeichnung = (string)$bezeichnung;
		}catch (Exception $e){}
		if(is_string($bezeichnung))
			$this->bezeichnung = $bezeichnung;
		else
			throw new Exception("Bezeichnung ungültig!");
	}
	
	
	function getId(){
			return $this->id;
	}
	
	function getBezeichnung(){
			return $this->bezeichnung;
	}
}
?>