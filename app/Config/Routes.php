<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

auth()->routes($routes);

$routes->group('admin', ['filter' => 'session'], function ($routes) {
    $routes->get('blog', 'Admin\BlogController::index');
    $routes->get('blog/create', 'Admin\BlogController::create');
    $routes->post('blog/store', 'Admin\BlogController::store');
    $routes->get('blog/edit/(:num)', 'Admin\BlogController::edit/$1');
    $routes->post('blog/update/(:num)', 'Admin\BlogController::update/$1');
    $routes->get('blog/delete/(:num)', 'Admin\BlogController::delete/$1');
});

$routes->get('blog', 'Blog::index');
$routes->get('blog/view/(:segment)', 'Blog::view/$1');
$routes->post('blog/comment/(:num)', 'Blog::comment/$1');
