-- Create database
CREATE DATABASE IF NOT EXISTS barber_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE barber_db;

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    login_attempts INT DEFAULT 0,
    locked_until DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- Insert default admin (password: admin123)
INSERT INTO admin_users (username, password_hash, email, full_name) VALUES 
("admin", "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", "admin@barber.com", "מנהל מערכת");

-- Services table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    duration INT NOT NULL DEFAULT 30,
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (is_active)
);

-- Insert default services
INSERT INTO services (name, description, price, duration, display_order) VALUES 
("תספורת גברים", "תספורת מקצועית בסגנון שלך", 70.00, 30, 1),
("תספורת ילדים", "תספורת עדינה לילדים עד גיל 12", 50.00, 25, 2),
("תספורת + זקן", "תספורת מלאה עם עיצוב זקן", 100.00, 45, 3),
("עיצוב זקן בלבד", "עיצוב וגיזום זקן מקצועי", 50.00, 20, 4);

-- Customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    notes TEXT,
    total_visits INT DEFAULT 0,
    last_visit DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_phone (phone),  -- רק אינדקס, לא UNIQUE
    INDEX idx_name (name)
);

-- טבלת תורים - לקוח יכול להופיע כמה פעמים
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    service_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'no_show') DEFAULT 'pending',
    token VARCHAR(64) UNIQUE NOT NULL,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id),
    UNIQUE KEY unique_appointment (appointment_date, appointment_time),  -- זה נשאר!
    INDEX idx_date (appointment_date),
    INDEX idx_status (status),
    INDEX idx_customer (customer_id)
);

-- Business hours table
CREATE TABLE IF NOT EXISTS business_hours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day_of_week INT NOT NULL COMMENT "1=Monday, 7=Sunday",
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    break_start TIME,
    break_end TIME,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_day (day_of_week),
    INDEX idx_day (day_of_week)
);

-- Insert default business hours (Sunday to Thursday)
INSERT INTO business_hours (day_of_week, start_time, end_time, is_active) VALUES 
(1, '09:00:00', '20:00:00', 1),  -- יום ראשון
(2, '09:00:00', '20:00:00', 1),  -- יום שני
(3, '09:00:00', '20:00:00', 1),  -- יום שלישי
(4, '09:00:00', '20:00:00', 1),  -- יום רביעי
(5, '09:00:00', '14:00:00', 1),  -- יום חמישי
(6, '00:00:00', '00:00:00', 0),  -- יום שישי - סגור
(7, '00:00:00', '00:00:00', 0);  -- יום שבת - סגור

-- Blocked dates (holidays, vacations)
CREATE TABLE IF NOT EXISTS blocked_dates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blocked_date DATE NOT NULL,
    reason VARCHAR(255),
    is_full_day BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_blocked_date (blocked_date),
    INDEX idx_date (blocked_date)
);

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM("text", "textarea", "image", "json") DEFAULT "text",
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, setting_type) VALUES 
("site_name", "ספר בראש צעיר", "text"),
("site_logo", "", "image"),
("site_email", "barber@example.com", "text"),
("site_phone", "050-0000000", "text"),
("site_address", "[כתובת תעודכן בהמשך]", "text"),
("whatsapp_number", "050-0000000", "text"),
("instagram_url", "https://instagram.com/", "text"),
("facebook_url", "https://facebook.com/", "text"),
("tiktok_url", "https://tiktok.com/", "text"),
("about_content", "<h3>הסיפור שלי</h3><p>אני מספר מקצועי עם ניסיון של 10 שנים...</p>", "textarea"),
("min_cancel_hours", "12", "text");

-- Rate limits table
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    action VARCHAR(50) NOT NULL,
    attempt_time DATETIME NOT NULL,
    INDEX idx_ip_action (ip_address, action),
    INDEX idx_time (attempt_time)
);

-- Activity logs table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM("admin", "customer") NOT NULL,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    details TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_type, user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
);
