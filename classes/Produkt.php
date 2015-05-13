<?php 
class Produkt{
	public $id;
	public $name;
	public $hersteller;
	public $beschreibung;
	public $preis;
	public $bestand;
	public $kategorie;
	
	
	function __construct($id, $name, $hersteller, $beschreibung, $preis, $bestand, Produktkategorie $kategorie){
		$this->setId($id);
		$this->setName($name);
		$this->setHersteller($hersteller);
		$this->setBeschreibung($beschreibung);
		$this->setPreis($preis);
		$this->setBestand($bestand);
		$this->setKategorie($kategorie);
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
			$this->name = utf8_encode($name);
		else
			throw new Exception("Name ungültig!");
	}
	
	function setHersteller($hersteller){
		try{
			$hersteller = (string)$hersteller;
		}catch (Exception $e){}
		if(is_string($hersteller))
			$this->hersteller = utf8_encode($hersteller);
		else
			throw new Exception("Hersteller ungültig!");
	}
	
	function setKategorie(Produktkategorie $kategorie){
		$this->kategorie = $kategorie;
	}
	
	function setBeschreibung($beschreibung){
		try{
			$beschreibung = (string)$beschreibung;
		}catch (Exception $e){}
		if(is_string($beschreibung))
			$this->beschreibung = utf8_encode($beschreibung);
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
		return $this->id;
	}
	
	function getName(){
		return utf8_decode($this->name);
	}
	
	function getHersteller(){
		return utf8_decode($this->hersteller);
	}
	
	function getBeschreibung(){
		return utf8_decode($this->beschreibung);
	}
	
	function getPreis(){
		return $this->preis;
	}
	
	
	function getBestand(){
		return $this->bestand;
	}
	
	function getKategorie(){
		return $this->kategorie;
	}
	
}
?>