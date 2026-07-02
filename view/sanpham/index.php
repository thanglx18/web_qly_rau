<?php
// Nạp các thành phần giao diện hệ thống
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<link rel="stylesheet" href="/hi/public/css/sanpham.css?v=<?= time() ?>">

<style>
/* CSS NỘI BỘ ĐỂ GHI ĐÈ TỨC THÌ (CHỐNG CACHE) */
.search-box {
    background: rgba(255, 255, 255, 0.05) !important;
    border: 1px solid rgba(82, 221, 181, 0.2) !important;
}
.search-box input#search-product-mgmt {
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
    color: #fff !important;
    outline: none !important;
    -webkit-appearance: none;
}
</style>

<div class="main-content">
    <div class="page-header">
        <h1 class="main-title">Quản lý Sản phẩm</h1>
        <div class="search-center-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="search-product-mgmt" placeholder="Tìm tên sản phẩm...">
            </div>
        </div>
        <button class="btn-add" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Thêm sản phẩm mới
        </button>
    </div>

    <div class="table-container">   
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên sản phẩm</th> 
                    <th>Danh mục</th>
                    <th>Nhà cung cấp</th>
                    <th>Giá bán</th>
                    <th>Kho</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="product-list">
                <?php if(isset($ds_sanpham) && !empty($ds_sanpham)): foreach($ds_sanpham as $sp): ?>
                <tr>
                    <td><?= $sp['id'] ?></td>
                    <td style="color: var(--primary-mint); font-weight: 500;">
                        <?= htmlspecialchars($sp['ten_san_pham']) ?>
                    </td>
                    <td><?= htmlspecialchars($sp['ten_danh_muc'] ?? 'Chưa phân loại') ?></td>
                    <td><?= htmlspecialchars($sp['ten_nha_cc'] ?? 'N/A') ?></td>
                    <td><?= number_format($sp['gia_ban']) ?>đ</td>
                    <td><?= $sp['so_luong_kho'] ?> <?= htmlspecialchars($sp['don_vi_tinh'] ?? '') ?></td>
                    <td>
                        <span class="badge <?= $sp['trang_thai'] == 1 ? 'status-ok' : 'status-low' ?>">
                            <?= $sp['trang_thai'] == 1 ? '✔ Đang bán' : '✖ Ngừng bán' ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <button class="btn-action btn-edit" onclick='editProduct(<?= json_encode($sp) ?>)'>
                            Sửa
                        </button>
                        <button class="btn-action btn-delete" onclick="deleteProduct(<?= $sp['id'] ?>)">
                            Xóa
                        </button>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="8" style="text-align:center">Không có sản phẩm nào được tìm thấy.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">THÊM SẢN PHẨM</h2>
        
        <form id="productForm">
            <input type="hidden" id="productId" name="id">
            
            <div class="form-row">
                <div class="form-group">
                    <label>Tên sản phẩm:</label>
                    <input type="text" id="ten" name="ten_san_pham" required placeholder="Ví dụ: Rau Cải Thìa Organic...">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label>Hình ảnh sản phẩm:</label>
                <div id="current-image-info" style="display:none; margin-bottom:12px; border: 1px dashed #444; padding: 10px; border-radius: 8px; text-align: center;"></div>
                <input type="file" id="hinh_anh" name="hinh_anh" accept="image/*">
                <small style="color: var(--text-muted); margin-top: 5px; display: block;">Chọn ảnh mới để cập nhật (định dạng .jpg, .png)</small>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Giá bán (VNĐ):</label>
                    <input type="number" id="gia" name="gia_ban" required placeholder="0">
                </div>
                <div class="form-group">
                    <label>Số lượng tồn:</label>
                    <input type="number" id="kho" name="so_luong_kho" required placeholder="100">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Danh mục:</label>
                    <select id="id_danh_muc" name="id_danh_muc" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php if(isset($danh_mucs)): foreach($danh_mucs as $dm): ?>
                            <option value="<?= $dm['id'] ?>"><?= htmlspecialchars($dm['ten_danh_muc']) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nhà cung cấp:</label>
                    <select id="id_nha_cc" name="id_nha_cc" required>
                        <option value="">-- Chọn nhà cung cấp --</option>
                        <?php if(isset($nha_ccs)): foreach($nha_ccs as $ncc): ?>
                            <option value="<?= $ncc['id'] ?>"><?= htmlspecialchars($ncc['ten_nha_cc']) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Đơn vị tính:</label>
                    <input type="text" id="don_vi" name="don_vi_tinh" placeholder="Kg, Bó, Túi...">
                </div>
                <div class="form-group">
                    <label>Trạng thái:</label>
                    <select id="trang_thai" name="trang_thai">
                        <option value="1">Đang bán</option>
                        <option value="0">Ngừng bán</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Hủy bỏ</button>
                <button type="submit" class="btn-save" id="btnSubmit">Lưu dữ liệu</button>
            </div>
        </form>
    </div>
</div>

<script src="/hi/public/js/sanpham.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>