<?php
include_once dirname(__FILE__)."/".'Interesse.php';
class Werbung {
	public $nummer;
	public $interessen = array();

	function __construct($nummer, array $interessen){
		$this->setNummer($nummer);
		$this->setInteressen($interessen);
	}
	

	function setNummer($nummer){
		try{
			$nummer=(int)$nummer;
		}
		catch(Exception $e){}
		if(is_int($nummer))
			$this->nummer = $nummer;
		else
			throw new Exception("Nummer ung�ltig!");
	}

	function setInteressen(array $interessen){
		foreach ($interessen as $interesse){
			if(!($interesse instanceof Interesse))
				throw new Exception("Interesse ung�ltig!");
		}
		$this->interessen = $interessen;
	}


	function getNummer(){
		return $this->nummer;
	}
	
	function getInteressen(){
		return $this->interessen;
	}
}
?>