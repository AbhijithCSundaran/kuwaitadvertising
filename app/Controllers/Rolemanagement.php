<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\Rolemanagement_model;
use CodeIgniter\Controller;

class Rolemanagement extends Controller
{
    protected $roleModel;
    protected $roleMenuModel;
    protected $menus = ['Dashboard', 'Users', 'Companies', 'Estimates', 'invoice', 'expense', 'ledger', 'Reports'];

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->roleMenuModel = new Rolemanagement_model();
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
                return $this->response->setJSON(['status' => 'error', 'message' => 'Role already exists.']);
            }
            return redirect()->back()->with('error', 'Role already exists.');
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
            return $this->response->setJSON(['status' => 'success', 'message' => 'Role created successfully.']);
        }

        return redirect()->to(base_url('rolemanagement/rolelist'))->with('success', 'Role created successfully.');
    }


    public function rolelist()
    {
        return view('rolelist');
    }

    public function rolelistajax()
    {
        $roleModel = new \App\Models\RoleModel();
        $menuModel = new \App\Models\Rolemanagement_model();

        $roles = $roleModel->findAll();
        $result = [];

        foreach ($roles as $role) {
            $permissions = $menuModel->where('role_id', $role['role_id'])
                                    ->where('access', 1)
                                    ->findAll();

            $menuList = array_column($permissions, 'menu_name');

            $result[] = [
                'role_id'     => $role['role_id'],
                'role_name'   => $role['role_name'],
                'created_at'  => $role['created_at'],
                'updated_at'  => $role['updated_at'],
                'permissions' => $menuList
            ];
        }

        return $this->response->setJSON(['roles' => $result]);
    }


    public function delete()
	{
		$role_id = $this->request->getPost('role_id');

		if (!$role_id) {
			return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid role ID']);
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
			return redirect()->back()->with('info', 'No changes detected to update.');
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
            return $this->response->setJSON(['status' => 'success', 'message' => 'Role updated successfully.']);
        }

        session()->setFlashdata('success', 'Role updated successfully.');
        return redirect()->to(base_url('rolemanagement/edit/' . $id));


	}

}
