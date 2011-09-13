<?php
class SDatabase extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}
	public function LocationInsertDB($info){
		// lock table and check duplicate first
		$sql = "lock table `Location` write";
		$this->db->query($sql);
		
		$sql = sprintf("select `LocationID` from `Location` where `name` = '%s' and `latitude` = %lf and `longitude` = %lf", $info["name"], $info["latitude"], $info["longitude"]);
		$ret = $this->db->query($sql);
		if ($row = $ret->row()){
			//if the record already exists, just skip the insertion and return 
			$sql = "unlock tables";
			$this->db->query($sql);
			return $row->LocationID;
		}

		// insert new record
		$insert_data = array(
			"name" => $info["name"],
			"latitude" => $info["latitude"],
			"longitude" => $info["longitude"],
			"FB_pageID" => $info["FB_pageID"]
		);
		$this->db->insert("Location", $insert_data);

		// get the LocationID
		$sql = sprintf("select `LocationID` from `Location` where `name` = '%s' and `latitude` = %lf and `longitude` = %lf", $info["name"], $info["latitude"], $info["longitude"]);
		$ret = $this->db->query($sql);
		if ($row = $ret->row()){
			$sql = "unlock tables";
			$this->db->query($sql);
			return $row->LocationID;
		}else {
			$sql = "unlock tables";
			$this->db->query($sql);
			return -1;
		}
	}
	
	public function LocationGetList($data){
		$u_lat = $data["latitude"] + $data["bound"];
		$l_lat = $data["latitude"] - $data["bound"];
		$u_long = $data["longitude"] + $data["bound"];
		$l_long = $data["longitude"] - $data["bound"];
		
		$sql = sprintf("
			select * from `Location` 
			where `latitude` <= %lf and `latitude` >= %lf 
			and `longitude` <= %lf and `longitude` >= %lf",
			$u_lat, $l_lat,$u_long, $l_long
		);
		$ret = $this->db->query($sql);
		if ($ret->num_rows() > 0){
			return $ret->result_array();
		}else{
			return NULL;
		}
	}
	public function TripInsertDB($data){
		// it won't check the duplicate trip, every trip will be treat as a new trip
	
		// insert new record
		$this->db->insert("Trip", $data);
		return $this->db->insert_id();
		// get the TripID
		/*
		$sql = sprintf("
			select `TripID` from `Trip` 
			where `UID` = '%s' and `name` = '%s' 
			and `StartTime` = '%s' and `EndTime` = '%s'
			order by `TripID` desc
			limit 1", 
			$data["UID"], $data["name"], $data["StartTime"], $data["EndTime"]);
		
		$ret = $this->db->query($sql);
		if ($row = $ret->row()){
			//$sql = "unlock tables";
			//$this->db->query($sql);
			return $row->TripID;
		}else {
			//$sql = "unlock tables";
			//$this->db->query($sql);
			return -1;
		}*/
	}
	public function ScenicInsertDB($data){
		// it won't check the duplicate Scenic, every Scenic will be treat as a new one	
		// insert new record
		$this->db->insert("Scenic", $data);
		return $this->db->insert_id();
	}
	public function ScenicUpdateDB($ScenicID, $data){
		$where = "`ScenicID` = ".$ScenicID;
		$sql = $this->db->update_string("Scenic", $data, $where);
		//echo $sql;
		$this->db->query($sql);
		return ;
	}
	public function ScenicGetList($data){
		// get the TripID
		$sql = sprintf("
			select `ScenicID`, `LocationID`, `StartTime`, `EndTime`, `Money`, `Note` 
			from `Scenic` 
			where `TripID` = '%s'", 
			$data["TripID"]);
		
		$ret = $this->db->query($sql);
		if ($ret->num_rows() > 0){
			return $ret->result_array();
		}else {
			return NULL;
		}
	}
}
?>
