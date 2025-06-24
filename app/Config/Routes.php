<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('dashboard','Dashboard::index');

$routes->get('logout', 'Auth::logout');

$routes->post('login/authenticate', 'Login::authenticate');
$routes->get('login','Login::index');

$routes->post('manageuser/userlistajax', 'Manageuser::userlistajax');
$routes->get('manageuser', 'Manageuser::index');
$routes->get('adduser', 'Manageuser::index');
$routes->get('adduser/(:num)', 'Manageuser::index/$1'); 
$routes->get('adduserlist', 'Manageuser::add');
$routes->post('manageuser/save', 'Manageuser::save');
$routes->get('manageuser/getUser/(:num)', 'Manageuser::getUser/$1');
$routes->post('manageuser/delete', 'Manageuser::delete');
$routes->post('manageuser/userlist', 'Manageuser::userlist');

$routes->get('managecompany', 'Managecompany::index'); 
$routes->post('managecompany/save', 'Managecompany::save'); 
$routes->get('managecompany/list', 'Managecompany::companyList');
$routes->get('companylist', 'Managecompany::companyList'); 
$routes->get('managecompany/getCompany/(:num)', 'Managecompany::getCompany/$1');
$routes->get('addcompany', 'Managecompany::add');           
$routes->get('addcompany/(:num)', 'Managecompany::add/$1');  
$routes->post('managecompany/delete', 'Managecompany::delete');
$routes->get('managecompany/getAllCompanies', 'Managecompany::getAllCompanies'); 
$routes->post('managecompany/companylistjson', 'Managecompany::companylistjson');



$routes->get('rolemanagement/create', 'Rolemanagement::create');
$routes->post('rolemanagement/store', 'Rolemanagement::store');
$routes->get('rolemanagement/rolelist', 'Rolemanagement::rolelist');
$routes->post('rolemanagement/rolelistajax', 'Rolemanagement::rolelistajax');
$routes->get('rolemanagement/edit/(:num)', 'Rolemanagement::edit/$1');
$routes->post('rolemanagement/update/(:num)', 'Rolemanagement::update/$1');
$routes->post('rolemanagement/delete', 'Rolemanagement::delete');

$routes->get('add_estimate', 'Estimate::add_estimate'); 
$routes->post('estimate/save', 'Estimate::save'); 
$routes->post('estimate/estimatelistajax', 'Estimate::estimatelistajax');
$routes->post('estimate/delete', 'Estimate::delete');
$routes->get('estimatelist', 'Estimate::estimatelist');
$routes->get('estimate/edit/(:num)', 'Estimate::edit/$1');
$routes->get('estimate/generateEstimate/(:num)', 'Estimate::generateEstimate/$1');
$routes->post('save', 'Estimate::saveEstimate');



// $routes->get('invoicelist', 'Invoice::invoicelist');

$routes->post('customer/create', 'Customer::create');
$routes->post('customer/get-address', 'Customer::get_address');
$routes->get('customer/search', 'Customer::search');

$routes->get('expense', 'Expense::index'); 
// $routes->get('addexpenselist', 'Expense::index'); 
$routes->get('addexpense', 'Expense::create');              
$routes->get('addexpense/(:num)', 'Expense::create/$1');
$routes->post('expense/store', 'Expense::store');
$routes->post('expense/list', 'Expense::expenselistajax');
$routes->post('expense/delete/(:num)', 'Expense::delete/$1');
$routes->post('expense/delete', 'Expense::delete'); 
$routes->post('expense/getExpensesAjax', 'Expense::getExpensesAjax');


// dashboard
$routes->post('dashboard/getTodayExpenseTotal', 'Dashboard::getTodayExpenseTotal');
$routes->post('dashboard/getMonthlyExpenseTotal', 'Dashboard::getMonthlyExpenseTotal');




//for report
$routes->get('expense/report', 'Expense::report');
$routes->post('expense/getExpenseReportAjax', 'Expense::getExpenseReportAjax');

$routes->get('sales/report', 'Sales::report');



$routes->get('companyledger', 'CompanyLedger::index');
$routes->post('companyledger/save', 'CompanyLedger::save');
