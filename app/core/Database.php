<?php
class Database {
    private $host = "localhost";      // Chạy trên XAMPP local
    private $db_name = "farmi_qly";   // Tên database
    private $username = "root";       // User của MySQL
    private $password = "";           // Mặc định của XAMPP trống
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            // Thiết lập chế độ báo lỗi để dễ debug
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Lỗi kết nối: " . $exception->getMessage();
        }
        return $this->conn;
    }
}