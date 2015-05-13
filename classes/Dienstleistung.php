<?php
include_once dirname(__FILE__)."/".'Skill.php';
include_once dirname(__FILE__)."/".'Arbeitsplatzausstattung.php';
include_once dirname(__FILE__)."/".'Haartyp.php';
class Dienstleistung{
	const NAME = "Dienstleistung";
	public $kuerzel;
	public $haartyp;
	public $name;
	public $benoetigteEinheiten;
	public $pausenEinheiten;
	public $skills = array();
	public $arbeitsplatzausstattungen = array();
	public $gruppierung;
	
	function __construct($kuerzel, Haartyp $haartyp, $name, $benoetigteEinheiten, $pausenEinheiten, array $skills, array $arbeitsplatzausstattungen, $gruppierung){
		$this->setKuerzel($kuerzel);
		$this->setName($name);
		$this->setBenoetigteEinheiten($benoetigteEinheiten);
		$this->setPausenEinheiten($pausenEinheiten);
		$this->setSkills($skills);
		$this->setArbeitsplatzausstattungen($arbeitsplatzausstattungen);
		$this->setGruppierung($gruppierung);
		$this->setHaartyp($haartyp);
	}
	
	function setKuerzel($kuerzel){
		try{
			$kuerzel = (string)$kuerzel;
		}catch (Exception $e){}
		if(is_string($kuerzel))
			$this->kuerzel = utf8_encode($kuerzel);
		else
			throw new Exception("Kuerzel ungültig!");
	}
	
	function setHaartyp(Haartyp $haartyp){
		$this->haartyp = $haartyp;
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
	
	function setBenoetigteEinheiten($benoetigteEinheiten){
		try{
			$benoetigteEinheiten=(int)$benoetigteEinheiten;
		}
		catch(Exception $e){}
		if(is_int($benoetigteEinheiten))
			$this->benoetigteEinheiten = $benoetigteEinheiten;
		else
			throw new Exception("Benoetigte Einheiten ungültig!");
	}
	
	function setPausenEinheiten($pausenEinheiten){
		try{
			$pausenEinheiten=(int)$pausenEinheiten;
		}
		catch(Exception $e){}
		if(is_int($pausenEinheiten))
			$this->pausenEinheiten = $pausenEinheiten;
		else
			throw new Exception("Pausen Einheiten ungültig!");
	}
	
	function setSkills(array $skills){
		foreach($skills as $skill)
		if(!($skill instanceof Skill))
			throw new Exception("Skill ungültig!");
		$this->skills = $skills;
	}
	
	function setArbeitsplatzausstattungen(array $arbeitsplatzausstattungen){
		foreach($arbeitsplatzausstattungen as $arbeitsplatzausstattung)
		if(!($arbeitsplatzausstattung instanceof Arbeitsplatzausstattung))
			throw new Exception("Arbeitsplatzausstattung ungültig!");
		$this->arbeitsplatzausstattungen = $arbeitsplatzausstattungen;
	}
	
	function setGruppierung($gruppierung){
		try{
			$gruppierung=(int)$gruppierung;
		}
		catch(Exception $e){}
		if(is_int($gruppierung))
			$this->gruppierung = $gruppierung;
		else
			throw new Exception("Gruppierung ungültig!");
	}
	

	function getKuerzel(){
			return utf8_decode($this->kuerzel);
	}
	
	function getHaartyp(){
		return $this->haartyp;
	}
	
	function getName(){
		return utf8_decode($this->name);
	}
	
	function getBenoetigteEinheiten(){
		return $this->benoetigteEinheiten;
	}
	
	function getPausenEinheiten(){
		return $this->pausenEinheiten;
	}
	
	function getSkills(){
		return $this->skills;
	}
	
	function getArbeitsplatzausstattungen(){
		return $this->arbeitsplatzausstattungen;
	}
	
	function getGruppierung(){
		return $this->gruppierung;
	}
}
?>