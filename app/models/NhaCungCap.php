<?php
class NhaCungCap {
    private $conn;
    private $table = "nha_cung_cap";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy toàn bộ danh sách
    public function read() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Thêm hoặc cập nhật
    public function save($data) {
        if (empty($data['id'])) {
            $query = "INSERT INTO " . $this->table . " (ten_nha_cc, so_dien_thoai, dia_chi) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$data['ten_nha_cc'], $data['so_dien_thoai'], $data['dia_chi']]);
        } else {
            $query = "UPDATE " . $this->table . " SET ten_nha_cc = ?, so_dien_thoai = ?, dia_chi = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$data['ten_nha_cc'], $data['so_dien_thoai'], $data['dia_chi'], $data['id']]);
        }
    }

    // Xóa
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}