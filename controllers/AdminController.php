<?php
// controllers/AdminController.php
class AdminController {
    
    private $db;
    private $rateLimiter;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->rateLimiter = new RateLimiter();
    }
    
    // ========== AUTHENTICATION ==========
    
    public function login() {
        if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            header("Location: " . SITE_URL . "/index.php?url=admin/dashboard");
            exit;
        }
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
            return;
        }
        
        require_once __DIR__ . "/../views/admin/login.php";
    }
    
    
   private function handleLogin() {
        header('Content-Type: application/json');
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if(!$this->rateLimiter->checkLimit('admin_login', 5, 900)) {
            echo json_encode([
                'success' => false,
                'message' => 'יותר מדי ניסיונות. נסה שוב בעוד 15 דקות.'
            ]);
            exit;
        }
        
        if(empty($username) || empty($password)) {
            echo json_encode([
                'success' => false,
                'message' => 'נא למלא את כל השדות'
            ]);
            exit;
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = 1");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['full_name'] ?? $username;
                
                $stmt = $this->db->prepare("UPDATE admin_users SET last_login = NOW(), login_attempts = 0 WHERE id = ?");
                $stmt->execute([$admin['id']]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'התחברת בהצלחה!',
                    'redirect' => SITE_URL . '/index.php?url=admin/dashboard'
                ]);
                exit;
            }
            
            if($admin) {
                $stmt = $this->db->prepare("UPDATE admin_users SET login_attempts = login_attempts + 1 WHERE username = ?");
                $stmt->execute([$username]);
            }
            
            echo json_encode([
                'success' => false,
                'message' => 'שם משתמש או סיסמה שגויים'
            ]);
            exit;
            
        } catch(Exception $e) {
            error_log("Login error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'שגיאת מערכת. אנא נסה שוב מאוחר יותר.'
            ]);
            exit;
        }
    }
    
    public function logout() {
        $_SESSION = array();
        session_destroy();
        header("Location: " . SITE_URL . "/index.php?url=admin/login");
        exit;
    }
    
    // ========== DASHBOARD ==========
    
    public function dashboard() {
        $this->checkAuth();
        $stats = $this->getDashboardStats();
        $upcoming = $this->getUpcomingAppointments();
        require_once __DIR__ . "/../views/admin/dashboard.php";
    }
    
    
    // ========== APPOINTMENTS ==========
    
    public function appointments() {
        $this->checkAuth();
        $appointments = $this->getAppointments();
        require_once __DIR__ . "/../views/admin/appointments.php";
    }
    


