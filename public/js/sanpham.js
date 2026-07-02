const API_URL = 'http://localhost/hi/public/index.php?url=api/product';
let currentProducts = [];

// --- 1. HÀM RENDER DÒNG SẢN PHẨM (REUSABLE) ---
function renderProductRow(sp) {
    const statusClass = (sp.so_luong_kho > 0) ? "status-ok" : "status-low";
    const statusText = (sp.so_luong_kho > 0) ? "✔ Đang bán" : "✖ Hết hàng";
    
    return `
        <tr>
            <td>#${sp.id}</td>
            <td><div class="product-name" style="color: var(--primary); font-weight: 500;">${sp.ten_san_pham}</div></td>
            <td><span class="badge-category">${sp.ten_danh_muc || 'Chưa phân loại'}</span></td>
            <td><span class="badge-supplier">${sp.ten_nha_cc || 'N/A'}</span></td>
            <td><b style="color: #FF6B6B;">${new Intl.NumberFormat().format(sp.gia_ban)}đ</b></td>
            <td>${sp.so_luong_kho}</td>
            <td><span class="badge ${statusClass}">${statusText}</span></td>
            <td>
                <div style="display: flex; justify-content: center; gap: 8px;">
                    <button class="btn-icon btn-edit" onclick="handleEditClick(${sp.id})" title="Sửa">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon btn-delete" onclick="deleteProduct(${sp.id})" title="Xóa">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
}

// --- 2. TẢI TOÀN BỘ SẢN PHẨM TỪ SERVER ---
async function loadAllProducts() {
    try {
        const response = await fetch(API_URL);
        const result = await response.json();
        const tbody = document.getElementById('product-list');
        if (!tbody) return;
        
        currentProducts = result.status === 'success' ? result.data : [];
        displayProducts(currentProducts); // Hiển thị sản phẩm ban đầu
        
    } catch (error) {
        console.error("Lỗi tải sản phẩm:", error);
    }
}

// --- 3. HIỂN THỊ DANH SÁCH RA BẢNG ---
function displayProducts(products) {
    const tbody = document.getElementById('product-list');
    if (!tbody) return;

    if (products.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--text-muted);">Không tìm thấy sản phẩm nào...</td></tr>';
        return;
    }

    tbody.innerHTML = products.map(sp => renderProductRow(sp)).join('');
}

// --- 4. TÌM KIẾM DỰA TRÊN DỮ LIỆU (DATA-DRIVEN SEARCH) ---
function filterProducts() {
    const term = document.getElementById('search-product-mgmt').value.toLowerCase().trim();
    
    if (term === '') {
        displayProducts(currentProducts);
        return;
    }

    const filtered = currentProducts.filter(p => 
        p.ten_san_pham.toLowerCase().includes(term) || 
        p.id.toString().includes(term.replace('#', '')) ||
        (p.ten_danh_muc && p.ten_danh_muc.toLowerCase().includes(term))
    );

    displayProducts(filtered);
}

// --- 5. QUẢN LÝ MODAL & FORM ---
function handleEditClick(id) {
    const sp = currentProducts.find(p => p.id == id);
    if (sp) {
        document.getElementById('modalTitle').innerText = "CHỈNH SỬA SẢN PHẨM";
        document.getElementById('btnSubmit').innerText = "Cập nhật sản phẩm";
        
        document.getElementById('productId').value = sp.id;
        document.getElementById('ten').value = sp.ten_san_pham;
        document.getElementById('gia').value = sp.gia_ban;
        document.getElementById('kho').value = sp.so_luong_kho;
        document.getElementById('id_danh_muc').value = sp.id_danh_muc || '';
        document.getElementById('id_nha_cc').value = sp.id_nha_cc || '';
        document.getElementById('don_vi').value = sp.don_vi_tinh || '';

        const imgInfo = document.getElementById('current-image-info');
        if (imgInfo) {
            if (sp.anh_san_pham) {
                imgInfo.innerHTML = `<img src="/hi/public/img/${sp.anh_san_pham}" style="max-height:80px; border-radius:6px; margin-top:5px;"> <span style="color:#9ca3af; margin-left:8px;">${sp.anh_san_pham}</span>`;
            } else {
                imgInfo.innerHTML = '<span style="color:#9ca3af;">Chưa có ảnh</span>';
            }
            imgInfo.style.display = 'block';
        }
        
        document.getElementById('productModal').style.display = 'block';
    }
}

document.getElementById('productForm').onsubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
        const response = await fetch(API_URL, {
            method: 'POST', 
            body: formData
        });
        
        const result = await response.json();
        if(result.status === 'success') {
            closeModal();
            loadAllProducts(); // Tải lại danh sách sau khi lưu
        } else {
            alert('Lỗi: ' + result.message);
        }
    } catch (error) {
        alert('Lỗi kết nối server!');
    }
};

function closeModal() {
    const modal = document.getElementById('productModal');
    if (modal) modal.style.display = 'none';
}

function openAddModal() {
    const modal = document.getElementById('productModal');
    const form = document.getElementById('productForm');
    
    if (modal && form) {
        document.getElementById('modalTitle').innerText = "THÊM SẢN PHẨM MỚI";
        document.getElementById('btnSubmit').innerText = "Lưu dữ liệu";
        form.reset(); 
        document.getElementById('productId').value = ''; 
        const imgInfo = document.getElementById('current-image-info');
        if (imgInfo) {
            imgInfo.style.display = 'none';
            imgInfo.innerHTML = '';
        }
        modal.style.display = 'block';
    }
}

async function deleteProduct(id) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
        try {
            const response = await fetch(`${API_URL}&id=${id}`, { method: 'DELETE' });
            const result = await response.json();
            loadAllProducts();
        } catch (error) {
            alert('Lỗi khi xóa!');
        }
    }
}

// --- 6. KHỞI CHẠY ---
document.addEventListener('DOMContentLoaded', () => {
    loadAllProducts();

    const searchInput = document.getElementById('search-product-mgmt');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            filterProducts();
        });
    }

    const fileInput = document.getElementById('hinh_anh');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const imgInfo = document.getElementById('current-image-info');
            if (this.files && this.files[0] && imgInfo) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgInfo.innerHTML = `<img src="${e.target.result}" style="max-height:80px; border-radius:6px; margin-top:5px;"> <span style="color:var(--text-muted); margin-left:8px;">${fileInput.files[0].name}</span>`;
                    imgInfo.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});

window.onclick = (event) => {
    const modal = document.getElementById('productModal');
    if (event.target == modal) closeModal();
}