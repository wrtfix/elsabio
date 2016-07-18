<?php
class Preguntas extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	function getPreguntas(){
		$this -> db -> from('pregunta');
		$query = $this -> db -> get();
		return $query->result();
	}
	
	
}
?>