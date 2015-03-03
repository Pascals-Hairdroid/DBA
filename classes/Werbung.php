<?php
include_once 'Interesse.php';
class Werbung {
	private $nummer;
	private $interessen = array();

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
			throw new Exception("Nummer ungültig!");
	}

	function setInteressen(array $interessen){
		foreach ($interessen as $interesse){
			if(!($interesse instanceof Interesse))
				throw new Exception("Interesse ungültig!");
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