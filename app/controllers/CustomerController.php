<?php

class CustomerController {
    public function view() {
        // Sử dụng đường dẫn tuyệt đối chuẩn theo project
        $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'customer' . DIRECTORY_SEPARATOR . 'index.php';
        
        if (file_exists($path)) {
            require_once $path;
        } else {
            die("Lỗi: Không tìm thấy file view tại $path");
        }
    }
}
