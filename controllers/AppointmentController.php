<?php
// controllers/AppointmentController.php
class AppointmentController {
    
    // פונקציה להצגת טופס הזמנת תור
    public function index() {
        require_once __DIR__ . "/../views/booking.php";
    }
    
    // פונקציה לקבלת שעות פנויות (API)
    public function getAvailableSlots() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        
        error_log(">>  getAvailableSlots");
        try {
            $serviceId = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;
            $date = isset($_GET['date']) ? $_GET['date'] : '';
            
            if(!$serviceId || !$date) {
                echo json_encode([]);
                return;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // קבלת משך השירות
            $stmt = $db->prepare("SELECT duration FROM services WHERE id = ? AND is_active = 1");
            $stmt->execute([$serviceId]);
            $service = $stmt->fetch();
            
            if(!$service) {
                echo json_encode([]);
                return;
            }
            
            // המרת תאריך ליום בשבוע (1=שני, 7=ראשון)
            $dayOfWeek = date('N', strtotime($date));
            
            // קבלת שעות פעילות
            $stmt = $db->prepare("SELECT start_time, end_time FROM business_hours WHERE day_of_week = ? AND is_active = 1");
            $stmt->execute([$dayOfWeek]);
            $hours = $stmt->fetch();
            
            if(!$hours) {
                echo json_encode([]);
                return;
            }
            
            // קבלת תורים קיימים
            $stmt = $db->prepare("SELECT appointment_time FROM appointments WHERE appointment_date = ? AND status != 'cancelled'");
            $stmt->execute([$date]);
            $bookedSlots = $stmt->fetchAll(PDO::FETCH_COLUMN);
            error_log("getAvailableSlots bookedSlots: " . print_r($bookedSlots, true));
            
            // יצירת שעות פנויות
            $duration = $service['duration'];
            $startTime = strtotime($hours['start_time']);
            $endTime = strtotime($hours['end_time']);
            $interval = $duration * 60;
            
            $availableSlots = [];
            $currentTime = $startTime;
            
            while($currentTime + $interval <= $endTime) {
                $slot = date('H:i:s', $currentTime);
                
                // בדיקה אם השעה לא תפוסה
                $isBooked = false;
                foreach($bookedSlots as $booked) {
                    if($booked == $slot) {
                        $isBooked = true;
                        break;
                    }
                }
                
                // בדיקה שהשעה לא עברה (ליום הנוכחי)
                $today = date('Y-m-d');
                if($date == $today) {
                    $now = time();
                    $slotTimestamp = strtotime($date . ' ' . $slot);
                    if($slotTimestamp <= $now) {
                        $isBooked = true;
                    }
                }
                
                if(!$isBooked) {
                    $availableSlots[] = $slot;
                }
                
                $currentTime += $interval;
            }
            
            error_log("getAvailableSlots availableSlots: " . print_r($availableSlots, true));
            echo json_encode($availableSlots);
            
        } catch(Exception $e) {
            error_log("Error in getAvailableSlots: " . $e->getMessage());
            echo json_encode([]);
        }
    }
    
