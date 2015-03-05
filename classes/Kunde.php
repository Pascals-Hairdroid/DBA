<?php
include_once 'Interesse.php';
class Kunde {
	private $email;
	private $vorname;
	private $nachname;
	private $telNr;
	private $freischaltung;
	private $foto;
	private $interessen = array();

	function __construct($email, $vorname, $nachname, $telNr, $freischaltung, $foto, array $interessen){
		$this->setEmail($email);
		$this->setVorname($vorname);
		$this->setNachname($nachname);
		$this->setTelNr($telNr);
		$this->setFreischaltung($freischaltung);
		$this->setFoto($foto);
		$this->setInteressen($interessen);
	}
	
	
	function setEmail($email){
		if(is_string($email))
			$this->email = $email;
		else
			throw new Exception("E-Mail ungültig!");
	}
	
	function setVorname($vorname){
		if(is_string($vorname))
			$this->vorname = $vorname;
		else
			throw new Exception("Vorname ungültig!");
	}
	
	function setNachname($nachname){
		if(is_string($nachname))
			$this->nachname = $nachname;
		else
			throw new Exception("Nachname ungültig!");
	}
	

	function setTelNr($telNr){
		if(is_string($telNr))
			$this->telNr = $telNr;
		else
			throw new Exception("Telefonnummer ungültig!");
	}
	
	function setFreischaltung($freischaltung){
		try{
			$freischaltung=(bool)$freischaltung;
		} catch(Exception $e){}
		if(is_bool($freischaltung))
			$this->freischaltung = $freischaltung;
		else
			throw new Exception("Freischaltung ungültig!");
	}
	
	function setFoto($foto){
		if(is_string($foto) || $foto == null)
			$this->foto = $foto;
		else
			throw new Exception("Foto ungültig!");
	}
	
	function setInteressen(array $interessen){
		foreach ($interessen as $interesse){
			if(!($interesse instanceof Interesse))
				throw new Exception("Interesse ungültig!");
		}
		$this->interessen = $interessen;
	}
	
	
	function getEmail(){
		return $this->email;
	}
	
	function getVorname(){
		return $this->vorname;
	}
	
	function getNachname(){
		return $this->nachname;
	}
	

	function getTelNr(){
		return $this->telNr;
	}
	
	function getFreischaltung(){
		return $this->freischaltung;
	}
	
	function getFoto(){
		return $this->foto;
	}
	
	function getInteressen(){
		return $this->interessen;
	}
}
?>