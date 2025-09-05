<?php

namespace Config;

use Config\Services;
/** @var \CodeIgniter\Router\RouteCollection $routes */
$routes = Services::routes();

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('DashboardController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/* Root */
$routes->get('/', 'DashboardController::index');

/* Auth */
$routes->match(['get','post'], 'login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');
$routes->get('whoami', 'AuthController::whoami'); // debug

/* Dashboard */
$routes->get('dashboard', 'DashboardController::index');

/* Settings (read-only dulu) */
$routes->get('settings', 'SettingsController::index');

/* Cameras (ADMIN) */
$routes->get('cameras', 'CamerasController::index');          // list
$routes->match(['get','post'], 'cameras/create', 'CamerasController::create');
$routes->match(['get','post'], 'cameras/edit/(:num)', 'CamerasController::edit/$1');
$routes->post('cameras/delete/(:num)', 'CamerasController::delete/$1');
