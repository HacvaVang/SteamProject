# Steam Project
## Tree directory
```
SteamProject/
├── app/                              # Mã nguồn ứng dụng theo mô hình MVC
│   ├── Controllers/                  # Tiếp nhận request, xử lý nghiệp vụ, gọi Model và View
│   │   ├── AuthController.php        # Đăng ký, đăng nhập, đăng xuất, đổi mật khẩu
│   │   ├── HomeController.php        # Trang chủ
│   │   ├── ContactController.php     # Trang liên hệ phía client và quản lý liên hệ phía admin
│   │   ├── SettingController.php     # Quản lý thông tin chung website: địa chỉ, SĐT, logo...
│   │   ├── AboutController.php       # Trang giới thiệu và quản lý nội dung giới thiệu
│   │   ├── FaqController.php         # Trang hỏi đáp và quản lý FAQ
│   │   ├── ProductController.php    # Danh sách, tìm kiếm, chi tiết và quản lý sản phẩm
│   │   ├── CartController.php       # Thêm/xóa/cập nhật sản phẩm trong giỏ hàng
│   │   ├── OrderController.php      # Tạo đơn hàng, danh sách, chi tiết và cập nhật trạng thái
│   │   ├── PostController.php       # Danh sách, tìm kiếm, chi tiết và quản lý bài viết
│   │   ├── CommentController.php    # Tạo, duyệt và xóa bình luận/đánh giá
│   │   └── UserController.php       # Admin quản lý thành viên: xem, khóa/mở khóa, reset mật khẩu
│   │
│   ├── Models/                       # Truy vấn và thao tác dữ liệu MySQL thông qua PDO
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── OrderItem.php             # Chi tiết từng sản phẩm trong đơn hàng
│   │   ├── Post.php
│   │   ├── Comment.php
│   │   ├── Contact.php
│   │   ├── Faq.php
│   │   └── Setting.php
│   │
│   └── Views/
│       ├── layouts/
│       │   ├── header.php            # Header và navbar phía client
│       │   ├── footer.php            # Footer phía client
│       │   ├── admin_header.php      # Header và sidebar TailAdmin
│       │   └── admin_footer.php      # Footer TailAdmin
│       │
│       ├── client/
│       │   ├── home/
│       │   │   └── index.php
│       │   ├── contact/
│       │   │   └── index.php
│       │   ├── about/
│       │   │   └── index.php
│       │   ├── faq/
│       │   │   └── index.php
│       │   ├── product/
│       │   │   ├── index.php          # Danh sách và tìm kiếm sản phẩm
│       │   │   ├── detail.php
│       │   │   └── cart.php           # Có thể tách thành thư mục cart/ nếu giao diện phát triển
│       │   ├── order/
│       │   │   ├── checkout.php       # Trang xác nhận thông tin đặt hàng
│       │   │   └── history.php        # Lịch sử đơn hàng của thành viên (nếu triển khai)
│       │   ├── pricing/
│       │   │   └── index.php          # Chỉ cần nếu bảng giá là trang riêng
│       │   ├── post/
│       │   │   ├── index.php
│       │   │   └── detail.php
│       │   └── auth/
│       │       ├── login.php
│       │       ├── register.php
│       │       └── profile.php
│       │
│       └── admin/
│           ├── dashboard.php
│           ├── contacts/
│           │   └── index.php
│           ├── settings/
│           │   └── index.php
│           ├── faqs/
│           │   ├── index.php
│           │   ├── create.php
│           │   └── edit.php
│           ├── products/
│           │   ├── index.php
│           │   ├── create.php
│           │   └── edit.php
│           ├── orders/
│           │   ├── index.php
│           │   └── detail.php
│           ├── posts/
│           │   ├── index.php
│           │   ├── create.php
│           │   └── edit.php
│           ├── comments/
│           │   └── index.php
│           └── users/
│               └── index.php
│
├── config/
│   ├── database.php                 # Khởi tạo kết nối PDO
│   └── config.php                   # BASE_URL, UPLOAD_DIR và cấu hình chung
│
├── core/                             # Thành phần nền tảng tự viết; không phải PHP Framework bên thứ ba
│   ├── Router.php                    # Ánh xạ URL đến Controller/action
│   ├── Controller.php                # Controller cơ sở: render view, nạp model
│   ├── Database.php                  # Helper truy vấn PDO và prepared statements
│   ├── Auth.php                      # Kiểm tra đăng nhập và phân quyền
│   ├── Validator.php                 # Kiểm tra dữ liệu đầu vào phía server
│   ├── Csrf.php                      # Tạo và xác thực CSRF token
│   └── Helpers.php                   # Escape HTML, upload ảnh, phân trang và tiện ích
│
├── public/                           # Document root; chỉ thư mục này được public trực tiếp
│   ├── index.php                     # Single entry point
│   ├── .htaccess                     # URL rewriting và quy tắc bảo vệ phù hợp
│   ├── uploads/
│   │   ├── products/
│   │   ├── posts/
│   │   └── avatars/
│   └── assets/
│       ├── css/
│       ├── js/
│       ├── images/
│       └── tailadmin/
│
├── database/
│   └── schema.sql                    # Tạo database, bảng và dữ liệu mẫu
│
├── .gitignore
└── README.md
```
