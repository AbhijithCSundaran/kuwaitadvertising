<?php
 
namespace App\Controllers;
 
use App\Controllers\BaseController;
use App\Models\EstimateModel;
use App\Models\EstimateItemModel;
use App\Models\customerModel;
use App\Models\Manageuser_Model;
 use App\Models\Managecompany_Model;
 use App\Models\RoleModel;

class Estimate extends BaseController
{
    public function estimatelist()
    {
        return view('estimatelist');
    }
    public function __construct(){
       // $this->session = \Session::get('');
       $this->session = \Config\Services::session();
 
       $session = \Config\Services::session();
        if (!$session->get('logged_in')) {
            header('Location: ' . base_url('/'));
            exit;
        }
    }
 
    public function add_estimate($id = null)
    {
        $estimateModel = new EstimateModel();
        $estimateItemModel = new EstimateItemModel();
        $customerModel = new customerModel();
 
        $data['customers'] = $customerModel->where('is_deleted', 0)->orderBy('customer_id', 'DESC')->findAll();
        $data['estimate'] = null;
        $data['items'] = [];
 
        if ($id) {
            $data['estimate'] = $estimateModel->find($id);
            $data['items'] = $estimateItemModel->where('estimate_id', $id)->findAll();
        }
 
        return view('add_estimate', $data);
    }
 
public function save()
{
    $estimateId = $this->request->getPost('estimate_id');
    $customerId = $this->request->getPost('customer_id');
    $address = trim($this->request->getPost('customer_address'));
    $discount = (float) $this->request->getPost('discount');
    $description = $this->request->getPost('description');
    $price = $this->request->getPost('price');
    $quantity = $this->request->getPost('quantity');
    $total = $this->request->getPost('total');
    $customerName = trim($this->request->getPost('customer_name'));
    $phoneNumber = trim($this->request->getPost('phone_number'));


    if (empty($customerId) || empty($address)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Please fill customer name and address.'
        ]);
    }

    $validItems = 0;
    foreach ($description as $key => $desc) {
        if (!empty(trim($desc))) {
            $validItems++;
        }
    }

    if ($validItems === 0) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Please fill at least one item with description.'
        ]);
    }

    // Recalculate totals
    $subtotal = 0;
    foreach ($total as $t) {
        $subtotal += (float)$t;
    }

    $discountAmount = ($subtotal * $discount) / 100;
    $grandTotal = $subtotal - $discountAmount;

    $estimateData = [
        'customer_id' => $customerId,
        'customer_address' => $address,
        'discount' => $discount,
        'total_amount' => $grandTotal,
        'date' => date('Y-m-d'),
        'phone_number' => $phoneNumber, 
    ];

    // Build items array
    $items = [];
    foreach ($description as $key => $desc) {
        $desc = trim($desc);
        $unitPrice = trim($price[$key]);
        $qty = trim($quantity[$key]);

        if ($desc === '' || $unitPrice === '' || $qty === '') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Each item must have Description, Unit Price, and Quantity filled.'
            ]);
        }

        $items[] = [
            'description' => $desc,
            'price' => (float)$unitPrice,
            'quantity' => (float)$qty,
            'total' => (float)$total[$key]
        ];
    }

    $estimateModel = new \App\Models\EstimateModel();
    $estimateItemModel = new \App\Models\EstimateItemModel();
    $customerModel = new \App\Models\customerModel();

    // ✅ Check and update customer name or address if needed
    $customer = $customerModel->find($customerId);
    if ($customer) {
        if (
            $customer['name'] !== $customerName ||
            $customer['address'] !== $address
        ) {
            $customerModel->update($customerId, [
                'name' => $customerName,
                'address' => $address
            ]);
        }
    }

    if (!empty($estimateId)) {
        // Update mode
        $existing = $estimateModel->find($estimateId);
        if (!$existing) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Estimate Not Found.'
            ]);
        }

        $hasChanges = (
            $existing['customer_id'] != $customerId ||
            $existing['customer_address'] !== $address ||
            $existing['discount'] != $discount ||
            $existing['total_amount'] != $grandTotal
        );

        if ($hasChanges) {
            $estimateModel->update($estimateId, $estimateData);
            $estimateItemModel->where('estimate_id', $estimateId)->delete();
            foreach ($items as &$item) {
                $item['estimate_id'] = $estimateId;
            }
            $estimateItemModel->insertBatch($items);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Estimate Updated Successfully.',
                'estimate_id' => $estimateId
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'nochange',
                'message' => 'No Changes Detected.',
                'estimate_id' => $estimateId
            ]);
        }

    } else {
        // Insert mode
        $estimateId = $estimateModel->insert($estimateData);
        foreach ($items as &$item) {
            $item['estimate_id'] = $estimateId;
        }
        $estimateItemModel->insertBatch($items);
        

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Generating Estimate.',
            'estimate_id' => $estimateId
        ]);
    }
}

 
 public function estimatelistajax()
{
    $request = service('request');
    $draw = $request->getPost('draw');
    $start = $request->getPost('start');
    $length = $request->getPost('length');
    $searchValue = $request->getPost('search')['value'];
 
    $orderColumnIndex = $request->getPost('order')[0]['column'] ?? 8; // fallback to estimate_id
    $orderDir = $request->getPost('order')[0]['dir'] ?? 'desc';
 
    // Match DataTable column index to DB field names
    $columns = [
        0 => 'estimate_id',          // slno
        1 => 'customers.name',
        2 => 'customers.address',
        3 => 'estimates.total_amount', // subtotal (not in DB directly)
        4 => 'estimates.discount',
        5 => 'estimates.total_amount',
        6 => 'estimates.date',
        7 => 'estimates.estimate_id',
        8 => 'estimates.estimate_id'
    ];
    $orderByColumn = $columns[$orderColumnIndex] ?? 'estimates.estimate_id';
 
    $estimateModel = new EstimateModel();
    $itemModel = new EstimateItemModel();
 
    $totalRecords = $estimateModel->getEstimateCount();
    $filteredRecords = $estimateModel->getFilteredCount($searchValue);
    $records = $estimateModel->getFilteredEstimates($searchValue, $start, $length, $orderByColumn, $orderDir);
 
    $data = [];
    $slno = $start + 1;
 
    foreach ($records as $row) {
        $items = $itemModel->where('estimate_id', $row['estimate_id'])->findAll();
        $descList = array_column($items, 'description');
 
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (float)$item['total'];
        }
 
        $data[] = [
            'slno'              => $slno++,
            'estimate_id'       => $row['estimate_id'],
            'customer_name'     => $row['customer_name'],
            'customer_address'  => $row['customer_address'],
            'subtotal'          => round($subtotal, 2),
            'discount'          => $row['discount'],
            'total_amount'      => round($row['total_amount'], 2),
            'date'              => $row['date'],
            'description'       => implode(', ', $descList),
        
        ];
    }
 
    return $this->response->setJSON([
        'draw' => intval($draw),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => $data,
    ]);
}
 
    public function delete()
    {
        $estimate_id = $this->request->getPost('estimate_id');
        if (!$estimate_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid ID']);
        }
 
        $estimateModel = new EstimateModel();
        $itemModel = new EstimateItemModel();
 
        $itemModel->where('estimate_id', $estimate_id)->delete();
        $estimateModel->delete($estimate_id);
 
        return $this->response->setJSON(['status' => 'success']);
    }
 
   public function edit($id)
{
    $estimateModel = new EstimateModel();
    $estimateItemModel = new EstimateItemModel();
    $customerModel = new customerModel();

    // Get estimate
    $estimate = $estimateModel
        ->select('estimates.*, customers.address AS customer_address, customers.name AS customer_name')
        ->join('customers', 'customers.customer_id = estimates.customer_id', 'left')
        ->where('estimate_id', $id)
        ->first();

    if (!$estimate) {
        return redirect()->to('estimatelist')->with('error', 'Estimate not found.');
    }

    // Now get customer details using estimate's customer_id
    $customer = $customerModel->find($estimate['customer_id']);

    $data = [
        'estimate' => $estimate,
        'items' => $estimateItemModel->where('estimate_id', $id)->findAll(),
        'customers' => $customerModel->where('is_deleted', 0)->orderBy('customer_id', 'DESC')->findAll(),
        'customer' => $customer
    ];

    return view('add_estimate', $data);
}

  public function generateEstimate($id)
{
    $estimateModel = new EstimateModel();
    $itemModel = new EstimateItemModel();
    $userModel = new Manageuser_Model();
    $companyModel = new Managecompany_Model();
    $roleModel = new RoleModel();

    // Fetch estimate with customer info
    $estimate = $estimateModel
        ->select('estimates.*, customers.name AS customer_name, customers.address AS customer_address')
        ->join('customers', 'customers.customer_id = estimates.customer_id', 'left')
        ->where('estimate_id', $id)
        ->first();

    if (!$estimate) {
        return redirect()->to('/estimatelist')->with('error', 'Estimate not found.');
    }

    // Fetch related data
    $items = $itemModel->where('estimate_id', $id)->findAll();
    $userId = session()->get('user_id');
    $userName = session()->get('user_Name');
    $roleId = session()->get('role_Id'); // ✅ Correct key from session
    $companyId = session()->get('company_id');

    // Get role name
    $roleName = session()->get('role_Name'); // Try session first
    if (!$roleName && $roleId) {
        $role = $roleModel->find($roleId);
        $roleName = $role['role_name'] ?? '';
    }

    // Fetch company details
    $company = $companyModel->find($companyId);

    // Prepare data
    $data = [
        'estimate'      => $estimate,
        'items'         => $items,
        'user_id'       => $userId,
        'user_name'     => $userName,
        'role_name'     => $roleName,
        'company_name'  => $company['company_name'] ?? ''
    ];

    // Load view
    if ($companyId == 69) {
        return view('generateestimate', $data);
    } elseif ($companyId == 70) {
        return view('generatequotation', $data);
    } else {
        return view('generateestimate', $data);
    }
}


        // dashboardlisting
    public function recentEstimates()
        {
            $estimateModel = new \App\Models\EstimateModel();
            $itemModel = new \App\Models\EstimateItemModel();
    
            $estimates = $estimateModel->getRecentEstimatesWithCustomer(10); // Includes name & address
 
        foreach ($estimates as &$est) {
            $items = $itemModel->where('estimate_id', $est['estimate_id'])->findAll();
 
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += (float)$item['total'];
            }
 
            $est['sub_total'] = round($subtotal, 2);
        }
 
        return $this->response->setJSON($estimates);
    }
 
    public function viewByCustomer($customerId)
    {
        $estimateModel = new \App\Models\EstimateModel();
        $itemModel = new \App\Models\EstimateItemModel();
        $customerModel = new \App\Models\customerModel();

        $estimates = $estimateModel
            ->where('customer_id', $customerId)
            ->orderBy('date', 'desc')
            ->findAll();

        foreach ($estimates as &$est) {
            $items = $itemModel->where('estimate_id', $est['estimate_id'])->findAll();
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += (float)$item['total'];
            }
            $est['items'] = $items;
            $est['subtotal'] = round($subtotal, 2);
        }

        $customer = $customerModel->find($customerId);

        return view('customer_estimates', [
            'estimates' => $estimates,
            'customer' => $customer
        ]);

    }
    public function fromEstimate($estimateId)
    {
        $estimateModel = new \App\Models\EstimateModel();
        $estimateItemModel = new \App\Models\EstimateitemModel();

        $estimate = $estimateModel->find($estimateId);
        if (!$estimate) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Estimate not found.");
        }

        $items = $estimateItemModel->where('estimate_id', $estimateId)->findAll();

        // Load your invoice design view and pass data
        return view('invoice/add_invoice', [
            'estimate' => $estimate,
            'items' => $items
        ]);
    }
}