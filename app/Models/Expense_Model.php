<?php

namespace App\Models;

use CodeIgniter\Model;

class Expense_Model extends Model
{
    protected $table = 'expenses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['date', 'particular', 'amount', 'payment_mode', 'reference']; 

    public function getAllExpenseCount() {
        return $this->db->query("select count(*) as totexpense from expenses")->getRow();
    }
	public function getAllFilteredRecords($condition,$fromstart,$tolimit) {
		
		 return $this->db->query("SELECT * FROM expenses WHERE $condition ORDER BY id DESC LIMIT $fromstart, $tolimit")->getResult();
	}
	public function getFilterExpenseCount($condition,$fromstart,$tolimit) {
		
		return $this->db->query("select count(*) as filRecords from expenses where ".$condition)->getRow();
	}
}
