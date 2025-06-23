<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'role_acces';
    protected $primaryKey = 'role_id';
    protected $allowedFields = ['role_name', 'created_at', 'updated_at'];

    public function getAllRoleCount() {
        return $this->db->query("select count(*) as totroles from role_acces")->getRow();
    }
	public function getAllFilteredRecords($condition,$fromstart,$tolimit) {
		
		 return $this->db->query("SELECT * FROM role_acces WHERE $condition ORDER BY role_id DESC LIMIT $fromstart, $tolimit")->getResult();
	}
	public function getFilterRoleCount($condition,$fromstart,$tolimit) {
		
		return $this->db->query("select count(*) as filRecords from role_acces where ".$condition)->getRow();
	}
}

