<?php
class Wochentag{
	private $kuerzel;
	private $bezeichnung;
	
	function __construct($kuerzel, $bezeichnung){
		$this->setKuerzel($kuerzel);
		$this->setBezeichnung($bezeichnung);
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