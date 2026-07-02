<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<link rel="stylesheet" href="/hi/public/css/dashboard.css">
<link rel="stylesheet" href="/hi/public/css/user.css">

<div class="main-content">
    <div class="page-header">
        <h1 class="main-title">Quản lý Tài khoản</h1>
        <div class="header-actions">
             <button class="btn-add" style="background: var(--secondary); color: #fff; margin-right: 10px;" onclick="loadUsers()">
                <i class="fas fa-sync-alt"></i> Làm mới
            </button>
            <button class="btn-add" onclick="openAddUserModal()">
                <i class="fas fa-user-plus"></i> Thêm tài khoản
            </button>
        </div>
    </div>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 15%;">Tên đăng nhập</th>
                    <th style="width: 20%;">Họ tên</th>
                    <th style="width: 15%;">Vai trò</th>
                    <th style="width: 15%;">Trạng thái</th>
                    <th style="width: 15%;">Ngày tạo</th>
                    <th style="width: 15%; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody id="user-list">
                <!-- Dữ liệu load bằng AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Thêm/Sửa Tài khoản -->
<div id="userModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <span class="close" onclick="closeUserModal()">&times;</span>
        <h2 id="modalTitle" style="text-align: center; color: var(--primary); margin-bottom: 25px;">TẠO TÀI KHOẢN MỚI</h2>
        
        <form id="userForm">
            <input type="hidden" id="user_id" name="id">
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Tên đăng nhập:</label>
                <input type="text" id="ten_dang_nhap" name="ten_dang_nhap" required placeholder="Ví dụ: nhanvien_1">
            </div>

            <!-- Ô mật khẩu cũ (Chỉ hiện khi sửa) -->
            <div id="oldPassGroup" class="form-group" style="margin-bottom: 15px; display: none;">
                <label style="color: #ff6b6b;">Mật khẩu hiện tại:</label>
                <input type="text" id="old_password" readonly style="background: rgba(231, 76, 60, 0.1); border: 1px solid rgba(231, 76, 60, 0.2); color: #ff6b6b; font-weight: 700;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label>Mật khẩu mới:</label>
                <input type="password" id="mat_khau" name="mat_khau" placeholder="Nhập mật khẩu mới...">
                <small id="pwdHint" style="color: #888; display: none;">Để trống nếu không muốn đổi</small>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label>Họ và tên:</label>
                <input type="text" id="ho_ten" name="ho_ten" required placeholder="Nhập họ và tên...">
            </div>

            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Vai trò:</label>
                    <select id="vai_tro" name="vai_tro" style="width: 100%; padding: 10px; background: #1a2c24; border: 1px solid #232d28; color: #fff; border-radius: 8px;">
                        <option value="staff">Nhân viên</option>
                        <option value="admin">Quản trị viên</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Trạng thái:</label>
                    <select id="trang_thai" name="trang_thai" style="width: 100%; padding: 10px; background: #1a2c24; border: 1px solid #232d28; color: #fff; border-radius: 8px;">
                        <option value="1">Đang hoạt động</option>
                        <option value="0">Đang khóa</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 20px;">
                <button type="button" class="btn-cancel" onclick="closeUserModal()">Hủy</button>
                <button type="submit" class="btn-save" style="background: var(--primary); color: #121212;">Lưu tài khoản</button>
            </div>
        </form>
    </div>
</div>

<script src="/hi/public/js/user.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
