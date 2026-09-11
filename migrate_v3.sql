-- Run this in phpMyAdmin (SQL tab, on the booking_app database) to upgrade an EXISTING
-- installation. If you're setting up fresh, just use the updated database.sql instead —
-- it already includes these changes, so you don't need to also run this file.

USE booking_app;

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
