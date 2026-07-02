<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/SanPham.php';
require_once __DIR__ . '/../models/DanhMuc.php';
require_once __DIR__ . '/../models/NhaCungCap.php';

class ProductController {
    private $db;
    private $sanPham;
    private $danhMuc;
    private $nhaCungCap;

    public function __construct() {
        // Khởi tạo kết nối thông qua lớp Database dùng chung
        $database = new Database();
        $this->db = $database->getConnection();
        $this->sanPham = new SanPham($this->db);
        $this->danhMuc = new DanhMuc($this->db);
        $this->nhaCungCap = new NhaCungCap($this->db);
    }

    // Hiển thị giao diện quản lý sản phẩm
    public function view() {
        // 1. Lấy danh sách sản phẩm từ Model
        $stmt = $this->sanPham->read();
        $ds_sanpham = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Lấy dữ liệu bổ trợ (Danh mục & Nhà cung cấp) từ Model chuẩn
        $danh_mucs = $this->danhMuc->read()->fetchAll(PDO::FETCH_ASSOC);
        $nha_ccs = $this->nhaCungCap->read()->fetchAll(PDO::FETCH_ASSOC);

        // 3. Nạp View
        require_once __DIR__ . '/../../view/sanpham/index.php';
    }

    // Xử lý lưu dữ liệu qua AJAX (REST API endpoint)
    public function save() {
        header("Content-Type: application/json");
        
        $id = $_POST['id'] ?? '';
        $payload = [
            'ten'    => $_POST['ten_san_pham'] ?? '',
            'gia'    => $_POST['gia_ban'] ?? 0,
            'kho'    => $_POST['so_luong_kho'] ?? 0,
            'don_vi' => $_POST['don_vi_tinh'] ?? '',
            'id_danh_muc' => $_POST['id_danh_muc'] ?? null,
            'id_nha_cc'   => $_POST['id_nha_cc'] ?? null,
            'anh'    => '' // Mặc định trống
        ];

        // Xử lý upload ảnh nếu có
        if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
            $target_dir = __DIR__ . "/../../public/img/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $ext = pathinfo($_FILES["hinh_anh"]["name"], PATHINFO_EXTENSION);
            $file_name = time() . "_" . uniqid() . "." . $ext;
            
            if (move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_dir . $file_name)) {
                $payload['anh'] = $file_name;
            }
        }

        try {
            if (empty($id)) {
                // Gọi hàm create từ Model SanPham
                $result = $this->sanPham->create($payload);
            } else {
                // Gọi hàm update từ Model SanPham
                $result = $this->sanPham->update($id, $payload);
            }

            echo json_encode($result ? ['status' => 'success', 'message' => 'Thành công'] : ['status' => 'error', 'message' => 'Không thể lưu dữ liệu']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    // API: Trả về danh sách sản phẩm dạng JSON
    public function index() {
        header("Content-Type: application/json");
        try {
            $stmt = $this->sanPham->readWithJoin();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    // API: Xóa sản phẩm
    public function destroy() {
        header("Content-Type: application/json");
        $id = $_GET['id'] ?? '';
        
        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'Thiếu ID sản phẩm']);
            exit;
        }

        try {
            $result = $this->sanPham->delete($id);
            echo json_encode($result ? ['status' => 'success', 'message' => 'Xóa thành công'] : ['status' => 'error', 'message' => 'Không thể xóa']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}