<?php 
class Arbeitsplatzausstattung{
	const NAME = "Arbeitsplatzausstattung";
	public $id;
	public $name;
	
	function __construct($id, $name){
		$this->setId($id);
		$this->setName($name);
	}
	
	
	function setId($id){
		try{
			$id=(int)$id;
		}
		catch(Exception $e){}
		if(is_int($id))
			$this->id = $id;
		else
			throw new Exception("Id ung�ltig!");
	}
	
	function setName($name){
		try{
			$name = (string)$name;
		}catch (Exception $e){}
		if(is_string($name))
			$this->name = utf8_encode($name);
		else
			throw new Exception("Name ung�ltig!");
	}

	
	function getId(){
			return $this->id;
	}
	
	function getName(){
			return utf8_decode($this->name);
	}
	
}
?>