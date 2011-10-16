<?php
// it duel with the login issue, it also provide the security check 
// before entering the db selection.

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(dirname(__FILE__)."/FB_info.php");

class Login_Model extends CI_Model{
	private $facebook = NULL;
	private $permission = null;
	public function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->load->library('session');
	}
	public function _isLogin(){
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
	public function Logout(){
		// get the type first, then clear the session field
		$type = $this->session->userdata('IdType');
		
		$newdata = array(
	      'NSid'  => null,
	      'Name'     => null,
	      'IdType' => null,
	      'logged_in' => false
      );		
		$this->session->set_userdata($newdata);
		
		return $type;
	}
	public function isSamePeople($uid){
		if ($this->_isLogin() == false){
			return false;
		}
		$loginid = $this->session->userdata('NSid');
		if ($uid == $loginid){
			return true;
		}else{
			return false;
		}
		
	}
}
