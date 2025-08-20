<?php

namespace App\Models;

use CodeIgniter\Model;

class Expense_Model extends Model
{
    protected $table = 'expenses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['date', 'particular', 'amount', 'payment_mode', 'reference', 'company_id', 'customer_id']; 

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

    public function getTodayExpenseTotal()
{
    $expenseModel = new Expense_Model();
    $companyId = session()->get('company_id');
    $today = date('Y-m-d');

    $total = $expenseModel
        ->selectSum('amount')
        ->where('company_id', $companyId)
        ->where('date', $today)
        ->first();

    return $this->response->setJSON([
        'total' => (float)($total['amount'] ?? 0)
    ]);
}

public function getMonthlyExpenseTotal()
{
    $expenseModel = new Expense_Model();
    $companyId = session()->get('company_id');

    $start = date('Y-m-01');
    $end = date('Y-m-t');

    $total = $expenseModel
        ->selectSum('amount')
        ->where('company_id', $companyId)
        ->where('date >=', $start)
        ->where('date <=', $end)
        ->first();

    return $this->response->setJSON([
        'total' => (float)($total['amount'] ?? 0)
    ]);
}

}
