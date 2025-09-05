<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Router\RouteCollection;
use Config\Services;

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
$routes->setAutoRoute(false); // biar eksplisit

// ROOT → dashboard (bukan welcome)
$routes->get('/', 'DashboardController::index');

// Auth
$routes->match(['get','post'], 'login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');
$routes->get('whoami', 'AuthController::whoami'); // debug

// Dashboard
$routes->get('dashboard', 'DashboardController::index');
