PRAGMA foreign_keys = ON;
-- Seed users & wallets mẫu để gắn khóa ngoại
INSERT OR IGNORE INTO users (
        account_id,
        user_name,
        email,
        password_hash,
        role,
        phone_number,
        country
    ) VALUES (
        1,
        'admin',
        'admin@steamproject.local',
        '$2y$12$H0IG.X5DqRiM5zeL893eR.1Fm/9TZSHbaiMING5h/6W3shf.eRe9G',
        'admin',
        '0900000001',
        'VN'
    );
INSERT OR IGNORE INTO users (
        account_id,
        user_name,
        email,
        password_hash,
        role,
        phone_number,
        country
    ) VALUES (
        2,
        'player1',
        'player1@steamproject.local',
        '$2y$12$qbPxPZ4dKfSzt/aGTVi3ReZEdc34e.WKzntyDW2hOFJ/48mL3eu5W',
        'user',
        '0900000002',
        'VN'
    );
INSERT OR IGNORE INTO wallets (
        wallet_id,
        account_id,
        balance,
        bank_account_activated
    ) VALUES (
        1,
        1,
        5000000,
        'yes'
    );
INSERT OR IGNORE INTO wallets (
        wallet_id,
        account_id,
        balance,
        bank_account_activated
    ) VALUES (
        2,
        2,
        1000000,
        'yes'
    );

