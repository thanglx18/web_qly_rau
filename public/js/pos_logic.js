let cart = [];
let currentPromo = null; // Lưu trữ promotion đang được áp dụng

// --- HỆ THỐNG THÔNG BÁO (TOAST) ---
// Tác dụng: Hiển thị các thông báo nhỏ góc màn hình (như: "Đã thêm hàng", "Hết hàng")
function showToast(message, type = 'error') {
    let toast = document.getElementById('pos-toast');
    // Nếu chưa có thẻ thông báo trên giao diện thì tạo mới
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'pos-toast';
        toast.style.position = 'fixed';
        toast.style.bottom = '20px';
        toast.style.right = '20px';
        toast.style.padding = '15px 25px';
        toast.style.borderRadius = '8px';
        toast.style.color = '#fff';
        toast.style.fontWeight = 'bold';
        toast.style.zIndex = '9999';
        toast.style.boxShadow = '0 10px 20px rgba(0,0,0,0.3)';
        toast.style.transition = 'all 0.3s ease';
        document.body.appendChild(toast);
    }
    
    // Màu đỏ cho lỗi, màu xanh cho thành công
    toast.style.background = type === 'error' ? '#ff4b2b' : '#38ada9';
    toast.innerText = message;
    toast.style.opacity = '1'; // Hiện lên
    toast.style.transform = 'translateY(0)';
    toast.style.pointerEvents = 'auto'; 
    
    // Tự động ẩn sau 3 giây
    setTimeout(() => {
        toast.style.opacity = '0'; // Mờ đi
        toast.style.transform = 'translateY(20px)';
        toast.style.pointerEvents = 'none'; 
    }, 3000);
}

// 1. Thêm sản phẩm vào giỏ hàng
// Tác dụng: Khi bấm vào sản phẩm, nó sẽ vào mảng 'cart'. Kiểm tra tồn kho trước khi thêm.
function addToCart(id, name, price, stock) {
    // Nếu kho hết hàng thì không cho thêm
    if (stock <= 0) {
        showToast(`Sản phẩm "${name}" đã hết hàng!`, 'error');
        return;
    }

    // Kiểm tra xem rau này đã có trong giỏ chưa
    const existing = cart.find(item => item.id === id);
    
    if (existing) {
        // Nếu có rồi mà tăng thêm 1 vượt quá kho thì báo lỗi
        if (existing.quantity + 1 > stock) {
            showToast(`Sản phẩm "${name}" chỉ còn ${stock} trong kho!`, 'error');
            return;
        }
        existing.quantity += 1; // Tăng số lượng
    } else {
        // Nếu chưa có thì thêm mới một Object vào mảng cart
        cart.push({ id, name, price, quantity: 1, stockLimit: stock });
    }
    renderCart(); // Vẽ lại giao diện giỏ hàng
    showToast(`Đã thêm ${name}`, 'success'); 
}

// 2. Vẽ lại giao diện giỏ hàng (Cập nhật danh sách bên phải màn hình)
// Tác dụng: Đồng bộ dữ liệu từ mảng 'cart' lên màn hình HTML.
function renderCart() {
    const body = document.getElementById('cart-body');
    if (!body) return;
    
    body.innerHTML = ''; // Xóa sạch danh sách cũ để vẽ mới
    let subtotal = 0;

    // Duyệt qua từng món rau trong giỏ hàng
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal; // Cộng dồn tổng tiền hàng
        // Tạo HTML cho từng dòng sản phẩm
        body.innerHTML += `
            <tr>
                <td>
                    <div style="font-weight: 600; font-size: 0.9rem;">${item.name}</div>
                    <div style="font-size: 0.75rem; color: var(--pos-text-muted);">${item.price.toLocaleString()}đ</div>
                </td>
                <td>
                    <input type="number" value="${item.quantity}" min="1" 
                           onchange="updateQty(${index}, this.value)" 
                           class="cart-qty-input">
                </td>
                <td style="text-align: right; font-weight: 700;">${itemTotal.toLocaleString()}đ</td>
                <td style="text-align: right;">
                    <i class="fas fa-times-circle" onclick="removeItem(${index})" 
                       style="color:#ff6b6b; cursor:pointer; font-size: 1.1rem;"></i>
                </td>
            </tr>
        `;
    });

    const itemsCountEl = document.getElementById('items-count');
    const subtotalEl = document.getElementById('subtotal');
    
    // Cập nhật số lượng và tổng tiền tạm tính lên UI
    if (itemsCountEl) itemsCountEl.innerText = `${cart.length} mặt hàng`;
    if (subtotalEl) subtotalEl.innerText = subtotal.toLocaleString() + 'đ';
    
    recalculateDiscount(); // Gọi hàm tính giảm giá tự động
}

