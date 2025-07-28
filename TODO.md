# TODO - Dự án Rút gọn Liên kết RGL TXA

## 📋 Tổng quan

Dự án **RGL TXA** là một hệ thống rút gọn liên kết hiện đại, được xây dựng bằng **PHP 8.0+**, **MySQL 8.0+**, và **Bootstrap 5**, tích hợp các tính năng web tiên tiến để mang lại trải nghiệm người dùng mượt mà, an toàn và trực quan. Giao diện lấy cảm hứng từ GitHub, hỗ trợ **chế độ tối (dark mode)**, **responsive design** cho cả desktop và mobile, và tối ưu hóa hiệu suất với các công nghệ như caching và indexing cơ sở dữ liệu. Mọi thứ khi làm xong thì hãy đánh dấu X vào công việc đó để tránh lặp lại. Luôn nhớ khi gõ TLOGON ở trò chuyện thì tức chỗ đố sẽ dùng `Logging class` nha. 

### Mục tiêu chính:
- **Chức năng cốt lõi**: 
  - Rút gọn liên kết với slug tùy chỉnh (mặc định 5 ký tự).
  - Trang chuyển hướng mượt mà với đồng hồ đếm ngược và thanh tiến trình.
  - Tạo QR code tự động cho liên kết rút gọn.
  - Thống kê chi tiết (lượt nhấp, lượt xem, thiết bị, thời gian).
- **Bảo mật**: 
  - Đăng nhập/đăng ký với mã hóa **bcrypt**, xác thực hai yếu tố (2FA), WebAuthn (Passkey), và OAuth (Google, GitHub).
  - Bảo vệ chống SQL injection, XSS, CSRF.
  - Tích hợp Google reCAPTCHA v3 cho khách chưa đăng nhập.
- **Quản lý**: 
  - Dashboard người dùng để quản lý liên kết và token API.
  - Admin panel để quản lý người dùng, cấu hình hệ thống, và thống kê.
- **Hiệu suất**: 
  - Tối ưu hóa với caching (Redis/Memcached), database indexing, và query optimization.
- **Triển khai**: 
  - Hỗ trợ triển khai với Docker, tích hợp PWA, và bảo mật cao cấp.

### Quản lý Database:
- **Class Database**: Xử lý tất cả các thao tác liên quan đến cơ sở dữ liệu. Mọi thay đổi cấu trúc (thêm/xóa cột) phải được cập nhật trong class này.
- **File `migrate.php`**: Chứa hàm khởi tạo tự động tạo các bảng cần thiết (nếu chưa tồn tại) bằng câu lệnh SQL. Chỉ cần gọi hàm này để cập nhật cấu trúc bảng.

### Công nghệ sử dụng:
- **Backend**: PHP 8.0+, MySQL 8.0+, PHPMailer, WebAuthn, Redis/Memcached.
- **Frontend**: Bootstrap 5, Tailwind CSS, Chart.js, JavaScript (AJAX, Web Push API).
- **Tích hợp bên thứ ba**: Google reCAPTCHA v3, Google OAuth, GitHub OAuth, QR Code (`endroid/qr-code`).

---

## 📦 File `composer.json`

```json
{
    "name": "txa/rgl-txa",
    "description": "Hệ thống rút gọn liên kết hiện đại với giao diện GitHub-style",
    "type": "project",
    "require": {
        "php": ">=8.0",
        "ext-json": "*",
        "ext-pdo": "*",
        "lbuchs/webauthn": "^2.2",
        "phpmailer/phpmailer": "^6.10",
        "endroid/qr-code": "^4.8",
        "google/recaptcha": "^1.3"
    },
    "autoload": {
        "psr-4": {
            "TXA\\": "src/",
            "PHPMailer\\PHPMailer\\": "vendor/phpmailer/phpmailer/src/"
        }
    }
}
```

---

## 📝 Danh sách TODO chi tiết

### 0. Giao diện cơ bản (index.php với Tailwind CSS)

