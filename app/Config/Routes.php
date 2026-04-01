<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Route untuk Meja
$routes->get('api/meja', 'api\MejaController::index');

// Grouping API biar rapi
$routes->group('api', function($routes) {
    // Handle OPTIONS untuk CORS (Penting buat Flutter Web/Chrome)
    $routes->options('login', 'api\LoginController::login'); 
    $routes->post('login', 'api\LoginController::login');
});

$routes->group('api', function($routes) {
    $routes->post('login', 'api\LoginController::login');
    $routes->post('register', 'api\RegisterController::create'); // Tambahkan ini
});