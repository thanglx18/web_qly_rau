<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Bật hiển thị lỗi để debug nếu vẫn lỗi 500
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/SanPham.php';

$database = new Database();
$db = $database->getConnection();
$sanPham = new SanPham($db);

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

switch($method) {
    case 'GET':
        $stmt = $sanPham->read();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Trả về dữ liệu nguyên bản để dễ khớp với Model
        echo json_encode(["data" => $data, "status" => "success"]);
        break;

    case 'POST':
    // Hỗ trợ cả Form Data và JSON raw
    $input = json_decode(file_get_contents("php://input"), true);

    $payload = [
        'ten_san_pham' => $_POST['ten_san_pham'] ?? ($input['ten_san_pham'] ?? ''),
        'gia_ban'      => $_POST['gia_ban'] ?? ($input['gia_ban'] ?? 0),
        'so_luong_kho' => $_POST['so_luong_kho'] ?? ($input['so_luong_kho'] ?? 0),
        'don_vi_tinh'  => $_POST['don_vi_tinh'] ?? ($input['don_vi_tinh'] ?? ''),
        'id_danh_muc'  => $_POST['id_danh_muc'] ?? ($input['id_danh_muc'] ?? null),
        'id_nha_cc'    => $_POST['id_nha_cc'] ?? ($input['id_nha_cc'] ?? null),
        'mo_ta'        => $_POST['mo_ta'] ?? ($input['mo_ta'] ?? ''),
        'trang_thai'   => $_POST['trang_thai'] ?? ($input['trang_thai'] ?? 1),
        'hinh_anh'     => $input['hinh_anh'] ?? ''
    ];

    // Xử lý upload ảnh
    if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
        $target_dir = "../../public/img/";
        // Kiểm tra và tạo thư mục nếu chưa có để tránh lỗi 500
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES["hinh_anh"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . "_" . uniqid() . "." . $ext;
        if(move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_dir . $file_name)) {
            $payload['hinh_anh'] = $file_name;
        }
    }

    // Ánh xạ dữ liệu để khớp hoàn toàn với các token trong SQL của SanPham model
    // (:ten, :gia, :kho, :don_vi, :anh, :id_danh_muc, :id_nha_cc)
    $dataForModel = [
        'ten'         => $payload['ten_san_pham'],
        'gia'         => $payload['gia_ban'],
        'kho'         => $payload['so_luong_kho'],
        'don_vi'      => $payload['don_vi_tinh'],
        'anh'         => $payload['hinh_anh'],
        'id_danh_muc' => $payload['id_danh_muc'],
        'id_nha_cc'   => $payload['id_nha_cc']
    ];

    if ($id) {
        if ($sanPham->update($id, $dataForModel)) {
            echo json_encode(["status" => "success", "message" => "Cập nhật thành công!"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Lỗi cập nhật cơ sở dữ liệu"]);
        }
    } else {
        if ($sanPham->create($dataForModel)) {
            echo json_encode(["status" => "success", "message" => "Thêm mới thành công!"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Lỗi thêm mới cơ sở dữ liệu"]);
        }
    }
    break;

    case 'DELETE':
        if ($id && $sanPham->delete($id)) {
            echo json_encode(["status" => "success", "message" => "Đã xóa sản phẩm!"]);
        }
        break;
}