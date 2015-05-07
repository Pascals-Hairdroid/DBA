<?php
include_once dirname(__FILE__)."/".'../conf/db_const.php';
include_once dirname(__FILE__)."/".'Interesse.php';

function exists($url){
  $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    // don't download content
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(curl_exec($ch)!==FALSE)
    {
        return true;
    }
    else
    {
        return false;
    }
}

class Werbung {
	public $nummer;
	public $titel;
	public $text;
	public $datum;
	public $interessen = array();
	public $fotos = array();

	function __construct($nummer, $titel, $text, DateTime $datum, array $interessen, array $fotos = array()){
		$this->setNummer($nummer);
		$this->setTitel($titel);
		$this->setText($text);
		$this->setDatum($datum);
		$this->setInteressen($interessen);
		$this->setFotos($fotos);
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
	
	function setDatum(DateTime $datum){
		$this->datum = $datum;
	}
	
	function setInteressen(array $interessen){
		foreach ($interessen as $interesse){
			if(!($interesse instanceof Interesse))
				throw new Exception("Interesse ungültig!");
		}
		$this->interessen = $interessen;
	}

	function setFotos(array $fotos){
		foreach ($fotos as $foto){
			try{
				$foto = (string)$foto;
			}catch (Exception $e){}
			if(!is_string($foto))
				throw new Exception("Fotos ungültig!");
		}
		$this->fotos = $fotos;
		
		
	}
	
	
	function getNummer(){
		return $this->nummer;
	}
	
	function getDatum(){
		return $this->datum;
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
	
	function getFotos(){
		return $this->fotos;
	}

	
	function updateFotos(){
		$i=NK_COUNTER_ZERO;
		foreach(unserialize(NK_Bild_Formate) as $format){
			$foto=(string) NK_Pfad_Werbung_Bild_beginn.$this->nummer.NK_Pfad_Werbung_Bild_mitte.$i.NK_Pfad_Werbung_Bild_ende.$format;
			while(exists($foto)){
				array_push($this->fotos, $foto);
				$i++;
				$foto=(string) NK_Pfad_Werbung_Bild_beginn.$this->nummer.NK_Pfad_Werbung_Bild_mitte.$i.NK_Pfad_Werbung_Bild_ende.$format;
			}
		}
	}
}
?>