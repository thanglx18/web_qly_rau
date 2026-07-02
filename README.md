# 🥬 Farmi - Hệ Thống Quản Lý Cửa Hàng Rau Sạch

Dự án quản lý cửa hàng rau củ quả, tích hợp POS bán hàng, quản lý kho, danh mục sản phẩm, nhà cung cấp, khách hàng và chương trình khuyến mãi. Dự án được phát triển theo mô hình MVC sử dụng PHP thuần và cơ sở dữ liệu MySQL, được đóng gói bằng Docker để dễ dàng triển khai.

---

## 🛠️ Công Nghệ Sử Dụng

*   **Backend:** PHP 8.1 (MVC Pattern, PDO)
*   **Database:** MySQL 8.0
*   **Frontend:** HTML, CSS, JavaScript (Vanilla JS & Modern UI)
*   **DevOps:** Docker, Docker Compose

---

## 🚀 Hướng Dẫn Cài Đặt và Khởi Chạy bằng Docker

Để chạy dự án này trên máy tính của bạn hoặc chia sẻ cho người khác, chỉ cần cài đặt **Docker Desktop** và làm theo các bước sau:

### 1. Clone Project
```bash
git clone https://github.com/thanglx18/web_ban_rau.git
cd web_ban_rau
```

### 2. Khởi chạy Docker Containers
Mở terminal tại thư mục gốc của dự án và chạy lệnh sau:
```bash
docker-compose up -d --build
```
*Lệnh này sẽ tự động build ứng dụng web PHP, tải MySQL, tạo database và tự động import dữ liệu ban đầu từ file `farmi_qly.sql`.*

### 3. Truy cập ứng dụng
Sau khi container khởi chạy thành công, truy cập vào trình duyệt theo địa chỉ:
👉 **[http://localhost:8080/](http://localhost:8080/)** (Hệ thống sẽ tự động redirect bạn sang **`http://localhost:8080/hi/public/`**)

---

## 🔑 Tài Khoản Đăng Nhập Mặc Định

Hệ thống có sẵn các tài khoản thử nghiệm sau (được tự động khởi tạo trong cơ sở dữ liệu):

| Vai Trò (Role) | Tên Đăng Nhập (Username) | Mật Khẩu (Password) |
| :--- | :--- | :--- |
| **Quản Trị Viên (Admin)** | `admin` | `123456` |
| **Nhân Viên (Staff)** | `nhanvien1` | `123456` |
| **Nhân Viên (Staff)** | `nhanvien2` | `123456` |

---

## 📂 Cấu Trúc Thư Mục Dự Án

```text
web_ban_rau/
├── app/                  # Thư mục mã nguồn chính (MVC)
│   ├── controllers/      # Bộ điều hướng hành động (Controllers)
│   ├── models/           # Xử lý logic dữ liệu và truy vấn DB (Models)
│   ├── core/             # Nhân hệ thống (Router, Database Connection...)
│   └── api/              # Các API endpoints hỗ trợ gọi AJAX/Fetch
├── view/                 # Chứa các giao diện trang web (Views)
├── public/               # Thư mục public của Web Server (CSS, JS, Image, index.php)
├── Dockerfile            # Hướng dẫn build Container cho PHP Web App
├── docker-compose.yml    # Cấu hình khởi chạy dịch vụ Web & Database
├── farmi_qly.sql         # File backup dữ liệu cơ sở dữ liệu mẫu
└── README.md             # Hướng dẫn sử dụng dự án này
```

---

## ⚙️ Hướng Dẫn Cài Đặt Thủ Công (Không Dùng Docker - qua XAMPP)

Nếu bạn muốn chạy trực tiếp bằng XAMPP trên Windows:

1.  Copy thư mục dự án vào thư mục `C:\xampp\htdocs\` và đổi tên thư mục thành `hi` (để đường dẫn là `C:\xampp\htdocs\hi\`).
2.  Mở **phpMyAdmin** (`http://localhost/phpmyadmin/`), tạo một cơ sở dữ liệu mới tên là `farmi_qly`.
3.  Import file `farmi_qly.sql` vào cơ sở dữ liệu vừa tạo.
4.  Mở trình duyệt và truy cập: **[http://localhost/hi/public/](http://localhost/hi/public/)**.