// Hàm cập nhật số lượng khi người dùng gõ số vào ô input trong giỏ hàng
function updateQty(index, val) {
    const newQty = parseInt(val) || 1; // Làm sạch dữ liệu đầu vào
    const item = cart[index];
    
    // Kiểm tra không cho nhập quá số lượng trong kho
    if (newQty > item.stockLimit) {
        showToast(`Sản phẩm "${item.name}" chỉ còn ${item.stockLimit} trong kho!`, 'error');
        item.quantity = item.stockLimit;
    } else if (newQty < 1) {
        item.quantity = 1; // Không cho nhập số âm hoặc 0
    } else {
        item.quantity = newQty;
    }
    renderCart(); // Cập nhật lại UI và Tiền
}

// Hàm xóa sản phẩm khỏi giỏ hàng
function removeItem(index) {
    cart.splice(index, 1); // Xóa phần tử tại vị trí index
    renderCart(); // Vẽ lại giỏ hàng
}

// Hàm tính toán con số Tổng Thanh Toán cuối cùng
function updateTotal() {
    // Tính tổng tiền hàng bằng hàm reduce
    let subtotal = cart.reduce((s, i) => s + (i.price * i.quantity), 0);
    const discountEl = document.getElementById('discount');
    const finalTotalEl = document.getElementById('final-total');
    
    let discount = 0;
    if (discountEl) {
        discount = parseInt(discountEl.value) || 0; // Lấy tiền giảm giá từ ô ẩn
    }
    
    let final = subtotal - discount; // Tổng cuối = Tiền hàng - Giảm giá
    if (finalTotalEl) {
        // Hiển thị kết quả lên màn hình, đảm bảo không bị số âm
        finalTotalEl.innerText = (final > 0 ? final : 0).toLocaleString() + 'đ';
    }
}

// --- XỬ LÝ MÃ KHUYẾN MÃI TỰ ĐỘNG ---
let activePromotions = [];

// Hàm tải các chương trình khuyến mãi đang chạy từ Server
async function loadActivePromotions() {
    try {
        const res = await fetch('/hi/app/api/khuyenmaiapi.php');
        const data = await res.json();
        if (data.status === 'success') {
            const today = new Date().toISOString().split('T')[0];   
            // Chỉ lấy các mã khuyến mãi có ngày bắt đầu <= hôm nay và ngày kết thúc >= hôm nay
            activePromotions = data.data.filter(p => p.ngay_bat_dau <= today && p.ngay_ket_thuc >= today);
            
            // Dán nhãn giảm giá lên giao diện
            applyPromotionsToUI();
        }
    } catch (e) {
        console.error("Lỗi tải khuyến mãi tự động:", e);
    }
}

// Hàm hiển thị Badge "Giảm X%" lên các thẻ sản phẩm rau
function applyPromotionsToUI() {
    if (activePromotions.length === 0) return;

    activePromotions.forEach(promo => {
        const phanTram = promo.phan_tram_giam;
        // Chuyển chuỗi ID sản phẩm "1,2,3" thành mảng [1,2,3]
        const ids = promo.danh_sach_san_pham ? promo.danh_sach_san_pham.split(',').map(id => id.trim()) : [];
        
        ids.forEach(id => {
            const card = document.getElementById('product-card-' + id);
            if (card) {
                // Nếu chưa có nhãn giảm giá thì mới thêm vào để tránh trùng lặp
                if (!card.querySelector('.promo-badge')) {
                    const badge = document.createElement('div');
                    badge.className = 'promo-badge';
                    badge.innerHTML = `<i class="fas fa-tag"></i> Giảm ${phanTram}%`;
                    card.style.position = 'relative'; 
                    card.appendChild(badge);
                }
            }
        });
    });
}

