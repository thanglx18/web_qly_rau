<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập | Farmi Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #52DDB5;
            --secondary: #3498db;
            --bg-dark: #0a110e;
            --card-bg: #141c18;
            --text-main: #e2e8f0;
            --text-dim: #94a3b8;
            --border: #232d28;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }

        body {
            background-color: var(--bg-dark);
            background-image: radial-gradient(circle at 50% 50%, #1a2c24 0%, var(--bg-dark) 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            position: relative;
            z-index: 10;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: -2px; left: -2px; right: -2px; bottom: -2px;
            background: linear-gradient(135deg, var(--primary), #3498db);
            border-radius: 22px;
            z-index: -1;
            opacity: 0.1;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: var(--primary);
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 2px;
            text-shadow: 0 0 10px rgba(82, 221, 181, 0.3);
        }

        .logo p {
            color: var(--text-dim);
            font-size: 14px;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: var(--text-dim);
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 16px;
        }

        .form-control {
            width: 100%;
            padding: 12px 45px 12px 45px;
            background: rgba(10, 16, 13, 0.5);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(82, 221, 181, 0.05);
            box-shadow: 0 0 0 4px rgba(82, 221, 181, 0.1);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 13px;
        }

        .remember {
            display: flex;
            align-items: center;
            color: var(--text-dim);
            cursor: pointer;
        }

        .remember input { margin-right: 8px; accent-color: var(--primary); }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), #3da88e);
            border: none;
            border-radius: 10px;
            color: #064e3b;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(82, 221, 181, 0.3);
            filter: brightness(1.1);
        }

        .btn-login:active { transform: translateY(0); }

        .alert {
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            display: none;
            text-align: center;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: var(--text-dim);
            font-size: 12px;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            left: auto !important;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            cursor: pointer;
            transition: all 0.3s;
            z-index: 20;
            font-size: 16px !important;
            opacity: 0.7;
        }

        .toggle-password:hover {
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="logo">
            <h1>FARMI ADMIN</h1>
            <p>Hệ thống quản lý nông sản thông minh</p>
        </div>

        <div id="loginAlert" class="alert alert-error"></div>

        <form id="loginForm">
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" class="form-control" placeholder="Nhập tên đăng nhập..." required value="<?php echo htmlspecialchars($_COOKIE['remember_username'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Mật khẩu</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Nhập mật khẩu..." required value="<?php echo htmlspecialchars($_COOKIE['remember_password'] ?? ''); ?>">
                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                </div>
            </div>

            <div class="remember-forgot">
                <label class="remember">
                    <input type="checkbox" name="remember" <?php echo isset($_COOKIE['remember_check']) ? 'checked' : ''; ?>> Ghi nhớ đăng nhập
                </label>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                <span>ĐĂNG NHẬP NGAY</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div class="footer-text">
            &copy; 2026 Farmi Solutions. Mọi quyền được bảo lưu.
        </div>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const loginAlert = document.getElementById('loginAlert');

        loginForm.onsubmit = async (e) => {
            e.preventDefault();
            
            // UI Feedback
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<span>ĐANG XÁC THỰC...</span> <i class="fas fa-spinner fa-spin"></i>';
            loginAlert.style.display = 'none';

            try {
                const formData = new FormData(loginForm);
                const response = await fetch('/hi/public/index.php?url=auth/login', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    loginAlert.className = 'alert' + (result.status === 'success' ? '' : ' alert-error');
                    loginAlert.innerText = result.message;
                    loginAlert.style.display = 'block';
                    loginAlert.style.background = 'rgba(82, 221, 181, 0.15)';
                    loginAlert.style.color = 'var(--primary)';
                    loginAlert.style.borderColor = 'rgba(82, 221, 181, 0.3)';
                    
                    setTimeout(() => {
                        window.location.href = '/hi/public/index.php?url=' + (result.redirect || 'home');
                    }, 800);
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                loginAlert.innerText = error.message || 'Lỗi kết nối máy chủ!';
                loginAlert.style.display = 'block';
                loginBtn.disabled = false;
                loginBtn.innerHTML = '<span>ĐĂNG NHẬP NGAY</span> <i class="fas fa-arrow-right"></i>';
            }
        };

        // Toggle Password Logic
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.onclick = function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        };
    </script>
</body>
</html>
