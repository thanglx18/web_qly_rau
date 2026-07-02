<?php
// Dashboard & Home
$router->get('', 'AuthController@login');
$router->get('home', 'DashboardController@index');
$router->get('dashboard', 'DashboardController@index');

// Hàng hóa
$router->get('product', 'ProductController@view');
$router->get('category', 'CategoryController@view');
$router->get('supplier', 'NhaCungCapController@view');

// Bán hàng & Khách hàng
$router->get('pos', 'PosController@view');
$router->get('order', 'OrderController@view');
$router->get('customer', 'CustomerController@view');

// Marketing & Hệ thống
$router->get('promotion', 'PromotionController@view');
$router->get('user', 'UserController@view');

// Xác thực
$router->get('login', 'AuthController@login');
$router->post('auth/login', 'AuthController@process');
$router->get('logout', 'AuthController@logout');