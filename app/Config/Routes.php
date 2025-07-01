<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false); // CRITICAL: Ensure this is FALSE for security and explicit routing


service('auth')->routes($routes);


$routes->get('/', 'Home::index', ['as' => 'homepage']); // Home page
$routes->get('about', 'Home::about', ['as' => 'about_us']); // About Us page// Contact Us page


$routes->get('products', 'ProductController::index', ['as' => 'products_list']); // View all products
$routes->get('products/(:num)', 'ProductController::show/$1', ['as' => 'product_detail']); // View single Product by ID


$routes->get('Category', 'CategoryController::index', ['as' => 'categories_list_public']); // View all categories
$routes->get('categories/(:num)', 'ProductController::index/$1', ['as' => 'products_by_category_id']); // ProductController::index method handles filtering


$routes->get('Cart', 'CartController::index', ['as' => 'cart_view']); // View the shopping Cart
$routes->post('Cart/add', 'CartController::add', ['as' => 'cart_add']); // Add item to Cart
$routes->post('Cart/update', 'CartController::update', ['as' => 'cart_update']); // Update item quantity in Cart
$routes->post('Cart/remove', 'CartController::remove', ['as' => 'cart_remove']); // Remove item from Cart



$routes->get('Checkout', 'CheckoutController::index', ['as' => 'checkout_view']); // CheckoutController process start
$routes->post('Checkout/process', 'CheckoutController::process', ['as' => 'checkout_process']); // Process Checkout


$routes->group('account', ['filter' => 'session'], function ($routes) {
    $routes->get('/', 'UserAccount::index', ['as' => 'user_dashboard']); // User dashboard
    $routes->get('profile', 'UserAccount::profile', ['as' => 'user_profile']); // User profile details
    $routes->get('orders', 'UserAccount::orders', ['as' => 'user_orders']); // User's order history
    $routes->get('orders/(:num)', 'UserAccount::orderDetail/$1', ['as' => 'user_order_detail']); // Specific order detail
    $routes->get('settings', 'UserAccount::settings', ['as' => 'user_settings']); // Account settings
});


$routes->group('Admin', ['filter' => 'group:Admin'], function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index', ['as' => 'admin_dashboard']); // Admin Dashboard

    // CRUD routes for Products. 'controller' option is important for namespaced controllers.
    $routes->resource('products', ['controller' => 'Admin\Products']);

    // CRUD routes for Categories
    $routes->resource('categories', ['controller' => 'Admin\Categories']); // This generates Admin/categories, Admin/categories/new, etc.

    // CRUD routes for Orders
    $routes->resource('orders', ['controller' => 'Admin\Orders']);

    // CRUD routes for Users (if you want Admin to manage users)
    $routes->resource('users', ['controller' => 'Admin\Users']);

    // Example for sales reports
    $routes->get('reports/sales', 'Admin\Reports::sales', ['as' => 'admin_sales_report']);
    // Example for site settings
    $routes->get('settings', 'Admin\Settings::index', ['as' => 'admin_settings']);
});