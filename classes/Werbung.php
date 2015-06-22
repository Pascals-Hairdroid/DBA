<?php
include_once dirname(__FILE__)."/".'../conf/db_const.php';
include_once dirname(__FILE__)."/".'Interesse.php';
include_once dirname(__FILE__)."/../".'Utils.php';


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
			$this->titel = utf8_encode($titel);
		else
			throw new Exception("Titel ungültig!");
	}
	
	function setText($text){
		try{
			$text = (string)$text;
		}catch (Exception $e){}
		if(is_string($text))
			$this->text = utf8_encode($text);
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
				$foto = utf8_encode((string)$foto);
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
		return utf8_decode($this->titel);
	}
	
	function getText(){
		return utf8_decode($this->text);
	}
	
	function getInteressen(){
		return $this->interessen;
	}
	
	function getFotos(){
		$f = array();
		foreach($this->fotos as $foto){
			array_push($f, utf8_decode($foto));
		}
		return $f;
	}

	
	function updateFotos(){
		$i=NK_COUNTER_ZERO;
		$formate=unserialize(NK_Bild_Formate);
		$this->fotos = array();
		$exist=true;
		while($exist){
			$one = false;
			if(!$one){
          		$foto=(string) NK_Pfad_Werbung_Bild_beginn.$this->nummer.NK_Pfad_Werbung_Bild_mitte.$i.NK_Pfad_Werbung_Bild_ende;
				$one=$exist=exists($foto);
				if($one)
					array_push($this->fotos, utf8_encode($foto));
			}
			$i++;
 		}
	}
	
	function fotosLoeschen(){
		$success = true;
		$i=NK_COUNTER_ZERO;
		$formate=unserialize(NK_Bild_Formate);
		$exist=true;
		while($exist){
			$one = false;
			if(!$one){
				$foto=(string) NK_Pfad_Werbung_Bildupload_beginn.$this->nummer.NK_Pfad_Werbung_Bild_mitte.$i.NK_Pfad_Werbung_Bild_ende;
				$one=$exist=file_exists($foto);
				if($one && $success)
					$success=unlink($foto);
			}
			$i++;
		}
		return $success;
	}
}
?>