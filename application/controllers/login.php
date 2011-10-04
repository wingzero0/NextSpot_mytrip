<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {
	function __construct(){
		parent::__construct();
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
