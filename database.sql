-- Run this once in phpMyAdmin (XAMPP) to create the database.
CREATE DATABASE IF NOT EXISTS booking_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE booking_app;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
  setting_key VARCHAR(100) PRIMARY KEY,
  setting_value LONGTEXT
);

CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  duration_minutes INT NOT NULL DEFAULT 30,
  price VARCHAR(50) DEFAULT '',
  sort_order INT DEFAULT 0,
  active TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS gallery_media (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('image','video') NOT NULL,
  filename VARCHAR(255) NOT NULL,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  service_id INT NOT NULL,
  customer_name VARCHAR(150) NOT NULL,
  customer_phone VARCHAR(30) NOT NULL,
  customer_email VARCHAR(150) NULL,
  booking_date DATE NOT NULL,
  booking_time TIME NOT NULL,
  status ENUM('confirmed','cancelled') NOT NULL DEFAULT 'confirmed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (service_id) REFERENCES services(id)
);

CREATE TABLE IF NOT EXISTS push_subscriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id INT NOT NULL,
  endpoint TEXT NOT NULL,
  endpoint_hash CHAR(64) NOT NULL,
  p256dh VARCHAR(255) NOT NULL,
  auth VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY endpoint_hash_unique (endpoint_hash),
  FOREIGN KEY (admin_id) REFERENCES admins(id)
);

-- Seed default services (matches the original design's placeholder services)
INSERT INTO services (name, duration_minutes, price, sort_order, active) VALUES
('תספורת קלאסית', 40, '', 1, 1),
('עיצוב זקן', 25, '', 2, 1),
('תספורת + זקן', 60, '', 3, 1),
('תספורת ילדים', 30, '', 4, 1);

-- Seed default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('owner_name', 'איתי וקנין'),
('tagline', 'ספר מעולם אחר'),
('phone', ''),
('whatsapp_phone', ''),
('email', ''),
('address', ''),
('instagram_url', ''),
('facebook_url', ''),
('tiktok_url', ''),
('slot_interval_minutes', '30'),
('working_hours', '{"0":{"closed":false,"open":"10:00","close":"20:00"},"1":{"closed":false,"open":"10:00","close":"20:00"},"2":{"closed":false,"open":"10:00","close":"20:00"},"3":{"closed":false,"open":"10:00","close":"20:00"},"4":{"closed":false,"open":"10:00","close":"20:00"},"5":{"closed":false,"open":"09:00","close":"15:00"},"6":{"closed":true,"open":"","close":""}}'),
('legal_privacy_text', 'טיוטת מדיניות פרטיות — יש לערוך בפאנל הניהול.'),
('legal_terms_text', 'טיוטת תקנון האתר — יש לערוך בפאנל הניהול.'),
('admin_notification_email', ''),
('mail_enabled', '0'),
('smtp_host', ''),
('smtp_port', '587'),
('smtp_username', ''),
('smtp_password', ''),
('smtp_secure', 'tls'),
('smtp_from_email', ''),
('smtp_from_name', '');