- [ ] **GIAO DIỆN**
  - [ ] **index.php**:
    - Thiết kế giao diện chính với **Tailwind CSS**, lấy cảm hứng từ GitHub: đơn giản, hiện đại, hỗ trợ **dark mode**.
    - Gồm một **ô input** (text field) để nhập URL gốc và một **nút "Rút gọn"** (button) với hiệu ứng hover (màu nền thay đổi, ví dụ: `hover:bg-blue-600`).
    - Hiển thị **toast notification** (sử dụng Tailwind CSS và JavaScript) cho các trạng thái: thành công (liên kết rút gọn), lỗi (URL không hợp lệ), hoặc yêu cầu xác minh reCAPTCHA.
    - Mô tả chi tiết về trang này kèm cả faq dropdown nữa, dùng card info bo góc, svg icon nha.
    - Sau khi rút gọn, hiển thị:
      - Liên kết ngắn (ví dụ: `domain.com/?t=abc12`).
      - **QR code** (tạo bằng `endroid/qr-code`) cho liên kết ngắn.
      - Nút **sao chép liên kết** (sử dụng Clipboard API).
    - Responsive design với Tailwind CSS (hỗ trợ mobile, tablet, desktop).
  - [ ] **redirect.php**:
    - Trang chuyển hướng đẹp, sử dụng **Bootstrap 5** và **Tailwind CSS**.
    - Hiển thị:
      - **Đồng hồ đếm ngược** (mặc định 10 giây, cấu hình qua `COUNTDOWN_SEC`) và **thanh tiến trình** (progress bar) để thể hiện thời gian chờ.
      - Sử dụng **cURL** để fetch trang đích trước, lấy **tiêu đề trang** (title) và **ảnh chụp nhanh** (nếu có thể, sử dụng thư viện như Browsershot hoặc đơn giản là favicon).
      - Nút "Chuyển hướng ngay" để bỏ qua đếm ngược.
      - Responsive và hỗ trợ dark mode.
  - [ ] **api/shorten.php**:
    - API endpoint mà `index.php` gọi đến để xử lý rút gọn liên kết (sử dụng AJAX POST).
    - Xử lý validation URL, kiểm tra domain cho phép (`ALLOWED_DOMAINS`), và tạo slug ngắn (độ dài từ `LENGTH_SHORTEN`).
    - Trả về JSON với liên kết ngắn, QR code base64 (nếu cần), và thông báo lỗi.
  - [ ] **config.php**:
    - File cấu hình các thông số cơ bản:
      - `SITE_NAME`, `VERSION_SITE`, `SITE_URL`.
      - `LENGTH_SHORTEN` (độ dài slug mặc định: 5).
      - `DEBUG_MODE`: Bật/tắt debug (lưu lỗi vào file, xem qua `debug.php` nếu bật).
      - `ALLOWED_DOMAINS`: Mảng domain được phép rút gọn.
      - `COUNTDOWN_SEC`: Thời gian đếm ngược (mặc định: 10).
      - Cấu hình database:
        ```php
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'xytiuwrv_txargl');
        define('DB_USER', 'xytiuwrv_txargl');
        define('DB_PASS', 'xytiuwrv_txargl');
        ```
      - Cấu hình session và security:
        ```php
        define('SESSION_NAME', 'RGL_SESSION');
        define('SESSION_LIFETIME', 3600); // 1 giờ
        define('BCRYPT_COST', 12);
        ```

- [ ] **SMOOTH URL (.htaccess file)**
  - [ ] **.htaccess** để rewrite URL chuyển hướng thành dạng ngắn: `domain.com/?t=slug`.
  - [ ] Ẩn đuôi file PHP và sử dụng đường dẫn tuyệt đối (ví dụ: thay `<script src='../js/loading.js'>` bằng `<script src="<?php echo SITE_URL; ?>/js/loading.js">`).
  - [ ] Rewrite URL để ẩn đuôi PHP và làm đẹp thanh địa chỉ:
    | URL Thay Đổi     | URL Gốc                  |
    |------------------|--------------------------|
    | users/login      | auth/login.php           |
    | users/forgot     | auth/forgot-password.php |
    | users/reg        | auth/register.php        |
    | users/logout     | auth/logout.php          |
    | users/verify?txa=token| auth/verify.php?txa=token     |
    | users/reset?txa=token | auth/reset-password.php?txa=token |
    | ?t=abc12         | redirect.php?t=abc12     |

