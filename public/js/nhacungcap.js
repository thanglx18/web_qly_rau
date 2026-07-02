document.getElementById('nccForm').onsubmit = function(e) {
    e.preventDefault();
    fetch('/hi/app/api/NhaCungCapAPI.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') location.reload();
    });
};

// Hàm mở Modal để thêm mới
function openModal() {
    document.getElementById('nccForm').reset();
    document.getElementById('nccId').value = '';
    document.getElementById('modalTitle').innerText = 'Thêm nhà cung cấp';
    document.getElementById('nccModal').style.display = 'block';
}

// Hàm đóng Modal
function closeModal() {
    document.getElementById('nccModal').style.display = 'none';
}

// Hàm xử lý Lưu (Thêm hoặc Sửa)
document.getElementById('nccForm').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('/hi/app/api/NhaCungCapAPI.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            alert('Thao tác thành công!');
            location.reload(); // Tải lại trang để cập nhật bảng
        } else {
            alert('Có lỗi xảy ra, vui lòng thử lại.');
        }
    });
};

// Hàm đổ dữ liệu vào Modal để Sửa
function editNCC(id, ten, sdt, diachi) {
    document.getElementById('modalTitle').innerText = 'Chỉnh sửa nhà cung cấp';
    document.getElementById('nccId').value = id;
    document.getElementById('ten_nha_cc').value = ten;
    document.getElementById('so_dien_thoai').value = sdt !== 'N/A' ? sdt : '';
    document.getElementById('dia_chi').value = diachi !== 'N/A' ? diachi : '';
    document.getElementById('nccModal').style.display = 'block';
}

// Hàm xử lý Xóa
function deleteNCC(id) {
    if(confirm('Bạn có chắc chắn muốn xóa nhà cung cấp này không?')) {
        fetch(`/hi/app/api/NhaCungCapAPI.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                location.reload();
            }
        });
    }
}