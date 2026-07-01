<?php
// public/index.php
session_start();

// 1. Nạp file core
require_once __DIR__ . "/../app/core/Router.php";

// 2. Khởi tạo đối tượng Router
$router = new Router();

// 3. Nạp các tệp định nghĩa route
// Lưu ý: Biến $router ở đây sẽ được sử dụng bên trong các file require này
require_once __DIR__ . "/../routes/web.php";
require_once __DIR__ . "/../routes/api.php";

// 4. Thực thi điều hướng
$router->run();