- [ ] **Cấu trúc database**
  - [ ] **Class Database**: Thực hiện kết nối PDO và các hàm CRUD. Bao gồm hàm migrate tự động tạo bảng.
  - [ ] Logo nằm ở `img/logo.png`.
  - [ ] Chỉnh để logo hiển thị kèm vào các email đc gửi đi và logo hiển thị ở file header vs các trang dăng kí, dăng nhập, quên mật khẩu, verify email.
  - [ ] Đã chỉnh favicon cho index, giờ đưa vào mọi trang khác hoặc tạo file tổng quát để import.
  - [ ] Chỉnh `site.webmanifest` để biến web thành PWA. Logo đã sẵn có.
  - [ ] Custom trang 404, 403 với giao diện hacker ngầu, trang 404 có đồng hồ đếm ngược 5s về trang chủ. Thông báo hay hơn, in URL hiện tại trên 404. Chỉnh .htaccess để hiển thị nội dung lỗi custom (không redirect).

- [ ] **Logging và Debug UI**
  - [ ] Tích hợp Logger class vào toàn bộ hệ thống.
  - [ ] Tất cả log đó lưu vào 1 file ở folder logs(nhưng chỉ lưu khi mà flag `DEBUG_MODE` đc bật trong config) và có thể xóa hoặc xem log bằng 1 file php khác.
Xóa là xóa theo thời gian.

### 1. 🔐 Hệ thống xác thực và đăng nhập

- [ ] **Tích hợp đăng nhập và đăng ký tài khoản**
  - [ ] Tạo form đăng nhập/đăng ký với Tailwind CSS.
  - [ ] Mã hóa mật khẩu an toàn bằng bcrypt.
  - [ ] Xác thực biểu mẫu (validation).
  - [ ] Xác nhận email cho đăng ký.
  - [ ] Quản lý phiên (session management).
  - [ ] Tạo file `auth/login.php`, `auth/register.php`, `auth/logout.php`.
  - [ ] Tạo file `auth/verify.php`, `auth/forgot-password.php`, `auth/reset-password.php`.
  - [ ] Tạo class `Auth` với đầy đủ chức năng.
  - [ ] Cập nhật database schema cho users và sessions.
  - [ ] Đặt thời gian hết hạn cho các link đặt lại mật khẩu và verify email để nêu hết hạn mà họ ms vô link đó thì báo hết hạn còn mà link đã đổi đc mật khẩu hoặc dùng để xác minh xong rồi(sau khi cập nhật mật khẩu/xác minh email thành công tự xóa cái token đó di) mà họ vẫn vô lại thì báo lỗi ra ngay.
  - [ ] Tích hợp vào trang chủ với menu user.
  - [ ] Thay đổi để gửi email bằng PHPMailer. Cấu hình mail:
    - SMTP server: Gmail.
    - Username: txafile@gmail.com.
    - Password: neqd axon hwxf gecc.
    - Lưu cấu hình ở `config.php`.
  - [ ] Thay đổi thông báo, nâng cấp giao diện cho email xác minh/đặt lại mật khẩu, và footer.
  - [ ] Bật UTF-8 cho mail để tránh lỗi font, thiết lập tổng quát ở file chính.

- [ ] **Dark Mode toàn hệ thống**
  - [ ] Thiết kế theo GitHub dark theme.
  - [ ] Toggle chế độ sáng/tối.
  - [ ] Lưu preference trong localStorage.
  - [ ] Tạo file `css/dark-mode.css`.

- [ ] **Responsive Design toàn hệ thống**
  - [ ] Tối ưu cho mobile.
  - [ ] Menu hamburger cho mobile.
  - [ ] Bootstrap 5 responsive classes.

