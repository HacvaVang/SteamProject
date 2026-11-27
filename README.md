# Steam Project
## How to host web server on Xampp
 1. Download Xampp
 2. Open httpd.conf file in `<Config>` tab (next to Apache module)
 3. Looking for DocumentRoot, change directory into `<Installed location>/SteamProject/`

````bash
cd SteamProject
php -S localhost:8000 -t . public/index.php
````

Truy cập web tại đường dẫn sau:
````
http://localhost:8000
````

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

 


Thực hiện chỉnh sửa tại file ***public/index.php*** để tạm thời thay controller trong việc chuyển trang.

Muốn thêm rel của stylesheet thì include ở trong ***layout/main.php***

## SQLite product data

The Store and Admin catalog read and write `SteamProject/storage/app.sqlite`
through PDO SQLite. The app uses `games` for product fields, `game_tags` for
tags, and the average of `reviews.rating_score` for ratings. The database's
`discount_check` is a boolean; the UI derives the displayed percentage from
`base_price` and `final_price`.

For a new database, from the repository root run:

```bash
cd SteamProject
php storage/fetch_steam_seed.php
sqlite3 storage/app.sqlite < storage/schema.sql
sqlite3 storage/app.sqlite < storage/seeders.sql
```

The PHP runtime must have `pdo_sqlite` enabled. To use a different database
file, set `STEAM_PROJECT_SQLITE_PATH` to its absolute path. The Steam seed
generator fetches Steam data, writes the SQL file, and downloads images; it
does not execute SQL.

## Cart and order management

The cart is synchronized to SQLite so Admin can review carts across browser
sessions. Until authentication is connected to the database, each cart is
associated with its PHP session and the displayed customer is the session's
current name. Checkout creates a pending demo order and snapshots the product
names and prices; it does not process a real payment.

Cart statuses are `active`, `abandoned`, and `converted`. Checkout changes an
active cart to `converted`; Admin can mark non-converted carts as active or
abandoned. Order statuses are `pending`, `processing`, `completed`, and
`cancelled`. Admin manages both lists at `/admin/orders`. The application
creates these tables on first use; they are also included in
`SteamProject/storage/schema.sql` for fresh databases.

The Admin catalog and commerce pages (`/admin/products` and `/admin/orders`)
use a standalone layout separate from the Store header and footer.
