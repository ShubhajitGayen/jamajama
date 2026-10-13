CREATE TABLE IF NOT EXISTS gallery_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  media_type ENUM('image','video') NOT NULL,
  title VARCHAR(150) NULL,
  media_path VARCHAR(255) NOT NULL,
  display_order INT UNSIGNED NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_gallery_order (display_order, id)
) ENGINE=InnoDB;
