<?php
class SanPham {
    private $conn;
    private $table = "san_pham";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $sql = "SELECT * FROM $this->table WHERE trang_thai = 1 ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public function readWithJoin() {
        $sql = "SELECT sp.*, dm.ten_danh_muc, ncc.ten_nha_cc 
                FROM $this->table sp 
                LEFT JOIN danh_muc dm ON sp.id_danh_muc = dm.id 
                LEFT JOIN nha_cung_cap ncc ON sp.id_nha_cc = ncc.id 
                WHERE sp.trang_thai = 1 
                ORDER BY sp.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public function create($data) {
        $sql = "INSERT INTO $this->table (ten_san_pham, gia_ban, so_luong_kho, don_vi_tinh, anh_san_pham, id_danh_muc, id_nha_cc, trang_thai) 
                VALUES (:ten, :gia, :kho, :don_vi, :anh, :id_danh_muc, :id_nha_cc, 1)";
        return $this->conn->prepare($sql)->execute($data);
    }

    public function update($id, $data) {
        $sql = "UPDATE $this->table SET ten_san_pham=:ten, gia_ban=:gia, so_luong_kho=:kho, don_vi_tinh=:don_vi, id_danh_muc=:id_danh_muc, id_nha_cc=:id_nha_cc";
        if ($data['anh'] != "") {
            $sql .= ", anh_san_pham=:anh";
        } else {
            unset($data['anh']); // Không cập nhật ảnh nếu trống
        }
        $sql .= " WHERE id=:id";
        $data['id'] = $id;
        return $this->conn->prepare($sql)->execute($data);
    }

    public function delete($id) {
        $sql = "UPDATE $this->table SET trang_thai = 0 WHERE id = :id";
        return $this->conn->prepare($sql)->execute(['id' => $id]);
    }

    public function getLowStock() {
        $sql = "SELECT * FROM $this->table WHERE so_luong_kho <= 10 AND trang_thai = 1"; // Mặc định cảnh báo khi tồn <= 10
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }
}