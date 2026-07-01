<?php

class UserController {
    public function view() {
        // Kiểm tra phân quyền: chỉ admin mới được vào
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /hi/public/index.php?url=home&error=forbidden');
            exit;
        }

        $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . 'index.php';
        
        if (file_exists($path)) {
            require_once $path;
        } else {
            die("Lỗi: Không tìm thấy file view tại $path");
        }
    }
}