    // פונקציה ליצירת תור חדש (API)
    public function create() {
        header('Content-Type: application/json');
        
        try {
            if($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $db = Database::getInstance()->getConnection();
            
            // בדיקה שהשעה עדיין פנויה
            $stmt = $db->prepare("SELECT id FROM appointments WHERE appointment_date = ? AND appointment_time = ? AND status != 'cancelled'");
            $stmt->execute([$_POST['appointment_date'], $_POST['appointment_time']]);
            
            if($stmt->fetch()) {
                throw new Exception('השעה כבר תפוסה');
            }
            
            $email = trim($_POST['email'] ?? '');

            // מצא או צור לקוח
            $stmt = $db->prepare("SELECT id, name, email FROM customers WHERE phone = ?");
            $stmt->execute([$_POST['phone']]);
            $customer = $stmt->fetch();

            if($customer) {
                $customerId = $customer['id'];
                if($customer['name'] != $_POST['name'] || $customer['email'] != $email) {
                    $stmt = $db->prepare("UPDATE customers SET name = ?, email = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$_POST['name'], $email, $customerId]);
                }
            } else {
                $stmt = $db->prepare("INSERT INTO customers (name, phone, email, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$_POST['name'], $_POST['phone'], $email]);
                $customerId = $db->lastInsertId();
            }
            
            // יצירת תור
            $token = bin2hex(random_bytes(32));
            $stmt = $db->prepare("INSERT INTO appointments (customer_id, service_id, appointment_date, appointment_time, token, notes, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
            $stmt->execute([
                $customerId,
                $_POST['service_id'],
                $_POST['appointment_date'],
                $_POST['appointment_time'],
                $token,
                $_POST['notes'] ?? null
            ]);
            
            $appointmentId = $db->lastInsertId();
            
            // WhatsApp message
            $whatsappMessage = "שלום {$_POST['name']},\n\n";
            $whatsappMessage .= "התור שלך נקבע בהצלחה!\n\n";
            $whatsappMessage .= "📋 פרטי התור:\n";
            $whatsappMessage .= "מספר תור: {$appointmentId}\n";
            $whatsappMessage .= "שירות: " . $this->getServiceName($_POST['service_id']) . "\n";
            $whatsappMessage .= "תאריך: " . date('d/m/Y', strtotime($_POST['appointment_date'])) . "\n";
            $whatsappMessage .= "שעה: " . substr($_POST['appointment_time'], 0, 5) . "\n\n";
            $whatsappMessage .= "לניהול תור:\n";
            $whatsappMessage .= SITE_URL . "/index.php?controller=booking&action=manage&id={$appointmentId}&token={$token}\n\n";
            $whatsappMessage .= "ניתן לבטל או לשנות תור עד " . MIN_CANCELLATION_HOURS . " שעות לפני התור.";
            
            $encodedMessage = urlencode($whatsappMessage);
            $customerPhone = preg_replace('/[^0-9]/', '', $_POST['phone']);
            $whatsappLink = "https://wa.me/972{$customerPhone}?text={$encodedMessage}";
            
            $_SESSION['whatsapp_link'] = $whatsappLink;
            $_SESSION['whatsapp_message'] = $whatsappMessage;
                    
            // עדכן סטטיסטיקות לקוח
            $stmt = $db->prepare("UPDATE customers SET total_visits = total_visits + 1, last_visit = NOW() WHERE id = ?");
            $stmt->execute([$customerId]);

            $emailData = $this->getAppointmentEmailData($appointmentId);
            if ($emailData) {
                Mailer::appointmentCreated($emailData);
            }

            echo json_encode([
                'success' => true,
                'appointment_id' => $appointmentId,
                'token' => $token
            ]);
            
        } catch(Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    // ========== ✅ FIXED: UPDATE APPOINTMENT (User) ==========
    public function update() {
        header('Content-Type: application/json');
        
        try {
            // Check if it's a POST request from form or JSON
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Form submission (from manage_booking.php)
                $id = $_POST['id'] ?? 0;
                $token = $_POST['token'] ?? '';
                $customer_name = $_POST['customer_name'] ?? '';
                $phone = $_POST['phone'] ?? '';
                $email = trim($_POST['email'] ?? '');
                $service_id = $_POST['service_id'] ?? 0;
                $appointment_date = $_POST['appointment_date'] ?? '';
                $appointment_time = $_POST['appointment_time'] ?? '';
                $notes = $_POST['notes'] ?? '';
            } else {
                // JSON API request
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['id'] ?? 0;
                $token = $data['token'] ?? '';
                $customer_name = $data['customer_name'] ?? '';
                $phone = $data['phone'] ?? '';
                $email = trim($data['email'] ?? '');
                $service_id = $data['service_id'] ?? 0;
                $appointment_date = $data['appointment_date'] ?? '';
                $appointment_time = $data['appointment_time'] ?? '';
                $notes = $data['notes'] ?? '';
            }
            
            if(!$id || !$token) {
                if($this->isAjax()) {
                    echo json_encode(['success' => false, 'error' => 'חסרים פרטים']);
                    exit;
                }
                $_SESSION['error'] = 'חסרים פרטים';
                header("Location: " . SITE_URL . "/index.php?controller=booking&action=manage&id=" . $id . "&token=" . $token);
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Verify token and get appointment
            $stmt = $db->prepare("SELECT a.*, c.id as customer_id, c.email as customer_email FROM appointments a
                                  JOIN customers c ON a.customer_id = c.id
                                  WHERE a.id = ? AND a.token = ?");
            $stmt->execute([$id, $token]);
            $appointment = $stmt->fetch();
            
            if(!$appointment) {
                if($this->isAjax()) {
                    echo json_encode(['success' => false, 'error' => 'תור לא נמצא']);
                    exit;
                }
                $_SESSION['error'] = 'תור לא נמצא';
                header("Location: " . SITE_URL . "/index.php?controller=booking&action=manage&id=" . $id . "&token=" . $token);
                exit;
            }
            
            if($appointment['status'] == 'cancelled') {
                if($this->isAjax()) {
                    echo json_encode(['success' => false, 'error' => 'לא ניתן לערוך תור שבוטל']);
                    exit;
                }
                $_SESSION['error'] = 'לא ניתן לערוך תור שבוטל';
                header("Location: " . SITE_URL . "/index.php?controller=booking&action=manage&id=" . $id . "&token=" . $token);
                exit;
            }
            
            // Check if new time is available (if changed)
            if($appointment_date != $appointment['appointment_date'] || $appointment_time != $appointment['appointment_time']) {
                $stmt = $db->prepare("SELECT id FROM appointments 
                                      WHERE appointment_date = ? AND appointment_time = ? 
                                      AND status != 'cancelled' AND id != ?");
                $stmt->execute([$appointment_date, $appointment_time, $id]);
                if($stmt->fetch()) {
                    if($this->isAjax()) {
                        echo json_encode(['success' => false, 'error' => 'השעה המבוקשת כבר תפוסה']);
                        exit;
                    }
                    $_SESSION['error'] = 'השעה המבוקשת כבר תפוסה';
                    header("Location: " . SITE_URL . "/index.php?controller=booking&action=manage&id=" . $id . "&token=" . $token);
                    exit;
                }
            }
            
            // Update customer (keep existing email if none was submitted)
            $emailToSave = $email !== '' ? $email : $appointment['customer_email'];
            $stmt = $db->prepare("UPDATE customers SET name = ?, phone = ?, email = ? WHERE id = ?");
            $stmt->execute([$customer_name, $phone, $emailToSave, $appointment['customer_id']]);
            
            // Update appointment
            $stmt = $db->prepare("UPDATE appointments SET 
                service_id = ?,
                appointment_date = ?,
                appointment_time = ?,
                notes = ?,
                updated_at = NOW()
                WHERE id = ? AND token = ?");
            
            $stmt->execute([
                $service_id,
                $appointment_date,
                $appointment_time,
                $notes,
                $id,
                $token
            ]);

            $emailData = $this->getAppointmentEmailData($id);
            if ($emailData) {
                Mailer::appointmentUpdated($emailData);
            }

            if($this->isAjax()) {
                echo json_encode(['success' => true]);
                exit;
            }
            
            $_SESSION['success'] = 'התור עודכן בהצלחה!';
            header("Location: " . SITE_URL . "/index.php?controller=booking&action=manage&id=" . $id . "&token=" . $token);
            exit;
            
        } catch(Exception $e) {
            error_log("User update appointment error: " . $e->getMessage());
            
            if($this->isAjax()) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            }
            
            $_SESSION['error'] = 'שגיאה בעדכון התור: ' . $e->getMessage();
            header("Location: " . SITE_URL . "/index.php?controller=booking&action=manage&id=" . $id . "&token=" . $token);
            exit;
        }
    }
    
    // פונקציה לביטול תור
    public function cancel() {
        header('Content-Type: application/json');
        
        try {
            // Check if GET or POST
            if($_SERVER['REQUEST_METHOD'] === 'GET') {
                $appointmentId = $_GET['id'] ?? 0;
                $token = $_GET['token'] ?? '';
            } else {
                $data = json_decode(file_get_contents('php://input'), true);
                $appointmentId = $data['id'] ?? 0;
                $token = $data['token'] ?? '';
            }
            
            if(!$appointmentId || !$token) {
                throw new Exception('חסרים פרטים');
            }
            
            $db = Database::getInstance()->getConnection();
            
            // קבלת פרטי התור
            $stmt = $db->prepare("SELECT * FROM appointments WHERE id = ? AND token = ? AND status != 'cancelled'");
            $stmt->execute([$appointmentId, $token]);
            $appointment = $stmt->fetch();
            
            if(!$appointment) {
                throw new Exception('תור לא נמצא');
            }
            
            // בדיקת זמינות לביטול
            $appointmentTime = strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
            $hoursDiff = ($appointmentTime - time()) / 3600;
            
            if($hoursDiff < MIN_CANCELLATION_HOURS) {
                throw new Exception('לא ניתן לבטל תור פחות מ-' . MIN_CANCELLATION_HOURS . ' שעות לפני הזמן');
            }
            
            // ביטול התור
            $stmt = $db->prepare("UPDATE appointments SET status = 'cancelled', updated_at = NOW() WHERE id = ? AND token = ?");
            $stmt->execute([$appointmentId, $token]);

            $emailData = $this->getAppointmentEmailData($appointmentId);
            if ($emailData) {
                Mailer::appointmentCancelled($emailData);
            }

            echo json_encode(['success' => true]);
            
        } catch(Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    // ========== ✅ FIXED: MANAGE (User) - Loads booking_manage.php ==========
    public function manage($id = null) {
        // Get parameters
        if(!$id) {
            $id = $_GET['id'] ?? null;
        }
        $token = $_GET['token'] ?? null;
        
        // Validate
        if(!$id || !$token) {
            header("Location: " . SITE_URL . "/views/booking.php");
            exit;
        }
        
        // Find appointment
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT a.*, c.name as customer_name, c.phone, c.email as customer_email, s.name as service_name, s.price
                              FROM appointments a
                              JOIN customers c ON a.customer_id = c.id
                              JOIN services s ON a.service_id = s.id
                              WHERE a.id = ? AND a.token = ?");
        $stmt->execute([$id, $token]);
        $appointment = $stmt->fetch();
        
        if(!$appointment) {
            header("Location: " . SITE_URL . "/views/booking.php");
            exit;
        }
        
        // ✅ Load the correct view file
        require_once __DIR__ . "/../views/manage_booking.php";
    }
    
    // Helper function - builds the data array Mailer needs for a notification email
    private function getAppointmentEmailData($appointmentId) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT a.id, a.token, a.appointment_date, a.appointment_time,
                                      c.name as customer_name, c.phone as customer_phone, c.email as customer_email,
                                      s.name as service_name
                               FROM appointments a
                               JOIN customers c ON a.customer_id = c.id
                               JOIN services s ON a.service_id = s.id
                               WHERE a.id = ?");
        $stmt->execute([$appointmentId]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $row['manage_link'] = SITE_URL . "/index.php?controller=booking&action=manage&id={$row['id']}&token={$row['token']}";
        return $row;
    }

    // Helper function
    private function getServiceName($serviceId) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT name FROM services WHERE id = ?");
        $stmt->execute([$serviceId]);
        $service = $stmt->fetch();
        return $service ? $service['name'] : '';
    }
    
    // Helper function to check if AJAX request
    private function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
?>