<?php
include_once dirname(__FILE__)."/".'Interesse.php';
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
		try{
			$email = (string)$email;
		}catch (Exception $e){}
		if(is_string($email))
			$this->email = $email;
		else
			throw new Exception("E-Mail ung�ltig!");
	}
	
	function setVorname($vorname){
		try{
			$vorname = (string)$vorname;
		}catch (Exception $e){}
		if(is_string($vorname))
			$this->vorname = $vorname;
		else
			throw new Exception("Vorname ung�ltig!");
	}
	
	function setNachname($nachname){
		try{
			$nachname = (string)$nachname;
		}catch (Exception $e){}
		if(is_string($nachname))
			$this->nachname = $nachname;
		else
			throw new Exception("Nachname ung�ltig!");
	}
	

	function setTelNr($telNr){
		try{
			$telNr = (string)$telNr;
		}catch (Exception $e){}
		if(is_string($telNr))
			$this->telNr = $telNr;
		else
			throw new Exception("Telefonnummer ung�ltig!");
	}
	
	function setFreischaltung($freischaltung){
		try{
			$freischaltung=(bool)$freischaltung;
		} catch(Exception $e){}
		if(is_bool($freischaltung))
			$this->freischaltung = $freischaltung;
		else
			throw new Exception("Freischaltung ung�ltig!");
	}
	
	function setFoto($foto){
		try{
			$foto = (string)$foto;
		}catch (Exception $e){}
		if(is_string($foto) || $foto == null)
			$this->foto = $foto;
		else
			throw new Exception("Foto ung�ltig!");
	}
	
	function setInteressen(array $interessen){
		foreach ($interessen as $interesse){
			if(!($interesse instanceof Interesse))
				throw new Exception("Interesse ung�ltig!");
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