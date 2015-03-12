<?php 
class Produkt{
	public $id;
	public $name;
	public $hersteller;
	public $beschreibung;
	public $preis;
	public $bestand;
	
	
	function __construct($id, $name, $hersteller, $beschreibung, $preis, $bestand){
		$this->setId($id);
		$this->setName($name);
		$this->setHersteller($hersteller);
		$this->setBeschreibung($beschreibung);
		$this->setPreis($preis);
		$this->setBestand($bestand);
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
	
	function setName($name){
		try{
			$name = (string)$name;
		}catch (Exception $e){}
		if(is_string($name))
			$this->name = $name;
		else
			throw new Exception("Name ungültig!");
	}
	
	function setHersteller($hersteller){
		try{
			$hersteller = (string)$hersteller;
		}catch (Exception $e){}
		if(is_string($hersteller))
			$this->hersteller = $hersteller;
		else
			throw new Exception("Hersteller ungültig!");
	}
	
	function setBeschreibung($beschreibung){
		try{
			$beschreibung = (string)$beschreibung;
		}catch (Exception $e){}
		if(is_string($beschreibung))
			$this->beschreibung = $beschreibung;
		else
			throw new Exception("Beschreibung ungültig!");
	}
	
	function setPreis($preis){
		try{
			$preis=(double)$preis;
		}
		catch(Exception $e){}
		if(is_double($preis))
			$this->preis = $preis;
		else
			throw new Exception("Preis ungültig!");
	}
	
	
	function setBestand($bestand){
		try{
			$bestand=(int)$bestand;
		}
		catch(Exception $e){}
		if(is_int($bestand))
			$this->bestand = $bestand;
		else
			throw new Exception("Bestand ungültig!");
	}
	
	
	function getId(){
		$this->id;
	}
	
	function getName(){
		$this->name;
	}
	
	function getHersteller(){
		$this->hersteller;
	}
	
	function getBeschreibung(){
		$this->beschreibung;
	}
	
	function getPreis(){
		$this->preis;
	}
	
	
	function getBestand(){
		$this->bestand;
	}
	
}
?>