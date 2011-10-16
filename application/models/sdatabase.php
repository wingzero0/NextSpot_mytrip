<?php
class SDatabase extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}
	public function LocationInsertDB($info){// no security issue
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
	
	public function LocationGetList($data){// no security issue
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
	public function LocationGetByID($data){// no security issue
		$sql = sprintf("select * from `Location` where `LocationID` = %d",
			$data["LocationID"]
		);
		$ret = $this->db->query($sql);
		if ($ret->num_rows() > 0){
			return $ret->result_array();
		}else{
			return NULL;
		}
	}
	public function TripInsertDB($data){//no security issue
		// it won't check the duplicate trip, every trip will be treat as a new trip
	
		// insert new record
		$this->db->insert("Trip", $data);
		return $this->db->insert_id();
	}
	public function TripGetList($data){
		// get the TripID
		$sql = sprintf("
			select `TripID`, `name`, `StartTime`, `EndTime` 
			from `Trip` 
			where `UID` = '%s'", 
			$data["UID"]);
		
		$ret = $this->db->query($sql);
		if ($ret->num_rows() > 0){
			return $ret->result_array();
		}else {
			return NULL;
		}
	}
	public function TripUpdateDB($TripID, $data){
		$where = "`TripID` = ".$TripID;
		$sql = $this->db->update_string("Trip", $data, $where);
		//echo $sql;
		$this->db->query($sql);
		return ;
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
	public function FBaccountInsertDB($info){
		// lock table and check duplicate first
		$sql = "lock table `User` write";
		$this->db->query($sql);
		
		$sql = sprintf("select * from `User` where `ExternalID` = '%s'", $info["id"]);
		$ret = $this->db->query($sql);
		
		if ($row = $ret->row()){
			$NSid = $row->ID;
			//if the record already exists, just skip the insertion and return 
			$sql = "unlock tables";
			$this->db->query($sql);
			return $NSid;
		}
		
		$insert_data = array(
			"Name" => $info["name"],
			"IDType" => 1, // 1 means FB account.
			"ExternalID" => $info["id"]
		);
		$this->db->insert("User", $insert_data);
		$NSid = $this->db->insert_id();
		
		$sql = "unlock tables";
		$this->db->query($sql);
			
		return $NSid;
	}
}
?>
