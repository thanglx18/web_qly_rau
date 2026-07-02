document.addEventListener('DOMContentLoaded', loadOrders);

function loadOrders() {
    fetch('/hi/app/api/orderapi.php')
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('order-list');
        tbody.innerHTML = '';
        
        if (data.status === 'success' && data.data.length > 0) {
            data.data.forEach(item => {
                const statusClass = `status-${item.trang_thai}`;
                const statusText = getStatusText(item.trang_thai);
                
                tbody.innerHTML += `
                    <tr>
                        <td class="order-id">#${item.id}</td>
                        <td>${item.ngay_tao}</td>
                        <td>${item.ten_khach_hang || 'Khách lẻ'}</td>
                        <td><small>${item.phuong_thuc_tt === 'tien_mat' ? 'Tiền mặt' : 'Chuyển khoản'}</small></td>
                        <td class="price-text">${new Intl.NumberFormat('vi-VN').format(item.tong_thanh_toan)}₫</td>
                        <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                        <td style="text-align: center;">
                            <button class="btn-action btn-view" title="Xem chi tiết" onclick="viewOrderDetails(${item.id})" style="background: var(--color-mint-dark); color: #fff; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Chưa có đơn hàng nào.</td></tr>';
        }
    });
}

function printOrder(id) {
    // Để in nhanh, ta sẽ mở modal rồi gọi in
    viewOrderDetails(id);
    setTimeout(() => {
        printCurrentOrder();
    }, 1000);
}

function printCurrentOrder() {
    window.print();
}

function getStatusText(status) {
    const map = {
        'xong': 'Hoàn tất',
        'huy': 'Đã hủy',
        'cho_xu_ly': 'Chờ xử lý',
        'dang_giao': 'Đang giao'
    };
    return map[status] || status;
}

function updateOrderStatus(id, newStatus) {
    if (!confirm(`Xác nhận đổi trạng thái đơn hàng #${id} sang "${getStatusText(newStatus)}"?`)) return;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('trang_thai', newStatus);

    fetch('/hi/app/api/orderapi.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            alert('Cập nhật thành công!');
            loadOrders();
        } else {
            alert('Lỗi: ' + data.message);
        }
    });
}

function viewOrderDetails(id) {
    document.getElementById('detailOrderId').innerText = id;
    fetch(`/hi/app/api/orderapi.php?id_details=${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const order = data.order;
            const details = data.details;
            
            // Điền thông tin chung
            document.getElementById('detailOrderDate').innerText = order.ngay_tao;
            document.getElementById('detailCustomerName').innerText = order.ten_khach_hang || 'Khách lẻ';
            document.getElementById('detailCustomerPhone').innerText = order.so_dien_thoai || '---';
            document.getElementById('detailCustomerAddress').innerText = order.dia_chi || '---';
            
            const statusBadge = document.getElementById('detailOrderStatus');
            statusBadge.innerText = getStatusText(order.trang_thai);
            statusBadge.className = `status-badge status-${order.trang_thai}`;
            
            document.getElementById('detailPaymentMethod').innerText = (order.phuong_thuc_tt === 'tien_mat' || order.phuong_thuc_tt === 'cash') ? 'Tiền mặt' : 'Chuyển khoản';
            
            // Điền danh sách sản phẩm
            const tbody = document.getElementById('order-details-body');
            tbody.innerHTML = '';
            details.forEach(detail => {
                tbody.innerHTML += `
                    <tr>
                        <td>${detail.ten_san_pham}</td>
                        <td style="text-align: center;">${detail.so_luong}</td>
                        <td style="text-align: right;">${new Intl.NumberFormat('vi-VN').format(detail.gia_luc_ban)}₫</td>
                        <td style="text-align: right;"><strong>${new Intl.NumberFormat('vi-VN').format(detail.so_luong * detail.gia_luc_ban)}₫</strong></td>
                    </tr>
                `;
            });
            
            // Điền tổng tiền
            document.getElementById('detailSubtotal').innerText = new Intl.NumberFormat('vi-VN').format(order.tong_tien_hang);
            document.getElementById('detailDiscount').innerText = new Intl.NumberFormat('vi-VN').format(order.so_tien_giam || 0);
            document.getElementById('detailTotal').innerText = new Intl.NumberFormat('vi-VN').format(order.tong_thanh_toan);
            
            document.getElementById('orderDetailModal').style.display = 'block';
        } else {
            alert('Lỗi: ' + data.message);
        }
    });
}

function closeDetailModal() {
    document.getElementById('orderDetailModal').style.display = 'none';
}

window.onclick = function(event) {
    let modal = document.getElementById('orderDetailModal');
    if (event.target == modal) modal.style.display = "none";
}
