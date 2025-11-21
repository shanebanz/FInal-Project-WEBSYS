<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public routes
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::register');
$routes->get('verify-email/(:any)', 'Auth::verifyEmail/$1');
$routes->get('forgot-password', 'Auth::forgotPassword');
$routes->post('forgot-password', 'Auth::forgotPassword');
$routes->get('reset-password/(:any)', 'Auth::resetPassword/$1');
$routes->post('reset-password', 'Auth::resetPassword');
$routes->get('logout', 'Auth::logout');

// Protected routes (require authentication)
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');
    
    // Equipment Management
    $routes->get('equipment', 'Equipment::index');
    $routes->get('equipment/create', 'Equipment::create');
    $routes->post('equipment/create', 'Equipment::create');
    $routes->get('equipment/edit/(:num)', 'Equipment::edit/$1');
    $routes->post('equipment/edit/(:num)', 'Equipment::edit/$1');
    $routes->get('equipment/delete/(:num)', 'Equipment::delete/$1');
    $routes->get('equipment/view/(:num)', 'Equipment::view/$1');
    
    // User Management
    $routes->get('users', 'Users::index');
    $routes->get('users/create', 'Users::create');
    $routes->post('users/create', 'Users::create');
    $routes->get('users/edit/(:num)', 'Users::edit/$1');
    $routes->post('users/edit/(:num)', 'Users::edit/$1');
    $routes->get('users/deactivate/(:num)', 'Users::deactivate/$1');
    $routes->get('users/activate/(:num)', 'Users::activate/$1');
    $routes->get('users/view/(:num)', 'Users::view/$1');
    
    // Borrowing Management
    $routes->get('borrowings', 'Borrowings::index');
    $routes->get('borrowings/create', 'Borrowings::create');
    $routes->post('borrowings/create', 'Borrowings::create');
    $routes->get('borrowings/return/(:num)', 'Borrowings::return/$1');
    $routes->post('borrowings/return/(:num)', 'Borrowings::return/$1');
    $routes->get('borrowings/view/(:num)', 'Borrowings::view/$1');
    
    // Reservation Management
    $routes->get('reservations', 'Reservations::index');
    $routes->get('reservations/create', 'Reservations::create');
    $routes->post('reservations/create', 'Reservations::create');
    $routes->get('reservations/approve/(:num)', 'Reservations::approve/$1');
    $routes->get('reservations/cancel/(:num)', 'Reservations::cancel/$1');
    $routes->get('reservations/view/(:num)', 'Reservations::view/$1');
    
    // Reports
    $routes->get('reports', 'Reports::index');
    $routes->get('reports/active-equipment', 'Reports::activeEquipment');
    $routes->get('reports/unusable-equipment', 'Reports::unusableEquipment');
    $routes->get('reports/borrowing-history', 'Reports::borrowingHistory');
    $routes->post('reports/borrowing-history', 'Reports::borrowingHistory');
    
    // About Page
    $routes->get('about', 'Pages::about');
    
    // Profile
    $routes->get('profile', 'Profile::index');
    $routes->post('profile/update', 'Profile::update');
    $routes->post('profile/change-password', 'Profile::changePassword');
});