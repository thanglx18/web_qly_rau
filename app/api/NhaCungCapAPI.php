<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/NhaCungCap.php';

$database = new Database();
$db = $database->getConnection();
$ncc = new NhaCungCap($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $stmt = $ncc->read();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "data" => $data]);
        break;

    case 'POST':
        // Đọc dữ liệu từ body (JSON)
        $json_data = json_decode(file_get_contents("php://input"), true);
        
        // Gộp dữ liệu từ cả POST và JSON để đảm bảo không bị thiếu
        $post_data = $_POST;
        $input = is_array($json_data) ? array_merge($post_data, $json_data) : $post_data;

        // Khởi tạo mảng data
        $data = [
            'id' => $input['id'] ?? null,
            'ten_nha_cc' => $input['ten_nha_cc'] ?? '',
            'so_dien_thoai' => $input['so_dien_thoai'] ?? '',
            'dia_chi' => $input['dia_chi'] ?? ''
        ];
        
        // 1. Kiểm tra trống tên
        if(empty(trim($data['ten_nha_cc']))) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Vui lòng nhập tên nhà cung cấp"]);
            exit;
        }

        // 2. Kiểm tra định dạng SĐT
        if (!preg_match('/^[0-9]{10,11}$/', $data['so_dien_thoai'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Số điện thoại không hợp lệ"]);
            exit;
        }

        // 3. Thực hiện lưu
        if ($ncc->save($data)) {
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Thêm nhà cung cấp thành công!"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Lỗi lưu dữ liệu"]);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if($id && $ncc->delete($id)) {
            echo json_encode(["status" => "success", "message" => "Xóa nhà cung cấp thành công!"]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Lỗi hoặc thiếu ID"]);
        }
        break;
}