INSERT OR REPLACE INTO games (game_id, name, description, base_price, final_price, discount_check, rating) VALUES (1, 'Counter-Strike 2', 'Trong hai thập kỷ qua, Counter-Strike đã mang lại trải nghiệm thi đấu cao cấp, được nhào nặn bởi hàng triệu người chơi trên toàn cầu. Và giờ chương tiếp theo của câu chuyện CS chuẩn bị khởi màn. Đây là Counter-Strike 2.', 0.00, 0.00, 0, 4.5);
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (1, 'Hành động');
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (1, 'Chơi miễn phí');
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (11, 'Counter-Strike 2 Update', 7, '2026-11-23 23:02:07', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (1, 11, 0.00);
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (12, 'Counter-Strike 2 Update', 7, '2026-11-19 00:57:59', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (1, 12, 0.00);
INSERT OR REPLACE INTO games (game_id, name, description, base_price, final_price, discount_check, rating) VALUES (2, 'Dota 2', 'Mỗi ngày, hàng triệu người chơi toàn cầu nhập vai một trong hàng trăm tướng Dota để tham chiến. Và dù là giờ chơi thứ 10 hay 10000, luôn có điều mới để khám phá. Được cập nhật thường xuyên để lối chơi, tính năng và tướng luôn tiến hóa, Dota 2 như có sức sống của riêng mình.', 0.00, 0.00, 0, 4.5);
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (2, 'Hành động');
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (2, 'Chiến thuật');
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (2, 'Chơi miễn phí');
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (21, '7.42a Gameplay Patch', 7, '2026-11-24 23:39:04', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (2, 21, 0.00);
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (22, '7.42 Gameplay Update', 7, '2026-11-18 20:00:46', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (2, 22, 0.00);
INSERT OR REPLACE INTO games (game_id, name, description, base_price, final_price, discount_check, rating) VALUES (3, 'ELDEN RING', 'THE CRITICALLY ACCLAIMED FANTASY ACTION RPG. Rise, Tarnished, and be guided by grace to brandish the power of the Elden Ring and become an Elden Lord in the Lands Between.', 990000.00, 990000.00, 0, 4.5);
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (3, 'Hành động');
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (3, 'Nhập vai (RPG)');
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (31, 'Alexander, Warrior Jar Artisan Keycap has arrived!', 7, '2026-10-30 09:39:00', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (3, 31, 0.00);
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (32, 'STARSCOURGE RADAHN JOINS THE ELDEN RING FIGUARTS MINI SERIES', 7, '2026-10-05 10:00:24', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (3, 32, 0.00);
INSERT OR REPLACE INTO games (game_id, name, description, base_price, final_price, discount_check, rating) VALUES (4, 'Stardew Valley', 'You''ve inherited your grandfather''s old farm plot in Stardew Valley. Armed with hand-me-down tools and a few coins, you set out to begin your new life. Can you learn to live off the land and turn these overgrown fields into a thriving home?', 165000.00, 165000.00, 0, 4.5);
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (4, 'Indie');
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (4, 'Nhập vai (RPG)');
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (4, 'Mô phỏng');
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (41, 'Stardew Valley Merchandise Spotlight', 7, '2026-11-20 21:02:25', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (4, 41, 0.00);
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (42, 'Stardew Valley 10-year Anniversary', 7, '2026-02-26 23:40:00', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (4, 42, 0.00);
INSERT OR REPLACE INTO games (game_id, name, description, base_price, final_price, discount_check, rating) VALUES (5, 'Cyberpunk 2077', 'Cyberpunk 2077 is an open-world, action-adventure RPG set in the dark future of Night City — a dangerous megalopolis obsessed with power, glamor, and ceaseless body modification.', 990000.00, 990000.00, 0, 4.5);
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (5, 'Nhập vai (RPG)');
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (51, 'REDstreams – Cyberpunk: Edgerunners 2', 7, '2026-10-26 14:39:00', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (5, 51, 0.00);
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (52, 'Cyberpunk 2077''s Edgerunners 2 Update (2.5) is out now!', 7, '2026-10-22 10:06:54', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (5, 52, 0.00);
INSERT OR REPLACE INTO games (game_id, name, description, base_price, final_price, discount_check, rating) VALUES (6, 'Slay the Spire 2', 'Tựa game xây dựng thẻ bài phong cách roguelike đỉnh cao nay đã có hậu bản! Xây dựng bộ bài độc đáo, chạm trán các sinh vật kỳ lạ và khám phá những cổ vật với sức mạnh vô song trong Slay the Spire 2!', 312000.00, 249500.00, 1, 4.5);
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (6, 'Indie');
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (6, 'Chiến thuật');
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (6, 'Truy cập sớm');
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (61, 'Beta Patch Notes - v0.114.0', 7, '2026-11-25 01:42:13', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (6, 61, 20.00);
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (62, 'Major Update #3 - v0.111.1', 7, '2026-11-17 01:07:11', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (6, 62, 20.00);
INSERT OR REPLACE INTO games (game_id, name, description, base_price, final_price, discount_check, rating) VALUES (7, 'Rain World', 'You are a nomadic slugcat, both predator and prey in a broken ecosystem. Grab your spear and brave the industrial wastes, hunting enough food to survive, but be wary— other, bigger creatures have the same plan... and slugcats look delicious.', 312500.00, 312500.00, 0, 4.5);
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (7, 'Hành động');
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (7, 'Phiêu lưu');
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (7, 'Indie');
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (71, 'Lost in Cult Design Works Book!', 7, '2026-09-29 17:00:57', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (7, 71, 0.00);
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (72, 'New Merch! Rain World: Anthology Edition', 7, '2026-09-06 19:30:24', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (7, 72, 0.00);
INSERT OR REPLACE INTO games (game_id, name, description, base_price, final_price, discount_check, rating) VALUES (8, 'Hollow Knight', 'Forge your own path in Hollow Knight! An epic action adventure through a vast ruined kingdom of insects and heroes. Explore twisting caverns, battle tainted creatures and befriend bizarre bugs, all in a classic, hand-drawn 2D style.', 165000.00, 165000.00, 0, 4.5);
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (8, 'Hành động');
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (8, 'Phiêu lưu');
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (8, 'Indie');
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (81, 'Patch Version 1.5.12620 Now Live', 7, '2026-03-27 04:25:36', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (8, 81, 0.00);
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (82, 'Patch Version 1.5.12459 Now Live', 7, '2026-02-05 23:28:25', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (8, 82, 0.00);
INSERT OR REPLACE INTO games (game_id, name, description, base_price, final_price, discount_check, rating) VALUES (9, 'Hollow Knight: Silksong', 'Discover a vast, haunted kingdom in Hollow Knight: Silksong! Explore, fight and survive as you ascend to the peak of a land ruled by silk and song.', 220000.00, 220000.00, 0, 4.5);
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (9, 'Hành động');
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (9, 'Phiêu lưu');
INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (9, 'Indie');
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (91, 'Patch Version 1.0.30000 Now Live', 7, '2026-03-16 03:24:05', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (9, 91, 0.00);
INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (92, 'Hollow Knight: Silksong - Sea of Sorrow is coming in 2026!', 7, '2025-12-16 03:37:23', NULL);
INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (9, 92, 0.00);
