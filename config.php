<?php
// ================== CONFIG RGL TXA ==================
// Thông tin site
const SITE_NAME = 'RGL TXA';
const VERSION_SITE = '1.0.0';
const SITE_URL = 'https://nrotxa.online'; // Sửa lại khi deploy thực tế
const SITE_SLOGAN = 'Rút gọn liên kết an toàn, nhanh chóng, miễn phí!';

// Độ dài slug rút gọn
const LENGTH_SHORTEN = 5;

// Debug mode
const DEBUG_MODE = true;

// Danh sách domain cho phép rút gọn
const ALLOWED_DOMAINS = [

    // Thêm domain khác nếu cần
];

// Thời gian đếm ngược chuyển hướng (giây)
const COUNTDOWN_SEC = 10;

// Cấu hình database
const DB_HOST = 'localhost';
const DB_NAME = 'xytiuwrv_txargl';
const DB_USER = 'xytiuwrv_txargl';
const DB_PASS = 'xytiuwrv_txargl';

// Session & security
const SESSION_NAME = 'TXA_SESSION';
const SESSION_LIFETIME = 3600; // 1 giờ
const BCRYPT_COST = 12;

// reCAPTCHA v2
const RECAPTCHA_ENABLED = true; // Bật/tắt reCAPTCHA toàn hệ thống
const RECAPTCHA_SITE_KEY = '6LdEupArAAAAALkDe4XQnULb3hNphQgYox28dJpe';
const RECAPTCHA_SECRET_KEY = '6LdEupArAAAAACKKtEIwyoKyNffquW7TCuBeuNxo';

// Thư mục lưu log
const LOG_DIR = '/logs';

// ================== END CONFIG ================== 