- [ ] **Loading States toàn hệ thống**
  - [ ] Progress indicators (spinners) cho tất cả form auth.
  - [ ] Smooth transitions (fade in/out) cho tất cả thông báo bao gồm cả thông báo trên các trang auth(login, register, forgot-password, reset-password).
  - [ ] AJAX form submission cho tất cả trang auth (login, register, forgot-password, reset-password).
  - [ ] AJAX để tránh phải reload lại các trang.

- [ ] **OAuth Integration**
  - [ ] Đăng nhập qua Google OAuth.
  - [ ] Đăng nhập qua GitHub OAuth.
  - [ ] Tạo file `auth/oauth.php`.

- [ ] **Tính năng Passkey (WebAuthn)**
  - [ ] Tích hợp WebAuthn API.
  - [ ] Tạo trang xác thực với tùy chọn Passkey.
  - [ ] Hỗ trợ khóa phần cứng để đăng nhập không cần mật khẩu.
  - [ ] Tạo file `auth/passkey.php`.

- [ ] **Thêm tinh chỉnh template email tùy chỉnh**
  - [ ] Folder `templates/` chứa các template email hoàn chỉnh.
  - [ ] Chỉnh để đọc template từ folder này trong file gửi mail, thay thế biến động.
  - [ ] Biến có sẵn: `{username}`, `{date}` (ngày hiện tại), `{time}` (HH:mm dd/MM/YY), `{site_name}`, `{verification_link}`, `{reset_link}`.
  - [ ] Chỉnh README để hướng dẫn chỉnh template và giải thích biến.

- [ ] **Xác thực hai yếu tố (2FA)**
  - [ ] Tích hợp TOTP (Time-based One-Time Password).
  - [ ] Tạo trang nhập mã 2FA tương tự GitHub.
  - [ ] Dropdown "Thêm tùy chọn" với Passkey, GitHub, Mã khôi phục.
  - [ ] Tạo file `auth/2fa.php`, `auth/2fa-setup.php`.

### 2. 🔗 Rút gọn liên kết và thống kê

- [ ] **Tích hợp rút gọn liên kết theo người dùng**
  - [ ] Cập nhật `LinkShortener` class để hỗ trợ user_id.
  - [ ] Cập nhật API `api/shorten.php` để xử lý user_token thay vì user_id.
  - [ ] Cập nhật trang chủ để sử dụng API mới.
  - [ ] Liên kết short links với user_id trong database.
  - [ ] Dashboard người dùng để quản lý liên kết.
  - [ ] Tạo file `dashboard/index.php` với đầy đủ chức năng.
  - [ ] **Hệ thống user_token** - Tạo và quản lý token cho API authentication.
  - [ ] **Cập nhật database** - Thêm cột user_token vào bảng users.
  - [ ] **Dashboard token management** - Hiển thị và tạo mới user_token.

- [ ] **Thống kê liên kết cơ bản**
  - [ ] Theo dõi lượt nhấp, lượt xem.
  - [ ] Thống kê tổng quan cho người dùng.
  - [ ] Liên kết gần đây và phổ biến nhất.
  - [ ] Tạo, xóa, cập nhật liên kết từ dashboard.

- [ ] **QR CODE CHO LINK**
  - [ ] Tự động tạo và hiển thị QR code cho link rút gọn ngay sau khi rút gọn.
  - [ ] Ở dashboard thêm mục menu hiển thị các mã QR cho các link nếu có.
  - [ ] Cập nhật lại cấu trúc Database.

- [ ] **WEB PWA**
  - [ ] Nâng cấp và hoàn thiện web PWA.

- [ ] **reCAPTCHA v3**
  - [ ] Tích hợp Google reCAPTCHA v3 cho khách khi rút gọn trên trang chủ (chỉ bật nếu chưa login). Khi login thì bỏ qua.
  - [ ] Lưu cấu hình ở config:
    ```php
    define('RECAPTCHA_SITE_KEY', '6LerfpArAAAAAP1_67GXYWBNpJA-r_fNc93W35Rx');
    define('RECAPTCHA_SECRET_KEY', '6LerfpArAAAAAEPpqRhXdthjuWvQRvzrFCl2I8n-');
    ```

