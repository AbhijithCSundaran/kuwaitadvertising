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

    //  public function authenticateNow($email = '', $password = '')
// {
//     $sql = "
//         SELECT user.user_id, user.name, user.email, user.role_id, user.company_id
//         FROM user
//         LEFT JOIN company ON company.company_id = user.company_id
//         WHERE user.email = ?
//           AND user.password = ?
//           AND company.company_status!  = 3

    //     ";

    //     $query = $this->db->query($sql, [$email, md5($password)]);
//     return $query->getRow();
// }
    public function authenticateNow($email = '', $password = '')
    {
        $sql = "
            SELECT 
                user.user_id, 
                user.name, 
                user.email, 
                user.role_id, 
                user.company_id,
                user.status AS user_status,
                company.company_status
            FROM user
            LEFT JOIN company ON company.company_id = user.company_id
            WHERE user.email = ?
              AND user.password = ?
            LIMIT 1
        ";

        $query = $this->db->query($sql, [$email, md5($password)]);
        $user = $query->getRow();

        if (!$user) {
            // No matching email/password
            return null;
        }

        // Check user active status
        if ($user->user_status != 1) {
            return null; // Blocked/inactive user
        }

        // Super admin can bypass company checks
        if ($user->role_id != 1) {
            if ($user->company_status != 1) {
                // Company is not active
                 return ['status' => 0, 'message' => 'Staff Access Restricted'];
            }
        }

        return $user;
    }   


}
