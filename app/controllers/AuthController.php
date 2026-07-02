<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $db;
    private $userModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new User($this->db);
    }

    public function login() {
        // Nếu đã đăng nhập thì về dashboard luôn
        if (isset($_SESSION['user'])) {
            $redirect = $_SESSION['user']['role'] === 'admin' ? 'home' : 'pos';
            header('Location: /hi/public/index.php?url=' . $redirect);
            exit;
        }
        
        $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'auth' . DIRECTORY_SEPARATOR . 'login.php';
        require_once $path;
    }

    public function process() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            echo json_encode(["status" => "error", "message" => "Vui lòng nhập đủ thông tin!"]);
            exit;
        }

        $user = $this->userModel->authenticate($username, $password);
        $remember = isset($_POST['remember']) && $_POST['remember'] == 'on';

        if ($user) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['ten_dang_nhap'],
                'fullname' => $user['ho_ten'],
                'role' => $user['vai_tro']
            ];

            // Xử lý lưu mật khẩu (Cookie 30 ngày)
            if ($remember) {
                setcookie('remember_username', $username, time() + (86400 * 30), "/");
                setcookie('remember_password', $password, time() + (86400 * 30), "/");
                setcookie('remember_check', 'checked', time() + (86400 * 30), "/");
            } else {
                // Xóa cookie nếu không tích chọn
                setcookie('remember_username', '', time() - 3600, "/");
                setcookie('remember_password', '', time() - 3600, "/");
                setcookie('remember_check', '', time() - 3600, "/");
            }

            $redirect = $user['vai_tro'] === 'admin' ? 'home' : 'pos';
            echo json_encode([
                "status" => "success", 
                "message" => "Đăng nhập thành công!",
                "redirect" => $redirect
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Tài khoản hoặc mật khẩu không chính xác!"]);
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /hi/public/index.php?url=login');
        exit;
    }
}
