document.addEventListener('DOMContentLoaded', loadCustomers);

function loadCustomers() {
    fetch('/hi/app/api/customerapi.php')
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('customer-list');
        tbody.innerHTML = '';
        
        if (data.status === 'success' && data.data.length > 0) {
            data.data.forEach(item => {
                tbody.innerHTML += `
                    <tr>
                        <td>${item.id}</td>
                        <td class="customer-name">${item.ho_ten}</td>
                        <td>${item.so_dien_thoai || '---'}</td>
                        <td>${item.email || '---'}</td>
                        <td><small>${item.dia_chi || '---'}</small></td>
                        <td><span class="points-badge">${item.diem_tich_luy}</span></td>
                        <td style="text-align: center;">
                            <button class="btn-action btn-edit" title="Sửa" onclick="editCustomer(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action btn-delete" title="Xóa" onclick="deleteCustomer(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Chưa có khách hàng nào.</td></tr>';
        }
    });
}

function openAddCustomerModal() {
    document.getElementById('modalTitle').innerText = 'THÊM KHÁCH HÀNG MỚI';
    document.getElementById('customerForm').reset();
    document.getElementById('cust_id').value = '';
    document.getElementById('customerModal').style.display = 'block';
}

function editCustomer(item) {
    document.getElementById('modalTitle').innerText = 'CẬP NHẬT THÔNG TIN';
    document.getElementById('cust_id').value = item.id;
    document.getElementById('ho_ten').value = item.ho_ten;
    document.getElementById('so_dien_thoai').value = item.so_dien_thoai;
    document.getElementById('email').value = item.email || '';
    document.getElementById('dia_chi').value = item.dia_chi || '';
    document.getElementById('diem_tich_luy').value = item.diem_tich_luy;
    document.getElementById('customerModal').style.display = 'block';
}

function closeCustomerModal() {
    document.getElementById('customerModal').style.display = 'none';
}

document.getElementById('customerForm').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/hi/app/api/customerapi.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            alert(data.message);
            closeCustomerModal();
            loadCustomers();
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => alert('Lỗi kết nối API!'));
};

function deleteCustomer(id) {
    if (confirm('Bạn có chắc chắn muốn xóa khách hàng này?')) {
        fetch(`/hi/app/api/customerapi.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                alert(data.message);
                loadCustomers();
            } else {
                alert('Lỗi: ' + data.message);
            }
        });
    }
}

window.onclick = function(event) {
    let modal = document.getElementById('customerModal');
    if (event.target == modal) modal.style.display = "none";
}
