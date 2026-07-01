<?php

class Order {
    private $conn;
    private $table_name = "don_hang";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách đơn hàng kèm tên khách hàng (Join với bảng khach_hang)
    public function read() {
        $query = "SELECT d.*, k.ho_ten as ten_khach_hang 
                FROM " . $this->table_name . " d
                LEFT JOIN khach_hang k ON d.id_khach_hang = k.id
                ORDER BY d.ngay_tao DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET trang_thai = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }

    // Tạo đơn hàng mới hoàn chỉnh (Transaction)
    public function createOrder($data, $items) {
        try {
            // 1. Kiểm tra tồn kho trước khi thực hiện Transaction
            foreach ($items as $item) {
                $querySub = "SELECT so_luong_kho, ten_san_pham FROM san_pham WHERE id = :id";
                $stmtSub = $this->conn->prepare($querySub);
                $stmtSub->execute([':id' => $item['id']]);
                $product = $stmtSub->fetch(PDO::FETCH_ASSOC);

                if (!$product || $product['so_luong_kho'] < $item['quantity']) {
                    $pName = $product ? $product['ten_san_pham'] : "Sản phẩm #" . $item['id'];
                    $stockAvail = $product ? $product['so_luong_kho'] : 0;
                    throw new Exception("Sản phẩm '{$pName}' không đủ tồn kho (Còn: {$stockAvail}).");
                }
            }

            $this->conn->beginTransaction();

            // 2. Chèn vào bảng don_hang (Khớp với Schema thực tế)
            $query = "INSERT INTO " . $this->table_name . " 
                    (id_khach_hang, id_nguoi_dung, tong_tien_hang, so_tien_giam, tong_thanh_toan, phuong_thuc_tt, trang_thai) 
                    VALUES (:id_khach_hang, :id_nguoi_dung, :tong_tien_hang, :so_tien_giam, :tong_thanh_toan, :phuong_thuc, 'xong')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':id_khach_hang' => $data['id_khach_hang'] ?? null,
                ':id_nguoi_dung' => $data['id_nguoi_dung'] ?? null,
                ':tong_tien_hang' => $data['tong_tien_hang'],
                ':so_tien_giam' => $data['so_tien_giam'] ?? 0,
                ':tong_thanh_toan' => $data['tong_thanh_toan'],
                ':phuong_thuc' => $data['phuong_thuc'] // 'tien_mat' hoặc 'chuyen_khoan'
            ]);

            $order_id = $this->conn->lastInsertId();

            // 3. Chèn vào chi_tiet_don_hang và trừ kho
            foreach ($items as $item) {
                // Lưu chi tiết
                $queryDetail = "INSERT INTO chi_tiet_don_hang 
                              (id_don_hang, id_san_pham, so_luong, gia_luc_ban) 
                              VALUES (:order_id, :product_id, :qty, :price)";
                $stmtDetail = $this->conn->prepare($queryDetail);
                $stmtDetail->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $item['id'],
                    ':qty' => $item['quantity'],
                    ':price' => $item['price']
                ]);

                // Trừ kho
                $queryStock = "UPDATE san_pham SET so_luong_kho = so_luong_kho - :qty WHERE id = :product_id";
                $stmtStock = $this->conn->prepare($queryStock);
                $stmtStock->execute([
                    ':qty' => $item['quantity'],
                    ':product_id' => $item['id']
                ]);
            }
            
            // 3. Cập nhật điểm tích lũy cho khách hàng (nếu có khách hàng)
            if (!empty($data['id_khach_hang'])) {
                // Thêm điểm từ đơn hàng hiện tại (sau khi đã trừ điểm đổi)
                $earnedPoints = (int) floor($data['tong_thanh_toan'] / 5000);
                
                // Trừ điểm khách đã dùng để đổi giảm giá
                $usedPoints = (int) ($data['points_to_use'] ?? 0);
                
                $finalPointChange = $earnedPoints - $usedPoints;
                
                if ($finalPointChange != 0) {
                    $queryPoints = "UPDATE khach_hang SET diem_tich_luy = IFNULL(diem_tich_luy, 0) + :change WHERE id = :customer_id";
                    $stmtPoints = $this->conn->prepare($queryPoints);
                    $stmtPoints->execute([
                        ':change' => $finalPointChange,
                        ':customer_id' => $data['id_khach_hang']
                    ]);
                }
            }

            $this->conn->commit();
            return $order_id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    // Lấy thông tin chi tiết các sản phẩm trong đơn hàng
    public function getDetails($order_id) {
        $query = "SELECT c.*, s.ten_san_pham 
                FROM chi_tiet_don_hang c
                JOIN san_pham s ON c.id_san_pham = s.id
                WHERE c.id_don_hang = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':order_id' => $order_id]);
        return $stmt;
    }

    // Lấy thông tin chung của một đơn hàng theo ID
    public function getById($id) {
        $query = "SELECT d.*, k.ho_ten as ten_khach_hang, k.so_dien_thoai, k.dia_chi 
                FROM " . $this->table_name . " d
                LEFT JOIN khach_hang k ON d.id_khach_hang = k.id
                WHERE d.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
