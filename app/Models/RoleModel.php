<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'role_acces';
    protected $primaryKey = 'role_id';
    protected $allowedFields = ['role_id','role_name',];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getAllRoleCount()
    {
        return $this->db->query("SELECT COUNT(*) AS totroles FROM role_acces")->getRow();
    }

    public function getAllFilteredRecords($condition, $fromstart, $tolimit, $orderBy = 'role_id', $orderDir = 'desc')
    {
        return $this->db->query("SELECT * FROM role_acces WHERE $condition ORDER BY $orderBy $orderDir LIMIT $fromstart, $tolimit")->getResult();
    }

    public function getFilterRoleCount($condition)
    {
        return $this->db->query("SELECT COUNT(*) AS filRecords FROM role_acces WHERE $condition")->getRow();
    }

}
