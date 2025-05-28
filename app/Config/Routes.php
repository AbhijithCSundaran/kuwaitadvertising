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
$routes->post('manageuser/deleteuser', 'Manageuser::deleteuser');
$routes->post('manageuser/userlist', 'Manageuser::userlist');


$routes->get('managecompany', 'Managecompany::index'); 
$routes->post('managecompany/save', 'Managecompany::save'); 
$routes->get('managecompany/list', 'Managecompany::companyList');
$routes->get('managecompany/getCompany/(:num)', 'Managecompany::getCompany/$1');
$routes->get('companylist', 'Managecompany::companyList');
$routes->get('addcompany', 'ManageCompany::add');
$routes->match(['post', 'delete'], 'managecompany/delete/(:num)', 'Managecompany::delete/$1');






