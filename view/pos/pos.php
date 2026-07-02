<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<link rel="stylesheet" href="/hi/public/css/pos_style.css">

<div class="main-content">
<div class="pos-container">
    <div class="pos-products">
        <div class="pos-header">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="search-product" placeholder="Tìm tên sản phẩm hoặc mã vạch..." onkeyup="filterProducts()">
            </div>
            <div class="categories-bar">
                <button class="btn-cat active" onclick="filterCategory('all')">Tất cả</button>
                <?php if(isset($danh_mucs)): foreach($danh_mucs as $dm): ?>
                    <button class="btn-cat" onclick="filterCategory('<?= htmlspecialchars($dm['ten_danh_muc']) ?>')">
                        <?= htmlspecialchars($dm['ten_danh_muc']) ?>
                    </button>
                <?php endforeach; endif; ?>
            </div>
        </div>
        
        <div class="product-grid" id="product-list">
            <?php if(isset($ds_sanpham)): foreach($ds_sanpham as $sp): 
                $img = !empty($sp['anh_san_pham']) ? "/hi/public/img/" . $sp['anh_san_pham'] : "https://via.placeholder.com/150";
                $isOut = $sp['so_luong_kho'] <= 0;
            ?>
            <div class="product-card <?= $isOut ? 'out-of-stock' : '' ?>" id="product-card-<?= $sp['id'] ?>" style="<?= $isOut ? 'opacity: 0.4; filter: grayscale(1); pointer-events: none;' : '' ?>" data-category="<?= htmlspecialchars($sp['ten_danh_muc'] ?? '') ?>" onclick="addToCart(<?= $sp['id'] ?>, '<?= addslashes($sp['ten_san_pham']) ?>', <?= $sp['gia_ban'] ?>, <?= $sp['so_luong_kho'] ?>)">
                <div class="p-img">
                    <img src="<?= $img ?>" alt="product">
                </div>
                <div class="p-info">
                    <p class="p-name"><?= htmlspecialchars($sp['ten_san_pham']) ?></p>
                    <p class="p-price"><?= number_format($sp['gia_ban']) ?>đ</p>
                    <span class="p-stock" id="stock-label-<?= $sp['id'] ?>" style="<?= $isOut ? 'color: #ff6b6b; font-weight: bold;' : '' ?>">Tồn: <span class="stock-qty-val"><?= $sp['so_luong_kho'] ?></span> <?= $isOut ? '(Hết)' : '' ?></span>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- KHU VỰC THANH TOÁN -->
    <div class="pos-checkout">
        <!-- Khu vực chọn Khách hàng -->
        <div class="customer-selection">
            <div class="customer-search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="customer-search" placeholder="SĐT hoặc Tên khách..." onkeyup="searchCustomer()">
                <button class="btn-add-cust-quick" onclick="openNewCustModal()" title="Thêm khách hàng mới">
                    <i class="fas fa-plus-circle"></i>
                </button>
                <div id="customer-results" class="search-results"></div>
            </div>
            <div id="selected-customer-info" class="selected-info" style="display: none;">
                <div class="cust-details">
                    <span class="cust-name">Khách: <b id="display-cust-name">...</b></span>
                    <span class="cust-points">Điểm: <b id="display-cust-points">0</b></span>
                </div>
                <div class="redeem-box">
                    <input type="number" id="points-to-use" placeholder="Dùng điểm..." onchange="applyPoints()">
                    <span class="redeem-label">đ</span>
                </div>
                <i class="fas fa-times-circle" onclick="clearCustomer()" title="Bỏ chọn"></i>
            </div>
        </div>

        <!-- Modal Thêm Khách Hàng Nhanh -->
        <div id="newCustModal" class="custom-modal-overlay">
            <div class="custom-modal-box" style="max-width: 400px;">
                <h3 style="margin-bottom: 20px; color: var(--pos-accent);">Thêm khách hàng mới</h3>
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; color: #8ca39d; font-size: 0.8rem;">Họ tên</label>
                    <input type="text" id="new-cust-name" style="width:100%; padding:10px; border-radius:8px; background:#0f1715; border:1px solid var(--pos-border); color:#fff;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:5px; color: #8ca39d; font-size: 0.8rem;">Số điện thoại</label>
                    <input type="text" id="new-cust-phone" style="width:100%; padding:10px; border-radius:8px; background:#0f1715; border:1px solid var(--pos-border); color:#fff;">
                </div>
                <div style="display:flex; gap:10px;">
                    <button class="btn-cancel-custom" onclick="closeNewCustModal()">Hủy</button>
                    <button class="btn-save-custom" onclick="saveNewCustomer()">Lưu khách hàng</button>
                </div>
            </div>
        </div>

        <div class="cart-header">
            <div>
                <h3 style="margin: 0;">Giỏ hàng</h3>
                <div style="font-size: 0.75rem; color: var(--pos-text-muted); margin-top: 4px;">
                    <?php 
                        $isAdmin = (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin');
                        $roleText = $isAdmin ? 'Quản trị' : 'Nhân viên';
                    ?>
                    <i class="fas <?= $isAdmin ? 'fa-user-shield' : 'fa-user-circle' ?>"></i> 
                    <?= $roleText ?>: <span id="staff-name"><?= $_SESSION['user']['fullname'] ?? 'Admin' ?></span>
                </div>
            </div>
            <span id="items-count">0 mặt hàng</span>
        </div>
        
        <div class="cart-table-container">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th style="width: 80px;">SL</th>
                        <th>Giá</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="cart-body">
                </tbody>
            </table>
        </div>

        <div class="checkout-summary">
            <div class="payment-methods">
                <button class="btn-pay active" onclick="this.parentElement.querySelectorAll('button').forEach(b=>b.classList.remove('active')); this.classList.add('active')">
                    <i class="fas fa-money-bill-wave"></i> 
                    <span>Tiền mặt</span>
                </button>
                <button class="btn-pay" onclick="this.parentElement.querySelectorAll('button').forEach(b=>b.classList.remove('active')); this.classList.add('active'); showVietQR()">
                    <i class="fas fa-university"></i> 
                    <span>Chuyển khoản</span>
                </button>
            </div>

            <div class="summary-box">
                <div class="summary-line">
                    <label>Tạm tính:</label>
                    <span id="subtotal">0đ</span>
                </div>
                <div class="summary-line">
                    <label>Giảm giá (đ):</label>
                    <input type="number" id="discount" value="0" readonly style="background: rgba(0,0,0,0.1); border: 1px solid transparent; text-align: right; font-weight: bold; width: 40%; outline: none; padding-right: 5px;">
                </div>
                <!-- Box tự động áp dụng KM -->
                <div id="promo-msg" style="font-size: 0.8rem; color: #52ddb5; text-align: right; min-height: 15px; margin-bottom: 5px;"></div>
                <div class="total-row">
                    <div class="total-label">TỔNG CỘNG</div>
                    <div class="total-amount" id="final-total">0đ</div>
                </div>
            </div>
            
            <button class="btn-submit-order" onclick="processPayment()">
                <i class="fas fa-check-circle"></i>
                XÁC NHẬN THANH TOÁN (F10)
            </button>
        </div>
    <!-- VietQR Modal -->
<div id="vietqr-modal" class="custom-modal-overlay">
    <div class="custom-modal-box" style="max-width: 450px; text-align: center; border-color: var(--pos-accent);">
        <h3 style="margin-bottom: 15px; color: var(--pos-accent); letter-spacing: 1px;">THANH TOÁN CHUYỂN KHOẢN</h3>
        
        <div id="qr-container" style="background: #fff; padding: 15px; border-radius: 12px; display: inline-block; margin-bottom: 20px; box-shadow: 0 0 25px rgba(82, 221, 181, 0.2);">
            <img id="qr-image" src="" alt="VietQR" style="width: 250px; height: 250px; display: block; object-fit: contain;">
        </div>

        <div class="qr-info" style="text-align: left; background: rgba(0,0,0,0.3); padding: 18px; border-radius: 12px; font-size: 0.95rem; margin-bottom: 25px; border: 1px solid var(--pos-border);">
            <div style="margin-bottom: 10px; display: flex; justify-content: space-between;">
                <span style="color: var(--pos-text-muted);">Ngân hàng:</span> 
                <b style="color: #fff;">Vietcombank</b>
            </div>
            <div style="margin-bottom: 10px; display: flex; justify-content: space-between;">
                <span style="color: var(--pos-text-muted);">Chủ tài khoản:</span> 
                <b style="color: #fff;">LE XUAN THANG</b>
            </div>
            <div style="margin-bottom: 10px; display: flex; justify-content: space-between;">
                <span style="color: var(--pos-text-muted);">Số tài khoản:</span> 
                <b style="color: var(--pos-accent); font-size: 1.1rem;">9339670825</b>
            </div>
            <div style="margin-bottom: 10px; display: flex; justify-content: space-between;">
                <span style="color: var(--pos-text-muted);">Số tiền:</span> 
                <b id="qr-amount" style="color: #ff6b6b; font-size: 1.2rem;">0đ</b>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--pos-text-muted);">Nội dung:</span> 
                <b style="color: #fff;">ThanhToanPOS</b>
            </div>
        </div>

        <button class="btn-submit-order" onclick="closeVietQR()" style="background: rgba(82, 221, 181, 0.1); border: 1px solid var(--pos-accent); color: var(--pos-accent);">
            <i class="fas fa-times-circle"></i> ĐÓNG CỬA SỔ
        </button>
    </div>
</div>
</div>

<script src="/hi/public/js/pos_logic.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>