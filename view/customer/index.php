<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<link rel="stylesheet" href="/hi/public/css/dashboard.css">
<link rel="stylesheet" href="/hi/public/css/customer.css">

<div class="main-content">
    <div class="page-header">
        <h1 class="main-title">Quản lý Khách hàng</h1>
        <div class="header-actions">
            <button class="btn-add" onclick="openAddCustomerModal()">
                <i class="fas fa-user-plus"></i> Thêm khách hàng
            </button>
        </div>
    </div>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 20%;">Họ tên</th>
                    <th style="width: 15%;">Số điện thoại</th>
                    <th style="width: 15%;">Email</th>
                    <th style="width: 25%;">Địa chỉ</th>
                    <th style="width: 10%;">Điểm</th>
                    <th style="width: 10%; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody id="customer-list">
                <!-- Dữ liệu load bằng AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Thêm/Sửa Khách hàng -->
<div id="customerModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <span class="close" onclick="closeCustomerModal()">&times;</span>
        <h2 id="modalTitle" style="text-align: center; color: var(--primary); margin-bottom: 25px;">THÊM KHÁCH HÀNG MỚI</h2>
        
        <form id="customerForm">
            <input type="hidden" id="cust_id" name="id">
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Họ và tên:</label>
                <input type="text" id="ho_ten" name="ho_ten" required placeholder="Nhập tên khách hàng...">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label>Số điện thoại:</label>
                <input type="text" id="so_dien_thoai" name="so_dien_thoai" required placeholder="Nhập số điện thoại...">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label>Email:</label>
                <input type="email" id="email" name="email" placeholder="Nhập email...">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label>Địa chỉ:</label>
                <input type="text" id="dia_chi" name="dia_chi" placeholder="Nhập địa chỉ...">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label>Điểm tích lũy:</label>
                <input type="number" id="diem_tich_luy" name="diem_tich_luy" value="0">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeCustomerModal()">Hủy</button>
                <button type="submit" class="btn-save" style="background: var(--primary); color: #121212;">Lưu thông tin</button>
            </div>
        </form>
    </div>
</div>

<script src="/hi/public/js/customer.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
