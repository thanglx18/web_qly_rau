<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/hi/public/css/dashboard.css">

<div class="main-content">
    <h1 class="main-title">Dashboard</h1>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'forbidden'): ?>
    <div style="background: rgba(231, 76, 60, 0.1); border: 1px solid #e74c3c; color: #ff6b6b; padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-lock"></i>
        <strong>Không có quyền truy cập!</strong> Chức năng này chỉ dành cho Quản trị viên.
    </div>
    <?php endif; ?>

    <div class="cards-container">
        <div class="stat-card">
            <div class="stat-content">
                <h4>Doanh thu</h4>
                <p id="stat-doanh-thu">0 đ</p>
            </div>
            <div class="stat-icon"><i class="fas fa-coins"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <h4>Đơn hôm nay</h4>
                <p id="stat-don-hang">0</p>
            </div>
            <div class="stat-icon"><i class="fas fa-shopping-basket"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <h4>Sản phẩm</h4>
                <p id="stat-san-pham">0</p>
            </div>
            <div class="stat-icon"><i class="fas fa-box-open"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <h4>Khách hàng</h4>
                <p id="stat-khach-hang">0</p>
            </div>
            <div class="stat-icon"><i class="fas fa-users"></i></div>
        </div>
    </div>

    <div class="chart-box">
        <h3 class="table-title">Doanh thu 7 ngày qua</h3>
        <div style="height: 300px;">
            <canvas id="chart"></canvas>
        </div>
    </div>

    <div class="top-products-box">
        <h3 class="table-title">Sản phẩm bán chạy</h3>
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Tên Sản phẩm</th>
                    <th style="text-align: right;">Số lượng đã bán</th>
                </tr>
            </thead>
            <tbody id="top-product-body">
                <tr><td colspan="2" style="text-align: center;">Đang tải dữ liệu...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Sản phẩm sắp hết hàng -->
    <div class="top-products-box" id="low-stock-section" style="display:none;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:5px;">
            <h3 class="table-title" style="margin:0; color:#ff6b6b;">
                <i class="fas fa-exclamation-triangle" style="margin-right:8px;"></i>
                Sản phẩm sắp hết hàng
            </h3>
            <span id="low-stock-count" style="background:rgba(231,76,60,0.15); color:#ff6b6b; border:1px solid rgba(231,76,60,0.3); padding:3px 12px; border-radius:20px; font-size:0.78rem; font-weight:700;"></span>
        </div>
        <p style="font-size:0.8rem; color:#A3BFB0; margin-bottom:15px;">Sản phẩm có số lượng tồn kho dưới 10</p>
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th style="text-align:center;">Tồn kho</th>
                    <th style="text-align:center;">Trạng thái</th>
                </tr>
            </thead>
            <tbody id="low-stock-body">
                <tr><td colspan="3" style="text-align:center;">Đang tải...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/hi/public/js/dashboard.js?v=<?php echo time(); ?>"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>