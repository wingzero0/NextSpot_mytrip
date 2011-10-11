<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//require("session.php");

class Login extends CI_Controller {
	function __construct(){
		//$this->load->library('session');
		parent::__construct();
		$this->load->library('session');
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
	private function _isLogin(){
		$NSid = $this->session->userdata('NSid');
		$logged = $this->session->userdata('logged_in');
		if ($NSid != null && $logged == TRUE){
			return true;
			//echo $NSid."\t".$logged."\n";
			//echo $this->session->userdata('Name')."\n";
			//echo $this->session->userdata('IdType')."\n";
		}else {
			return false;
		}
		
	}
	public function IsLogin(){
		$ret_array["result"] = false;
		$ret = $this->_isLogin();
		if ($ret == true){
			$ret_array["result"] = true;
		}
		echo json_encode($ret_array);
	}
	public function GetLoginURL(){
		$ret_array["result"] = false;
		$this->load->model('Facebook_Handshaking');
		
		$ret = $this->IsFbRedirect();
		if ($ret == true){
			// we catch the redirection, no need to do anything.
			return true;
		}
		$ret_array["fb_login_url"] = $this->Facebook_Handshaking->get_login_url();
		$ret_array["ns_login_url"] = "http://kit.csie.ntu.edu.tw";
		printf("<p><a href=%s>login</a></p>",$ret_array["fb_login_url"]);
		echo json_encode($ret_array);
	}
	public function Logout(){
		// get the type first, then clear the session field
		// redirect to FB at the final (it won't be back)
		$type = $this->session->userdata('IdType');
		
		$newdata = array(
	      'NSid'  => null,
	      'Name'     => null,
	      'IdType' => null,
	      'logged_in' => false
      );		
		$this->session->set_userdata($newdata);
		
		if ($type == "FB"){
			// redirect to fb logouturl
			$this->load->model('Facebook_Handshaking');
			$url = $this->Facebook_Handshaking->get_logout_url();
			$this->load->helper('url');
			redirect($url, 'refresh');
		}
		$ret_array["result"] = true;
		echo json_encode($ret_array);
	}
	public function GetUserInfo() {
		if 
	}
	private function RegisterFbAccount($profile){
		// set up session and register db record
		if ($profile == false){
			// something wrong
			return false;
		}
		// select FB id in db
		// insert if not exist
		// select our db id
		$NSid = "1000"; // fake id
		$newdata = array(
	      'NSid'  => $NSid,
	      'Name'     => $profile["name"],
	      'IdType' => 'FB',
	      'logged_in' => TRUE
      );

		$this->session->set_userdata($newdata);
	}
	private function IsFbRedirect(){
		if (isset($_GET["state"])){
			// capture the facebook unexpacted redirect
			$profile = $this->Facebook_Handshaking->getUserInfo();
			//print_r($profile);
			$this->RegisterFbAccount($profile);
			$this->load->helper('url');
			redirect("http://kit.csie.ntu.edu.tw/NextSpot/", 'refresh');
			return true; // capture login
		}
		return false; // not fb redircet
	}
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
?>
