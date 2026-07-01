<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<link rel="stylesheet" href="/hi/public/css/sanpham.css"> 
<div class="main-content">
    <div class="page-header">
        <h1 class="main-title">Quản lý Danh mục</h1>
        <button class="btn-add" onclick="openAddCategoryModal()">
            <i class="fas fa-plus"></i> Thêm danh mục mới
        </button>
    </div>

    <div class="table-container">   
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 10%;">ID</th>
                    <th style="width: 40%;">Tên danh mục</th>
                    <th style="width: 35%;">Mô tả</th>
                    <th style="width: 15%; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody id="category-list">
                <?php if(isset($danh_mucs)): foreach($danh_mucs as $dm): ?>
                <tr>
                    <td><?= $dm['id'] ?></td>
                    <td><span class="category-badge"><?= htmlspecialchars($dm['ten_danh_muc']) ?></span></td>
                    <td><?= htmlspecialchars($dm['mo_ta']) ?></td>
                    <td style="text-align: center;">
                        <button class="btn-action btn-edit" onclick="editCategory(<?= $dm['id'] ?>, '<?= addslashes($dm['ten_danh_muc']) ?>', '<?= addslashes($dm['mo_ta']) ?>')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action btn-delete" onclick="deleteCategory(<?= $dm['id'] ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="categoryModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <span class="close" onclick="closeCategoryModal()">&times;</span>
        <h2 id="modalTitle" style="text-align: center; color: var(--primary); margin-bottom: 25px;">DANH MỤC</h2>
        
        <form id="categoryForm">
            <input type="hidden" id="categoryId" name="id">
            <div class="form-group">
                <label>Tên danh mục:</label>
                <input type="text" id="ten_danh_muc" name="ten_danh_muc" required placeholder="Ví dụ: Trái cây tươi...">
            </div>
            <div class="form-group">
                <label>Mô tả:</label>
                <textarea id="mo_ta" name="mo_ta" rows="4" placeholder="Nhập mô tả danh mục..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeCategoryModal()">Hủy bỏ</button>
                <button type="submit" class="btn-save">Lưu dữ liệu</button>
            </div>
        </form>
    </div>
</div>

<script src="/hi/public/js/category.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>