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
    $sql = "SELECT user_id, name, email, role_id FROM user WHERE email = ? AND password = ?";
    $query = $this->db->query($sql, [$email, md5($password)]);
    return $query->getRow();
}

}
