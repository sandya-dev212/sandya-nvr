<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/', 'DashboardController::index');
$routes->match(['get','post'],'/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

$routes->get('/dashboard', 'DashboardController::index');

$routes->get('/settings', 'SettingsController::index'); // admin only (sederhana dulu)
