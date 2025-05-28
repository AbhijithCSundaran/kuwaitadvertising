<?php
namespace App\Models;
use CodeIgniter\Model;


class Manageuser_Model extends Model{
	protected $table = 'user';
    protected $primaryKey = 'user_id';
    protected $allowedFields = ['name', 'uname', 'password', 'email','phonenumber'];
    protected $returnType = 'array';   
}