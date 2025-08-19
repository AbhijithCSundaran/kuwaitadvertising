<?php
 
namespace App\Controllers;
 
use App\Controllers\BaseController;
use App\Models\EstimateModel;
use App\Models\EstimateItemModel;
use App\Models\customerModel;
use App\Models\Manageuser_Model;
 use App\Models\Managecompany_Model;
 use App\Models\RoleModel;
 use Google\Cloud\Translate\V2\TranslateClient;


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
            'message' => 'Please Fill Customer Name And Address.'
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
            'message' => 'Please Fill At Least One Item With Description.'
        ]);
    }

    // Recalculate totals
    $subtotal = 0;
    foreach ($total as $t) {
        $subtotal += (float)$t;
    }

    $discountAmount = ($subtotal * $discount) / 100;
    $grandTotal = $subtotal - $discountAmount;

    $companyId = session()->get('company_id');
    $estimateData = [
        'customer_id' => $customerId,
        'customer_address' => $address,
        'discount' => $discount,
        'total_amount' => $grandTotal,
        'date' => date('Y-m-d'),
        'phone_number' => $phoneNumber, 
        'company_id'       => $companyId
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
                'message' => 'Each Item Must Have Description, Unit Price, And Quantity Filled.'
            ]);
        }

        $items[] = [
            'description' => $desc,
            'price' => (float)$unitPrice,
            'quantity' => (float)$qty,
            'total' => (float)$total[$key]
        ];
    }

    $estimateModel = new EstimateModel();
    $estimateItemModel = new EstimateItemModel();
    $customerModel = new customerModel();
    $customer = $customerModel->find($customerId);
        if ($customer) {
            // Update customer info if changed
            if ($customer['name'] !== $customerName || $customer['address'] !== $address) {
                $customerModel->update($customerId, [
                    'name' => $customerName,
                    'address' => $address
                ]);
            }

            // Fix: Allow discounts lower than fixed_discount, only block if exceeding max
            $maxDiscount = isset($customer['fixed_discount']) ? (float)$customer['fixed_discount'] : 100; // default 100%
            if ($discount > $maxDiscount) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => "Discount cannot exceed maximum allowed value of $maxDiscount%"
                ]);
            }

        }

    if (!empty($estimateId)) {
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
    $searchValue = trim($request->getPost('search')['value'] ?? '');
 
    $orderColumnIndex = $request->getPost('order')[0]['column'] ?? 8;
    $orderDir = $request->getPost('order')[0]['dir'] ?? 'desc';

    $columns = [
        0 => 'estimate_id',         
        1 => 'customers.name',
        2 => 'customers.address',
        3 => 'estimates.total_amount', 
        4 => 'estimates.discount',
        5 => 'estimates.total_amount',
        6 => 'estimates.date',
        7 => 'estimates.estimate_id',
        8 => 'estimates.estimate_id'
    ];
    $orderByColumn = $columns[$orderColumnIndex] ?? 'estimates.estimate_id';
 
    $companyId = session()->get('company_id');

    $estimateModel = new EstimateModel();
    $itemModel = new EstimateItemModel();
 
    $totalRecords = $estimateModel->getEstimateCount($companyId);
    $filteredRecords = $estimateModel->getFilteredCount($searchValue, $companyId);
    $records = $estimateModel->getFilteredEstimates($searchValue, $start, $length, $orderByColumn, $orderDir,  $companyId);
 
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
            'is_converted'      =>$row['is_converted'],
        
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
    $customer = $customerModel->find($estimate['customer_id']);

    $data = [
        'estimate' => $estimate,
        'items' => $estimateItemModel->where('estimate_id', $id)->findAll(),
        'customers' => $customerModel->where('is_deleted', 0)->orderBy('customer_id', 'DESC')->findAll(),
        'customer' => $customer
    ];

    return view('add_estimate', $data);
}
private function translateToArabic($text)
{
    if (empty($text)) {
        return '';
    }

    $url = "https://api.mymemory.translated.net/get?q=" . urlencode($text) . "&langpair=en|ar";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        return $text;
    }

    $result = json_decode($response, true);
    return $result['responseData']['translatedText'] ?? $text;
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
    $roleId = session()->get('role_Id'); 
    $companyId = session()->get('company_id');

    // Get role name
    $roleName = session()->get('role_Name');
    if (!$roleName && $roleId) {
        $role = $roleModel->find($roleId);
        $roleName = $role['role_name'] ?? '';
    }
    $companyId = $estimate['company_id'] ?? session()->get('company_id');
    $company = $companyModel->find($companyId) ?? [
        'company_name' => '',
        'company_name_ar' => '',
        'email' => '',
         'address' => '',
         'address_ar'   => '',
        'phone' => ''
    ];
  if (empty($company['company_name_ar']) && !empty($company['company_name'])) {
        $translated = $this->translateToArabic($company['company_name']);

        if (!empty($translated)) {
            $companyModel->update($companyId, ['company_name_ar' => $translated]);
            $company['company_name_ar'] = $translated;
        }
    }
     if (empty(trim($company['address_ar'])) && !empty(trim($company['address']))) {
        $translatedAddress = $this->translateToArabic($company['address']);
        if (!empty($translatedAddress) && $translatedAddress !== $company['address']) {
            $companyModel->update($companyId, ['address_ar' => $translatedAddress]);
            $company['address_ar'] = $translatedAddress;
        }
    }
    $data = [
        'estimate'      => $estimate,
        'items'         => $items,
        'user_id'       => $userId,
        'user_name'     => $userName,
        'role_name'     => $roleName,
        'company_name'  => $company['company_name'] ?? '',
        'company_name_ar'  => $company['company_name_ar'] ?? '',
          'address'      => $company['address'] ?? '',
        'address_ar'   => $company['address_ar'] ?? '',
        'company'   => $company
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
            $estimateModel = new EstimateModel();
            $itemModel = new EstimateItemModel();
    
            $estimates = $estimateModel->getRecentEstimatesWithCustomer(10); 
 
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
        $estimateModel = new EstimateModel();
        $itemModel = new EstimateItemModel();
        $customerModel = new customerModel();

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
}