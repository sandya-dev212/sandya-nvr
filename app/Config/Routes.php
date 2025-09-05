<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes = Services::routes();

// Default setup
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('DashboardController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false); // kita atur manual saja

// >>> ROOT KE DASHBOARD (bukan welcome) <<<
$routes->get('/', 'DashboardController::index');

// Auth
$routes->match(['get','post'], 'login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

// Dashboard
$routes->get('dashboard', 'DashboardController::index');

// Debug whoami (boleh hapus nanti)
$routes->get('whoami', 'AuthController::whoami');
