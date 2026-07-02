<?php

class DashboardController {
    public function index() {
        // Chỉ admin mới được xem Dashboard thống kê
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] !== 'admin') {
            header('Location: /hi/public/index.php?url=pos');
            exit;
        }

        require_once __DIR__ . "/../../view/dashboard/index.php";
    }
}