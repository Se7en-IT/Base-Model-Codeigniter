<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model("test_model");
	}

	/* CREATE */
	function insert(){
		var_dump($this->test_model->insert(array(
			"name"=>"Test",
			"number"=>123456,
			"date"=>date("Y-m-d")
		)));
	}
	/* READ */
	function find($id){
		var_dump($this->test_model->find($id));
	}
	function find_by_name($name){
		var_dump($this->test_model->find_by_name($name));
	}
	function find_where(){
		var_dump($this->test_model->find_where(array(
			"name"=>"Test",
			"number"=>123456
		)));
	}
	function find_all(){
		var_dump($this->test_model->find_all());
	}

	/* UPDATE */
	function update($id){
		var_dump($this->test_model->update($id,array(
			"name"=>"Test2"
			)));
	}
	function update_by_name($name){
		var_dump($this->test_model->update_by_name($name,array(
			"name"=>"Test"
			)));
	}
	function update_where(){
		var_dump($this->test_model->update_where(array(
			"name"=>"Test",
			"number"=>123456
		),array(
			"name"=>"Test2"
			)));
	}

	/* DELETE */
	function delete($id){
		var_dump($this->test_model->delete($id));
	}
	function delete_by_name($name){
		var_dump($this->test_model->delete_by_name($name));
	}
	function delete_where(){
		var_dump($this->test_model->delete_where(array(
			"name"=>"Test",
			"number"=>123456
		)));
	}

	/* ORM */
	function save(){
		var_dump($this->test_model->save(array(
			"name"=>"Test2",
			"number"=>1234567,
			"date"=>date("Y-m-d")
		)));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */