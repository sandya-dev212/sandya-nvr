<?php

namespace Config;

$routes = Services::routes();

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

// ====== NVR Routes ======
$routes->get('/', 'AuthController::entrypoint');           // root -> login/dashboard
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::loginPost');
$routes->get('logout', 'AuthController::logout');

$routes->get('dashboard', 'DashboardController::index');

// Cameras (ADMIN)
$routes->get ('/cameras',               'CamerasController::index');
$routes->get ('/cameras/create',        'CamerasController::create');
$routes->post('/cameras/create',        'CamerasController::create');
$routes->get ('/cameras/edit/(:num)',   'CamerasController::edit/$1');
$routes->post('/cameras/edit/(:num)',   'CamerasController::edit/$1');
$routes->get ('/cameras/delete/(:num)', 'CamerasController::delete/$1');

// ====== END NVR Routes ======

