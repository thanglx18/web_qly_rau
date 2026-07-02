<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE");

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/NhaCungCap.php';

$database = new Database();
$db = $database->getConnection();
$ncc = new NhaCungCap($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $stmt = $ncc->read();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "data" => $data]);
        break;

    case 'POST':
        $data = [
            'id' => $_POST['id'] ?? null,
            'ten_nha_cc' => $_POST['ten_nha_cc'],
            'so_dien_thoai' => $_POST['so_dien_thoai'],
            'dia_chi' => $_POST['dia_chi']
        ];
        echo json_encode($ncc->save($data) ? ["status" => "success"] : ["status" => "error"]);
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        echo json_encode($ncc->delete($id) ? ["status" => "success"] : ["status" => "error"]);
        break;
}