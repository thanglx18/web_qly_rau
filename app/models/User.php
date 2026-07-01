<?php

class User {
    private $conn;
    private $table_name = "nguoi_dung";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Xác thực đăng nhập
    public function authenticate($username, $password) {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE ten_dang_nhap = :username 
                AND mat_khau = :password 
                AND trang_thai = 1"; // Chỉ cho phép tài khoản đang hoạt động
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':username' => $username,
            ':password' => $password
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách nhân viên
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Kiểm tra tên đăng nhập tồn tại
    public function checkUsernameExists($username, $excludeId = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE ten_dang_nhap = :username";
        if ($excludeId) {
            $query .= " AND id != :excludeId";
        }
        $stmt = $this->conn->prepare($query);
        $params = [':username' => $username];
        if ($excludeId) {
            $params[':excludeId'] = $excludeId;
        }
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    // Thêm nhân viên mới
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                (ten_dang_nhap, mat_khau, ho_ten, vai_tro, trang_thai) 
                VALUES (:ten_dang_nhap, :mat_khau, :ho_ten, :vai_tro, :trang_thai)";
        
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':ten_dang_nhap' => $data->ten_dang_nhap,
            ':mat_khau' => $data->mat_khau, // Lưu trực tiếp theo y/c user (hoặc hash nếu cần)
            ':ho_ten' => $data->ho_ten,
            ':vai_tro' => $data->vai_tro,
            ':trang_thai' => $data->trang_thai ?? 1
        ]);
    }

    // Cập nhật thông tin nhân viên
    public function update($data) {
        $query = "UPDATE " . $this->table_name . " 
                SET ten_dang_nhap = :ten_dang_nhap, 
                    ho_ten = :ho_ten, 
                    vai_tro = :vai_tro, 
                    trang_thai = :trang_thai";
        
        // Chỉ cập nhật mật khẩu nếu được cung cấp
        if (!empty($data->mat_khau)) {
            $query .= ", mat_khau = :mat_khau";
        }

        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $params = [
            ':ten_dang_nhap' => $data->ten_dang_nhap,
            ':ho_ten' => $data->ho_ten,
            ':vai_tro' => $data->vai_tro,
            ':trang_thai' => $data->trang_thai,
            ':id' => $data->id
        ];

        if (!empty($data->mat_khau)) {
            $params[':mat_khau'] = $data->mat_khau;
        }

        return $stmt->execute($params);
    }

    // Xóa vĩnh viễn (Permanent Delete) theo yêu cầu của user
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}
