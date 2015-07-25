<?php
include_once dirname(__FILE__)."/".'Interesse.php';
class Kunde {
	const NAME = "Kunde";
	public $email;
	public $vorname;
	public $nachname;
	public $telNr;
	public $freischaltung;
	public $foto;
	public $interessen = array();

	function __construct($email, $vorname, $nachname, $telNr, $freischaltung, $foto, array $interessen){
		$this->setEmail($email);
		$this->setVorname($vorname);
		$this->setNachname($nachname);
		$this->setTelNr($telNr);
		$this->setFreischaltung($freischaltung);
		$this->setFoto($foto);
		$this->setInteressen($interessen);
	}
	
	// Setter
	function setEmail($email){
		try{
			$email = (string)$email;
		}catch (Exception $e){}
		if(is_string($email))
			$this->email = utf8_encode($email);
		else
			throw new Exception("E-Mail ungültig!");
	}
	
	function setVorname($vorname){
		try{
			$vorname = (string)$vorname;
		}catch (Exception $e){}
		if(is_string($vorname))
			$this->vorname = utf8_encode($vorname);
		else
			throw new Exception("Vorname ungültig!");
	}
	
	function setNachname($nachname){
		try{
			$nachname = (string)$nachname;
		}catch (Exception $e){}
		if(is_string($nachname))
			$this->nachname = utf8_encode($nachname);
		else
			throw new Exception("Nachname ungültig!");
	}
	

	function setTelNr($telNr){
		try{
			$telNr = (string)$telNr;
		}catch (Exception $e){}
		if(is_string($telNr))
			$this->telNr = utf8_encode($telNr);
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
		try{
			$foto = (string)$foto;
		}catch (Exception $e){}
		if(is_string($foto) || $foto == null)
			$this->foto = utf8_encode($foto);
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
	
	//Getter
	function getEmail(){
		return utf8_decode($this->email);
	}
	
	function getVorname(){
		return utf8_decode($this->vorname);
	}
	
	function getNachname(){
		return utf8_decode($this->nachname);
	}
	

	function getTelNr(){
		return utf8_decode($this->telNr);
	}
	
	function getFreischaltung(){
		return $this->freischaltung;
	}
	
	function getFoto(){
		return utf8_decode($this->foto);
	}
	
	function getInteressen(){
		return $this->interessen;
	}
}
?>