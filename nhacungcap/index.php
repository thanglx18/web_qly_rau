<?php
// 1. Nạp Header và Sidebar để giữ giao diện Farmi Admin
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<link rel="stylesheet" href="/hi/public/css/nhacungcap.css">

<div class="main-content">
    <div class="page-header">
        <h1 class="main-title">Quản lý Nhà cung cấp</h1>
        <button class="btn-add" onclick="openModal()">
            <i class="fas fa-plus"></i> Thêm nhà cung cấp
        </button>
    </div>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>TÊN NHÀ CUNG CẤP</th>
                    <th>SỐ ĐIỆN THOẠI</th>
                    <th>ĐỊA CHỈ</th>
                    <th style="text-align: center;">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($ds_ncc) && !empty($ds_ncc)): foreach($ds_ncc as $n): ?>
                <tr>
                    <td><?= $n['id'] ?></td>
                    <td style="color: var(--primary-color); font-weight: 500;"><?= htmlspecialchars($n['ten_nha_cc']) ?></td>
                    <td><?= htmlspecialchars($n['so_dien_thoai'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($n['dia_chi'] ?? 'N/A') ?></td>
                    <td style="text-align: center;">
                        <button class="btn-edit" style="color: #fbbf24; background: none; border: none; cursor: pointer; margin-right: 10px;" 
                                onclick="editNCC(<?= $n['id'] ?>, '<?= addslashes($n['ten_nha_cc']) ?>', '<?= $n['so_dien_thoai'] ?? '' ?>', '<?= addslashes($n['dia_chi'] ?? '') ?>')">
                            <i class="fas fa-edit"></i> Sửa
                        </button>
                        <button class="btn-delete" style="color: #f87171; background: none; border: none; cursor: pointer;" 
                                onclick="deleteNCC(<?= $n['id'] ?>)">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="5" style="text-align: center; padding: 20px; color: #9ca3af;">Không có dữ liệu.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="nccModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle" style="margin-bottom:20px; color: var(--primary-color);">Thêm nhà cung cấp</h2>
        
        <form id="nccForm">
            <input type="hidden" name="id" id="nccId">
            
            <div class="form-group">
                <label>Tên nhà cung cấp</label>
                <input type="text" name="ten_nha_cc" id="ten_nha_cc" required placeholder="Ví dụ: Nông sản sạch Ba Chúc...">
            </div>
            
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="so_dien_thoai" id="so_dien_thoai" placeholder="Nhập số điện thoại (vd: 0987...)">
            </div>
            
            <div class="form-group">
                <label>Địa chỉ</label>
                <input type="text" name="dia_chi" id="dia_chi" placeholder="Nhập địa chỉ nhà cung cấp...">
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn-save">Lưu lại</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script src="/hi/public/js/nhacungcap.js"></script>