<?php

class Router {
    private $routes = [];

    // Bắt buộc phải có hàm này để web.php không báo lỗi Fatal Error
    public function get($uri, $action) {
        $this->routes['GET'][$uri] = $action;
    }

    public function post($uri, $action) {
        $this->routes['POST'][$uri] = $action;
    }

    public function delete($uri, $action) {
        $this->routes['DELETE'][$uri] = $action;
    }

    public function run() {
        $url = $_GET['url'] ?? '';
        $url = rtrim($url, '/');
        $method = $_SERVER['REQUEST_METHOD'];

        // ƯU TIÊN 1: Kiểm tra các Route được định nghĩa thủ công (web.php, api.php)
        if (isset($this->routes[$method][$url])) {
            $action = $this->routes[$method][$url];
            $this->callAction($action);
            return;
        }

        // ƯU TIÊN 2: Automatic Routing (Dùng cho các URL như supplier, category)
        $parts = explode('/', $url);
        $slug = !empty($parts[0]) ? strtolower($parts[0]) : 'home';
        
        // Bảng ánh xạ để đảm bảo tìm đúng file Controller
        $map = [
            'supplier' => 'NhaCungCapController',
            'category' => 'CategoryController',
            'product'  => 'ProductController',
            'home'     => 'DashboardController',
            'dashboard'=> 'DashboardController'
        ];

        $controllerName = $map[$slug] ?? ucfirst($slug) . 'Controller';
        
        // Nếu slug là login/logout thì dùng AuthController
        if (in_array($slug, ['login', 'logout'])) {
            $controllerName = 'AuthController';
        }

        $methodAction = $parts[1] ?? (in_array($slug, ['home', 'dashboard', 'login', 'logout']) ? 'index' : 'view');

        // Đặc cách cho logout
        if ($slug === 'logout') $methodAction = 'logout';
        if ($slug === 'login' && $method === 'POST') $methodAction = 'process';

        $this->executeController($controllerName, $methodAction);
    }

    private function callAction($action) {
        // Tách chuỗi Controller@method (ví dụ: ProductController@index)
        if (strpos($action, '@') !== false) {
            list($controller, $method) = explode('@', $action);
            $this->executeController($controller, $method);
        }
    }

    private function executeController($controllerName, $methodAction) {
        // Sử dụng đường dẫn tuyệt đối dựa trên cấu trúc thư mục của bạn
        $controllerFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controllerName . '.php';
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            if (class_exists($controllerName)) {
                $obj = new $controllerName();
                if (method_exists($obj, $methodAction)) {
                    $obj->$methodAction();
                } else {
                    die("Lỗi: Không tìm thấy hàm $methodAction");
                }
            }
        } else {
            die("Lỗi: Không tìm thấy file Controller tại $controllerFile");
        }
    }
}