<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\Rolemanagement_Model;
use CodeIgniter\Controller;

class Rolemanagement extends Controller
{
    protected $roleModel;
    protected $roleMenuModel;
    protected $menus = ['Dashboard', 'Users', 'Companies', 'Estimates', 'Invoice', 'Expense', 'Ledger', 'Reports'];

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->roleMenuModel = new Rolemanagement_Model();
        $this->db = \Config\Database::connect(); 
        helper(['form', 'url']);
    }


    public function create()
    {
        return view('roleform', ['menus' => $this->menus]);
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
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Role Already Exists.']);
            }
            return redirect()->back()->with('error', 'Role Already Exists.');
        }

       
        $this->roleModel->insert([
            'role_name'  => ucwords($normalized_role_name), 
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

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Role Created Successfully.']);
        }

        return redirect()->to(base_url('rolemanagement/rolelist'))->with('success', 'Role Created Successfully.');
    }


    public function rolelist()
    {
        return view('rolelist');
    }

    public function rolelistajax()
{
    header('Content-Type: application/json'); // ensure correct JSON output

    $draw       = $_POST['draw'] ?? 1;
    $fromstart  = $_POST['start'] ?? 0;
    $tolimit    = $_POST['length'] ?? 10;
    $search     = $_POST['search']['value'] ?? '';

    $condition = "1=1";
    if (!empty($search)) {
        $search = $this->db->escapeLikeString(trim($search));
        $condition .= " AND role_name LIKE '%{$search}%'";
    }

    $slno = $fromstart + 1;

    $roleModel = new \App\Models\RoleModel();
    $menuModel = new \App\Models\Rolemanagement_model();

    $totalRec = $roleModel->getAllFilteredRecords($condition, $fromstart, $tolimit);
    $result = [];

    foreach ($totalRec as $role) {
        $permissions = $menuModel->where('role_id', $role->role_id)
            ->where('access', 1)
            ->findAll();

        $menuList = array_column($permissions, 'menu_name');

        $result[] = [
            'slno'        => $slno++,
            'role_id'     => $role->role_id,
            'role_name'   => $role->role_name,
            'created_at'  => $role->created_at,
            'updated_at'  => $role->updated_at,
            'permissions' => $menuList
        ];
    }

    $totalCount = $roleModel->getAllRoleCount()->totroles;
    $filteredCount = $roleModel->getFilterRoleCount($condition, $fromstart, $tolimit)->filRecords;

    $response = [
        "draw" => intval($draw),
        "recordsTotal" => $totalCount,
        "recordsFiltered" => $filteredCount,
        "data" => $result
    ];

    echo json_encode($response);
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

    
    $normalized_role_name = trim(preg_replace('/\s+/', ' ', strtolower($role_name_raw)));

    $access_data = $this->request->getPost('access') ?? [];

    
    $normalizedAccess = [];
    foreach ($this->menus as $menu) {
        $normalizedAccess[$menu] = isset($access_data[$menu]) ? 1 : 0;
    }

    $existingRole = $this->roleModel->find($id);
    $existingPermissions = $this->roleMenuModel->where('role_id', $id)->findAll();

    $oldAccess = [];
    foreach ($this->menus as $menu) {
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
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Role Name Already Exists.']);
        }
        return redirect()->back()->with('error', 'Role Name Already Exists.');
    }

    
    $currentRoleNameNormalized = strtolower(trim(preg_replace('/\s+/', ' ', $existingRole['role_name'])));
    $isNameChanged = $currentRoleNameNormalized !== $normalized_role_name;
    $isAccessChanged = $normalizedAccess !== $oldAccess;

    if (!$isNameChanged && !$isAccessChanged) {
        return redirect()->back()->with('info', 'No Changes Detected To Update.');
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

    if ($this->request->isAJAX()) {
        return $this->response->setJSON(['status' => 'success', 'message' => 'Role Updated Successfully.']);
    }

    session()->setFlashdata('success', 'Role Updated Successfully.');
    return redirect()->to(base_url('rolemanagement/edit/' . $id));
}


}
