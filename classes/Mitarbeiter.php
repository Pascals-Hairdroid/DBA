<?php 
include_once dirname(__FILE__)."/".'Skill.php';
include_once dirname(__FILE__)."/".'Urlaub.php';
include_once dirname(__FILE__)."/".'Dienstzeit.php';

class Mitarbeiter {
	public $svnr;
	public $vorname;
	public $nachname;
	public $motto;
	public $skills = array();
	public $admin;
	public $urlaube = array();
	public $dienstzeiten = array();
	
	function __construct($svnr, $vorname, $nachname, $motto,  array $skills, $admin, array $urlaube, array $dienstzeiten){
		$this->setSvnr($svnr);
		$this->setVorname($vorname);
		$this->setNachname($nachname);
		$this->setMotto($motto);
		$this->setSkills($skills);
		$this->setAdmin($admin);
		$this->setUrlaube($urlaube);
		$this->setDienstzeiten($dienstzeiten);
	}
	
	function setSvnr($svnr){
		if(is_numeric($svnr))
			$this->svnr = $svnr;
		else
			throw new Exception("SVNr. ungültig!");
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
	
	function setMotto($motto){
		try{
			$motto = (string)$motto;
		}catch (Exception $e){}
		if(is_string($motto))
			$this->motto = utf8_encode($motto);
		else
			throw new Exception("Motto ungültig!");
	}
	
	function setSkills(array $skills){
		foreach ($skills as $skill){
			if(!($skill instanceof Skill))
				throw new Exception("Skill ungültig!");
		}
		$this->skills = $skills;
	}
	
	function setAdmin($admin){
		try{
			$admin=(bool)$admin;
		} catch(Exception $e){}
		if(is_bool($admin))
			$this->admin = $admin;
		else
			throw new Exception("Admin ungültig!");
	}
	
	function setUrlaube(array $urlaube){
		foreach ($urlaube as $urlaub){
			if(!($urlaub instanceof Urlaub))
				throw new Exception("Urlaub ungültig!");
		}
		$this->urlaube = $urlaube;
	}
	
	function setDienstzeiten(array $dienstzeiten){
		foreach ($dienstzeiten as $dienstzeit){
			if(!($dienstzeit instanceof Dienstzeit))
				throw new Exception("Dienstzeit ungültig!");
		}
		$this->dienstzeiten = $dienstzeiten;
	}
	

	function getSvnr(){
		return $this->svnr;
	}
	
	function getVorname(){
		return utf8_decode($this->vorname);
	}
	
	function getNachname(){
		return utf8_decode($this->nachname);
	}
	
	function getMotto(){
		return utf8_decode($this->motto);
	}
	
	function getSkills(){
		return $this->skills;
	}
	
	function getAdmin(){
		return $this->admin;
	}
	
	function getUrlaube(){
		return $this->urlaube;
	}
	
	function getDienstzeiten(){
		return $this->dienstzeiten;
	}
}
?>