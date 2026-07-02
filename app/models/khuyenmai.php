<?php

class KhuyenMai {
    private $conn;
    private $table_name = "khuyen_mai";

    public $id;
    public $danh_sach_san_pham;
    public $ma_code;
    public $phan_tram_giam;
    public $ngay_bat_dau;
    public $ngay_ket_thuc;
    public $trang_thai;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả khuyến mãi
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE trang_thai = 1 ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Thêm mới
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                (danh_sach_san_pham, ma_code, phan_tram_giam, ngay_bat_dau, ngay_ket_thuc, trang_thai) 
                VALUES (:danh_sach_san_pham, :ma_code, :phan_tram_giam, :ngay_bat_dau, :ngay_ket_thuc, 1)";
        
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':danh_sach_san_pham' => $data->danh_sach_san_pham,
            ':ma_code' => $data->ma_code,
            ':phan_tram_giam' => $data->phan_tram_giam,
            ':ngay_bat_dau' => $data->ngay_bat_dau,
            ':ngay_ket_thuc' => $data->ngay_ket_thuc
        ]);
    }

    // Cập nhật
    public function update($data) {
        $query = "UPDATE " . $this->table_name . " 
                SET danh_sach_san_pham = :danh_sach_san_pham,
                    ma_code = :ma_code, 
                    phan_tram_giam = :phan_tram_giam, 
                    ngay_bat_dau = :ngay_bat_dau, 
                    ngay_ket_thuc = :ngay_ket_thuc 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':danh_sach_san_pham' => $data->danh_sach_san_pham,
            ':ma_code' => $data->ma_code,
            ':phan_tram_giam' => $data->phan_tram_giam,
            ':ngay_bat_dau' => $data->ngay_bat_dau,
            ':ngay_ket_thuc' => $data->ngay_ket_thuc,
            ':id' => $data->id
        ]);
    }

    // Xóa mềm (Soft Delete)
    public function delete($id) {
        $query = "UPDATE " . $this->table_name . " SET trang_thai = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}
