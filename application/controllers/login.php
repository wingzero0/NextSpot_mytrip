<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//require("session.php");

class Login extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->model('Login_Model');
	}
	function index(){
		echo "welcome to NextSpot";
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
	public function IsLogin(){
		$ret_array["result"] = false;
		$ret = $this->Login_Model->_isLogin();
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
		//printf("<p><a href=%s>login</a></p>",$ret_array["fb_login_url"]);
		echo json_encode($ret_array);
	}
	public function GetLoginURLHook(){
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
		//echo json_encode($ret_array);
	}
	public function Logout(){
		$type = $this->Login_Model->Logout();
		
		if ($type == "FB"){
			// redirect to fb logouturl
			$this->load->model('Facebook_Handshaking');
			$url = $this->Facebook_Handshaking->get_logout_url();
			$this->load->helper('url');
			//$ret_array["FB_logout"] = $url;
			redirect($url, 'refresh');
			// facebook will redirect to this page;
			// I can't catch it because it doesn't contain any argument in url. 
		}
		$ret_array["result"] = true;
		echo json_encode($ret_array);
	}
	public function GetUserInfo() {
		$ret_array["result"] = false;
		$ret = $this->Login_Model->_isLogin();
		if ($ret == true){
			$ret_array["result"] = true;
			$ret_array["UID"] = $this->session->userdata('NSid');
			$ret_array["Name"] = $this->session->userdata('Name');
			$ret_array["AccountType"] = $this->session->userdata('IdType');
		}
		echo json_encode($ret_array);
		 
	}
	private function RegisterFbAccount($profile){
		// set up session and register db record
		if ($profile == false){
			// something wrong
			return false;
		}
		// insert if not exist
		$this->load->model('SDatabase');
		$NSid = $this->SDatabase->FBaccountInsertDB($profile);
		
		echo $NSid;
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
