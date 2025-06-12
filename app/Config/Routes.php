<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('dashboard','Dashboard::index');

$routes->post('login/authenticate', 'Login::authenticate');

$routes->get('manageuser', 'Manageuser::index');
$routes->get('adduser', 'Manageuser::index');
$routes->get('adduser/(:num)', 'Manageuser::index/$1'); 
$routes->get('adduserlist', 'Manageuser::add');
$routes->post('manageuser/save', 'Manageuser::save');
$routes->get('manageuser/getUser/(:num)', 'Manageuser::getUser/$1');
$routes->post('manageuser/delete/(:num)', 'Manageuser::delete/$1');
$routes->post('manageuser/userlist', 'Manageuser::userlist');



$routes->get('managecompany', 'Managecompany::index'); 
$routes->post('managecompany/save', 'Managecompany::save'); 
$routes->get('managecompany/list', 'Managecompany::companyList');
$routes->get('companylist', 'Managecompany::companyList'); 
$routes->get('managecompany/getCompany/(:num)', 'Managecompany::getCompany/$1');
$routes->get('addcompany', 'Managecompany::add');            // New form
$routes->get('addcompany/(:num)', 'Managecompany::add/$1');  // Edit form
$routes->match(['post', 'delete'], 'managecompany/delete/(:num)', 'Managecompany::delete/$1');
$routes->get('managecompany/getAllCompanies', 'Managecompany::getAllCompanies'); // ✅ Add this line


$routes->get('rolemanagement/create', 'Rolemanagement::create');
$routes->post('rolemanagement/store', 'Rolemanagement::store');
$routes->get('rolemanagement/rolelist', 'Rolemanagement::rolelist');
$routes->get('rolemanagement/rolelistajax', 'Rolemanagement::rolelistajax');
$routes->get('rolemanagement/edit/(:num)', 'Rolemanagement::edit/$1');
$routes->post('rolemanagement/update/(:num)', 'Rolemanagement::update/$1');
$routes->post('rolemanagement/delete', 'Rolemanagement::delete');


$routes->get('add_estimate', 'Estimate::add_estimate'); 
$routes->post('estimate/save', 'Estimate::save'); 
$routes->get('estimate/estimatelistajax', 'Estimate::estimatelistajax');
$routes->post('estimate/delete', 'Estimate::delete');
$routes->get('estimatelist', 'Estimate::estimatelist'); // Optional: view route
$routes->get('estimate/edit/(:num)', 'Estimate::edit/$1');

$routes->get('expense', 'Expense::index'); 
$routes->get('addexpenselist', 'Expense::index'); 
$routes->get('addexpense', 'Expense::create');
$routes->get('addexpense/(:num)', 'Expense::create/$1');
$routes->post('expense/store', 'Expense::store');
$routes->post('expense/list', 'Expense::expenselistajax');
$routes->post('expense/delete/(:num)', 'Expense::delete/$1');
$routes->post('expense/delete', 'Expense::delete'); 
