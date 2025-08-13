<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\Rolemanagement_Model;
use CodeIgniter\Controller;

class Rolemanagement extends Controller
{
    protected $roleModel;
    protected $roleMenuModel;
    protected $menus = [
    'dashboard'         => 'Dashboard',
    'adduserlist'       => 'Users',
    'companylist'       => 'Companies',
    'estimatelist'      => 'Estimates',
    'invoices'          => 'Invoice',
    'expense'           => 'Expense',
    'ledger'            => 'Ledger',
    'reports'           => 'Reports',
    'rolemanagement'    => 'Role Management',
    'customer'          => 'Customer List',
    'cashreceipt'       => 'Cash Receipt'  
];
    
    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->roleMenuModel = new Rolemanagement_Model();
        $this->db = \Config\Database::connect(); 
        helper(['form', 'url']);

        $session = \Config\Services::session();
        if (!$session->get('logged_in')) {
            header('Location: ' . base_url('/'));
            exit;
        }
    }

    public function create()
    {
        return view('roleform', [
            'menus' => $this->menus
        ]);
    }

    public function store()
    {
        $role_name_raw = $this->request->getPost('role_name');
        $access_data = $this->request->getPost('access');

        $normalized_role_name = trim(preg_replace('/\s+/', ' ', strtolower($role_name_raw)));

        $duplicate = $this->roleModel
            ->where('REPLACE(LOWER(TRIM(role_name)), " ", "") =', str_replace(' ', '', $normalized_role_name))
            ->first();

        if ($duplicate) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Role Already Exists.']);
        }

        $this->roleModel->insert([
            'role_name' => ucwords($normalized_role_name),
        ]);

         $company_id = session()->get('company_id');
    if (empty($company_id)) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Company ID missing.']);
    }

    // Insert role with company_id
    $this->roleModel->insert([
        'role_name'  => ucwords($normalized_role_name),
        'company_id' => $company_id
    ]);

        $role_id = $this->roleModel->getInsertID();

        if (!empty($access_data)) {
            foreach ($access_data as $menu => $value) {
                $this->roleMenuModel->insert([
                    'role_id' => $role_id,
                    'menu_name' => $menu,
                    'access' => 1
                ]);
            }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Role Created Successfully.']);
    }

    public function rolelist()
    {
        return view('rolelist');
    }

   public function rolelistajax()
{
    header('Content-Type: application/json');

    $draw = $_POST['draw'] ?? 1;
    $fromstart = $_POST['start'] ?? 0;
    $tolimit = $_POST['length'] ?? 10;
    $search = $_POST['search']['value'] ?? '';

    $search = trim($this->db->escapeLikeString($search));
    $condition = "1=1";
    $company_id = session()->get('company_id'); // your selected company
// do NOT append company_id to $condition here

if (empty($company_id)) {
    die("Company ID missing in session!");
}
    if (!empty($search)) {
        $condition .= " AND (
            role_name LIKE '%{$search}%' 
            OR DATE_FORMAT(created_at, '%d-%m-%Y') LIKE '%{$search}%' 
            OR DATE_FORMAT(updated_at, '%d-%m-%Y') LIKE '%{$search}%'
        )";
    }

    // Sorting
    $columns = ['slno', 'role_name', 'permissions', 'created_at', 'updated_at', 'action', 'role_id'];
    $orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
    $orderDir = $_POST['order'][0]['dir'] ?? 'desc';
    $orderBy = $columns[$orderColumnIndex] ?? 'role_id';
    $allowedOrderColumns = ['role_name', 'created_at', 'updated_at', 'role_id'];
    if (!in_array($orderBy, $allowedOrderColumns)) {
        $orderBy = 'role_id';
    }

    $slno = $fromstart + 1;

    // Pass $company_id to match your model signature
    $totalRec = $this->roleModel->getAllFilteredRecords($condition, $fromstart, $tolimit, $orderBy, $orderDir, $company_id);
    $result = [];
    $searchLower = strtolower($search);

    foreach ($totalRec as $role) {
        $permissions = $this->roleMenuModel
            ->where('role_id', $role->role_id)
            ->where('access', 1)
            ->findAll();

        $menuList = [];
        foreach ($permissions as $perm) {
            $menuKey = $perm['menu_name'];
            $menuLabel = $this->menus[$menuKey] ?? $menuKey;
            $menuList[] = $menuLabel;
        }

        if (!empty($searchLower)) {
            $allPermissions = implode(' ', array_map('strtolower', $menuList));
            $created = date('d-m-Y', strtotime($role->created_at ?? ''));
            $updated = date('d-m-Y', strtotime($role->updated_at ?? ''));
            $roleName = strtolower($role->role_name);
            $searchText = "$roleName $allPermissions $created $updated";

            if (strpos($searchText, $searchLower) === false) {
                continue;
            }
        }

        $result[] = [
            'slno'        => $slno++,
            'role_id'     => $role->role_id,
            'role_name'   => $role->role_name,
            'created_at'  => $role->created_at,
            'updated_at'  => $role->updated_at,
            'permissions' => $menuList
        ];
    }

    // Also pass $company_id to these methods
   $totalRec = $this->roleModel->getAllFilteredRecords($condition, $fromstart, $tolimit, $orderBy, $orderDir, $company_id);
