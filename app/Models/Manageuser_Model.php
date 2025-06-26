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
	public function getAllFilteredRecords($condition, $start, $limit)
	{
		 $db = \Config\Database::connect();
    		$sql = "SELECT user.*, role_acces.role_name 
            FROM user 
            LEFT JOIN role_acces ON role_acces.role_id = user.role_id 
            WHERE $condition 
            ORDER BY user.user_id DESC 
            LIMIT $start, $limit";

   		 return $db->query($sql)->getResult();
	}

	public function getFilterUserCount($condition,$fromstart,$tolimit) {
		
		$db = \Config\Database::connect();
    	$sql = "SELECT COUNT(*) as filRecords 
            FROM user 
            LEFT JOIN role_acces ON role_acces.role_id = user.role_id 
            WHERE $condition";

    return $db->query($sql)->getRow();
	}
}