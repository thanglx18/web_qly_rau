<?php
// 1. Cấu hình Header chuẩn REST API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Tắt hiển thị lỗi HTML để tránh làm hỏng JSON trả về
ini_set('display_errors', 0);
error_reporting(0);

// 2. Nạp kết nối và Model (Điều chỉnh đường dẫn theo cấu trúc của bạn)
require_once __DIR__ . '/../core/Database.php';
// Giả sử bạn đã có Model Category.php, nếu chưa hãy tạo để quản lý SQL
// require_once __DIR__ . '/../models/Category.php'; 

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$id_from_url = isset($_GET['id']) ? intval($_GET['id']) : null;

switch($method) {
    case 'GET':
        // Logic lấy danh sách danh mục
        $query = "SELECT * FROM danh_muc ORDER BY id DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $num = $stmt->rowCount();
        if($num > 0) {
            $ds_category = array("data" => array());
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $item = array(
                    "id" => $id,
                    "ten" => $ten_danh_muc,
                    "mo_ta" => $mo_ta
                );
                array_push($ds_category["data"], $item);
            }
            echo json_encode($ds_category);
        } else {
            echo json_encode(array("message" => "Không tìm thấy danh mục nào."));
        }
        break;

    case 'POST':
        // Logic Thêm mới hoặc Cập nhật (nếu có ID)
        $ten = $_POST['ten_danh_muc'] ?? '';
        $mo_ta = $_POST['mo_ta'] ?? '';
        $id = $_POST['id'] ?? '';

        if (empty($id)) {
            $query = "INSERT INTO danh_muc (ten_danh_muc, mo_ta) VALUES (?, ?)";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([$ten, $mo_ta]);
        } else {
            $query = "UPDATE danh_muc SET ten_danh_muc = ?, mo_ta = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([$ten, $mo_ta, $id]);
        }

        echo json_encode($result ? ["status" => "success"] : ["status" => "error"]);
        break;

    case 'DELETE':
        // Logic xóa danh mục
        if ($id_from_url) {
            $query = "DELETE FROM danh_muc WHERE id = ?";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([$id_from_url]);
            echo json_encode($result ? ["status" => "success"] : ["status" => "error"]);
        }
        break;
}