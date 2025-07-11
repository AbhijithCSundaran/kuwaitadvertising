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
	public function getAllFilteredRecords($condition, $fromstart, $tolimit, $orderBy = 'id', $orderDir = 'desc') {
        $orderBy = in_array($orderBy, ['id', 'date', 'particular', 'amount', 'payment_mode']) ? $orderBy : 'id';
        $orderDir = strtolower($orderDir) === 'asc' ? 'ASC' : 'DESC';

        return $this->db->query("SELECT * FROM expenses WHERE $condition ORDER BY $orderBy $orderDir LIMIT $fromstart, $tolimit")->getResult();
    }

	public function getFilterExpenseCount($condition) {
		
		return $this->db->query("select count(*) as filRecords from expenses where ".$condition)->getRow();
	}
}
