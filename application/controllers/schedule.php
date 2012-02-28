<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Schedule extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('Login_Model');
		$ret_array["result"] = false;
		$ret_array["TripID"] = -1;
		
		if ($this->Login_Model->_isLogin() == false){
			$ret_array["error_msg"] = "incorrect login";
			echo json_encode($ret_array);
			die();
		}
	}
	function CreateTrip(){
		// create trip and it will echo ret_array object
		// ret_array contain result flag, TripID, or sometimes with error_msg 

		$ret_array["result"] = false;
		$ret_array["TripID"] = -1;

		if ( !isset($_POST["name"]) || !isset($_POST["start_time"]) 
			|| !isset($_POST["end_time"]) || !isset($_POST["uid"]) 
			|| !isset($_POST["trip_type"]) ) {
				$ret_array["error_msg"] = "please specify the name, start_time, end time and uid in post method";
				echo json_encode($ret_array);
				return false;
		}

		if ( $this->Login_Model->isSamePeople($_POST["uid"]) == false ){
			$ret_array["error_msg"] = "uid in post method does not match login uid";
			echo json_encode($ret_array);
			return false;
		}

		$data = array(
			"name" => $_POST["name"],
			"StartTime" => $_POST["start_time"],
			"EndTime" => $_POST["end_time"],
			"TripType" => $_POST["trip_type"],
			"OwnerID" => $_POST["uid"],
		);
		
		$this->load->model("SDatabase");
		$ret = $this->SDatabase->TripInsertDB($data);
		if ($ret == -1){
			$ret_array["error_msg"] = "db error, Trip not found (create failed)";
			echo json_encode($ret_array);
			return false;
		}
		$ret_array["TripID"] = $ret;
		
		$data = array(
			"UID" => $_POST["uid"],
			"TripID" => $ret_array["TripID"],
		);
		$ret = $this->SDatabase->TakePartInInsertDB($data);
		if ($ret !== true){
			$ret_array["error_msg"] = "db error, TakePartIn table erro (create failed)";
			echo json_encode($ret_array);
			return false;
		}else{
			$ret_array["result"] = true;
			echo json_encode($ret_array);
			return true;
		}
	}
	function GetTrips(){
		// get trips with specify UID and it will echo ret_array object
		// ret_array contain result(true / false), TripList, 
		// or sometimes with error_msg 

		$ret_array["result"] = false;
		$ret_array["TripList"] = NULL;


		$fields[0] = "uid";

		if ($this->check_post_field($ret_array, $fields) == false){
			return false;
		}
		
		if ( $this->Login_Model->isSamePeople($_POST["uid"]) == false ){
			$ret_array["error_msg"] = "uid in post method does not match login uid";
			echo json_encode($ret_array);
			return false;
		}

		$data = array(
			"UID" => $_POST["uid"]
		);

		$this->load->model("SDatabase");
		$ret = $this->SDatabase->TripGetList($data);
		if ($ret == NULL){
			$ret_array["error_msg"] = 
				sprintf("Trips of the uid %s not found", $data["UID"]);
			echo json_encode($ret_array);
			return false;	
		}else{
			$ret_array["result"] = true;
			$ret_array["TripList"] = $ret;
			echo json_encode($ret_array);
			return true;
		}
	}
	function UpdateTrips(){
		// update trips with TripIDs and it will echo ret_array object
		// ret_array contain result(true / false), 
		// or sometimes with error_msg 

		$ret_array["result"] = false;

		$fields[0] = "num";

		if ($this->check_post_field($ret_array, $fields) == false){
			return false;
		}

		$num = intval($_POST["num"]);
		for($i = 1;$i<=$num;$i++){
			$fields[] = "trip_id_".$i;
			$fields[] = "name_".$i;
			$fields[] = "start_".$i;
			$fields[] = "end_".$i;
			$fields[] = "trip_type_".$i;
		}
		if ($this->check_post_field($ret_array, $fields) == false){
			return false;
		}

		// security check
		$TripIDs = array();
		for($i = 1;$i<=$num;$i++){
			$TripIDs[] = intval($_POST["trip_id_".$i]);
		}
		if ( $this->Login_Model->TripsAccessCheck($TripIDs) == false ){
			$ret_array["error_msg"] = "Security Access error: trip_ids in post method cannot be accessed by login uid.";
			echo json_encode($ret_array);
			return false;
		}


		//insert
		$this->load->model("SDatabase");

		for($i = 1;$i<=$num;$i++){
			$TripID = intval($_POST["trip_id_".$i]);
			$data = array(
				"name" => $_POST["name_".$i],
				"StartTime" => $_POST["start_".$i],
				"EndTime" => $_POST["end_".$i],
				"TripType" => intval($_POST["trip_type_".$i])
			);

			$this->SDatabase->TripUpdateDB($TripID, $data);
		}

		$ret_array["result"] = true;
		echo json_encode($ret_array);
		return true;
	}
	function GetScenics(){
		// get scenics with specify TripID and it will echo ret_array object
		// ret_array contain result(true / false), ScenicList, 
		// or sometimes with error_msg 

		$ret_array["result"] = false;
		$ret_array["ScenicList"] = NULL;

		$fields[0] = "trip_id";

		if ($this->check_post_field($ret_array, $fields) == false){
			return false;
		}

		$TripIDs = array();
		$TripIDs[] = intval($_POST["trip_id"]);

		if ( $this->Login_Model->TripsAccessCheck($TripIDs) == false ){
			$ret_array["error_msg"] = "Security Access error: trip_ids in post method cannot be accessed by login uid.";
			echo json_encode($ret_array);
			return false;
		}

		$data = array(
			"TripID" => $_POST["trip_id"]
		);

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

	function CreateScenics(){
		// create scenics with specify TripID and it will echo ret_array object
		// ret_array contain result(true / false), ScenicIDList, 
		// or sometimes with error_msg 

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
			$fields[] = "note_".$i;
		}
		if ($this->check_post_field($ret_array, $fields) == false){
			return false;
		}

		$TripIDs[0] = $_POST["trip_id"];
		if ( $this->Login_Model->TripsAccessCheck($TripIDs) == false ){
			$ret_array["error_msg"] = "Security Access error: trip_ids in post method cannot be accessed by login uid.";
			echo json_encode($ret_array);
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
				"Money" => $_POST["money_".$i],
				"Note" => $_POST["note_".$i]
			);
			$ret_array["ScenicIDList"][$i] = $this->SDatabase->ScenicInsertDB($data);
		}

		$ret_array["result"] = true;
		echo json_encode($ret_array);
		return true;
	}
	function UpdateScenics(){
		// update scenics with ScenicIDs and it will echo ret_array object
		// ret_array contain result(true / false), 
		// or sometimes with error_msg 

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
			$fields[] = "note_".$i;
		}
		if ($this->check_post_field($ret_array, $fields) == false){
			return false;
		}

		// security check
		$scenicIDs = array();
		for($i = 1;$i<=$num;$i++){
			$scenicIDs[] = intval($_POST["scenic_id_".$i]);
		}
		if ( $this->Login_Model->ScenicsAccessCheck($scenicIDs) == false ){
			$ret_array["error_msg"] = "Security Access error: scenic_ids in post method cannot be accessed by login uid.";
			echo json_encode($ret_array);
			return false;
		}


		//insert
		$this->load->model("SDatabase");

		//$num = intval($_GET["num"]);
		for($i = 1;$i<=$num;$i++){

			$ScenicID = $_POST["scenic_id_".$i];
			$data = array(
				"LocationID" => $_POST["location_id_".$i],
				"StartTime" => $_POST["start_".$i],
				"EndTime" => $_POST["end_".$i],
				"Money" => $_POST["money_".$i],
				"Note" => $_POST["note_".$i]
			);
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
				$ret_array["error_msg"] = "please specify the $name in post method";
				echo json_encode($ret_array);
				return false;
			}
		return true;
	}
}
?>
