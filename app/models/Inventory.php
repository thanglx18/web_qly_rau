<?php

class Inventory {
    private $conn;
    private $table_name = "lich_su_kho";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy lịch sử biến động kho
    public function read() {
        $query = "SELECT l.*, s.ten_san_pham, s.don_vi_tinh 
                FROM " . $this->table_name . " l
                JOIN san_pham s ON l.id_san_pham = s.id
                ORDER BY l.ngay_tao DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Thêm bản ghi biến động kho và cập nhật số lượng tồn kho
    public function addMovement($id_san_pham, $loai, $so_luong, $ly_do, $id_don_hang = null) {
        $this->conn->beginTransaction();

        try {
            // 1. Kiểm tra nếu là xuất từ đơn hàng, xem đã xuất chưa để tránh trừ trùng
            if ($id_don_hang) {
                $checkQuery = "SELECT id FROM lich_su_kho WHERE id_don_hang = :id_don_hang AND id_san_pham = :id_san_pham";
                $checkStmt = $this->conn->prepare($checkQuery);
                $checkStmt->execute([':id_don_hang' => $id_don_hang, ':id_san_pham' => $id_san_pham]);
                if ($checkStmt->rowCount() > 0) {
                    $this->conn->rollBack();
                    return true; // Đã trừ rồi, không báo lỗi
                }
            }

            // 2. Kiểm tra tồn hiện tại nếu là 'xuat'
            if ($loai == 'xuat') {
                $query = "SELECT so_luong_kho FROM san_pham WHERE id = :id FOR UPDATE";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([':id' => $id_san_pham]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row['so_luong_kho'] < $so_luong) {
                    throw new Exception("Số lượng xuất (" . $so_luong . ") vượt quá tồn kho hiện tại!");
                }
            }

            // 3. Thêm vào lịch sử
            $query = "INSERT INTO " . $this->table_name . " 
                    (id_san_pham, id_don_hang, loai_bien_dong, so_luong, ly_do) 
                    VALUES (:id_san_pham, :id_don_hang, :loai, :so_luong, :ly_do)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':id_san_pham' => $id_san_pham,
                ':id_don_hang' => $id_don_hang,
                ':loai' => $loai,
                ':so_luong' => $so_luong,
                ':ly_do' => $ly_do
            ]);

            // 4. Cập nhật bảng san_pham
            $operator = ($loai == 'nhap') ? '+' : '-';
            $query = "UPDATE san_pham SET so_luong_kho = so_luong_kho " . $operator . " :so_luong WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':so_luong' => $so_luong,
                ':id' => $id_san_pham
            ]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return $e->getMessage();
        }
    }

    // Lấy các sản phẩm sắp hết hàng
    public function getLowStockItems() {
        $query = "SELECT id, ten_san_pham, so_luong_kho, nguong_canh_bao, don_vi_tinh 
                FROM san_pham 
                WHERE so_luong_kho <= nguong_canh_bao 
                ORDER BY so_luong_kho ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
