<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<link rel="stylesheet" href="/hi/public/css/khuyenmai.css">
<link rel="stylesheet" href="/hi/public/css/sanpham.css"> <!-- Tái sử dụng bảng chuẩn của Admin -->

<div class="main-content">
    <div class="page-header">
        <h1 class="main-title">Quản lý Khuyến mãi</h1>
        <button class="btn-add" onclick="openAddPromoModal()">
            <i class="fas fa-plus"></i> Tạo chương trình mới
        </button>
    </div>

    <!-- Container cho bảng dữ liệu -->
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 15%;">Mã Code</th>
                    <th style="width: 20%;">Sản phẩm áp dụng</th>
                    <th style="width: 15%;">Giảm giá (%)</th>
                    <th style="width: 15%;">Ngày bắt đầu</th>
                    <th style="width: 15%;">Ngày kết thúc</th>
                    <th style="width: 15%; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody id="promo-list">
                <!-- Dữ liệu sẽ được load bằng AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Thêm/Sửa Khuyến mãi -->
<div id="promoModal" class="modal">
    <div class="modal-content" style="max-width: 550px;">
        <span class="close" onclick="closePromoModal()">&times;</span>
        <h2 id="modalTitle" style="text-align: center; color: #ff6b6b; margin-bottom: 25px; font-weight: 800;">KHUYẾN MÃI</h2>
        
        <form id="promoForm">
            <input type="hidden" id="promoId" name="id">
            <input type="hidden" id="danh_sach_san_pham" name="danh_sach_san_pham">

            <div class="form-group">
                <label><i class="fas fa-boxes"></i> Sản phẩm áp dụng (Chọn nhiều):</label>
                
                <input type="text" id="search_promo_product" placeholder="Tìm kiếm sản phẩm (Tên, ID)..." style="width: 100%; padding: 8px 12px; margin-bottom: 8px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 6px; font-family: inherit; font-size: 0.9rem;" onkeyup="filterPromoProducts()">
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding: 0 5px;">
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="check_all_products" style="width: 16px; height: 16px; cursor: pointer; accent-color: #ff6b6b;" onchange="toggleAllProducts(this)">
                        <span style="color: #ff6b6b; font-weight: 500; font-size: 0.9rem;">Chọn tất cả</span>
                    </label>
                    <span id="selected_count" style="font-size: 0.8rem; color: #a3bfbc;">Đã chọn: 0</span>
                </div>

                <div id="product_checkboxes" style="max-height: 180px; overflow-y: auto; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; display: flex; flex-direction: column; gap: 10px;">
                    <div style="color: rgba(255,255,255,0.5); font-style: italic; text-align: center;">Đang tải danh sách...</div>
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-barcode"></i> Mã Code:</label>
                <input type="text" id="ma_code" name="ma_code" required placeholder="Ví dụ: SUMMER2024">
            </div>

            <div class="form-group">
                <label><i class="fas fa-percentage"></i> Phần trăm giảm giá:</label>
                <input type="number" id="phan_tram_giam" name="phan_tram_giam" required min="1" max="100" placeholder="Nhập số từ 1-100">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 20px;">
                <div class="form-group">
                    <label><i class="fas fa-calendar-alt"></i> Ngày bắt đầu:</label>
                    <input type="date" id="ngay_bat_dau" name="ngay_bat_dau" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar-check"></i> Ngày kết thúc:</label>
                    <input type="date" id="ngay_ket_thuc" name="ngay_ket_thuc" required>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 30px;">
                <button type="button" class="btn-cancel" onclick="closePromoModal()">Hủy bỏ</button>
                <button type="submit" class="btn-save" style="background: linear-gradient(90deg, #ff6b6b, #ff8e53);">Lưu chương trình</button>
            </div>
        </form>
    </div>
</div>

<script src="/hi/public/js/khuyenmai.js?v=<?= time() ?>"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
