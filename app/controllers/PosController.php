<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/SanPham.php';
require_once __DIR__ . '/../models/DanhMuc.php';

class PosController {
    public function view() {
        $db = (new Database())->getConnection();
        $productModel = new SanPham($db);
        $categoryModel = new DanhMuc($db);

        $ds_sanpham = $productModel->readWithJoin()->fetchAll(PDO::FETCH_ASSOC);
        $danh_mucs = $categoryModel->read()->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../view/pos/pos.php';
    }
}
