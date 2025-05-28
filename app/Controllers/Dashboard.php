<?php

namespace App\Controllers;
use App\Models\Login_Model;

class Dashboard extends BaseController
{
    public function index()
    {
        // $model = new loginModel();

       
        // $data['user'] = $model->findAll();

        
        // return view('dashboard_view', $data);
         return view('dashboard');
    }
}
