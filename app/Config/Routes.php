<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

service('auth')->routes($routes);

$routes->get('/', 'Home::index');
$routes->get('products', 'Product::index');
$routes->get('products/(:num)', 'Product::show/$1'); // View single product
$routes->post('cart/add', 'Cart::add');
$routes->get('cart', 'Cart::index');
// ... more cart, checkout routes

$routes->group('account', ['filter' => 'session'], function ($routes) {
    $routes->get('/', 'UserAccount::index');
    $routes->get('orders', 'UserAccount::orders');
    // ...
});

$routes->group('admin', ['filter' => 'group:admin'], function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->resource('products'); // CRUD for products (Admin\Products controller)
    $routes->resource('categories'); // CRUD for categories (Admin\Categories controller)
    $routes->resource('orders'); // Manage orders (Admin\Orders controller)
    // ... more admin routes
});
