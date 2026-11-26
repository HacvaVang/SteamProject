PRAGMA foreign_keys = ON;

-- 1. USER
CREATE TABLE IF NOT EXISTS users (
    account_id      INTEGER PRIMARY KEY AUTOINCREMENT,
    user_name       TEXT NOT NULL UNIQUE,
    email           TEXT NOT NULL UNIQUE,
    password_hash   TEXT NOT NULL,
    role            TEXT NOT NULL DEFAULT 'user' CHECK (role IN ('user', 'admin')),
    phone_number    TEXT,
    date_of_birth   TEXT,
    country         TEXT,
    created_at      TEXT NOT NULL DEFAULT (datetime('now'))
);

-- 2. WALLET (1 - 1 Own với USER, composite/embedded Bank Account)
CREATE TABLE IF NOT EXISTS wallets (
    wallet_id               INTEGER PRIMARY KEY AUTOINCREMENT,
    account_id              INTEGER NOT NULL UNIQUE,
    balance                 REAL NOT NULL DEFAULT 0.0 CHECK (balance >= 0),
    bank_account_name       TEXT,
    bank_account_pin        TEXT,
    bank_account_activated  TEXT,
    FOREIGN KEY (account_id) REFERENCES users(account_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- 3. GAMES
CREATE TABLE IF NOT EXISTS games (
    game_id         INTEGER PRIMARY KEY AUTOINCREMENT,
    steam_app_id    INTEGER UNIQUE,
    name            TEXT NOT NULL,
    description     TEXT,
    image_url       TEXT,
    base_price      REAL NOT NULL DEFAULT 0.0 CHECK (base_price >= 0),
    final_price     REAL NOT NULL DEFAULT 0.0 CHECK (final_price >= 0),
    discount_check  INTEGER NOT NULL DEFAULT 0 CHECK (discount_check IN (0, 1)),
    rating          REAL DEFAULT 0.0 CHECK (rating >= 0 AND rating <= 5)
);

-- Thuộc tính đa trị Tag của GAMES
CREATE TABLE IF NOT EXISTS game_tags (
    game_id INTEGER NOT NULL,
    tag     TEXT NOT NULL,
    PRIMARY KEY (game_id, tag),
    FOREIGN KEY (game_id) REFERENCES games(game_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- 4. TRANSACTION (1 - N Purchase với WALLET)
CREATE TABLE IF NOT EXISTS transactions (
    transaction_id  INTEGER PRIMARY KEY AUTOINCREMENT,
    wallet_id       INTEGER NOT NULL,
    total_price     REAL NOT NULL DEFAULT 0.0 CHECK (total_price >= 0),
    status          TEXT NOT NULL DEFAULT 'pending',
    date_purchased  TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (wallet_id) REFERENCES wallets(wallet_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Bảng trung gian Purchase (N - M giữa TRANSACTION và GAMES)
CREATE TABLE IF NOT EXISTS purchase_items (
    transaction_id     INTEGER NOT NULL,
    game_id            INTEGER NOT NULL,
    purchase_quantity  INTEGER NOT NULL DEFAULT 1 CHECK (purchase_quantity > 0),
    total_amount       REAL NOT NULL DEFAULT 0.0 CHECK (total_amount >= 0),
    PRIMARY KEY (transaction_id, game_id),
    FOREIGN KEY (transaction_id) REFERENCES transactions(transaction_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (game_id) REFERENCES games(game_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Store cart and checkout records use the active PHP session until auth is connected.
CREATE TABLE IF NOT EXISTS store_orders (
    order_id       INTEGER PRIMARY KEY AUTOINCREMENT,
    session_key    TEXT NOT NULL,
    customer_name  TEXT NOT NULL DEFAULT 'Guest',
    status         TEXT NOT NULL DEFAULT 'pending'
                   CHECK (status IN ('pending', 'processing', 'completed', 'cancelled')),
    total_price    REAL NOT NULL DEFAULT 0 CHECK (total_price >= 0),
    created_at     TEXT NOT NULL DEFAULT (datetime('now')),
    updated_at     TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS store_carts (
    cart_id             INTEGER PRIMARY KEY AUTOINCREMENT,
    session_key         TEXT NOT NULL,
    customer_name       TEXT NOT NULL DEFAULT 'Guest',
    status              TEXT NOT NULL DEFAULT 'active'
                        CHECK (status IN ('active', 'abandoned', 'converted')),
    converted_order_id  INTEGER,
    created_at          TEXT NOT NULL DEFAULT (datetime('now')),
    updated_at          TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (converted_order_id) REFERENCES store_orders(order_id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS store_cart_items (
    cart_id   INTEGER NOT NULL,
    game_id   INTEGER NOT NULL,
    quantity  INTEGER NOT NULL CHECK (quantity > 0),
    PRIMARY KEY (cart_id, game_id),
    FOREIGN KEY (cart_id) REFERENCES store_carts(cart_id)
        ON DELETE CASCADE,
    FOREIGN KEY (game_id) REFERENCES games(game_id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS store_order_items (
    order_item_id  INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id       INTEGER NOT NULL,
    game_id        INTEGER NOT NULL,
    game_name      TEXT NOT NULL,
    unit_price     REAL NOT NULL CHECK (unit_price >= 0),
    quantity       INTEGER NOT NULL CHECK (quantity > 0),
    line_total     REAL NOT NULL CHECK (line_total >= 0),
    FOREIGN KEY (order_id) REFERENCES store_orders(order_id)
        ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_store_carts_session_status
    ON store_carts(session_key, status, cart_id);
CREATE INDEX IF NOT EXISTS idx_store_carts_status_updated
    ON store_carts(status, updated_at);
CREATE INDEX IF NOT EXISTS idx_store_orders_status_created
    ON store_orders(status, created_at);
CREATE INDEX IF NOT EXISTS idx_store_order_items_order
    ON store_order_items(order_id);

-- 5. NEWS
CREATE TABLE IF NOT EXISTS news (
    news_id     INTEGER PRIMARY KEY AUTOINCREMENT,
    name        TEXT NOT NULL,
    duration    INTEGER,
    date_start  TEXT,
    date_end    TEXT
);

-- Quan hệ Relates (M - N giữa GAMES và NEWS)
CREATE TABLE IF NOT EXISTS game_news (
    game_id        INTEGER NOT NULL,
    news_id        INTEGER NOT NULL,
    pct_discounted REAL DEFAULT 0.0 CHECK (pct_discounted >= 0 AND pct_discounted <= 100),
    PRIMARY KEY (game_id, news_id),
    FOREIGN KEY (game_id) REFERENCES games(game_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (news_id) REFERENCES news(news_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Quan hệ Receive (USER nhận/xem NEWS)
CREATE TABLE IF NOT EXISTS user_news (
    account_id  INTEGER NOT NULL,
    news_id     INTEGER NOT NULL,
    received_at TEXT NOT NULL DEFAULT (datetime('now')),
    PRIMARY KEY (account_id, news_id),
    FOREIGN KEY (account_id) REFERENCES users(account_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (news_id) REFERENCES news(news_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- 6. FORUM (1 - N Create Forum với USER)
CREATE TABLE IF NOT EXISTS forums (
    forum_id      INTEGER PRIMARY KEY AUTOINCREMENT,
    account_id    INTEGER NOT NULL,
    title         TEXT NOT NULL,
    content       TEXT,
    media_url     TEXT,
    status        TEXT NOT NULL DEFAULT 'open',
    date_created  TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (account_id) REFERENCES users(account_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Quan hệ Discuss + Reply giữa USER và FORUM
CREATE TABLE IF NOT EXISTS forum_replies (
    reply_id      INTEGER PRIMARY KEY AUTOINCREMENT,
    forum_id      INTEGER NOT NULL,
    account_id    INTEGER NOT NULL,
    text          TEXT NOT NULL,
    media_url     TEXT,
    no_of_reply   INTEGER NOT NULL DEFAULT 0 CHECK (no_of_reply >= 0),
    date_replied  TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (forum_id) REFERENCES forums(forum_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (account_id) REFERENCES users(account_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- 7. REVIEWS (1 - N Has với GAMES, 1 - N Feedback với USER)
CREATE TABLE IF NOT EXISTS reviews (
    review_id     INTEGER PRIMARY KEY AUTOINCREMENT,
    game_id       INTEGER NOT NULL,
    account_id    INTEGER NOT NULL,
    comment       TEXT,
    rating_score  INTEGER NOT NULL CHECK (rating_score >= 1 AND rating_score <= 5),
    date_posted   TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (game_id) REFERENCES games(game_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (account_id) REFERENCES users(account_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Index hỗ trợ truy vấn nhanh
CREATE INDEX IF NOT EXISTS idx_transactions_wallet ON transactions(wallet_id);
CREATE INDEX IF NOT EXISTS idx_forums_account ON forums(account_id);
CREATE INDEX IF NOT EXISTS idx_forum_replies_forum ON forum_replies(forum_id);
CREATE INDEX IF NOT EXISTS idx_forum_replies_account ON forum_replies(account_id);
CREATE INDEX IF NOT EXISTS idx_reviews_game ON reviews(game_id);
CREATE INDEX IF NOT EXISTS idx_reviews_account ON reviews(account_id);