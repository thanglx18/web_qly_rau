let myChart = null;

// ==========================================
// LOAD 4 SỐ THỐNG KÊ, BIỂU ĐỒ VÀ BẢNG PHỤ
// ==========================================
async function loadDashboardStats() {
    try {
        const response = await fetch('/hi/app/api/thongkeapi.php'); 
        const result = await response.json();
        
        if (result && result.status === 'success') {
            document.getElementById('stat-doanh-thu').innerText = result.tong_doanh_thu;
            document.getElementById('stat-don-hang').innerText = result.tong_don_hang;
            document.getElementById('stat-san-pham').innerText = result.tong_san_pham;
            document.getElementById('stat-khach-hang').innerText = result.tong_khach_hang;

            if (result.chart) {
                updateChart(result.chart);
            }

            // Render danh sách sắp hết hàng
            if (result.san_pham_sap_het !== undefined) {
                renderLowStock(result.san_pham_sap_het);
            }

            // Render Top 10 sản phẩm bán chạy nhất
            if (result.top_san_pham_ban_chay) {
                renderTopProducts(result.top_san_pham_ban_chay);
            }
        } else if (result.error) {
            console.error('Lỗi từ API:', result.error);
        }
    } catch (error) {
        console.error('Lỗi gọi API Thống kê:', error);
    }
}

function renderLowStock(items) {
    const section = document.getElementById('low-stock-section');
    const body    = document.getElementById('low-stock-body');
    const counter = document.getElementById('low-stock-count');
    if (!section || !body) return;

    if (!items || items.length === 0) {
        section.style.display = 'none';
        return;
    }

    section.style.display = 'block';
    counter.innerText = items.length + ' sản phẩm';

    body.innerHTML = '';
    items.forEach(item => {
        const qty     = parseFloat(item.so_luong_kho);
        const unit    = item.don_vi_tinh || '';
        const isEmpty = qty <= 0;

        const statusHtml = isEmpty
            ? `<span style="background:rgba(231,76,60,0.15);color:#ff6b6b;border:1px solid rgba(231,76,60,0.3);padding:2px 10px;border-radius:20px;font-size:0.72rem;font-weight:700;">Hết hàng</span>`
            : `<span style="background:rgba(255,193,7,0.12);color:#ffc107;border:1px solid rgba(255,193,7,0.3);padding:2px 10px;border-radius:20px;font-size:0.72rem;font-weight:700;">Sắp hết</span>`;

        const qtyColor = isEmpty ? '#ff6b6b' : '#ffc107';

        body.innerHTML += `
            <tr>
                <td style="font-weight:600;">${item.ten_san_pham}</td>
                <td style="text-align:center;font-weight:800;color:${qtyColor};">${qty} ${unit}</td>
                <td style="text-align:center;">${statusHtml}</td>
            </tr>
        `;
    });
}

function renderTopProducts(items) {
    const tableBody = document.getElementById('top-product-body');
    if (!tableBody) return;

    tableBody.innerHTML = '';
    if (!items || items.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="2" style="text-align:center;">Chưa có dữ liệu bán hàng</td></tr>';
        return;
    }

    items.forEach(sp => {
        tableBody.innerHTML += `
            <tr>
                <td style="font-weight:600;">${sp.ten_san_pham}</td>
                <td style="text-align: right; font-weight:700; color:#52DDB5;">
                    ${parseFloat(sp.tong_ban)} ${sp.don_vi_tinh}
                </td>
            </tr>
        `;
    });
}

function updateChart(chartData) {
    const ctx = document.getElementById('chart');
    if (!ctx) return;

    if (myChart) {
        myChart.destroy();
    }

    myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: chartData.values,
                borderColor: '#52DDB5',
                backgroundColor: 'rgba(82, 221, 181, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#52DDB5',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' ₫';
                        }
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' }, 
                    ticks: { 
                        color: '#A3BFB0',
                        callback: function(value) {
                            if (value >= 1000000) return (value / 1000000) + ' Tr';
                            if (value >= 1000) return (value / 1000) + ' K';
                            return value;
                        }
                    } 
                },
                x: { grid: { display: false }, ticks: { color: '#A3BFB0' } }
            }
        }
    });
}

// CHẠY KHI TRANG SẴN SÀNG
document.addEventListener('DOMContentLoaded', () => {
    loadDashboardStats();
});
