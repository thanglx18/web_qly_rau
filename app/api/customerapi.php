<?php
// 1. Cấu hình Header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

ini_set('display_errors', 0);
error_reporting(0);

// 2. Nạp kết nối và Model
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Customer.php';

$database = new Database();
$db = $database->getConnection();
$customer = new Customer($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $stmt = $customer->read();
        $num = $stmt->rowCount();
        
        if($num > 0) {
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status" => "success", "data" => $data]);
        } else {
            echo json_encode(["status" => "success", "data" => [], "message" => "Không có khách hàng nào."]);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents("php://input"));
        
        $data = new stdClass();
        $data->id = $_POST['id'] ?? ($input->id ?? null);
        $data->ho_ten = $_POST['ho_ten'] ?? ($input->ho_ten ?? '');
        $data->so_dien_thoai = $_POST['so_dien_thoai'] ?? ($input->so_dien_thoai ?? '');
        $data->email = $_POST['email'] ?? ($input->email ?? '');
        $data->dia_chi = $_POST['dia_chi'] ?? ($input->dia_chi ?? '');
        $data->diem_tich_luy = $_POST['diem_tich_luy'] ?? ($input->diem_tich_luy ?? 0);

        // 1. Kiểm tra trống họ tên
        if(empty(trim($data->ho_ten))) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Vui lòng nhập họ tên khách hàng"]);
            exit;
        }

        // 3. Kiểm tra định dạng SĐT (Chỉ chấp nhận 10-11 chữ số)
        if (!preg_match('/^[0-9]{10,11}$/', $data->so_dien_thoai)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Số điện thoại không hợp lệ"]);
            exit;
        }

        // 4. Kiểm tra trùng số điện thoại trong DB
        if ($customer->checkPhoneExists($data->so_dien_thoai, $data->id)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Số điện thoại này đã tồn tại trong hệ thống. Vui lòng nhập số khác!"]);
            exit;
        }

        // 5. Thực hiện thêm/cập nhật
        if (empty($data->id)) {
            $result = $customer->create($data);
            $msg = "Thêm khách hàng mới thành công!";
        } else {
            $result = $customer->update($data);
            $msg = "Cập nhật khách hàng thành công!";
        }

        if($result) {
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => $msg]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Lỗi xử lý database"]);
        }
        break;
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if($id) {
            if($customer->delete($id)) {
                echo json_encode(["status" => "success", "message" => "Xóa khách hàng thành công!"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Lỗi khi xóa"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Thiếu ID"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Phương thức không hỗ trợ"]);
        break;
}