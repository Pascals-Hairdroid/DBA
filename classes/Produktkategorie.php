<?php 
class Produktkategorie{
	public $kuerzel;
	public $bezeichnung;
	
	
	function __construct($kuerzel, $bezeichnung){
		$this->setBezeichnung($bezeichnung);
		$this->setKuerzel($kuerzel);
	}
	
	
	
	function setKuerzel($kuerzel){
		try{
			$kuerzel = (string)$kuerzel;
		}catch (Exception $e){}
		if(is_string($kuerzel))
			$this->kuerzel = $kuerzel;
		else
			throw new Exception("Kuerzel ungültig!");
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
	
	function getKuerzel(){
		return $this->kuerzel;
	}
	
	
	function getBezeichnung(){
		return $this->bezeichnung;
	}
	
}
?>