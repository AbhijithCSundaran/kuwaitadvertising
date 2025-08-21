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
public function getAllFilteredRecords($condition, $fromstart, $tolimit, $orderBy = 'id', $orderDir = 'desc')
{
    $allowedColumns = [
            'id'            => 'e.id',
            'date'          => 'e.date',
            'particular'    => 'e.particular',
            'amount'        => 'e.amount',
            'payment_mode'  => 'e.payment_mode',
            'reference'     => 'e.reference',
            'customer_id'   => 'e.customer_id',
            'company_id'    => 'e.company_id',
            'customer_name' => 'c.name', 
        ];

    $orderBy = in_array($orderBy, $allowedColumns) ? $orderBy : 'id';
    $orderDir = strtolower($orderDir) === 'asc' ? 'ASC' : 'DESC';
    
      $condition = str_replace('company_id', 'e.company_id', $condition);
        $condition = str_replace('customer_id', 'e.customer_id', $condition);

    $sql = "
        SELECT e.*, c.name AS customer_name
        FROM expenses e
        LEFT JOIN customers c ON c.customer_id = e.customer_id
        WHERE $condition
        ORDER BY e.$orderBy $orderDir
        LIMIT $fromstart, $tolimit
    ";

    return $this->db->query($sql)->getResult();
}



	public function getFilterExpenseCount($condition) {
		
		 $condition = str_replace('company_id', 'e.company_id', $condition);
        $condition = str_replace('customer_id', 'e.customer_id', $condition);

        $sql = "
            SELECT COUNT(*) AS filRecords
            FROM expenses e
            LEFT JOIN customers c ON c.customer_id = e.customer_id
            WHERE $condition
        ";
         return $this->db->query($sql)->getRow();
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