- [ ] **Thống kê chi tiết với biểu đồ**
  - [ ] Biểu đồ thống kê với Chart.js.
  - [ ] Tạo file `dashboard/stats.php`.
  - [ ] Biểu đồ theo thời gian, địa điểm, thiết bị.

### 3. 👨‍💼 Admin Panel

- [ ] **Chức năng admin cơ bản**
  - [ ] Quản lý liên kết.
  - [ ] Xem thống kê trang web.
  - [ ] Bộ lọc thời gian: 1h, 3h, 1 ngày, 3 ngày, 7 ngày, 1 tháng, toàn thời gian.
  - [ ] Tạo file `admin/index.php`.

- [ ] **Giao diện admin với Tailwind CSS**
  - [ ] Menu admin với biểu tượng hamburger.
  - [ ] Menu responsive trên mobile và desktop.
  - [ ] Tên trang web trong menu.
  - [ ] Theme admin màu đỏ.
  - [ ] Navigator panel riêng cho admin.

- [ ] **Quản lý người dùng và cấu hình**
  - [ ] Quản lý người dùng.
  - [ ] Cấu hình trang web.
  - [ ] Cài đặt email và bảo mật.
  - [ ] Toggle trạng thái user.
  - [ ] Tạo file `admin/users.php`, `admin/settings.php`.

- [ ] **Hệ thống admin roles và export/import**
  - [ ] Thêm cột is_admin vào database.
  - [ ] Kiểm tra quyền admin với Auth::isAdmin().
  - [ ] Toggle quyền admin cho users.
  - [ ] Không cho phép xóa quyền admin của chính mình.
  - [ ] Export users ra CSV và JSON (tải xuống dạng blob URL).
  - [ ] Import users từ CSV và JSON.
  - [ ] Ghi log hoạt động admin.
  - [ ] Tạo class DataManager để xử lý export/import.
  - [ ] Bảo vệ thư mục exports.

### 4. 🛡️ Bảo mật và phát hiện

- [ ] **Phát hiện AdBlock**
  - [ ] JavaScript để kiểm tra trình chặn quảng cáo.
  - [ ] Hiển thị thông báo lịch sự nếu phát hiện.
  - [ ] Tạo file `js/adblock-detector.js`.
  - [ ] Mã hóa file này và chặn truy cập trực tiếp.

### 5. 📊 Thống kê và API

- [ ] **Thống kê footer**
  - [ ] Hiển thị số liệu cơ bản trên trang chủ.
  - [ ] Card Bootstrap đẹp mắt.
  - [ ] Liên kết đến trang thống kê chi tiết.
  - [ ] Cập nhật `index.php` footer.

- [ ] **API RESTful**
  - [ ] Endpoint tạo liên kết rút gọn.
  - [ ] Endpoint lấy thông tin liên kết.
  - [ ] Xác thực bằng token API.
  - [ ] Bổ sung bookmarklet hỗ trợ rút gọn nhanh trang hiện tại và hiển thị modal thông báo:
    ```javascript
    javascript:void((function () {var h = 'https://txabio.txafile.fun';var e = document.createElement('script');e.setAttribute('data-url', h);e.setAttribute('data-token', '6691eaf61fc372f85c86bad3d4b21f22');e.setAttribute('id', 'gem_bookmarklet');e.setAttribute('type', 'text/javascript');e.setAttribute('src', h+'/static/bookmarklet.js?v=1745586064');document.body.appendChild(e);})());
    ```
  - [ ] Tạo file `api/v1/links.php`, `api/v1/auth.php`.

- [ ] **Cập nhật test_debug.php**
  - [ ] Bao gồm token API trong tất cả requests.
  - [ ] Xác thực API calls.

- [ ] **Trang docs API**
  - [ ] Hướng dẫn sử dụng API.
  - [ ] Ví dụ code.
  - [ ] Liên kết từ trang API.
  - [ ] Tạo file `docs/api.php`.

### 6. 👤 Trang cá nhân người dùng

