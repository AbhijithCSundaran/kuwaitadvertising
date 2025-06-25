<?php
namespace App\Models;
use CodeIgniter\Model;


class Manageuser_Model extends Model{
	protected $table = 'user';
    protected $primaryKey = 'user_id';
    protected $allowedFields = ['name', 'email', 'phonenumber', 'password','role_id','company_id'];

    public function getAllUserCount() {
        return $this->db->query("select count(*) as totuser from user")->getRow();
    }
	public function getAllFilteredRecords($condition,$fromstart,$tolimit) {
		
		 return $this->db->query("SELECT * FROM user WHERE $condition ORDER BY user_id DESC LIMIT $fromstart, $tolimit")->getResult();
	}
	public function getFilterUserCount($condition,$fromstart,$tolimit) {
		
		return $this->db->query("select count(*) as filRecords from user where ".$condition)->getRow();
	}
}