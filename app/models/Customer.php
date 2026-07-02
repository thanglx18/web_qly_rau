<?php

class Customer {
    private $conn;
    private $table_name = "khach_hang";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách khách hàng
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Kiểm tra số điện thoại tồn tại
    public function checkPhoneExists($phone, $excludeId = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE so_dien_thoai = :phone";
        if ($excludeId) {
            $query .= " AND id != :excludeId";
        }
        $stmt = $this->conn->prepare($query);
        $params = [':phone' => $phone];
        if ($excludeId) {
            $params[':excludeId'] = $excludeId;
        }
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    // Thêm khách hàng mới
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                (ho_ten, so_dien_thoai, email, dia_chi, diem_tich_luy) 
                VALUES (:ho_ten, :so_dien_thoai, :email, :dia_chi, :diem_tich_luy)";
        
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':ho_ten' => $data->ho_ten,
            ':so_dien_thoai' => $data->so_dien_thoai,
            ':email' => $data->email,
            ':dia_chi' => $data->dia_chi,
            ':diem_tich_luy' => $data->diem_tich_luy ?? 0
        ]);
    }

    // Cập nhật thông tin khách hàng
    public function update($data) {
        $query = "UPDATE " . $this->table_name . " 
                SET ho_ten = :ho_ten, 
                    so_dien_thoai = :so_dien_thoai, 
                    email = :email, 
                    dia_chi = :dia_chi, 
                    diem_tich_luy = :diem_tich_luy
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':ho_ten' => $data->ho_ten,
            ':so_dien_thoai' => $data->so_dien_thoai,
            ':email' => $data->email,
            ':dia_chi' => $data->dia_chi,
            ':diem_tich_luy' => $data->diem_tich_luy,
            ':id' => $data->id
        ]);
    }

    // Xóa khách hàng
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}
