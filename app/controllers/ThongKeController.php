<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../app/core/Database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // 1. Tính tổng doanh thu từ bảng don_hang
    $query_dt = "SELECT SUM(tong_thanh_toan) as total FROM don_hang WHERE trang_thai = 'xong'";
    $stmt_dt = $db->prepare($query_dt);
    $stmt_dt->execute();
    $row_dt = $stmt_dt->fetch(PDO::FETCH_ASSOC);
    $doanh_thu = number_format($row_dt['total'] ?? 0, 0, ',', '.') . " đ";

    // 2. Đếm số đơn hàng hôm nay
    $query_don = "SELECT COUNT(*) as total FROM don_hang WHERE DATE(ngay_tao) = CURDATE()";
    $stmt_don = $db->prepare($query_don);
    $stmt_don->execute();
    $row_don = $stmt_don->fetch(PDO::FETCH_ASSOC);

    // 3. Đếm tổng số sản phẩm
    $query_sp = "SELECT COUNT(*) as total FROM san_pham";
    $stmt_sp = $db->prepare($query_sp);
    $stmt_sp->execute();
    $row_sp = $stmt_sp->fetch(PDO::FETCH_ASSOC);

    // 4. Đếm tổng số khách hàng
    $query_kh = "SELECT COUNT(*) as total FROM khach_hang";
    $stmt_kh = $db->prepare($query_kh);
    $stmt_kh->execute();
    $row_kh = $stmt_kh->fetch(PDO::FETCH_ASSOC);

    // Trả về kết quả JSON cho JavaScript
    echo json_encode([
        "tong_doanh_thu" => $doanh_thu,
        "tong_don_hang" => $row_don['total'],
        "tong_san_pham" => $row_sp['total'],
        "tong_khach_hang" => $row_kh['total']
    ]);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}