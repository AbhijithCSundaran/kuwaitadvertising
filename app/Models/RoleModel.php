<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'role_acces';
    protected $primaryKey = 'role_id';
    protected $allowedFields = ['role_name', 'created_at', 'updated_at'];
}
