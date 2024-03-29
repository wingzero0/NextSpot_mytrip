<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Location extends CI_Controller {
	function __construct(){
		parent::__construct();
		$ret_array["result"] = false;
		$ret_array["LocationID"] = -1;
		
		if ($this->check_login() == false){
			$ret_array["error_msg"] = "incorrect login";
			echo json_encode($ret_array);
			die();
		}
	}
	function create(){
		$ret_array["result"] = false;
		$ret_array["LocationID"] = -1;
		
		if (!isset($_POST["name"]) || !isset($_POST["latitude"]) 
			|| !isset($_POST["longitude"]) ){
				$ret_array["error_msg"] = "please specify the name, latitude, longitude in post method";
				echo json_encode($ret_array);
				return false;
			}
		$info = array(
			"name" => $_POST["name"],
			"latitude" => doubleval($_POST["latitude"]),
			"longitude" => doubleval($_POST["longitude"]),
			"FB_pageID" => NULL
		);
		/*
		$info = array(
			"name" => $_GET["name"],
			"latitude" => doubleval($_GET["latitude"]),
			"longitude" => doubleval($_GET["longitude"]),
			"FB_pageID" => NULL
		);*/
		$this->load->model("SDatabase");
		$ret = $this->SDatabase->LocationInsertDB($info);
		if ($ret == -1){
			$ret_array["error_msg"] = "db error, Location not found (create failed)";
			echo json_encode($ret_array);
			return false;	
		}else{
			$ret_array["result"] = true;
			$ret_array["LocationID"] = $ret;
			echo json_encode($ret_array);
			return true;
		}

	}
	function get_list(){
		$ret_array["result"] = false;
		$ret_array["list"] = NULL;
		
		if (!isset($_POST["latitude"]) || !isset($_POST["longitude"])){
			$ret_array["error_msg"] = "please specify latitude, longitude in post method";
			echo json_encode($ret_array);
			return false;
		}
		
		if (!isset($_POST["radius"])){
			$bound = 0.001;	
		}else{
			$bound = doubleval($_POST["radius"]) / 10000000;
		}
		$data = array(
			"latitude" => doubleval($_POST["latitude"]),
			"longitude" => doubleval($_POST["longitude"]),
			"bound" => $bound
		);
		/*
		$data = array(
			"latitude" => doubleval($_GET["latitude"]),
			"longitude" => doubleval($_GET["longitude"]),
			"bound" => 1.0
		);*/
		$this->load->model("SDatabase");
		$ret = $this->SDatabase->LocationGetList($data);
		if ($ret == NULL){
			$ret_array["error_msg"] = "Location not found";
			echo json_encode($ret_array);
			return false;
		}else{
			$ret_array["list"] = $ret;
			$ret_array["result"] = true;
			echo json_encode($ret_array);
			return true;
		}
	}
	function get_by_id(){
		$ret_array["result"] = false;
		$ret_array["list"] = NULL;
		
		$fields[0] = "LocationID";
		
		
		if ($this->check_post_field($ret_array, $fields) == false){
			return false;
		}
				
		$data = array(
			"LocationID" => intval($_POST["LocationID"])
		);
		/*
		$data = array(
			"LocationID" => intval($_GET["LocationID"])
		);
		*/
		
		$this->load->model("SDatabase");
		$ret = $this->SDatabase->LocationGetByID($data);
		if ($ret == NULL){
			$ret_array["error_msg"] = "Location not found";
			echo json_encode($ret_array);
			return false;
		}else{
			$ret_array["list"] = $ret;
			$ret_array["result"] = true;
			echo json_encode($ret_array);
			return true;
		}
	}
	function check_login(){
		// non-complete
		return true; // for test
		if (!isset($_SESSION["login"]) || $_SESSION["login"] !==true){ 
			//echo "login first\n<br>";
			return false;
		}else {
			return true;
		}
	}
	private function check_post_field($ret_array, $fields){
		foreach($fields as $i => $name)
		if ( !isset($_POST[$name]) ){
			$ret_array["error_msg"] = "please specify the $name in post method";
			echo json_encode($ret_array);
			return false;
		}
		return true;
	}
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
?>
