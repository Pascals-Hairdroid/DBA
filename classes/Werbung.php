<?php
include_once dirname(__FILE__)."/".'Interesse.php';
class Werbung {
	public $nummer;
	public $titel; 				// + in DB: varchar(45)
	public $text; 				// + in DB: varchar(500)
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
			throw new Exception("Nummer ungültig!");
	}
	
	function setTitel($titel){
		try{
			$titel = (string)$titel;
		}catch (Exception $e){}
		if(is_string($titel))
			$this->titel = $titel;
		else
			throw new Exception("Titel ungültig!");
	}
	
	function setText($text){
		try{
			$text = (string)$text;
		}catch (Exception $e){}
		if(is_string($text))
			$this->text = $text;
		else
			throw new Exception("Text ungültig!");
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
	
	function getTitel(){
		return $this->titel;
	}
	
	function getText(){
		return $this->text;
	}
	
	function getInteressen(){
		return $this->interessen;
	}
}
?>