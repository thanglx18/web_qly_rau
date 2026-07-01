<?php

class CategoryController {
    public function view() {
        $conn = mysqli_connect("localhost", "root", "", "farmi_qly");
        $result = mysqli_query($conn, "SELECT * FROM danh_muc ORDER BY id DESC");
        $danh_mucs = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        // Đường dẫn chuẩn dựa trên cấu trúc folder HI/view
        $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'category' . DIRECTORY_SEPARATOR . 'index.php';
        require_once $path;
    }
    public function save() {
        $conn = mysqli_connect("localhost", "root", "", "farmi_qly");
        
        $id = $_POST['id'] ?? '';
        $ten = $_POST['ten_danh_muc'];
        $mo_ta = $_POST['mo_ta'];

        if ($id == '') {
            $sql = "INSERT INTO danh_muc (ten_danh_muc, mo_ta) VALUES ('$ten', '$mo_ta')";
        } else {
            $sql = "UPDATE danh_muc SET ten_danh_muc = '$ten', mo_ta = '$mo_ta' WHERE id = $id";
        }

        $response = mysqli_query($conn, $sql) 
            ? ['status' => 'success'] 
            : ['status' => 'error', 'message' => mysqli_error($conn)];
        
        echo json_encode($response); // Trả về JSON theo chuẩn REST
        exit;
    }
    public function delete() {
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? '';
        
        if (!empty($id)) {
            $conn = mysqli_connect("localhost", "root", "", "farmi_qly");
            $sql = "DELETE FROM danh_muc WHERE id = $id";
            
            if (mysqli_query($conn, $sql)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Không thể xóa danh mục này']);
            }
        }
        exit;
    }
}