- [ ] **Giao diện hồ sơ GitHub-style**
  - [ ] Thanh menu bên trái với biểu tượng.
  - [ ] Các phần: Hồ sơ công khai, Tài khoản, Giao diện, Trợ năng, Thông báo, Truy cập.
  - [ ] Lịch sử đăng nhập, quản lí phiên qua bảng user_sessions.
  - [ ] Tạo file `profile/index.php`.

- [ ] **Thanh điều hướng trên cùng**
  - [ ] Biểu tượng Octocat.
  - [ ] Nhãn "Bảng điều khiển".
  - [ ] Biểu tượng tìm kiếm, thông báo, ảnh hồ sơ.
  - [ ] Tạo file `profile/layout.php`.

- [ ] **Khu vực nội dung chính**
  - [ ] Trường Tên (có thể chỉnh sửa).
  - [ ] Ảnh hồ sơ (upload/chỉnh sửa hình tròn).
  - [ ] Email công khai (dropdown).
  - [ ] Tiểu sử (textarea với @mention).
  - [ ] Đại từ (dropdown).
  - [ ] Tạo file `profile/edit.php`.

### 7. 🔄 Tính năng nâng cao

- [ ] **Sao lưu và khôi phục dữ liệu**
  - [ ] Tải xuống tệp sao lưu (JSON/CSV).
  - [ ] Khôi phục dữ liệu từ tệp sao lưu.
  - [ ] Tạo file `backup/export.php`, `backup/import.php`.

- [ ] Tích hợp Bootstrap 5 vào trang users và trang admin.

- [ ] **Thông báo đẩy (Push Notifications)**
  - [ ] Tích hợp Web Push API.
  - [ ] Thông báo khi có lượt nhấp mới.
  - [ ] Thông báo khi truy cập từ thiết bị lạ.
  - [ ] Tạo file `notifications/push.php`.

- [ ] **Chia sẻ mạng xã hội**
  - [ ] Nút chia sẻ Facebook, Twitter, WhatsApp.
  - [ ] Thiết kế đẹp mắt với Bootstrap.
  - [ ] Tạo file `social/share.php`.

- [ ] **Quản lý danh sách yêu thích**
  - [ ] Đánh dấu liên kết quan trọng.
  - [ ] Truy cập nhanh từ trang cá nhân.
  - [ ] Tạo file `favorites/index.php`.

### 8. 🗄️ Database và Backend

- [ ] **Cập nhật cấu trúc database**
  - [ ] Bảng cho 2FA, OAuth, Passkey.
  - [ ] Bảng cho thống kê chi tiết.
  - [ ] Bảng cho cấu hình admin.
  - [ ] Cập nhật `database.sql`.

- [ ] **Tối ưu hóa performance**
  - [ ] Caching với Redis/Memcached.
  - [ ] Database indexing.
  - [ ] Query optimization.

### 9. 🧪 Testing và Deployment

- [ ] **Unit Testing**
  - [ ] PHPUnit tests.
  - [ ] API endpoint testing.
  - [ ] Tạo thư mục `tests/`.

- [ ] **Security Testing**
  - [ ] SQL injection prevention.
  - [ ] XSS protection.
  - [ ] CSRF protection.

- [ ] **Deployment**
  - [ ] Docker configuration.
  - [ ] Environment variables.
  - [ ] Production optimization.

## 🎯 Ưu tiên thực hiện

0->1->2->4->3->5->6->8->7->9.

## 📝 Ghi chú

- Sử dụng PHP 8.0+ cho các tính năng hiện đại.
- Bootstrap 5 cho UI/UX nhất quán.
- MySQL 8.0+ cho database.
- Chart.js cho biểu đồ.
- WebAuthn API cho Passkey.
- Web Push API cho notifications. 


## 📖 Nhật ký thay đổi & các cải tiến đã thực hiện

> **Lưu ý:** Đây là các thay đổi/cải tiến đã thực hiện trong quá trình phát triển, KHÔNG PHẢI trạng thái hoàn thành của các mục trong TODO checklist ở trên.