$totalCount = $this->roleModel->getAllRoleCount($company_id);
$filteredCountObj = $this->roleModel->getFilterRoleCount($condition, $company_id);
$filteredCount = $filteredCountObj->filRecords ?? 0;

    echo json_encode([
        "draw" => intval($draw),
        "recordsTotal" => $totalCount,
        "recordsFiltered" => $filteredCount,
        "data" => $result
    ]);
}

    public function delete()
    {
        $role_id = $this->request->getPost('role_id');

        if (!$role_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid Role ID']);
        }

        $this->roleModel->delete($role_id);
        $this->roleMenuModel->where('role_id', $role_id)->delete();

        return $this->response->setJSON(['status' => 'success']);
    }

    public function edit($id)
    {
        $role = $this->roleModel->find($id);
        $permissions = $this->roleMenuModel->where('role_id', $id)->findAll();

        $access = [];
        foreach ($permissions as $perm) {
            $access[$perm['menu_name']] = $perm['access'];
        }

        return view('roleform', [
            'role' => $role,
            'access' => $access,
            'menus' => $this->menus,
        ]);
    }

    public function update($id)
    {
        $role_name_raw = $this->request->getPost('role_name');
        $access_data = $this->request->getPost('access') ?? [];

        $normalized_role_name = trim(preg_replace('/\s+/', ' ', strtolower($role_name_raw)));

        $normalizedAccess = [];
        foreach (array_keys($this->menus) as $menu) {
            $normalizedAccess[$menu] = isset($access_data[$menu]) ? 1 : 0;
        }

        $existingRole = $this->roleModel->find($id);
        $existingPermissions = $this->roleMenuModel->where('role_id', $id)->findAll();

        $oldAccess = [];
        foreach (array_keys($this->menus) as $menu) {
            $oldAccess[$menu] = 0;
        }
        foreach ($existingPermissions as $perm) {
            $oldAccess[$perm['menu_name']] = (int) $perm['access'];
        }

        $duplicate = $this->roleModel
            ->where('REPLACE(LOWER(TRIM(role_name)), " ", "") =', str_replace(' ', '', $normalized_role_name))
            ->where('role_id !=', $id)
            ->first();

        if ($duplicate) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Role Name Already Exists.']);
        }

        $currentRoleNameNormalized = strtolower(trim(preg_replace('/\s+/', ' ', $existingRole['role_name'])));
        $isNameChanged = $currentRoleNameNormalized !== $normalized_role_name;
        $isAccessChanged = $normalizedAccess !== $oldAccess;

        if (!$isNameChanged && !$isAccessChanged) {
            return $this->response->setJSON(['status' => 'info', 'message' => 'No Changes Detected To Update.']);
        }

        $this->roleModel->update($id, [
            'role_name' => ucwords($normalized_role_name),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->roleMenuModel->where('role_id', $id)->delete();
        foreach ($normalizedAccess as $menu => $value) {
            if ($value == 1) {
                $this->roleMenuModel->insert([
                    'role_id' => $id,
                    'menu_name' => $menu,
                    'access' => 1
                ]);
            }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Role Updated Successfully.']);
    }
}
