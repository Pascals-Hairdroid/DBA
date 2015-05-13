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
			$this->kuerzel = utf8_encode($kuerzel);
		else
			throw new Exception("Kuerzel ungültig!");
	}
	
	function setBezeichnung($bezeichnung){
		try{
			$bezeichnung = (string)$bezeichnung;
		}catch (Exception $e){}
		if(is_string($bezeichnung))
			$this->bezeichnung = utf8_encode($bezeichnung);
		else
			throw new Exception("Bezeichnung ungültig!");
	}
	
	function getKuerzel(){
		return utf8_decode($this->kuerzel);
	}
	
	
	function getBezeichnung(){
		return utf8_decode($this->bezeichnung);
	}
	
}
?>