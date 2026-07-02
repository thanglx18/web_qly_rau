async function loadLowStock() {
    try {
        const response = await fetch('/hi/app/api/inventoryapi.php?action=low_stock');
        const result = await response.json();
        const container = document.getElementById('low-stock-alert-container');
        const list = document.getElementById('low-stock-list');
        
        if (result.status === 'success' && result.data.length > 0) {
            container.style.display = 'block';
            list.innerHTML = '';
            result.data.forEach(item => {
                const isOutOfStock = item.so_luong_kho <= 0;
                list.innerHTML += `
                    <div style="background: rgba(0,0,0,0.2); padding: 12px; border-radius: 8px; border-left: 4px solid #e74c3c;">
                        <div style="font-weight: 700; color: #fff; margin-bottom: 5px;">${item.ten_san_pham}</div>
                        <div style="font-size: 13px; color: #ff6b6b;">
                            Số lượng: <span style="font-weight: 800;">${item.so_luong_kho}</span> ${item.don_vi_tinh}
                            <span class="badge-xuat" style="margin-left:5px; font-size:10px;">${isOutOfStock ? 'Hết hàng' : 'Sắp hết'}</span>
                        </div>
                    </div>
                `;
            });
        } else {
            container.style.display = 'none';
        }
    } catch (e) {
        console.error('Lỗi tải cảnh báo:', e);
    }
}

async function loadProducts() {
    try {
        const response = await fetch('/hi/app/api/SanPhamController.php');
        const result = await response.json();
        if (result.status === 'success') {
            const select = document.getElementById('productSelect');
            result.data.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = `${p.ten} (Tồn: ${p.kho} ${p.don_vi})`;
                select.appendChild(opt);
            });
        }
    } catch (e) {
        console.error('Lỗi tải sản phẩm:', e);
    }
}

async function loadHistory() {
    try {
        const response = await fetch('/hi/app/api/inventoryapi.php');
        const result = await response.json();
        const body = document.getElementById('inventory-body');
        body.innerHTML = '';

        if (result.data.length === 0) {
            body.innerHTML = '<tr><td colspan="5" style="text-align: center;">Chưa có biến động kho nào.</td></tr>';
            return;
        }

        result.data.forEach(item => {
            const badgeClass = item.loai === 'nhap' ? 'badge-nhap' : 'badge-xuat';
            const badgeText = item.loai === 'nhap' ? 'Nhập' : 'Xuất';
            body.innerHTML += `
                <tr>
                    <td style="color: #A3BFB0; font-size: 13px;">${item.ngay_tao}</td>
                    <td style="font-weight: 600;">${item.ten_san_pham}</td>
                    <td><span class="${badgeClass}">${badgeText}</span></td>
                    <td style="font-weight: 700;">${item.loai === 'nhap' ? '+' : '-'}${item.so_luong} ${item.don_vi}</td>
                    <td style="font-size: 13px; color: #A3BFB0;">${item.ly_do}</td>
                </tr>
            `;
        });
    } catch (e) {
        console.error('Lỗi tải lịch sử:', e);
    }
}

function openAddStockModal() {
    document.getElementById('stockModal').style.display = 'flex';
}

function closeStockModal() {
    document.getElementById('stockModal').style.display = 'none';
    document.getElementById('stockForm').reset();
}

document.getElementById('stockForm').onsubmit = async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('/hi/app/api/inventoryapi.php', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: { 'Content-Type': 'application/json' }
        });
        const result = await response.json();

        if (result.status === 'success') {
            alert(result.message);
            closeStockModal();
            loadHistory();
            location.reload(); // Reload để cập nhật tồn kho trong select
        } else {
            alert('Lỗi: ' + result.message);
        }
    } catch (e) {
        alert('Lỗi kết nối máy chủ!');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
    loadHistory();
    loadLowStock();
});
