<?php
// 1. Cấu hình Header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Tắt hiển thị lỗi HTML để tránh làm hỏng JSON trả về
ini_set('display_errors', 0);
error_reporting(0);

// 2. Nạp kết nối và Model (Sửa đúng tên file khuyenmai.php)
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/khuyenmai.php';

$database = new Database();
$db = $database->getConnection();
$khuyenMai = new KhuyenMai($db);

$method = $_SERVER['REQUEST_METHOD'];
// Lấy ID từ URL (nếu có)
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

switch($method) {
    case 'GET':
        $stmt = $khuyenMai->read();
        $num = $stmt->rowCount();
        
        if($num > 0) {
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status" => "success", "data" => $data]);
        } else {
            echo json_encode(["status" => "success", "data" => [], "message" => "Không có dữ liệu"]);
        }
        break;

    case 'POST':
        // Hỗ trợ cả Form Data và JSON
        $input = json_decode(file_get_contents("php://input"));
        
        $data = new stdClass();
        $data->id = $_POST['id'] ?? ($input->id ?? '');
        $data->danh_sach_san_pham = $_POST['danh_sach_san_pham'] ?? ($input->danh_sach_san_pham ?? null);
        $data->ma_code = $_POST['ma_code'] ?? ($input->ma_code ?? '');
        $data->phan_tram_giam = $_POST['phan_tram_giam'] ?? ($input->phan_tram_giam ?? '');
        $data->ngay_bat_dau = $_POST['ngay_bat_dau'] ?? ($input->ngay_bat_dau ?? '');
        $data->ngay_ket_thuc = $_POST['ngay_ket_thuc'] ?? ($input->ngay_ket_thuc ?? '');

        if(!empty($data->ma_code)) {
            if (empty($data->id)) {
                $result = $khuyenMai->create($data);
                $msg = "Thêm khuyến mãi thành công!";
            } else {
                $result = $khuyenMai->update($data);
                $msg = "Cập nhật khuyến mãi thành công!";
            }

            if($result) {
                echo json_encode(["status" => "success", "message" => $msg]);
            } else {
                echo json_encode(["status" => "error", "message" => "Lỗi xử lý database"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Vui lòng nhập đầy đủ thông tin"]);
        }
        break;

    case 'DELETE':
        if($id) {
            if($khuyenMai->delete($id)) {
                echo json_encode(["status" => "success", "message" => "Xóa thành công!"]);
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
