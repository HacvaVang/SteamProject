# Steam Project
## Tree directory
```
SteamProject/
│
├── app/
│   │
│   ├── Controllers/                 # PHP - Xử lý yêu cầu
│   │   ├── HomeController.php
│   │   ├── AuthController.php
│   │   ├── UserController.php
│   │   ├── ProductController.php
│   │   ├── CartController.php
│   │   ├── OrderController.php
│   │   ├── NewsController.php
│   │   ├── CommentController.php
│   │   ├── ContactController.php
│   │   ├── FAQController.php
│   │   ├── AboutController.php
│   │   │
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── UserController.php
│   │       ├── ProductController.php
│   │       ├── OrderController.php
│   │       ├── NewsController.php
│   │       ├── CommentController.php
│   │       ├── ContactController.php
│   │       ├── FAQController.php
│   │       └── SettingController.php
│   │
│   ├── Models/                      # PHP - Làm việc với database
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Cart.php
│   │   ├── CartItem.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── News.php
│   │   ├── Comment.php
│   │   ├── Contact.php
│   │   ├── FAQ.php
│   │   └── Setting.php
│   │
│   ├── Views/                       # Giao diện HTML + PHP
│   │   │
│   │   ├── layouts/
│   │   │   ├── header.php
│   │   │   ├── navbar.php
│   │   │   ├── footer.php
│   │   │   └── main.php
│   │   │
│   │   ├── home/
│   │   │   └── index.php
│   │   │
│   │   ├── auth/
│   │   │   ├── login.php
│   │   │   └── register.php
│   │   │
│   │   ├── user/
│   │   │   ├── profile.php
│   │   │   └── change-password.php
│   │   │
│   │   ├── products/
│   │   │   ├── index.php
│   │   │   └── detail.php
│   │   │
│   │   ├── cart/
│   │   │   └── index.php
│   │   │
│   │   ├── orders/
│   │   │   ├── checkout.php
│   │   │   ├── history.php
│   │   │   └── detail.php
│   │   │
│   │   ├── news/
│   │   │   ├── index.php
│   │   │   └── detail.php
│   │   │
│   │   ├── about/
│   │   │   └── index.php
│   │   │
│   │   ├── contact/
│   │   │   └── index.php
│   │   │
│   │   ├── faq/
│   │   │   └── index.php
│   │   │
│   │   └── admin/
│   │       ├── layouts/
│   │       │   ├── header.php
│   │       │   ├── sidebar.php
│   │       │   └── footer.php
│   │       │
│   │       ├── dashboard/
│   │       │   └── index.php
│   │       ├── users/
│   │       │   └── index.php
│   │       ├── products/
│   │       │   ├── index.php
│   │       │   ├── create.php
│   │       │   └── edit.php
│   │       ├── orders/
│   │       │   └── index.php
│   │       ├── news/
│   │       │   ├── index.php
│   │       │   ├── create.php
│   │       │   └── edit.php
│   │       ├── comments/
│   │       │   └── index.php
│   │       ├── contacts/
│   │       │   └── index.php
│   │       ├── faqs/
│   │       │   └── index.php
│   │       └── settings/
│   │           └── index.php
│   │
│   ├── Core/                        # PHP - MVC tự xây dựng
│   │   ├── Database.php
│   │   ├── Router.php
│   │   ├── Controller.php
│   │   └── View.php
│   │
│   ├── Middleware/                  # PHP - Kiểm tra quyền
│   │   ├── AuthMiddleware.php
│   │   └── AdminMiddleware.php
│   │
│   └── Helpers/                     # PHP - Hàm dùng chung
│       ├── Validator.php
│       ├── Csrf.php
│       ├── Upload.php
│       ├── Pagination.php
│       └── Security.php
│
├── config/                          # PHP - Cấu hình
│   ├── database.php
│   └── app.php
│
├── database/                        # SQL - Cơ sở dữ liệu
│   ├── steamproject.sql
│   ├── schema.sql
│   └── seeders.sql
│
├── public/                          # Thư mục truy cập công khai
│   │
│   ├── index.php                    # PHP - Entry Point
│   ├── .htaccess
│   │
│   ├── html/                        # HTML - Giao diện tĩnh
│   │   ├── 404.html
│   │   └── maintenance.html
│   │
│   ├── assets/
│   │   │
│   │   ├── css/                     # CSS - Giao diện
│   │   │   ├── style.css
│   │   │   ├── responsive.css
│   │   │   ├── auth.css
│   │   │   ├── product.css
│   │   │   ├── cart.css
│   │   │   └── admin.css
│   │   │
│   │   ├── js/                      # JS - Tương tác
│   │   │   ├── main.js
│   │   │   ├── auth.js
│   │   │   ├── user.js
│   │   │   ├── product.js
│   │   │   ├── cart.js
│   │   │   ├── checkout.js
│   │   │   ├── comment.js
│   │   │   └── admin.js
│   │   │
│   │   ├── images/                  # Hình ảnh giao diện
│   │   │   ├── logo/
│   │   │   ├── banners/
│   │   │   ├── products/
│   │   │   ├── news/
│   │   │   └── avatars/
│   │   │
│   │   └── libraries/               # Thư viện frontend
│   │       ├── bootstrap/
│   │       ├── jquery/
│   │       └── tailadmin/
│   │
│   └── uploads/                     # Hình ảnh upload lên server
│       ├── products/
│       ├── news/
│       └── avatars/
│
├── routes/                          # PHP - Định tuyến
│   └── web.php
│
├── storage/
│   └── logs/
│
├── .gitignore
└── README.md
```
