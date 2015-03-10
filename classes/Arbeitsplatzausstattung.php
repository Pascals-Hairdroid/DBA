<?php 
class Arbeitsplatzausstattung{
	private $id;
	private $name;
	
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
			throw new Exception("Id ungültig!");
	}
	
	function setName($name){
		try{
			$name = (string)$name;
		}catch (Exception $e){}
		if(is_string($name))
			$this->name = $name;
		else
			throw new Exception("Name ungültig!");
	}

	
	function getId(){
			return $this->id;
	}
	
	function getName(){
			return $this->name;
	}
	
}
?>