### 1. Cải tiến hệ thống admin roles
- Thêm cột `is_admin` vào database: Đã thêm cột `is_admin` (BOOLEAN) vào bảng `users`
- Tạo class `Auth` với phương thức `isAdmin()`: Kiểm tra quyền admin một cách an toàn
- Thêm phương thức `toggleAdminStatus()`: Cho phép admin thêm/xóa quyền admin cho users khác
- Bảo vệ admin hiện tại: Không cho phép admin tự xóa quyền admin của chính mình
- Thêm phương thức `getAdmins()`: Lấy danh sách tất cả admin trong hệ thống

### 2. Hệ thống export/import dữ liệu
- Tạo class `DataManager`: Xử lý export/import users ra CSV và JSON
- Export với tải xuống thực tế: File được tải xuống ngay lập tức thay vì chỉ hiển thị thông báo
- Import từ file: Hỗ trợ import users từ CSV và JSON
- Bảo mật thư mục exports: Tạo `.htaccess` để bảo vệ thư mục exports
- Danh sách file export: Hiển thị danh sách các file đã export với nút tải xuống
- Tạo file `get_export_files.php`: API để refresh danh sách file export

### 3. Cải tiến giao diện admin
- Navigation riêng cho admin: Tạo `includes/admin-navigation.php` với theme màu đỏ
- Gateway access control: Tạo `admin-access.php` để kiểm soát truy cập admin panel
- Responsive design: Tối ưu hiển thị trên mobile và desktop
- Menu hamburger: Menu responsive với biểu tượng hamburger

### 4. Thống kê admin nâng cao
- Bộ lọc thời gian: Thêm các filter 1h, 3h, 1d, 3d, 7d, 1m, all time
- API endpoint `admin-stats.php`: Trả về dữ liệu thống kê theo thời gian
- Cải tiến `LinkShortener::getAdminStats()`: Hỗ trợ lọc theo thời gian
- Chart.js responsive: Tối ưu hiển thị biểu đồ trên mobile và PC
- Cập nhật `getTopUsers()`: Hỗ trợ lọc theo thời gian

### 5. Cải tiến quản lý users
- Bảng users với cột Admin: Hiển thị trạng thái admin của từng user
- Nút toggle admin: Thêm/xóa quyền admin cho users (trừ chính mình)
- Export/Import section: Giao diện riêng cho export/import data
- Logging admin actions: Ghi log tất cả hành động của admin

### 6. Cải tiến settings
- Tạo class `Settings`: Quản lý settings phân biệt giữa config file và database
- Settings persistence: Một số settings lưu trong config.php, một số trong database
- Form validation: Cải thiện validation cho settings

### 7. Cải tiến test_debug.php
- Form fields thay vì JSON: Thay đổi từ textarea JSON sang input fields riêng biệt
- User token management: Pre-fill user token từ session và có nút copy
- Random token generation: Tạo token ngẫu nhiên cho guests
- Bypass reCAPTCHA: Bỏ qua reCAPTCHA khi có user token
- Debug information: Hiển thị thông tin User Status và Token Type

### 8. Cải tiến navigation
- Shared navigation: Tạo `includes/navigation.php` cho regular users
- Admin navigation: Tạo `includes/admin-navigation.php` riêng cho admin
- Conditional admin link: Chỉ hiển thị link Admin Panel nếu user là admin

### 9. Cải tiến export functionality
- Force download: Thêm headers để force browser download file
- File listing: Hiển thị danh sách file đã export
- Auto refresh: JavaScript để refresh danh sách file sau khi export
- Security: Kiểm tra quyền truy cập file export

### 10. Cải tiến database schema
- Migration script: Cập nhật `admin_migration.sql` với các cột mới
- Admin logs table: Tạo bảng `admin_logs` để ghi log
- Data exports table: Tạo bảng `data_exports` để track export history
- User token: Thêm cột `user_token` cho API authentication

#### 🔧 Các file đã tạo/sửa đổi

