<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<link rel="stylesheet" href="/hi/public/css/dashboard.css">
<link rel="stylesheet" href="/hi/public/css/order.css">

<div class="main-content">
    <div class="page-header">
        <h1 class="main-title">Lịch sử Đơn hàng</h1>
        <div class="header-actions">
            <button class="btn-add" onclick="loadOrders()" style="background: var(--secondary); color: #fff;">
                <i class="fas fa-sync-alt"></i> Làm mới
            </button>
        </div>
    </div>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 8%;">Mã ĐH</th>
                    <th style="width: 15%;">Thời gian</th>
                    <th style="width: 20%;">Khách hàng</th>
                    <th style="width: 12%;">Thanh toán</th>
                    <th style="width: 15%;">Tổng tiền</th>
                    <th style="width: 15%;">Trạng thái</th>
                    <th style="width: 15%; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody id="order-list">
                <!-- Dữ liệu load bằng AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Chi tiết Đơn hàng -->
<div id="orderDetailModal" class="modal">
    <div class="modal-content invoice-modal">
        <span class="close no-print" onclick="closeDetailModal()">&times;</span>
        
        <div id="printableArea">
            <div class="invoice-header">
                <div class="invoice-brand">
                    <i class="fas fa-leaf"></i> <span>FARMI</span>
                    <p style="font-size: 0.8rem; font-weight: normal; margin-top: 5px; opacity: 0.7;">Hệ thống quản lý cửa học Rau sạch</p>
                </div>
                <div class="invoice-title">
                    <h2>HÓA ĐƠN BÁN HÀNG</h2>
                    <p>Mã số: <span id="detailOrderId" class="text-mint"></span></p>
                    <small id="detailOrderDate"></small>
                </div>
            </div>
            <hr class="invoice-divider">
            <div class="invoice-info-grid">
                <div class="info-block">
                    <h4><i class="fas fa-user-tag"></i> Khách hàng</h4>
                    <p class="customer-name" id="detailCustomerName"></p>
                    <p><i class="fas fa-phone-alt"></i> <span id="detailCustomerPhone"></span></p>
                    <p><i class="fas fa-map-marker-alt"></i> <span id="detailCustomerAddress"></span></p>
                </div>
                <div class="info-block">
                    <h4><i class="fas fa-credit-card"></i> Thanh toán</h4>
                    <p>Trạng thái: <span id="detailOrderStatus" class="status-badge"></span></p>
                    <p>Hình thức: <span id="detailPaymentMethod"></span></p>
                    <p>Thu ngân: <span id="detailUserName">Admin</span></p>
                </div>
            </div>

            <div class="invoice-table-wrapper">
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>SẢN PHẨM</th>
                            <th class="text-center">SL</th>
                            <th class="text-right">ĐƠN GIÁ</th>
                            <th class="text-right">THÀNH TIỀN</th>
                        </tr>
                    </thead>
                    <tbody id="order-details-body">
                        <!-- Data load AJAX -->
                    </tbody>
                </table>
            </div>

            <div class="invoice-footer-content">
                <div class="invoice-thanks">
                    <p>Cảm ơn quý khách đã tin dùng sản phẩm sạch!</p>
                    <p>Hẹn gặp lại quý khách lần sau.</p>
                </div>
                <div class="invoice-summary">
                    <div class="sum-row">
                        <span>Tiền hàng:</span>
                        <span id="detailSubtotal">0</span>₫
                    </div>
                    <div class="sum-row discount">
                        <span>Giảm giá:</span>
                        - <span id="detailDiscount">0</span>₫
                    </div>
                    <div class="sum-row total">
                        <span>TỔNG CỘNG:</span>
                        <span id="detailTotal" class="text-mint">0</span>₫
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-actions no-print">
            <button class="btn-action-print" onclick="printCurrentOrder()">
                <i class="fas fa-print"></i> In hóa đơn
            </button>
            <button class="btn-action-close" onclick="closeDetailModal()">Đóng</button>
        </div>
    </div>
</div>

<script src="/hi/public/js/order.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
