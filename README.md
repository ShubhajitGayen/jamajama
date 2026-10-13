# Jama Jama Tour & Travels (PHP + MySQL)
1. Create DB + tables: `mysql -u root -p < schema.sql` (needs MySQL 5.7+/MariaDB 10.2+).
2. For an existing database, back it up and run the migrations that have not yet been applied. The package-image migration is `mysql --default-character-set=utf8mb4 -u root -p jamajama < migrations/20261013_add_package_image.sql` (run once; it adds nullable `packages.image_path`). Also apply the translations and gallery migrations if needed: `mysql --default-character-set=utf8mb4 -u root -p jamajama < migrations/20261013_add_content_translations.sql` and `mysql --default-character-set=utf8mb4 -u root -p jamajama < migrations/20261013_add_gallery_items.sql`. Do not run a migration if its columns/table already exist.
3. Edit `config.php` with your DB credentials, WhatsApp number, phone, email.
4. Create an admin: `php create_admin.php yourname 'a-long-password'`.
5. Make `uploads/` writable by the web server (755/775, never 777); the application creates `uploads/packages/` as needed. Apache: `.htaccess` blocks script execution there. On Nginx add: `location ^~ /uploads/ { location ~ \.php$ { return 403; } }`.
6. Video limit: `.user.ini` asks for 200 MB; hosts may override. The admin shows the real server limit and gives a clear error if exceeded.
7. Visit `/admin/login.php`. Manage package details and images, including Bengali/Hindi package text, at `/admin/packages.php`; add translated banner copy in `/admin/banners.php`; upload and manage gallery images/videos at `/admin/gallery.php`.
Use HTTPS in production.