**Files mới tạo:**
- `src/DataManager.php` - Class xử lý export/import
- `src/Settings.php` - Class quản lý settings
- `includes/navigation.php` - Navigation cho regular users
- `includes/admin-navigation.php` - Navigation cho admin
- `admin-access.php` - Gateway cho admin panel
- `admin/admin-stats.php` - API endpoint cho thống kê admin
- `admin/get_export_files.php` - API cho danh sách file export
- `admin/import_users.php` - API xử lý import users
- `exports/.htaccess` - Bảo vệ thư mục exports

**Files đã sửa đổi:**
- `admin/index.php` - Thêm time filters và chart improvements
- `admin/users.php` - Thêm export/import, admin roles, file download
- `admin/settings.php` - Tích hợp Settings class
- `src/Auth.php` - Thêm admin methods
- `src/LinkShortener.php` - Thêm admin stats methods
- `test_debug.php` - Cải tiến form và token management
- `admin_migration.sql` - Cập nhật schema

**Tính năng bổ sung không có trong TODO.md:**
- Hệ thống logging admin actions
- File export với download thực tế
- Time filters cho admin statistics
- Admin navigation riêng biệt
- Gateway access control
- Auto-refresh export file list
- Enhanced test_debug.php interface 


# RGL API Tester (test_debug.php) - Đây là phần để mà test các api có trên trang

## 📋 Mục đích

File **test_debug.php** là công cụ giao diện web giúp kiểm thử nhanh các API chính của hệ thống Rút gọn Liên kết RGL TXA, hỗ trợ cả người dùng đã đăng nhập và khách (guest).

---

## 🚀 Các chức năng chính

### 1. Test API rút gọn liên kết
- **Endpoint:** `POST /api/shorten.php`
- Nhập URL cần rút gọn, nhập hoặc tự động điền User Token.
- Gửi request và xem trực tiếp response JSON trả về.
- Có thể copy nhanh kết quả response.

### 2. Test API chuyển hướng
- **Endpoint:** `GET /{shortId}`
- Nhập shortId để kiểm tra chuyển hướng.
- Xem response header, status code, thời gian phản hồi.

### 3. Quick Test
- Các nút test nhanh các trường hợp phổ biến:
  - URL hợp lệ
  - URL không hợp lệ
  - URL rỗng
- Tự động điền dữ liệu mẫu và gửi request.

### 4. Quản lý User Token
- Nếu đã đăng nhập: tự động lấy token thật, readonly.
- Nếu là khách: sinh token ngẫu nhiên, có nút tạo lại nhanh.
- Nút copy token tiện lợi.

### 5. Debug Information
- Hiển thị thông tin cấu hình hệ thống:
  - Debug mode, Site URL, Database, Short ID Length
  - User Status (Logged In/Guest), Token Type
  - PHP Version, Server, Thời gian hiện tại, Log Directory

---

## 🖥️ Giao diện
- Thiết kế với **Tailwind CSS** hiện đại, responsive.
- Có header, các card test API, quick test, debug info rõ ràng.
- Sử dụng icon FontAwesome.

---

## 📝 Hướng dẫn sử dụng nhanh
1. **Test rút gọn:**
   - Nhập URL cần rút gọn, kiểm tra/copy User Token nếu cần.
   - Nhấn "Send Request" để gửi request, xem kết quả trả về.
2. **Test chuyển hướng:**
   - Nhập shortId, nhấn "Test Redirect" để kiểm tra response.
3. **Quick Test:**
   - Nhấn các nút để test nhanh các trường hợp mẫu.
4. **Xem debug:**
   - Kéo xuống cuối trang để xem thông tin cấu hình và hệ thống.

---

## ⚠️ Lưu ý bảo mật
- Không nên để file này trên môi trường production thực tế.
- Chỉ sử dụng cho mục đích kiểm thử, debug nội bộ.
- Token user thật chỉ hiển thị cho chính user đó khi đã đăng nhập.

---

## 📄 Tóm tắt code
- Sử dụng PHP để lấy thông tin user, token, cấu hình.
- Giao diện và logic test API hoàn toàn bằng HTML + Tailwind CSS + JavaScript thuần.
- Các hàm JS hỗ trợ gửi request, copy kết quả, sinh token, test nhanh.