// Hàm tính toán tổng số tiền giảm giá (Từ Khuyến mãi và Điểm khách hàng)
function recalculateDiscount() {
    let discountAmount = 0;
    const msgEl = document.getElementById('promo-msg');
    let autoPromoMessages = [];

    // 1. Tính tiền giảm từ Điểm tích lũy của khách (1 điểm = 1000đ)
    let pointDiscount = 0;
    const pointsInput = document.getElementById('points-to-use');
    if (pointsInput && parseInt(pointsInput.value)) {
        pointDiscount = parseInt(pointsInput.value) * 1000;
    }

    // 2. Tính tiền giảm từ các chương trình Khuyến Mãi đang chạy
    let promoDiscount = 0;
    let productsDiscounted = new Set();
    
    if (activePromotions.length > 0) {
        cart.forEach(item => {
            // Duyệt qua từng mã khuyến mãi để xem món rau này có được giảm không
            for (let promo of activePromotions) {
                const phanTram = parseFloat(promo.phan_tram_giam) || 0;
                const validIds = promo.danh_sach_san_pham ? promo.danh_sach_san_pham.split(',').map(id => id.trim()) : [];
                
                if (validIds.includes(item.id.toString())) {
                    // Công thức: Giá x Số lượng x % Giảm
                    promoDiscount += (item.price * item.quantity * (phanTram / 100));
                    productsDiscounted.add(promo.ma_code); 
                    break; 
                }
            }
        });
    }

    // Hiển thị thông báo các mã đã áp dụng
    if (productsDiscounted.size > 0) {
        autoPromoMessages.push(`Tự động áp dụng mã: ${Array.from(productsDiscounted).join(', ')}`);
    }

    // Tổng giảm giá = Giảm từ điểm + Giảm từ KM
    discountAmount = Math.round(pointDiscount + promoDiscount);
    
    // Cập nhật giá trị vào ô 'discount' ẩn
    const discountEl = document.getElementById('discount');
    if (discountEl) {
        discountEl.value = discountAmount;
    }
    
    if (msgEl) {
        msgEl.innerText = autoPromoMessages.join(' | ');
    }
    
    updateTotal(); // Tính lại tổng tiền thanh toán cuối cùng
}

// 3. Lọc sản phẩm theo tên
// Hàm tìm kiếm sản phẩm theo tên (Bộ lọc nhanh tại POS)
function filterProducts() {
    let input = document.getElementById('search-product').value.toLowerCase();
    let cards = document.getElementsByClassName('product-card');
    
    // Duyệt qua tất cả các card rau, cái nào không khớp tên thì ẩn đi
    for (let card of cards) {
        let name = card.querySelector('.p-name').innerText.toLowerCase();
        card.style.display = name.includes(input) ? "flex" : "none";
    }
}

