let allProducts = [];

document.addEventListener('DOMContentLoaded', async function() {
    await loadProducts();
    loadPromotions();
});

// 0. Tải danh sách Sản phẩm để gắn vào Khuyến mãi
function loadProducts() {
    return fetch('/hi/public/index.php?url=api/product')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            allProducts = data.data;
            const container = document.getElementById('product_checkboxes');
            if (container) {
                container.innerHTML = '';
                allProducts.forEach(sp => {
                    container.innerHTML += `
                        <label class="promo-cb-item" style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 5px; border-radius: 4px; transition: background 0.2s;">
                            <input type="checkbox" value="${sp.id}" class="promo-product-cb" style="width: 18px !important; height: 18px !important; margin: 0 !important; cursor: pointer; accent-color: #ff6b6b; box-shadow: none !important;" onchange="updateSelectedCount()">
                            <span style="color: #fff; font-size: 0.95rem;">${sp.ten_san_pham} <span style="color: #a3bfbc; font-size: 0.85rem;">(ID: ${sp.id})</span></span>
                        </label>
                    `;
                });
            }
        }
    })
    .catch(error => console.error('Lỗi load sản phẩm:', error));
}

// Hàm cập nhật số lượng đã chọn
function updateSelectedCount() {
    const checkedBoxes = document.querySelectorAll('.promo-product-cb:checked:not([style*="display: none"])');
    const label = document.getElementById('selected_count');
    if (label) label.innerText = `Đã chọn: ${checkedBoxes.length}`;
    
    // Kiểm tra xem chọn tất cả có nên tự tick không
    const allVisible = document.querySelectorAll('.promo-cb-item:not([style*="display: none"]) .promo-product-cb');
    const checkAllBox = document.getElementById('check_all_products');
    if (checkAllBox && allVisible.length > 0) {
        const allChecked = Array.from(allVisible).every(cb => cb.checked);
        checkAllBox.checked = allChecked;
    }
}

// Hàm Tìm kiếm sản phẩm trong danh sách
function filterPromoProducts() {
    const term = document.getElementById('search_promo_product').value.toLowerCase().trim();
    const items = document.querySelectorAll('.promo-cb-item');
    
    items.forEach(item => {
        const text = item.innerText.toLowerCase();
        if (text.includes(term)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
    updateSelectedCount();
}

// Hàm Chọn tất cả
function toggleAllProducts(masterCheckbox) {
    const isChecked = masterCheckbox.checked;
    const items = document.querySelectorAll('.promo-cb-item:not([style*="display: none"]) .promo-product-cb');
    
    items.forEach(cb => {
        cb.checked = isChecked;
    });
    updateSelectedCount();
}

// 1. Tải danh sách từ Server
function loadPromotions() {
    fetch('/hi/app/api/khuyenmaiapi.php')
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('promo-list');
        tbody.innerHTML = '';
        
        if (data.status === 'success' && data.data.length > 0) {
            data.data.forEach(item => {
                // Hiển thị nhiều sản phẩm
                let productNames = 'Áp dụng: Toàn bộ';
                if (item.danh_sach_san_pham && item.danh_sach_san_pham !== '') {
                    const ids = item.danh_sach_san_pham.split(',');
                    const names = ids.map(id => {
                        const sp = allProducts.find(p => p.id == id);
                        return sp ? sp.ten_san_pham : `ID:${id}`;
                    });
                    productNames = names.join(', ');
                }

                tbody.innerHTML += `
                    <tr>
                        <td>${item.id}</td>
                        <td><strong style="color: #ff6b6b;">${item.ma_code}</strong></td>
                        <td style="color: var(--primary); font-weight: 500; font-size: 0.9em; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${productNames}">${productNames}</td>
                        <td><span class="category-badge" style="background: rgba(255, 107, 107, 0.1); color: #ff6b6b; border: 1px solid #ff6b6b;">${item.phan_tram_giam}%</span></td>
                        <td>${item.ngay_bat_dau}</td>
                        <td>${item.ngay_ket_thuc}</td>
                        <td style="text-align: center;">
                            <button class="btn-action btn-edit" onclick="editPromo(${JSON.stringify(item).replace(/"/g, '&quot;')})" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action btn-delete" onclick="deletePromo(${item.id})" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Chưa có chương trình khuyến mãi nào.</td></tr>';
        }
    })
    .catch(error => console.error('Lỗi load data:', error));
}

// 2. Điều khiển Modal
function openAddPromoModal() {
    document.getElementById('modalTitle').innerText = "TẠO KHUYẾN MÃI MỚI";
    document.getElementById('promoForm').reset();
    document.getElementById('promoId').value = '';
    
    // Bỏ chọn tất cả checkbox và reset search
    const searchInput = document.getElementById('search_promo_product');
    if(searchInput) {
        searchInput.value = '';
        filterPromoProducts();
    }
    document.querySelectorAll('.promo-product-cb').forEach(cb => cb.checked = false);
    updateSelectedCount();
    
    document.getElementById('promoModal').style.display = 'block';
}

function closePromoModal() {
    document.getElementById('promoModal').style.display = 'none';
}

function editPromo(item) {
    document.getElementById('modalTitle').innerText = "CẬP NHẬT CHƯƠNG TRÌNH";
    document.getElementById('promoId').value = item.id;
    
    // Reset search
    const searchInput = document.getElementById('search_promo_product');
    if(searchInput) {
        searchInput.value = '';
        filterPromoProducts();
    }

    // Tick vào các checkbox tương ứng
    document.querySelectorAll('.promo-product-cb').forEach(cb => cb.checked = false);
    if (item.danh_sach_san_pham && item.danh_sach_san_pham !== '') {
        const ids = item.danh_sach_san_pham.split(',');
        ids.forEach(id => {
            const cb = document.querySelector(`.promo-product-cb[value="${id}"]`);
            if (cb) cb.checked = true;
        });
    }
    updateSelectedCount();

    document.getElementById('ma_code').value = item.ma_code;
    document.getElementById('phan_tram_giam').value = item.phan_tram_giam;
    document.getElementById('ngay_bat_dau').value = item.ngay_bat_dau;
    document.getElementById('ngay_ket_thuc').value = item.ngay_ket_thuc;
    document.getElementById('promoModal').style.display = 'block';
}

// 3. Xử lý Lưu (Thêm/Sửa)
document.getElementById('promoForm').onsubmit = function(e) {
    e.preventDefault();
    
    // Gom danh sách ID đã chọn
    const checked = Array.from(document.querySelectorAll('.promo-product-cb:checked')).map(cb => cb.value);
    if (checked.length === 0) {
        alert("Vui lòng chọn ít nhất một sản phẩm để áp dụng!");
        return;
    }
    
    document.getElementById('danh_sach_san_pham').value = checked.join(',');

    const formData = new FormData(this);
    
    fetch('/hi/app/api/khuyenmaiapi.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            alert(data.message || 'Lưu dữ liệu thành công!');
            closePromoModal();
            loadPromotions();
        } else {
            alert('Lỗi: ' + (data.message || 'Không thể lưu dữ liệu'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra: ' + error.message + '\nVui lòng kiểm tra Console (F12) để biết chi tiết.');
    });
};

// 4. Xử lý Xóa
function deletePromo(id) {
    if (confirm('Bạn có chắc chắn muốn xóa chương trình này?')) {
        fetch(`/hi/app/api/khuyenmaiapi.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                alert('Xóa thành công!');
                loadPromotions();
            } else {
                alert('Lỗi: ' + data.message);
            }
        });
    }
}

// Đóng modal khi click ra ngoài
window.onclick = function(event) {
    let modal = document.getElementById('promoModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
