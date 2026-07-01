<?php
// 1. Cấu hình Header (Giữ nguyên)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Tắt hiển thị lỗi HTML để tránh làm hỏng JSON trả về
ini_set('display_errors', 0);
error_reporting(0);

// 2. Nạp kết nối và Model
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/SanPham.php';

$database = new Database();
$db = $database->getConnection();
$sanPham = new SanPham($db);

$method = $_SERVER['REQUEST_METHOD'];
// Lấy ID từ URL (dùng cho GET chi tiết, PUT hoặc DELETE)
$id_from_url = isset($_GET['id']) ? intval($_GET['id']) : null;

switch($method) {
    case 'GET':
        $stmt = $sanPham->read();
        $num = $stmt->rowCount();
        if($num > 0) {
            $ds_san_pham = array("data" => array());
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // QUAN TRỌNG: Phải dùng $row['id'] thay vì $id (biến $id từ URL thường là null khi gọi GET danh sách)
                $item = array(
                    "id"         => $row['id'],
                    "ten"        => $row['ten_san_pham'],
                    "gia"        => number_format($row['gia_ban'], 0, ',', '.') . " VNĐ",
                    "kho"        => $row['so_luong_kho'],
                    "don_vi"     => $row['don_vi_tinh'],
                    "trang_thai" => ($row['so_luong_kho'] > $row['nguong_canh_bao']) ? "Còn hàng" : "Sắp hết"
                );
                array_push($ds_san_pham["data"], $item);
            }
            echo json_encode($ds_san_pham);
        } else {
            echo json_encode(array("data" => [], "message" => "Trống."));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->ten)) {
            try {
                // Thêm sản phẩm với trang_thai mặc định là 1 (Hiển thị)
                $sql = "INSERT INTO san_pham (ten_san_pham, gia_ban, so_luong_kho, don_vi_tinh, nguong_canh_bao, trang_thai) 
                        VALUES (:ten, :gia, :kho, :don_vi, 5, 1)";
                $stmt = $db->prepare($sql);
                
                $result = $stmt->execute([
                    ':ten'    => $data->ten, 
                    ':gia'    => $data->gia,
                    ':kho'    => $data->kho, 
                    ':don_vi' => $data->don_vi
                ]);

                if($result) {
                    echo json_encode(["message" => "Thêm sản phẩm mới thành công!"]);
                } else {
                    echo json_encode(["message" => "Lỗi: Không thể lưu vào database"]);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["message" => "Lỗi hệ thống: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(["message" => "Vui lòng nhập đầy đủ tên sản phẩm"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if($id_from_url && !empty($data->ten)) {
            $sql = "UPDATE san_pham SET ten_san_pham=:ten, gia_ban=:gia, so_luong_kho=:kho, don_vi_tinh=:don_vi WHERE id=:id";
            $stmt = $db->prepare($sql);
            if($stmt->execute([
                ':ten' => $data->ten, ':gia' => $data->gia,
                ':kho' => $data->kho, ':don_vi' => $data->don_vi, ':id' => $id_from_url
            ])) {
                echo json_encode(["message" => "Cập nhật thành công!"]);
            }
        }
        break;

    case 'DELETE':
        if($id_from_url) {
            try {
                // Thay vì DELETE, ta dùng UPDATE để ẩn sản phẩm
                $sql = "UPDATE san_pham SET trang_thai = 0 WHERE id = :id"; 
                $stmt = $db->prepare($sql);
                
                // Trong file SanPhamController.php
if($stmt->execute([':id' => $id_from_url])) {
    // Sửa chữ trong ngoặc kép này thành bất cứ gì bạn muốn hiện lên màn hình
                echo json_encode(["message" => "Xóa sản phẩm thành công!"]); 

                } else {
                    echo json_encode(["message" => "Lỗi: Không thể cập nhật trạng thái"]);
                }
            } catch (Exception $e) {
                echo json_encode(["message" => "Lỗi hệ thống: " . $e->getMessage()]);
            }
        }
        exit;
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Phương thức không hỗ trợ"]);
        break;
}