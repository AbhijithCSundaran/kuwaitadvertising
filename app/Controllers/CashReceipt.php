<?php namespace App\Controllers;

use App\Models\CashReceiptModel;
use CodeIgniter\API\ResponseTrait;

class CashReceipt extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        return view('cashlist'); // Your view with the table and JS
    }

  public function ajaxList()
{
    return $this->response->setJSON([
        "draw" => 1,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => []
    ]);
}

}
