<?php 
include_once dirname(__FILE__)."/".'Skill.php';
include_once dirname(__FILE__)."/".'Urlaub.php';
include_once dirname(__FILE__)."/".'Dienstzeit.php';

class Mitarbeiter {
	private $svnr;
	private $vorname;
	private $nachname;
	private $skills = array();
	private $admin;
	private $urlaube = array();
	private $dienstzeiten = array();
	
	function __construct($svnr, $vorname, $nachname, array $skills, $admin, array $urlaube, array $dienstzeiten){
		$this->setSvnr($svnr);
		$this->setVorname($vorname);
		$this->setNachname($nachname);
		$this->setSkills($skills);
		$this->setAdmin($admin);
		$this->setDienstzeiten($dienstzeiten);
	}
	
	function setSvnr($svnr){
		try{
			$svnr=(int)$svnr;
		}
		catch(Exception $e){}
		if(is_int($svnr))
			$this->svnr = $svnr;
		else
			throw new Exception("SVNr. ungültig!");
	}
	
	function setVorname($vorname){
		try{
			$vorname = (string)$vorname;
		}catch (Exception $e){}
		if(is_string($vorname))
			$this->vorname = $vorname;
		else
			throw new Exception("Vorname ungültig!");
	}
	
	function setNachname($nachname){
		try{
			$nachname = (string)$nachname;
		}catch (Exception $e){}
		if(is_string($nachname))
			$this->nachname = $nachname;
		else
			throw new Exception("Nachname ungültig!");
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
		return $this->vorname;
	}
	
	function getNachname(){
		return $this->nachname;
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