public function updateAppointment() {
    $this->checkAuth();
    
    // ✅ הוסף debug
    error_log("=== updateAppointment called ===");
    error_log("POST: " . print_r($_POST, true));
    
    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? '';
    $customer_name = $_POST['customer_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $service_id = $_POST['service_id'] ?? 0;
    $appointment_date = $_POST['appointment_date'] ?? '';
    $appointment_time = $_POST['appointment_time'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    // ✅ debug isAjax
    $isAjax = $this->isAjax();
    error_log("isAjax: " . ($isAjax ? 'true' : 'false'));
    
    if(empty($id)) {
        error_log("No ID provided");
        if($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'תור לא נמצא']);
            exit;
        }
        $_SESSION['admin_error'] = "תור לא נמצא";
        header("Location: " . SITE_URL . "/index.php?url=admin/appointments");
        exit;
    }
    
    try {
        // If only status is provided (quick status update)
        if($status && empty($customer_name) && empty($phone)) {
            error_log("Status only update: ID=$id, Status=$status");
            $stmt = $this->db->prepare("UPDATE appointments SET status = ?, updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([$status, $id]);
            
            error_log("Update result: " . ($result ? 'success' : 'failed'));
            
            if($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }
            
            $_SESSION['admin_success'] = "סטטוס התור עודכן בהצלחה!";
            header("Location: " . SITE_URL . "/index.php?url=admin/appointments");
            exit;
        }
        
        // ... שאר הקוד ...
        
    } catch(Exception $e) {
        error_log("Update error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        if($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
        
        $_SESSION['admin_error'] = "שגיאה בעדכון התור: " . $e->getMessage();
        header("Location: " . SITE_URL . "/index.php?url=admin/appointments");
        exit;
    }
}

// ✅ DELETE APPOINTMENT
public function deleteAppointment($id) {
    $this->checkAuth();
    
    $isAjax = $this->isAjax();
    
    try {
        $stmt = $this->db->prepare("SELECT * FROM appointments WHERE id = ?");
        $stmt->execute([$id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$appointment) {
            if($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'תור לא נמצא']);
                exit;
            }
            $_SESSION['admin_error'] = "תור לא נמצא";
            header("Location: " . SITE_URL . "/index.php?url=admin/appointments");
            exit;
        }
        
        $stmt = $this->db->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->execute([$id]);
        
        if($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }
        
        $_SESSION['admin_success'] = "התור נמחק בהצלחה!";
    } catch(Exception $e) {
        error_log("Delete appointment error: " . $e->getMessage());
        
        if($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
        
        $_SESSION['admin_error'] = "שגיאה במחיקת התור";
    }
    
    header("Location: " . SITE_URL . "/index.php?url=admin/appointments");
    exit;
}

private function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
    
    // ========== SERVICES ==========
    
    public function services() {
        $this->checkAuth();
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->saveService();
            return;
        }
        $services = $this->getServices();
        require_once __DIR__ . "/../views/admin/services.php";
    }
    
    
    public function deleteService($id) {
        $this->checkAuth();
        
        try {
            $stmt = $this->db->prepare("SELECT image FROM services WHERE id = ?");
            $stmt->execute([$id]);
            $service = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stmt = $this->db->prepare("DELETE FROM services WHERE id = ?");
            $stmt->execute([$id]);
            
            if(!empty($service['image'])) {
                $imagePath = __DIR__ . '/../uploads/Images/' . $service['image'];
                if(file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $_SESSION['admin_success'] = "השירות נמחק בהצלחה!";
        } catch(Exception $e) {
            error_log("Delete service error: " . $e->getMessage());
            $_SESSION['admin_error'] = "שגיאה במחיקת השירות";
        }
        
        header("Location: " . SITE_URL . "/index.php?url=admin/services");
        exit;
    }
    
    private function saveService() {
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? 0;
        $duration = $_POST['duration'] ?? 30;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $existingImage = $_POST['existing_image'] ?? '';
        
        $imageName = $existingImage;
        if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/Images/';
            if(!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $file = $_FILES['image'];
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if(in_array(strtolower($extension), $allowedExtensions)) {
                $imageName = time() . '_' . uniqid() . '.' . $extension;
                $targetPath = $uploadDir . $imageName;
                
                if(move_uploaded_file($file['tmp_name'], $targetPath)) {
                    if(!empty($existingImage) && $existingImage != $imageName) {
                        $oldPath = $uploadDir . $existingImage;
                        if(file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                } else {
                    $imageName = $existingImage;
                }
            }
        }
        
        try {
            if($id) {
                $stmt = $this->db->prepare("UPDATE services SET 
                    name = ?, 
                    description = ?, 
                    price = ?, 
                    duration = ?, 
                    is_active = ?, 
                    image = ? 
                    WHERE id = ?");
                $stmt->execute([$name, $description, $price, $duration, $is_active, $imageName, $id]);
                $_SESSION['admin_success'] = "השירות עודכן בהצלחה!";
            } else {
                $stmt = $this->db->prepare("INSERT INTO services 
                    (name, description, price, duration, display_order, is_active, image) 
                    VALUES (?, ?, ?, ?, (SELECT COALESCE(MAX(display_order), 0) + 1 FROM services s2), ?, ?)");
                $stmt->execute([$name, $description, $price, $duration, $is_active, $imageName]);
                $_SESSION['admin_success'] = "השירות נוצר בהצלחה!";
            }
        } catch(Exception $e) {
            error_log("Save service error: " . $e->getMessage());
            $_SESSION['admin_error'] = "שגיאה בשמירת השירות";
        }
        
        header("Location: " . SITE_URL . "/index.php?url=admin/services");
        exit;
    }
    
    // ========== CUSTOMERS ==========
    
    public function customers() {
        $this->checkAuth();
        $customers = $this->getCustomers();
        require_once __DIR__ . "/../views/admin/customers.php";
    }
    
  
    public function customerDetails($id) {
        $this->checkAuth();
        $customer = $this->getCustomerById($id);
        $appointments = $this->getCustomerAppointments($id);
        require_once __DIR__ . "/../views/admin/customer_details.php";
    }
    
    // ========== SCHEDULE ==========
    
    public function schedule() {
        $this->checkAuth();
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->saveSchedule();
            return;
        }
        $hours = $this->getBusinessHours();
        $blockedDates = $this->getBlockedDates();
        require_once __DIR__ . "/../views/admin/schedule.php";
    }
    
    
    public function addBlockedDate() {
        $this->checkAuth();
        $date = $_POST['date'] ?? '';
        $reason = $_POST['reason'] ?? '';
        
        try {
            $stmt = $this->db->prepare("INSERT INTO blocked_dates (blocked_date, reason) VALUES (?, ?)");
            $stmt->execute([$date, $reason]);
            $_SESSION['admin_success'] = "היום החסום נוסף בהצלחה!";
        } catch(Exception $e) {
            error_log("Add blocked date error: " . $e->getMessage());
            $_SESSION['admin_error'] = "שגיאה בהוספת היום החסום";
        }
        
        header("Location: " . SITE_URL . "/index.php?url=admin/schedule");
        exit;
    }
    
    public function deleteBlockedDate($id) {
        $this->checkAuth();
        try {
            $stmt = $this->db->prepare("DELETE FROM blocked_dates WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['admin_success'] = "היום החסום נמחק בהצלחה!";
        } catch(Exception $e) {
            error_log("Delete blocked date error: " . $e->getMessage());
            $_SESSION['admin_error'] = "שגיאה במחיקת היום החסום";
        }
        header("Location: " . SITE_URL . "/index.php?url=admin/schedule");
        exit;
    }
    
    private function saveSchedule() {
        try {
            foreach($_POST['hours'] as $day => $data) {
                $start = $data['start'] ?? '00:00:00';
                $end = $data['end'] ?? '00:00:00';
                $is_active = isset($data['active']) ? 1 : 0;
                
                $stmt = $this->db->prepare("UPDATE business_hours SET start_time = ?, end_time = ?, is_active = ? WHERE day_of_week = ?");
                $stmt->execute([$start, $end, $is_active, $day]);
            }
            $_SESSION['admin_success'] = "שעות הפעילות נשמרו בהצלחה!";
        } catch(Exception $e) {
            error_log("Save schedule error: " . $e->getMessage());
            $_SESSION['admin_error'] = "שגיאה בשמירת שעות הפעילות";
        }
        
        header("Location: " . SITE_URL . "/index.php?url=admin/schedule");
        exit;
    }
    
    // ========== SETTINGS ==========
    
    public function settings() {
        $this->checkAuth();
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->saveSettings();
            return;
        }
        $settings = $this->getSettings();
        require_once __DIR__ . "/../views/admin/settings.php";
    }
    
    private function saveSettings() {
        try {
            foreach($_POST as $key => $value) {
                if($key == 'submit') continue;
                
                $stmt = $this->db->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$value, $key]);
            }
            $_SESSION['admin_success'] = "ההגדרות נשמרו בהצלחה!";
        } catch(Exception $e) {
            error_log("Save settings error: " . $e->getMessage());
            $_SESSION['admin_error'] = "שגיאה בשמירת ההגדרות";
        }
        
        header("Location: " . SITE_URL . "/index.php?url=admin/settings");
        exit;
    }
    
    // ========== LOGS ==========
    
    public function logs() {
        $this->checkAuth();
        $logs = $this->getLogs();
        require_once __DIR__ . "/../views/admin/logs.php";
    }
    
    
    public function clearLogs() {
        $this->checkAuth();
        $logFile = __DIR__ . "/../logs/system.log";
        if(file_exists($logFile)) {
            file_put_contents($logFile, '');
            $_SESSION['admin_success'] = "הלוגים נוקו בהצלחה!";
        }
        header("Location: " . SITE_URL . "/index.php?url=admin/logs");
        exit;
    }
    
    // ========== DATA RETRIEVAL METHODS ==========
    
    private function getDashboardStats() {
        $stats = [
            'today_appointments' => 0,
            'week_appointments' => 0,
            'new_customers' => 0,
            'cancelled' => 0,
            'revenue' => 0,
            'total_customers' => 0,
            'completed' => 0
        ];
        
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM appointments WHERE appointment_date = CURDATE() AND status != 'cancelled'");
            $stmt->execute();
            $stats['today_appointments'] = $stmt->fetch()['count'] ?? 0;
            
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM appointments WHERE YEARWEEK(appointment_date) = YEARWEEK(CURDATE()) AND status != 'cancelled'");
            $stmt->execute();
            $stats['week_appointments'] = $stmt->fetch()['count'] ?? 0;
            
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM customers WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
            $stmt->execute();
            $stats['new_customers'] = $stmt->fetch()['count'] ?? 0;
            
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM customers");
            $stmt->execute();
            $stats['total_customers'] = $stmt->fetch()['count'] ?? 0;
            
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM appointments WHERE status = 'cancelled' AND MONTH(appointment_date) = MONTH(CURDATE()) AND YEAR(appointment_date) = YEAR(CURDATE())");
            $stmt->execute();
            $stats['cancelled'] = $stmt->fetch()['count'] ?? 0;
            
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM appointments WHERE status = 'completed' AND MONTH(appointment_date) = MONTH(CURDATE()) AND YEAR(appointment_date) = YEAR(CURDATE())");
            $stmt->execute();
            $stats['completed'] = $stmt->fetch()['count'] ?? 0;
            
            $stmt = $this->db->prepare("SELECT COALESCE(SUM(s.price), 0) as total FROM appointments a JOIN services s ON a.service_id = s.id WHERE MONTH(a.appointment_date) = MONTH(CURDATE()) AND YEAR(a.appointment_date) = YEAR(CURDATE()) AND a.status = 'completed'");
            $stmt->execute();
            $stats['revenue'] = $stmt->fetch()['total'] ?? 0;
            
        } catch(Exception $e) {
            error_log("Stats error: " . $e->getMessage());
        }
        
        return $stats;
    }
    
    private function getUpcomingAppointments() {
        try {
            $stmt = $this->db->prepare("SELECT a.*, c.name, c.phone, s.name as service_name 
                                         FROM appointments a 
                                         JOIN customers c ON a.customer_id = c.id 
                                         JOIN services s ON a.service_id = s.id 
                                         WHERE a.appointment_date >= CURDATE() AND a.status != 'cancelled'
                                         ORDER BY a.appointment_date ASC, a.appointment_time ASC 
                                         LIMIT 10");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(Exception $e) {
            error_log("Upcoming appointments error: " . $e->getMessage());
            return [];
        }
    }
    
    private function getAppointments() {
        try {
            $filter = $_GET['filter'] ?? 'all';
            $search = $_GET['search'] ?? '';
            
            $query = "SELECT a.*, c.name, c.phone, s.name as service_name 
                      FROM appointments a 
                      JOIN customers c ON a.customer_id = c.id 
                      JOIN services s ON a.service_id = s.id";
            
            $params = [];
            
            if($filter == 'today') {
                $query .= " WHERE a.appointment_date = CURDATE()";
            } elseif($filter == 'upcoming') {
                $query .= " WHERE a.appointment_date >= CURDATE() AND a.status != 'cancelled'";
            } elseif($filter == 'cancelled') {
                $query .= " WHERE a.status = 'cancelled'";
            } elseif($search) {
                $query .= " WHERE c.name LIKE :search OR c.phone LIKE :search";
                $params[':search'] = "%$search%";
            }
            
            $query .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch(Exception $e) {
            error_log("Appointments error: " . $e->getMessage());
            return [];
        }
    }
    
    private function getServices() {
        try {
            $stmt = $this->db->query("SELECT * FROM services ORDER BY display_order");
            return $stmt->fetchAll();
        } catch(Exception $e) {
            error_log("Services error: " . $e->getMessage());
            return [];
        }
    }
    
    private function getCustomers() {
        try {
            $search = $_GET['search'] ?? '';
            $query = "SELECT *, (SELECT COUNT(*) FROM appointments WHERE customer_id = customers.id) as total_visits FROM customers";
            
            if($search) {
                $query .= " WHERE name LIKE :search OR phone LIKE :search";
                $stmt = $this->db->prepare($query);
                $stmt->execute([':search' => "%$search%"]);
            } else {
                $stmt = $this->db->query($query);
            }
            
            return $stmt->fetchAll();
        } catch(Exception $e) {
            error_log("Customers error: " . $e->getMessage());
            return [];
        }
    }
    
    private function getCustomerById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(Exception $e) {
            error_log("Customer error: " . $e->getMessage());
            return null;
        }
    }
    
    private function getCustomerAppointments($id) {
        try {
            $stmt = $this->db->prepare("SELECT a.*, s.name as service_name FROM appointments a JOIN services s ON a.service_id = s.id WHERE a.customer_id = ? ORDER BY a.appointment_date DESC");
            $stmt->execute([$id]);
            return $stmt->fetchAll();
        } catch(Exception $e) {
            error_log("Customer appointments error: " . $e->getMessage());
            return [];
        }
    }
    
    private function getBusinessHours() {
        try {
            $stmt = $this->db->query("SELECT * FROM business_hours ORDER BY day_of_week");
            return $stmt->fetchAll();
        } catch(Exception $e) {
            error_log("Business hours error: " . $e->getMessage());
            return [];
        }
    }
    
    private function getBlockedDates() {
        try {
            $stmt = $this->db->query("SELECT * FROM blocked_dates ORDER BY blocked_date DESC LIMIT 50");
            return $stmt->fetchAll();
        } catch(Exception $e) {
            error_log("Blocked dates error: " . $e->getMessage());
            return [];
        }
    }
    
    private function getSettings() {
        try {
            $stmt = $this->db->query("SELECT * FROM settings");
            $settings = [];
            while($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            return $settings;
        } catch(Exception $e) {
            error_log("Settings error: " . $e->getMessage());
            return [];
        }
    }
    
    private function getLogs() {
        $logFile = __DIR__ . "/../logs/system.log";
        $logs = [];
        if(file_exists($logFile)) {
            $content = file_get_contents($logFile);
            $lines = explode("\n", $content);
            $logs = array_reverse(array_slice($lines, -500));
        }
        return $logs;
    }
    
    // ========== HELPERS ==========
    
    private function checkAuth() {
        if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header("Location: " . SITE_URL . "/index.php?url=admin/login");
            exit;
        }
    }
    
}
?>