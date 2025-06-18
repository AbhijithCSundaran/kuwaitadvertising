<?php

namespace App\Controllers;
use App\Models\Login_Model;

class Dashboard extends BaseController
{
    public function index()
    {
         return view('dashboard');
    }
}
