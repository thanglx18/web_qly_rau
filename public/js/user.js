document.addEventListener('DOMContentLoaded', loadUsers);

function loadUsers() {
    fetch('/hi/app/api/userapi.php')
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('user-list');
        tbody.innerHTML = '';
        
        if (data.status === 'success' && data.data.length > 0) {
            data.data.forEach(item => {
                const statusIcon = item.trang_thai == 1 ? '<i class="fas fa-check-circle status-active"></i> Hoạt động' : '<i class="fas fa-lock status-locked"></i> Đã khóa';
                const roleClass = `role-${item.vai_tro}`;
                
                tbody.innerHTML += `
                    <tr>
                        <td>${item.id}</td>
                        <td class="username-text">@${item.ten_dang_nhap}</td>
                        <td><strong>${item.ho_ten}</strong></td>
                        <td><span class="role-badge ${roleClass}">${item.vai_tro}</span></td>
                        <td>${statusIcon}</td>
                        <td><small>${item.ngay_tao}</small></td>
                        <td style="text-align: center;">
                            <button class="btn-action btn-edit" title="Sửa" onclick="editUser(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                                <i class="fas fa-user-edit"></i>
                            </button>
                            <button class="btn-action btn-delete" title="Xóa vĩnh viễn" onclick="deleteUser(${item.id})">
                                <i class="fas fa-user-minus"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Chưa có tài khoản nào.</td></tr>';
        }
    });
}

function openAddUserModal() {
    document.getElementById('modalTitle').innerText = 'TẠO TÀI KHOẢN MỚI';
    document.getElementById('userForm').reset();
    document.getElementById('user_id').value = '';
    document.getElementById('oldPassGroup').style.display = 'none'; // Ẩn mật khẩu cũ khi thêm mới
    document.getElementById('pwdHint').style.display = 'none';
    document.getElementById('mat_khau').required = true;
    document.getElementById('userModal').style.display = 'block';
}

function editUser(item) {
    document.getElementById('modalTitle').innerText = 'CẬP NHẬT TÀI KHOẢN';
    document.getElementById('user_id').value = item.id;
    document.getElementById('ten_dang_nhap').value = item.ten_dang_nhap;
    document.getElementById('ho_ten').value = item.ho_ten;
    document.getElementById('vai_tro').value = item.vai_tro;
    document.getElementById('trang_thai').value = item.trang_thai;
    
    // Hiển thị và điền mật khẩu cũ
    document.getElementById('oldPassGroup').style.display = 'block';
    document.getElementById('old_password').value = item.mat_khau;

    document.getElementById('mat_khau').value = '';
    document.getElementById('mat_khau').required = false;
    document.getElementById('pwdHint').style.display = 'block';
    document.getElementById('userModal').style.display = 'block';
}

function closeUserModal() {
    document.getElementById('userModal').style.display = 'none';
}

document.getElementById('userForm').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/hi/app/api/userapi.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            alert(data.message);
            closeUserModal();
            loadUsers();
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => alert('Lỗi kết nối API!'));
};

function deleteUser(id) {
    if (confirm('CẢNH BÁO: Bạn có muốn XÓA VĨNH VIỄN tài khoản này không? Thao tác này không thể hoàn tác!')) {
        fetch(`/hi/app/api/userapi.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                alert(data.message);
                loadUsers();
            } else {
                alert('Lỗi: ' + data.message);
            }
        });
    }
}

window.onclick = function(event) {
    let modal = document.getElementById('userModal');
    if (event.target == modal) modal.style.display = "none";
}
