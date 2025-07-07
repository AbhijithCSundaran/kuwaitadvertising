<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Expense_Model;

class Expense extends BaseController
{

    public function index()
    {
        return view('addexpenselist');
    }

    public function create($id = null)
    {
        $data = [
            'isEdit' => !empty($id),
            'expense' => null
        ];

        if ($id) {
            $expenseModel = new Expense_Model();
            $data['expense'] = $expenseModel->find($id);
        }

        return view('addexpense', $data);
    }

    public function store()
    {
        $expenseModel = new Expense_Model();

        $id           = $this->request->getPost('id');
        $date         = $this->request->getPost('date');
        $convertedDate = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
        $particular   = $this->request->getPost('particular');
        $amount       = $this->request->getPost('amount');
        $payment_mode = $this->request->getPost('payment_mode');
        $reference    = $this->request->getPost('reference');

        if (empty($date) || empty($particular) || empty($amount) || empty($payment_mode)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Please Fill All Mandatory Fields.'
            ]);
        }

        $data = [
            'date' => $convertedDate,
            'particular'   => $particular,
            'amount'       => $amount,
            'payment_mode' => $payment_mode,
            'reference'    => $reference
        ];

        if (!empty($id)) {
            $existing = $expenseModel->find($id);
            if (!$existing) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Expense Not Found.'
                ]);
            }

            $hasChanges = (
                $existing['date'] !== $data['date'] ||
                $existing['particular'] !== $data['particular'] ||
                $existing['amount'] != $data['amount'] ||
                $existing['payment_mode'] !== $data['payment_mode'] ||
                $existing['reference'] !== $data['reference']
            );

            if ($hasChanges) {
                $expenseModel->update($id, $data);
                $msg = 'Expense Updated Successfully.';
            } else {
                $msg = 'No Changes Detected.';
            }
        } else {
           $expenseModel->insert($data);
            $msg = 'Expense Created Successfully.';
            return $this->response->setJSON([
                'status' => 'success',
                'message' => $msg,
                'redirect_to_list' => false
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $msg,
             'redirect_to_list' => !empty($id)
        ]);
    }

 public function getExpensesAjax()
{
    $model = new \App\Models\Expense_Model();

    $draw = $_POST['draw'];
    $fromstart = $_POST['start'];
    $tolimit = $_POST['length'];
    $order = $_POST['order'][0]['dir'] ?? 'desc';
    $search = $_POST['search']['value'];
    $slno = $fromstart + 1;

    $condition = "1=1";
    if ($search) {
    $search = trim($search);
    $condition .= " AND (DATE_FORMAT(date, '%d-%m-%Y') LIKE '%$search%' OR payment_mode LIKE '%$search%' OR particular LIKE '%$search%' OR amount LIKE '%$search%')";
}



    $totalRec = $model->getAllFilteredRecords($condition, $fromstart, $tolimit);

    $result = [];
    foreach ($totalRec as $expense) {
        $formattedDate = date('d-m-Y', strtotime($expense->date)); 
        $result[] = [
            'slno'         => $slno++,
            'id'           => $expense->id,
            'date'         => $formattedDate, 
            'particular'   => $expense->particular,
            'amount'       => $expense->amount,
            'payment_mode' => $expense->payment_mode 
        ];
    }

    $totExpenseCount = $model->getAllExpenseCount();
    $totFilterCounts = $model->getFilterExpenseCount($condition, $fromstart, $tolimit);

    $response = [
        "draw" => intval($draw),
        "iTotalRecords" => $totExpenseCount->totexpense ?? 0,
        "iTotalDisplayRecords" => $totFilterCounts->filRecords ?? 0,
        "data" => $result
    ];

    return $this->response->setJSON($response);
}

   public function delete()
{
    $expense_id = $this->request->getPost('expense_id');

    if (!$expense_id) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid expense ID']);
    }

    $expenseModel = new \App\Models\Expense_Model();
    $expenseModel->delete($expense_id);

    return $this->response->setJSON(['status' => 'success']);
}


    //for report
    public function report()
    {
        return view('expensereport');
    }

   public function getExpenseReportAjax()
{
    $model = new \App\Models\Expense_Model();

    $date     = $this->request->getPost('date');       
    $month    = $this->request->getPost('month');
    $year     = $this->request->getPost('year');
    $fromDate = $this->request->getPost('fromDate');
    $toDate   = $this->request->getPost('toDate');

    $builder = $model->builder();

    
    if (!empty($fromDate) && !empty($toDate)) {
        $builder->where('date >=', $fromDate)->where('date <=', $toDate);
    }
    
    elseif (!empty($date)) {
        $builder->where('DATE(date)', $date);
    }
    
    elseif (!empty($month) && !empty($year)) {
        $builder->where('MONTH(date)', $month);
        $builder->where('YEAR(date)', $year);
    }
   
    elseif (!empty($year)) {
        $builder->where('YEAR(date)', $year);
    }

    $builder->orderBy('date', 'DESC')->orderBy('id', 'DESC');

    $data = $builder->get()->getResult();

    return $this->response->setJSON($data);

}

    }
