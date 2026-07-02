<?php
session_start();
// 1. Cấu hình Header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

ini_set('display_errors', 0);
error_reporting(0);

// 2. Nạp kết nối và Model
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Order.php';

$database = new Database();
$db = $database->getConnection();
$order = new Order($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Nếu có id_details thì lấy chi tiết đơn hàng
        if (isset($_GET['id_details'])) {
            $id = intval($_GET['id_details']);
            $orderInfo = $order->getById($id);
            if ($orderInfo) {
                $stmt = $order->getDetails($id);
                $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([
                    "status" => "success", 
                    "order" => $orderInfo,
                    "details" => $details
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "Không tìm thấy đơn hàng."]);
            }
            break;
        }

        // Mặc định lấy danh sách đơn hàng
        $stmt = $order->read();
        $num = $stmt->rowCount();
        
        if($num > 0) {
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status" => "success", "data" => $data]);
        } else {
            echo json_encode(["status" => "success", "data" => [], "message" => "Không có đơn hàng nào."]);
        }
        break;

    case 'POST':
        // Lấy dữ liệu (Thử JSON trước, nếu không có thì lấy $_POST)
        $input = json_decode(file_get_contents("php://input"), true);
        $data = $input ?? $_POST;
        
        // Trường hợp 1: Tạo đơn hàng mới từ POS
        if (isset($data['cart'])) {
            // Ánh xạ phuong_thuc_tt để khớp Enum trong Database (tien_mat, chuyen_khoan)
            $pm = 'tien_mat';
            if (isset($data['payment_method']) && strpos(mb_strtolower($data['payment_method'], 'UTF-8'), 'chuyển') !== false) {
                $pm = 'chuyen_khoan';
            }

            $orderData = [
                'id_khach_hang' => $data['customer_id'] ?? null,
                'id_nguoi_dung' => $_SESSION['user']['id'] ?? null,
                'tong_tien_hang' => $data['subtotal'] ?? $data['total'],
                'so_tien_giam' => $data['discount'] ?? 0,
                'tong_thanh_toan' => $data['total'],
                'phuong_thuc' => $pm,
                'points_to_use' => $data['points_to_use'] ?? 0
            ];
            
            $new_id = $order->createOrder($orderData, $data['cart']);
            
            if ($new_id) {
                echo json_encode(["status" => "success", "message" => "Thanh toán thành công!", "order_id" => $new_id]);
            } else {
                echo json_encode(["status" => "error", "message" => "Lỗi khi lưu đơn hàng vào Database"]);
            }
            break;
        }

        // Trường hợp 2: Cập nhật trạng thái đơn hàng (Dùng cho Admin)
        $id = $_POST['id'] ?? null;
        $status = $_POST['trang_thai'] ?? null;

        if($id && $status) {
            // ... (giữ nguyên logic cập nhật trạng thái đã có)
            if ($status == 'xong') {
                require_once __DIR__ . '/../models/Inventory.php';
                $inventory = new Inventory($db);
                $detailsStmt = $order->getDetails($id);
                $items = $detailsStmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($items)) {
                    foreach ($items as $item) {
                        $inventory->addMovement($item['id_san_pham'], 'xuat', $item['so_luong'], "Xuất kho cho đơn hàng #" . $id, $id);
                    }
                }
            }

            if($order->updateStatus($id, $status)) {
                echo json_encode(["status" => "success", "message" => "Cập nhật trạng thái thành công!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Lỗi khi cập nhật database"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Thiếu thông tin ID hoặc trạng thái"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Phương thức không hỗ trợ"]);
        break;
}
