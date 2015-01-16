<?php 
class Mitarbeiter {
	private $svnr;
	private $vorname;
	private $nachname;
	private $skills = array();
	private $admin;
	
	function __construct($svnr, $vorname, $nachname, array $skills, $admin){
		$this->setSvnr($svnr);
		$this->setVorname($vorname);
		$this->setNachname($nachname);
		$this->setSkills($skills);
		$this->setAdmin($admin);
	}
	
	function setSvnr($svnr){
		if(is_int($svnr))
			$this->svnr = $svnr;
		else
			throw new Exception("SVNr. ung�ltig!");
	}
	
	function setVorname($vorname){
		if(is_string($vorname))
			$this->vorname = $vorname;
		else
			throw new Exception("Vorname ung�ltig!");
	}
	
	function setNachname($nachname){
		if(is_string($nachname))
			$this->nachname = $nachname;
		else
			throw new Exception("Nachname ung�ltig!");
	}
	
	function setSkills(array $skills){
		foreach ($skills as $skill){
			if(!($skill instanceof Skill))
				throw new Exception("Skills ung�ltig!");
		}
		$this->skills = $skills;
	}
	
	function setAdmin($admin){
		if(is_bool($admin))
			$this->admin = $admin;
		else
			throw new Exception("Admin ung�ltig!");
	}
}
?>