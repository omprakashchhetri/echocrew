<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('enquiry', 'Enquiry::store', ['filter' => 'throttle:enquiry,5,3600']);

// SEO
$routes->get('services', 'Services::index');
$routes->get('services/(:segment)', 'Services::show/$1');
$routes->get('sitemap.xml', 'Sitemap::index');

// Brute-force guard on the login form; declared before Shield's own routes so it wins.
$routes->post('login', '\CodeIgniter\Shield\Controllers\LoginController::loginAction', ['filter' => 'throttle:login,10,300']);
auth()->routes($routes);

// Admin area: staff only (Shield permission `admin.access`). Every state change is POST + CSRF.
$routes->group('admin', ['filter' => 'permission:admin.access', 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');

    $routes->get('blog', 'BlogController::index');
    $routes->get('blog/create', 'BlogController::create');
    $routes->post('blog/store', 'BlogController::store');
    $routes->get('blog/edit/(:num)', 'BlogController::edit/$1');
    $routes->post('blog/update/(:num)', 'BlogController::update/$1');
    $routes->post('blog/status/(:num)', 'BlogController::status/$1');
    $routes->post('blog/delete/(:num)', 'BlogController::delete/$1');

    $routes->post('media/upload', 'MediaController::upload', ['filter' => 'throttle:upload,60,600']);

    $routes->get('categories', 'CategoryController::index');
    $routes->post('categories/save', 'CategoryController::save');
    $routes->post('categories/save/(:num)', 'CategoryController::save/$1');
    $routes->post('categories/delete/(:num)', 'CategoryController::delete/$1');

    $routes->get('tags', 'TagController::index');
    $routes->post('tags/store', 'TagController::store');
    $routes->post('tags/delete/(:num)', 'TagController::delete/$1');

    $routes->get('comments', 'CommentController::index');
    $routes->post('comments/status/(:num)', 'CommentController::status/$1');
    $routes->post('comments/delete/(:num)', 'CommentController::delete/$1');

    $routes->get('enquiries', 'EnquiryController::index');
    $routes->get('enquiries/(:num)', 'EnquiryController::show/$1');
    $routes->post('enquiries/status/(:num)', 'EnquiryController::status/$1');
    $routes->post('enquiries/delete/(:num)', 'EnquiryController::delete/$1');

    $routes->group('users', ['filter' => 'permission:users.edit'], static function ($routes) {
        $routes->get('/', 'UserController::index');
        $routes->get('create', 'UserController::create');
        $routes->post('store', 'UserController::store');
        $routes->get('edit/(:num)', 'UserController::edit/$1');
        $routes->post('update/(:num)', 'UserController::update/$1');
        $routes->post('ban/(:num)', 'UserController::ban/$1');
        $routes->post('delete/(:num)', 'UserController::delete/$1');
    });
});

$routes->get('blog', 'Blog::index');
$routes->get('blog/view/(:segment)', 'Blog::view/$1');
$routes->get('blog/category/(:segment)', 'Blog::category/$1');
$routes->get('blog/tag/(:segment)', 'Blog::tag/$1');
$routes->get('blog/feed.xml', 'Blog::feed');
$routes->post('blog/comment/(:num)', 'Blog::comment/$1', ['filter' => 'throttle:comment,8,600']);
