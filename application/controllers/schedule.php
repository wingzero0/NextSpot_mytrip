<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Schedule extends CI_Controller {
	function __construct(){
		parent::__construct();
		$ret_array["result"] = false;
		$ret_array["TripID"] = -1;
		
		if ($this->check_login() == false){
			$ret_array["error_msg"] = "incorrect login";
			echo json_encode($ret_array);
			die();
		}
	}
	function create(){
		$ret_array["result"] = false;
		$ret_array["TripID"] = -1;
		
		if ( !isset($_POST["name"]) || !isset($_POST["start_time"]) 
			|| !isset($_POST["end_time"]) || !isset($_POST["uid"]) ) {
				$ret_array["error_msg"] = "please specify the name, start_time, end time and uid in post mothed";
				echo json_encode($ret_array);
				return false;
			}

		$data = array(
			"name" => $_POST["name"],
			"StartTime" => $_POST["start_time"],
			"EndTime" => $_POST["end_time"],
			"UID" => $_POST["uid"]
		);
		/*
		$data = array(
			"name" => $_GET["name"],
			"StartTime" => $_GET["start_time"],
			"EndTime" => $_GET["end_time"],
			"UID" => $_GET["uid"]
		);*/
		$this->load->model("SDatabase");
		$ret = $this->SDatabase->TripInsertDB($data);
		if ($ret == -1){
			$ret_array["error_msg"] = "db error, Trip not found (create failed)";
			echo json_encode($ret_array);
			return false;
		}else{
			$ret_array["result"] = true;
			$ret_array["TripID"] = $ret;
			echo json_encode($ret_array);
			return true;
		}

	}
	function get_scenics(){
		$ret_array["result"] = false;
		$ret_array["ScenicList"] = NULL;
		
		$fields[0] = "trip_id";
		
		if ($this->check_post_field($ret_array, $fields) == false){
			return false;
		}
		
		$data = array(
			"TripID" => $_POST["trip_id"]
		);
		/*
		$data = array(
			"TripID" => $_GET["trip_id"]
		);*/
		$this->load->model("SDatabase");
		$ret = $this->SDatabase->ScenicGetList($data);
		if ($ret == NULL){
			$ret_array["error_msg"] = 
				sprintf("Scenics of the Trip %s not found", $data["TripID"]);
			echo json_encode($ret_array);
			return false;	
		}else{
			$ret_array["result"] = true;
			$ret_array["ScenicList"] = $ret;
			echo json_encode($ret_array);
			return true;
		}
	}
	
	function create_scenics(){
		$ret_array["result"] = false;
		$ret_array["ScenicIDList"] = NULL;
		
		$fields[0] = "trip_id";
		$fields[1] = "num";
		
		if ($this->check_post_field($ret_array, $fields) == false){
			return false;
		}
		$num = intval($_POST["num"]);
		for($i = 1;$i<=$num;$i++){
			$fields[] = "location_id_".$i;
			$fields[] = "start_".$i;
			$fields[] = "end_".$i;
			$fields[] = "money_".$i;
		}
		if ($this->check_post_field($ret_array, $fields) == false){
			return false;
		}
		
		
		//insert
		$this->load->model("SDatabase");
		 
		//$num = intval($_GET["num"]);
		for($i = 1;$i<=$num;$i++){
			
			$data = array(
				"TripID" => $_POST["trip_id"],
				"LocationID" => $_POST["location_id_".$i],
				"StartTime" => $_POST["start_".$i],
				"EndTime" => $_POST["end_".$i],
				"Money" => $_POST["money_".$i]
			);
			/*
			$data = array(
				"TripID" => $_GET["trip_id"],
				"LocationID" => $_GET["location_id_".$i],
				"StartTime" => $_GET["start_".$i],
				"EndTime" => $_GET["end_".$i],
				"Money" => $_GET["money_".$i]
			);*/
			$ret_array["ScenicIDList"][$i] = $this->SDatabase->ScenicInsertDB($data);
		}
		
		$ret_array["result"] = true;
		echo json_encode($ret_array);
		return true;
	}
	function update_scenics(){
		$ret_array["result"] = false;
		//$ret_array["ScenicIDList"] = NULL;
		
		$fields[0] = "num";
		
		if ($this->check_post_field($ret_array, $fields) == false){
			return false;
		}
		$num = intval($_POST["num"]);
		for($i = 1;$i<=$num;$i++){
			$fields[] = "scenic_id_".$i;
			$fields[] = "location_id_".$i;
			$fields[] = "start_".$i;
			$fields[] = "end_".$i;
			$fields[] = "money_".$i;
		}
		if ($this->check_post_field($ret_array, $fields) == false){
			return false;
		}
		
		
		//insert
		$this->load->model("SDatabase");
		 
		//$num = intval($_GET["num"]);
		for($i = 1;$i<=$num;$i++){
			/*
			$ScenicID = $_POST["scenic_id_".$i];
			$data = array(
				"LocationID" => $_POST["location_id_".$i],
				"StartTime" => $_POST["start_".$i],
				"EndTime" => $_POST["end_".$i],
				"Money" => $_POST["money_".$i]
			);*/
			/*
			$ScenicID = $_GET["scenic_id_".$i];
			$data = array(
				"LocationID" => $_GET["location_id_".$i],
				"StartTime" => $_GET["start_".$i],
				"EndTime" => $_GET["end_".$i],
				"Money" => $_GET["money_".$i]
			);*/
			$this->SDatabase->ScenicUpdateDB($ScenicID, $data);
			//$ret_array["ScenicIDList"][$i] = $this->SDatabase->ScenicInsertDB($data);
		}
		
		$ret_array["result"] = true;
		echo json_encode($ret_array);
		return true;
	}
	private function check_post_field($ret_array, $fields){
		foreach($fields as $i => $name)
		if ( !isset($_POST[$name]) ){
			$ret_array["error_msg"] = "please specify the $name in post mothed";
			echo json_encode($ret_array);
			return false;
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
}
?>