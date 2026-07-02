<?php
// 1. Cấu hình Header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

ini_set('display_errors', 0);
error_reporting(0);

// 2. Nạp kết nối và Model (Đảm bảo require model đúng case)
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/User.php';

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $stmt = $userModel->read();
        $num = $stmt->rowCount();
        if($num > 0) {
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status" => "success", "data" => $data]);
        } else {
            echo json_encode(["status" => "success", "data" => [], "message" => "Không có nhân viên nào."]);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents("php://input"));
        
        $data = new stdClass();
        $data->id = $_POST['id'] ?? ($input->id ?? null);
        $data->ten_dang_nhap = $_POST['ten_dang_nhap'] ?? ($input->ten_dang_nhap ?? '');
        $data->mat_khau = $_POST['mat_khau'] ?? ($input->mat_khau ?? '');
        $data->ho_ten = $_POST['ho_ten'] ?? ($input->ho_ten ?? '');
        $data->vai_tro = $_POST['vai_tro'] ?? ($input->vai_tro ?? 'staff');
        $data->trang_thai = $_POST['trang_thai'] ?? ($input->trang_thai ?? 1);

        if(!empty($data->ten_dang_nhap) && !empty($data->ho_ten)) {
            // Kiểm tra trùng tên đăng nhập
            if ($userModel->checkUsernameExists($data->ten_dang_nhap, $data->id)) {
                echo json_encode(["status" => "error", "message" => "Tên đăng nhập này đã tồn tại!"]);
                exit;
            }

            if (empty($data->id)) {
                if (empty($data->mat_khau)) {
                    echo json_encode(["status" => "error", "message" => "Vui lòng nhập mật khẩu cho tài khoản mới!"]);
                    exit;
                }
                $result = $userModel->create($data);
                $msg = "Thêm nhân viên thành công!";
            } else {
                $result = $userModel->update($data);
                $msg = "Cập nhật nhân viên thành công!";
            }

            if($result) {
                echo json_encode(["status" => "success", "message" => $msg]);
            } else {
                echo json_encode(["status" => "error", "message" => "Lỗi xử lý database"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Vui lòng nhập đủ Tên đăng nhập và Họ tên"]);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if($id) {
            if($userModel->delete($id)) {
                echo json_encode(["status" => "success", "message" => "Xóa nhân viên vĩnh viễn thành công!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Lỗi khi xóa"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Thiếu ID"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Phương thức không hỗ trợ"]);
        break;
}
