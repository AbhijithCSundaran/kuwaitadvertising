<?php
namespace App\Models;
use CodeIgniter\Model;


class Manageuser_Model extends Model{
	protected $table = 'user';
    protected $primaryKey = 'user_id';
    protected $allowedFields = ['name', 'email', 'phonenumber', 'password','role_id','company_id'];

    public function getAllUserCount()
    {
        $db = \Config\Database::connect();
        return $db->query("SELECT COUNT(*) as totuser FROM user")->getRow();
    }
    public function getFilterUserCount($condition) {
		
		$db = \Config\Database::connect();
    	$builder = $db->table('user');
        $builder->select('COUNT(*) as totuser');
        $builder->join('role_acces', 'role_acces.role_id = user.role_id', 'left');
        $builder->where($condition);

    return $builder->select("COUNT(*) as totuser")->get()->getRow();

	}
	public function getAllFilteredRecords($condition, $fromstart, $tolimit, $orderColumn = 'user_id', $orderDir = 'DESC')
{
    $allowedColumns = ['user_id', 'name', 'role_name', 'email', 'phonenumber'];

    if (!in_array($orderColumn, $allowedColumns)) {
        $orderColumn = 'user_id';
    }

    $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

    $db = \Config\Database::connect();
    $builder = $db->query("
        SELECT user.*, role_acces.role_name 
        FROM user 
        LEFT JOIN role_acces ON role_acces.role_id = user.role_id 
        WHERE $condition 
        ORDER BY $orderColumn $orderDir 
        LIMIT $fromstart, $tolimit
    ");

    return $builder->getResult();
}


	
}