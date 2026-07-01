<?php

$router->get('api/product', 'ProductController@index');
$router->get('api/product/detail', 'ProductController@detailApi');
$router->get('api/product/top', 'ProductController@top');

// POST API
$router->post('api/product', 'ProductController@save');

// DELETE API
$router->delete('api/product', 'ProductController@destroy');