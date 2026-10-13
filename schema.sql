CREATE DATABASE IF NOT EXISTS jamajama CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jamajama;
CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS hero_videos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description TEXT NULL,
  video_path VARCHAR(255) NOT NULL,
  poster_path VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 0,
  -- NULL unless active; UNIQUE index guarantees at most one active video
  active_flag TINYINT(1) GENERATED ALWAYS AS (IF(is_active=1,1,NULL)) STORED,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_one_active (active_flag)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS hero_banners (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  title_bn VARCHAR(150) NULL,
  title_hi VARCHAR(150) NULL,
  subtitle VARCHAR(200) NULL,
  subtitle_bn VARCHAR(200) NULL,
  subtitle_hi VARCHAR(200) NULL,
  description TEXT NULL,
  description_bn TEXT NULL,
  description_hi TEXT NULL,
  image_path VARCHAR(255) NOT NULL,
  button_text VARCHAR(60) NULL,
  button_text_bn VARCHAR(60) NULL,
  button_text_hi VARCHAR(60) NULL,
  button_url VARCHAR(255) NULL,
  display_order INT UNSIGNED NOT NULL DEFAULT 1,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_banner_public (is_active, display_order)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS packages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  name_bn VARCHAR(100) NULL, name_hi VARCHAR(100) NULL,
  line1 VARCHAR(100), line2 VARCHAR(100), line3 VARCHAR(100),
  line1_bn VARCHAR(100), line1_hi VARCHAR(100),
  line2_bn VARCHAR(100), line2_hi VARCHAR(100),
  line3_bn VARCHAR(100), line3_hi VARCHAR(100),
  price INT UNSIGNED NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  image_path VARCHAR(255) NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS gallery_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  media_type ENUM('image','video') NOT NULL,
  title VARCHAR(150) NULL,
  media_path VARCHAR(255) NOT NULL,
  display_order INT UNSIGNED NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_gallery_order (display_order, id)
) ENGINE=InnoDB;
INSERT INTO packages (name,line1,line2,line3,price,sort_order,name_hi,name_bn,line1_hi,line1_bn,line2_hi,line2_bn,line3_hi,line3_bn)
SELECT * FROM (
 SELECT 'Economy Umrah','7 Days / 6 Nights','3★ Hotel (Near Haram)','Visa Assistance',65000,1,'किफायती उमराह','सাশ্রয়ী ওমরাহ','7 दिन / 6 रात','৭ দিন / ৬ রাত','3★ होटल (हरम के पास)','৩★ হোটেল (হারামের কাছে)','वीज़ा सहायता','ভিসা সহায়তা' UNION ALL
 SELECT 'Standard Umrah','10 Days / 9 Nights','4★ Hotel (Near Haram)','Transport Included',85000,2,'स्टैंडर्ड उमराह','স্ট্যান্ডার্ড ওমরাহ','10 दिन / 9 रात','১০ দিন / ৯ রাত','4★ होटल (हरम के पास)','৪★ হোটেল (হারামের কাছে)','परिवहन शामिल','পরিবহন অন্তর্ভুক্ত' UNION ALL
 SELECT 'Premium Umrah','12 Days / 11 Nights','5★ Hotel (Near Haram)','VIP Transport',115000,3,'प्रीमियम उमराह','প্রিমিয়াম ওমরাহ','12 दिन / 11 रात','১২ দিন / ১১ রাত','5★ होटल (हरम के पास)','৫★ হোটেল (হারামের কাছে)','वीआईपी परिवहन','ভিআইপি পরিবহন' UNION ALL
 SELECT 'Hajj Package','15 Days / 14 Nights','4★ Hotel (Near Haram)','Full Guidance',450000,4,'हज पैकेज','হজ প্যাকেজ','15 दिन / 14 रात','১৫ দিন / ১৪ রাত','4★ होटल (हरम के पास)','৪★ হোটেল (হারামের কাছে)','पूरा मार्गदर्शन','সম্পূর্ণ গাইডেন্স'
) t WHERE NOT EXISTS (SELECT 1 FROM packages);
CREATE TABLE IF NOT EXISTS gallery_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  media_type ENUM('image','video') NOT NULL,
  title VARCHAR(150) NULL,
  media_path VARCHAR(255) NOT NULL,
  display_order INT UNSIGNED NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_gallery_order (display_order, id)
) ENGINE=InnoDB;
