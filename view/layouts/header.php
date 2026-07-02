<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmi Admin</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/hi/public/css/dashboard.css">
    <link rel="stylesheet" href="/hi/public/css/sanpham.css">
    
    <style>
        /* Thêm một chút reset để font chữ đồng bộ */
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #121212; /* Màu tối khớp với dashboard của bạn */
        }

        /* Logout Modal Styles */
        .logout-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .logout-modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .logout-modal-card {
            background: rgba(30, 30, 30, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 1.5rem;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .logout-modal-overlay.active .logout-modal-card {
            transform: scale(1);
        }

        .logout-icon {
            font-size: 3rem;
            color: #ff6b6b;
            margin-bottom: 1.5rem;
            display: inline-block;
            background: rgba(255, 107, 107, 0.1);
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
        }

        .logout-modal-card h3 {
            color: #fff;
            margin: 0 0 0.5rem 0;
            font-size: 1.5rem;
        }

        .logout-modal-card p {
            color: #aaaaaa;
            margin-bottom: 2rem;
        }

        .logout-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn-modal {
            padding: 0.8rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            flex: 1;
            font-size: 1rem;
        }

        .btn-cancel {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-confirm-logout {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5253 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        .btn-confirm-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
        }
    </style>
</head>
<body>