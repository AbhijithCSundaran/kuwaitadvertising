<?php
namespace App\Models;
use CodeIgniter\Model;

class Login_Model extends Model
{
    protected $table = 'user';

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

  public function authenticateNow($email = '', $password = '')
{
    $sql = "
        SELECT user.user_id, user.name, user.email, user.role_id, user.company_id
        FROM user
        JOIN company ON company.company_id = user.company_id
        WHERE user.email = ? 
          AND user.password = ? 
          AND company.deleted_at IS NULL
    ";
    $query = $this->db->query($sql, [$email, md5($password)]);
    return $query->getRow();
}

}
