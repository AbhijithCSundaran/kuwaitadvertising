<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\Rolemanagement_Model;
use CodeIgniter\Controller;

class Rolemanagement extends Controller
{
    protected $roleModel;
    protected $roleMenuModel;
    protected $menus = ['Dashboard', 'Users', 'Companies', 'Estimates', 'invoice', 'expense', 'ledger', 'Reports'];

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->roleMenuModel = new Rolemanagement_Model();
        helper(['form', 'url']);
    }

    public function create()
    {
        return view('roleform', ['menus' => $this->menus]);
    }

    public function store()
    {
        $role_name = $this->request->getPost('role_name');
        $access_data = $this->request->getPost('access');

        if ($this->roleModel->where('role_name', $role_name)->first()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Role Already Exists.']);
            }
            return redirect()->back()->with('error', 'Role Already Exists.');
        }

        $this->roleModel->insert([
            'role_name' => $role_name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
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
        $roleModel = new \App\Models\RoleModel();
        $menuModel = new \App\Models\Rolemanagement_model();
        
        $draw = $_POST['draw'];
        $fromstart = $_POST['start'];
        $tolimit = $_POST['length'];
        $order = $_POST['order'][0]['dir'];
        $search = $_POST['search']['value'];
		$slno = $fromstart + 1;
		$condition = "1=1";
		if($search) {
			$condition .= " and role_name like '%".trim($search)."%'";
		}
		$totalRec = $roleModel->getAllFilteredRecords($condition,$fromstart,$tolimit);
		$result = [];

        foreach ($totalRec as $role) {
            $permissions = $menuModel->where('role_id', $role->role_id)
                ->where('access', 1)
                ->findAll();

            $menuList = array_column($permissions, 'menu_name');

            $result[] = [
				'slno'=>$slno,
                'role_id' => $role->role_id,
                'role_name' => $role->role_name,
                'created_at' => $role->created_at,
                'updated_at' => $role->updated_at,
                'permissions' => $menuList
            ];
			$slno++;	
        }
		//response-
		$rowResult = $roleModel->getAllRoleCount();
		$totRoleCount = $rowResult->totroles;
		$filterRowResult = $roleModel->getFilterRoleCount($condition,$fromstart,$tolimit);
		$totFilterCounts = $filterRowResult->filRecords;
		
		$response = array("draw"=>intval($draw),
						"iTotalRecords"=>$totRoleCount,
						"iTotalDisplayRecords"=>$totFilterCounts,
						"roles"=>$result);
		
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
        $role_name = $this->request->getPost('role_name');
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


        $isNameChanged = $existingRole['role_name'] !== $role_name;
        $isAccessChanged = $normalizedAccess !== $oldAccess;

        if (!$isNameChanged && !$isAccessChanged) {
            return redirect()->back()->with('info', 'No Changes Detected To Update.');
        }


        $this->roleModel->update($id, [
            'role_name' => $role_name,
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