// 4. Lọc sản phẩm theo danh mục
// Hàm lọc sản phẩm theo Danh mục (Ví dụ: Rau lá, Củ quả...)
function filterCategory(category) {
    // Đổi màu nút đang chọn
    const buttons = document.querySelectorAll('.btn-cat');
    buttons.forEach(btn => {
        if (btn.innerText.trim() === category || (category === 'all' && btn.innerText.trim() === 'Tất cả')) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    // Hiện/Ẩn các sản phẩm thuộc danh mục đó
    const cards = document.querySelectorAll('.product-card');
    cards.forEach(card => {
        const cardCat = card.getAttribute('data-category');
        if (category === 'all' || cardCat === category) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

// --- QUẢN LÝ KHÁCH HÀNG ---
let allCustomers = [];
let selectedCustomerId = null;
let selectedCustomerPoints = 0;

// Hàm tải danh sách khách hàng từ Database qua API
async function loadCustomers() {
    try {
        const response = await fetch('/hi/app/api/customerapi.php');
        const result = await response.json();
        if (result.status === 'success') {
            allCustomers = result.data; // Lưu vào mảng để tìm kiếm nhanh
        }
    } catch (e) { console.error("Lỗi tải khách hàng:", e); }
}

// Hàm tìm kiếm khách hàng khi gõ tên hoặc SĐT
function searchCustomer() {
    const input = document.getElementById('customer-search').value.toLowerCase().trim();
    const resultsDiv = document.getElementById('customer-results');
    
    if (input.length < 1) {
        resultsDiv.style.display = 'none';
        return;
    }

    // Tìm trong mảng khách hàng đã load sẵn
    const matches = allCustomers.filter(c => 
        c.ho_ten.toLowerCase().includes(input) || 
        c.so_dien_thoai.includes(input)
    ).slice(0, 5); 

    if (matches.length > 0) {
        // Hiển thị danh sách gợi ý
        resultsDiv.innerHTML = matches.map(c => `
            <div class="search-item" onclick="selectCustomer(${c.id}, '${c.ho_ten}', ${c.diem_tich_luy})">
                <div style="font-weight:600;">${c.ho_ten}</div>
                <div style="font-size:0.75rem; color:#A3BFB0;">${c.so_dien_thoai} - Điểm: ${c.diem_tich_luy}</div>
            </div>
        `).join('');
        resultsDiv.style.display = 'block';
    } else {
        resultsDiv.innerHTML = '<div class="search-item" style="color:#ff6b6b; cursor:default;">Không tìm thấy khách này...</div>';
        resultsDiv.style.display = 'block';
    }
}

// Hàm chọn một khách hàng cho đơn hàng hiện tại
function selectCustomer(id, name, points) {
    selectedCustomerId = id;
    selectedCustomerPoints = points; 
    document.getElementById('customer-search').value = "";
    document.getElementById('customer-results').style.display = 'none';
    
    // Ẩn ô tìm kiếm và Hiện hộp thông tin khách đã chọn
    document.querySelector('.customer-search-box').style.display = 'none';
    const infoBox = document.getElementById('selected-customer-info');
    infoBox.style.display = 'flex';
    document.getElementById('display-cust-name').innerText = name;
    document.getElementById('display-cust-points').innerText = points;
    
    const pointsInput = document.getElementById('points-to-use');
    if (pointsInput) pointsInput.value = '';
}

// Hàm áp dụng điểm tích lũy để giảm giá trực tiếp
function applyPoints() {
    const pointsInput = document.getElementById('points-to-use');
    const discountEl = document.getElementById('discount');
    if (!pointsInput || !discountEl) return;

    let points = parseInt(pointsInput.value) || 0;
    
    // Kiểm tra không dùng quá số điểm khách đang có
    if (points > selectedCustomerPoints) {
        alert("Khách hàng chỉ có " + selectedCustomerPoints + " điểm!");
        points = selectedCustomerPoints;
        pointsInput.value = points;
    }

    if (points < 0) {
        points = 0;
        pointsInput.value = 0;
    }

    // Sau khi nhập điểm, gọi hàm tính lại tiền giảm giá
    recalculateDiscount();
}

// Hàm hủy chọn khách hàng
function clearCustomer() {
    selectedCustomerId = null;
    selectedCustomerPoints = 0;
    // Hiện lại ô tìm kiếm, ẩn hộp thông tin
    document.querySelector('.customer-search-box').style.display = 'flex';
    document.getElementById('selected-customer-info').style.display = 'none';
    document.getElementById('customer-search').focus();
    
    // Xóa điểm đã nhập và tính lại tiền
    const pointsInput = document.getElementById('points-to-use');
    if (pointsInput) pointsInput.value = '';
    recalculateDiscount();
}

// Thêm khách hàng mới nhanh
function openNewCustModal() {
    document.getElementById('newCustModal').style.display = 'flex';
    document.getElementById('new-cust-name').focus();
}

function closeNewCustModal() {
    document.getElementById('newCustModal').style.display = 'none';
    document.getElementById('new-cust-name').value = '';
    document.getElementById('new-cust-phone').value = '';
}

// Hàm lưu khách hàng mới vào Database
async function saveNewCustomer() {
    const name = document.getElementById('new-cust-name').value.trim();
    const phone = document.getElementById('new-cust-phone').value.trim();

    if (!name || !phone) {
        alert("Vui lòng nhập đủ Tên và Số điện thoại!");
        return;
    }

    try {
        // Gửi lệnh POST kèm dữ liệu JSON qua API
        const response = await fetch('/hi/app/api/customerapi.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ho_ten: name, so_dien_thoai: phone })
        });
        const result = await response.json();

        if (result.status === 'success') {
            await loadCustomers(); // Tải lại danh sách mới nhất
            // Tự động chọn luôn khách vừa tạo
            const newCust = allCustomers.find(c => c.so_dien_thoai === phone);
            if (newCust) {
                selectCustomer(newCust.id, newCust.ho_ten, 0);
            }
            closeNewCustModal();
        } else {
            alert("Lỗi: " + result.message);
        }
    } catch (e) {
        alert("Lỗi kết nối máy chủ!");
    }
}

// Gọi load khách hàng và khuyến mãi khi bắt đầu
document.addEventListener('DOMContentLoaded', () => {
    loadCustomers();
    loadActivePromotions();
});

// [QUAN TRỌNG NHẤT] Hàm thực hiện thanh toán đơn hàng
// Tác dụng: Gom dữ liệu giỏ hàng gửi lên Server qua REST API để lưu hóa đơn.
async function processPayment(e) {
    if (e && e.preventDefault) e.preventDefault();
    
    // 1. Kiểm tra giỏ hàng có đồ chưa
    if (cart.length === 0) {
        showToast("Giỏ hàng đang trống!", 'error');
        return;
    }
    
    // 2. Tính toán lại các con số cuối cùng
    const subtotal = cart.reduce((s, i) => s + (i.price * i.quantity), 0);
    const discountEl = document.getElementById('discount');
    const discount = parseInt(discountEl ? discountEl.value : 0) || 0;
    const finalTotal = subtotal - discount;
    
    // 3. Lấy phương thức thanh toán (Tiền mặt hoặc Chuyển khoản)
    const activePayBtn = document.querySelector('.btn-pay.active');
    const paymentMethod = activePayBtn && activePayBtn.querySelector('span') ? activePayBtn.querySelector('span').innerText : 'Tiền mặt';

    if (confirm(`Xác nhận thanh toán đơn hàng: ${finalTotal.toLocaleString()}đ?`)) {
        try {
            // 4. Đóng gói dữ liệu (Payload) gửi lên Server
            const payload = {
                cart: cart,
                subtotal: subtotal,
                discount: discount,
                total: finalTotal,
                payment_method: paymentMethod,
                customer_id: selectedCustomerId,
                points_to_use: parseInt(document.getElementById('points-to-use')?.value || 0)
            };

            // 5. Gửi yêu cầu lưu hóa đơn qua REST API (Phương thức POST)
            const response = await fetch('/hi/app/api/orderapi.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (result.status === 'success') {
                showToast("Thanh toán thành công!", 'success');
                
                // 6. Cập nhật tồn kho hiển thị ngay lập tức trên UI (không cần load lại trang)
                cart.forEach(item => {
                    const card = document.getElementById('product-card-' + item.id);
                    if (card) {
                        item.stockLimit -= item.quantity; // Trừ kho ảo
                        const stockValEl = card.querySelector('.stock-qty-val');
                        if (stockValEl) stockValEl.innerText = item.stockLimit;

                        // Cập nhật lại sự kiện click để lần sau lấy đúng số lượng tồn mới
                        card.setAttribute('onclick', `addToCart(${item.id}, '${item.name.replace(/'/g, "\\'")}', ${item.price}, ${item.stockLimit})`);

                        // Nếu hết rau thì làm mờ thẻ sản phẩm
                        if (item.stockLimit <= 0) {
                            card.classList.add('out-of-stock');
                            card.style.opacity = '0.4';
                            card.style.filter = 'grayscale(1)';
                            card.style.pointerEvents = 'none';
                        }
                    }
                });

                // 7. Reset giỏ hàng và giao diện về trạng thái ban đầu
                cart = [];
                renderCart();
                
                if (discountEl) discountEl.value = 0;
                const promoMsg = document.getElementById('promo-msg');
                if (promoMsg) promoMsg.innerText = '';

                clearCustomer(); // Hủy chọn khách hàng
                loadCustomers(); // Load lại để cập nhật điểm mới của khách
            } else {
                showToast("Lỗi: " + result.message);
            }
        } catch (error) {
            console.error("Payment Error:", error);
            showToast("Có lỗi xảy ra khi kết nối máy chủ!");
        }
    }
}

// 5. Hỗ trợ phím tắt (F10 để thanh toán nhanh)
document.addEventListener('keydown', function(e) {
    if (e.key === 'F10') {
        e.preventDefault();
        processPayment();
    }
});

// --- CHỨC NĂNG VIETQR ---
// Hàm tạo mã VietQR tự động để khách chuyển khoản
function showVietQR() {
    // Kiểm tra giỏ hàng
    if (cart.length === 0) {
        showToast("Vui lòng thêm sản phẩm vào giỏ hàng trước!", 'error');
        return;
    }

    const subtotal = cart.reduce((s, i) => s + (i.price * i.quantity), 0);
    const discount = parseInt(document.getElementById('discount')?.value || 0) || 0;
    const finalTotal = subtotal - discount;
    const amount = finalTotal > 0 ? finalTotal : 0;

    // Hiển thị số tiền lên modal QR
    document.getElementById('qr-amount').innerText = amount.toLocaleString() + 'đ';
    
    // Các thông số ngân hàng
    const bankId = "VCB";
    const accountNo = "9339670825";
    const template = "compact2";
    const description = encodeURIComponent("ThanhToanPOS");
    const accountName = encodeURIComponent("LE XUAN THANG");
    
    // Gọi API của VietQR để tạo ảnh mã QR
    const qrUrl = `https://img.vietqr.io/image/${bankId}-${accountNo}-${template}.png?amount=${amount}&addInfo=${description}&accountName=${accountName}`;
    
    const qrImg = document.getElementById('qr-image');
    if (qrImg) {
        qrImg.src = qrUrl;
    }
    
    // Mở Modal QR
    const modal = document.getElementById('vietqr-modal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeVietQR() {
    const modal = document.getElementById('vietqr-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}