-- Run this in phpMyAdmin (SQL tab, on the booking_app database) to upgrade an EXISTING
-- installation. If you're setting up fresh, just use the updated database.sql instead —
-- it already includes these changes, so you don't need to also run this file.

USE booking_app;

ALTER TABLE bookings
  ADD COLUMN customer_email VARCHAR(150) NULL AFTER customer_phone,
  ADD COLUMN status ENUM('confirmed','cancelled') NOT NULL DEFAULT 'confirmed' AFTER booking_time;

INSERT INTO settings (setting_key, setting_value) VALUES
('admin_notification_email', ''),
('mail_enabled', '0'),
('smtp_host', ''),
('smtp_port', '587'),
('smtp_username', ''),
('smtp_password', ''),
('smtp_secure', 'tls'),
('smtp_from_email', ''),
('smtp_from_name', '')
ON DUPLICATE KEY UPDATE setting_value = setting_value;
