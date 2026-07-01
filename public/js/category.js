// 1. Hiển thị Modal
function openAddCategoryModal() {
    const modal = document.getElementById('categoryModal');
    if (modal) {
        document.getElementById('modalTitle').innerText = "THÊM DANH MỤC MỚI";
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = '';
        modal.style.display = 'block';
    }
}

function closeCategoryModal() {
    document.getElementById('categoryModal').style.display = 'none';
}

function editCategory(id, ten, mo_ta) {
    document.getElementById('modalTitle').innerText = "CẬP NHẬT DANH MỤC";
    document.getElementById('categoryId').value = id;
    document.getElementById('ten_danh_muc').value = ten;
    document.getElementById('mo_ta').value = mo_ta;
    document.getElementById('categoryModal').style.display = 'block';
}

// 2. Xử lý Gửi dữ liệu qua REST API (Thêm/Sửa)
// Đợi DOM load xong mới gán sự kiện
document.addEventListener('DOMContentLoaded', function() {
    const categoryForm = document.getElementById('categoryForm');
    if (categoryForm) {
        categoryForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            // Đổi đường dẫn trỏ trực tiếp vào CategoryAPI.php
            fetch('/hi/app/api/CategoryAPI.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    alert('Lưu thành công!');
                    location.reload();
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể lưu dữ liệu'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra trong quá trình kết nối API');
            });
        };
    }
});

// 3. Xử lý Xóa qua REST API
function deleteCategory(id) {
    if (confirm('Bạn có chắc chắn muốn xóa danh mục này?')) {
        // Gọi DELETE tới CategoryAPI.php kèm tham số ID
        fetch(`/hi/app/api/CategoryAPI.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                alert('Xóa thành công!');
                location.reload();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể xóa'));
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

// Đóng modal khi click ra ngoài
window.onclick = function(event) {
    let modal = document.getElementById('categoryModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}