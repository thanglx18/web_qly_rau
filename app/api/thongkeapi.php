<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

require_once __DIR__ . '/../core/Database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo json_encode(["error" => "Không thể kết nối database"]);
    exit;
}

try {
    // 1. Tổng doanh thu từ đơn hàng HOÀN TẤT (trang_thai = 'xong')
    $query_dt = "SELECT COALESCE(SUM(tong_thanh_toan), 0) as total FROM don_hang WHERE trang_thai = 'xong'";
    $stmt_dt = $db->prepare($query_dt);
    $stmt_dt->execute();
    $row_dt = $stmt_dt->fetch(PDO::FETCH_ASSOC);
    $doanh_thu_raw = (float)$row_dt['total'];
    $doanh_thu = number_format($doanh_thu_raw, 0, ',', '.') . " đ";

    // 2. Đếm số đơn hàng hôm nay (tất cả trạng thái)
    $query_don = "SELECT COUNT(*) as total FROM don_hang WHERE DATE(ngay_tao) = CURDATE()";
    $stmt_don = $db->prepare($query_don);
    $stmt_don->execute();
    $row_don = $stmt_don->fetch(PDO::FETCH_ASSOC);

    // 3. Đếm tổng số sản phẩm
    $query_sp = "SELECT COUNT(*) as total FROM san_pham WHERE trang_thai = 1";
    $stmt_sp = $db->prepare($query_sp);
    $stmt_sp->execute();
    $row_sp = $stmt_sp->fetch(PDO::FETCH_ASSOC);

    // 4. Đếm tổng số khách hàng
    $query_kh = "SELECT COUNT(*) as total FROM khach_hang";
    $stmt_kh = $db->prepare($query_kh);
    $stmt_kh->execute();
    $row_kh = $stmt_kh->fetch(PDO::FETCH_ASSOC);

    // 5. Dữ liệu biểu đồ doanh thu 7 ngày gần nhất (chỉ đơn HOÀN TẤT)
    $query_chart = "SELECT DATE(ngay_tao) as ngay, COALESCE(SUM(tong_thanh_toan), 0) as tong
                    FROM don_hang
                    WHERE trang_thai = 'xong'
                    AND DATE(ngay_tao) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                    GROUP BY DATE(ngay_tao)
                    ORDER BY ngay ASC";
    $stmt_chart = $db->prepare($query_chart);
    $stmt_chart->execute();

    // Tạo mảng 7 ngày với giá trị mặc định = 0
    $days = [];
    for ($i = 6; $i >= 0; $i--) {
        $date_key = date('Y-m-d', strtotime("-$i days"));
        $days[$date_key] = 0;
    }

    // Đổ dữ liệu thực tế vào
    while ($row = $stmt_chart->fetch(PDO::FETCH_ASSOC)) {
        if (isset($days[$row['ngay']])) {
            $days[$row['ngay']] = (float)$row['tong'];
        }
    }

    $labels = [];
    $values = [];
    foreach ($days as $date => $total) {
        $labels[] = date('d/m', strtotime($date));
        $values[] = $total;
    }

    // 6. Lấy danh sách sản phẩm sắp hết hàng (tồn kho < 10)
    $query_low = "SELECT ten_san_pham, so_luong_kho, don_vi_tinh
                  FROM san_pham
                  WHERE trang_thai = 1 AND so_luong_kho < 10
                  ORDER BY so_luong_kho ASC
                  LIMIT 20";
    $stmt_low = $db->prepare($query_low);
    $stmt_low->execute();
    $low_stock = $stmt_low->fetchAll(PDO::FETCH_ASSOC);

    // 7. Lấy danh sách Top 10 sản phẩm bán chạy nhất
    $query_top = "SELECT sp.ten_san_pham, SUM(ct.so_luong) as tong_ban, sp.so_luong_kho, sp.don_vi_tinh
                  FROM chi_tiet_don_hang ct
                  JOIN san_pham sp ON ct.id_san_pham = sp.id
                  JOIN don_hang dh ON ct.id_don_hang = dh.id
                  WHERE dh.trang_thai = 'xong'
                  GROUP BY sp.id
                  ORDER BY tong_ban DESC
                  LIMIT 10";
    $stmt_top = $db->prepare($query_top);
    $stmt_top->execute();
    $top_products = $stmt_top->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status"            => "success",
        "tong_doanh_thu"    => $doanh_thu,
        "tong_doanh_thu_so" => $doanh_thu_raw,
        "tong_don_hang"     => (int)$row_don['total'],
        "tong_san_pham"     => (int)$row_sp['total'],
        "tong_khach_hang"   => (int)$row_kh['total'],
        "chart" => [
            "labels" => $labels,
            "values" => $values
        ],
        "san_pham_sap_het" => $low_stock,
        "top_san_pham_ban_chay" => $top_products
    ]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
