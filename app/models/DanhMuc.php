<?php
class DanhMuc {
    private $conn;
    private $table = "danh_muc";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy toàn bộ danh sách danh mục
    public function read() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy chi tiết một danh mục
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt;
    }

    // Thêm hoặc cập nhật danh mục
    public function save($data) {
        if (empty($data['id'])) {
            // Thêm mới
            $query = "INSERT INTO " . $this->table . " (ten_danh_muc, mo_ta) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$data['ten_danh_muc'], $data['mo_ta'] ?? ""]);
        } else {
            // Cập nhật
            $query = "UPDATE " . $this->table . " SET ten_danh_muc = ?, mo_ta = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$data['ten_danh_muc'], $data['mo_ta'] ?? "", $data['id']]);
        }
    }

    // Xóa danh mục
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
