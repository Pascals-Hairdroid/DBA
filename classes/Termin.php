<?php
include_once dirname(__FILE__)."/".'Mitarbeiter.php';
include_once dirname(__FILE__)."/".'Arbeitsplatz.php';
include_once dirname(__FILE__)."/".'Kunde.php';
include_once dirname(__FILE__)."/".'Dienstleistung.php';

class Termin {
	public $zeitstempel;
	public $mitarbeiter;
	public $arbeitsplatz;
	public $kunde;
	public $frisurwunsch;
	public $dienstleistung;

	function __construct(DateTime $zeitstempel, Mitarbeiter $mitarbeiter, Arbeitsplatz $arbeitsplatz, Kunde $kunde, $frisurwunsch, Dienstleistung $dienstleistung){
		$this->setZeitstempel($zeitstempel);
		$this->setMitarbeiter($mitarbeiter);
		$this->setArbeitsplatz($arbeitsplatz);
		$this->setKunde($kunde);
		$this->setFrisurwunsch($frisurwunsch);
		$this->setDienstleistung($dienstleistung);
	}
	

	function setZeitstempel(DateTime $zeitstempel){
		$this->zeitstempel = $zeitstempel;
	}

	function setMitarbeiter(Mitarbeiter $mitarbeiter){
		$this->mitarbeiter = $mitarbeiter;
	}

	function setArbeitsplatz(Arbeitsplatz $arbeitsplatz){
		$this->arbeitsplatz = $arbeitsplatz;
	}

	function setKunde(Kunde $kunde){
		$this->kunde = $kunde;
	}
	
	function setFrisurwunsch($frisurwunsch){
		try{
			$frisurwunsch = (string)$frisurwunsch;
		}catch (Exception $e){}
		if(is_string($frisurwunsch))
			$this->frisurwunsch = utf8_encode($frisurwunsch);
		else
			throw new Exception("Frisurwunsch ung�ltig!");
	}
	
	function setDienstleistung(Dienstleistung $dienstleistung){
		$this->dienstleistung = $dienstleistung;
	}
	

	function getZeitstempel(){
		return $this->zeitstempel;
	}

	function getMitarbeiter(){
		return $this->mitarbeiter;
	}

	function getArbeitsplatz(){
		return $this->arbeitsplatz;
	}

	function getKunde(){
		return $this->kunde;
	}
	
	function getFrisurwunsch(){
		return utf8_decode($this->frisurwunsch);
	}
	
	function getDienstleistung(){
		return $this->dienstleistung;
	}
}
?>