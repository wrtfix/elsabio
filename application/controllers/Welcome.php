<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct()
	{
		parent:: __construct();
		$this->load->model('preguntas','',TRUE);
		// $this->load->helper(array('form', 'url'));

	}


	public function index()
	{

	    // echo json_encode( "[{'pregunta':'Cuantas veces dice la palabra amo el cantante Axcel en el tema Amo', 'opcion':[1,10,20],'respuesta':[20]},{'pregunta':'Cuantas veces dice la palabra alegria el cantante Fito Paez en el tema Y dale alegria a tu corazon', 'opcion':[1,10,20],'respuesta':[20]},{'pregunta':'Ordene de menor a mayor las siguientes pelotas', 'opcion':['Futbol','Tenis','Voley','Basket','Golf'],'respuesta':['Golf','Tenis','Voley','Futbol','Basket']},{'pregunta':'Que tienen en comun', 'opcion':['Messi','Maradona','Pele'],'respuesta':['Son jugadores de futbol']},{'pregunta':'Las canchas de futbol de 11 jugadores mide 70mtrs', 'opcion':['Verdadero','Falso'],'respuesta':['Falso']}]" );
	    $this->load->helper('url');
		// $this->load->view('upload_form', array('error' => ' ' ));
		// $respuesta = json_encode($this->preguntas->getPreguntas());
		// print_r($respuesta); 

		$this->load->view('welcome_message',array('error' => ' ' ));
	}

	public function getPreguntas()
	{
		$respuesta = json_encode($this->preguntas->getPreguntas());
		print_r($respuesta); 
	}


	public function getKey()
	{
		$respuesta = json_encode(count($this->preguntas->getPreguntas()));
		print_r($respuesta); 
	}
	
}
