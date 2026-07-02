<?php
class NhaCungCapController {
    public function view() {
        $conn = mysqli_connect("localhost", "root", "", "farmi_qly");
        $result = mysqli_query($conn, "SELECT * FROM nha_cung_cap ORDER BY id DESC");
        $ds_ncc = mysqli_fetch_all($result, MYSQLI_ASSOC);

        require_once __DIR__ . '/../../view/nhacungcap/index.php';
    }
}