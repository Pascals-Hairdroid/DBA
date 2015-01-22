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
}
?>