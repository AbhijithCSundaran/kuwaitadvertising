<?php
namespace App\Models;
use CodeIgniter\Model;


class Login_Model extends Model{
	public function __construct(){
		$this->db=\Config\Database::connect();
	}
	public function authenticateNow($email='',$password=''){
		// echo "select email,password from user where email = '".$email."' and password= '".$password."'";	exit(0);
		return $this->db->query("SELECT name,email, password, user_id FROM user WHERE email = '".$email."' AND password= '".$password."'")->getRow();

	}
}
?>
	
	