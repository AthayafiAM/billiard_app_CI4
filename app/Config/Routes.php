<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// 🔥 API GROUP
$routes->group('api', function($routes) {

    // LOGIN & REGISTER
    $routes->post('login', 'Api\LoginController::login');
    $routes->post('register', 'Api\RegisterController::create');

    // CLUBS
    $routes->get('clubs', 'Api\ClubController::index');
    $routes->post('clubs', 'Api\ClubController::create');
    $routes->put('clubs/(:num)', 'Api\ClubController::update/$1');
    $routes->delete('clubs/(:num)', 'Api\ClubController::delete/$1');

    // TABLES
    $routes->get('tables', 'Api\MejaController::index');
    $routes->post('tables', 'Api\MejaController::create');
    $routes->put('tables/(:num)', 'Api\MejaController::update/$1');
    $routes->delete('tables/(:num)', 'Api\MejaController::delete/$1');

    // 🔥 BOOKINGS (TARUH DI SINI)
    $routes->get('bookings', 'Api\BookingController::index');
    $routes->post('bookings', 'Api\BookingController::create');
    $routes->delete('bookings/(:num)', 'Api\BookingController::delete/$1');
    $routes->get('bookings/times', 'Api\BookingController::getBlockedTimes');

    // USERS
    $routes->get('profile', 'Api\UpdateProfileController::getProfile');
    $routes->post('update-profile', 'Api\UpdateProfileController::updateProfile');
    $routes->options('update-profile', 'Api\UpdateProfileController::updateProfile');
});