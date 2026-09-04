<?php
class Appointment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAvailableSlots($date, $serviceId) {
        // Get service duration
        $service = $this->getService($serviceId);
        if(!$service) return [];
        
        $duration = $service["duration"];
        
        // Get business hours for the day
        $dayOfWeek = date("N", strtotime($date));
        $hours = $this->getBusinessHours($dayOfWeek);
        if(!$hours) return [];
        
        // Get existing appointments for the date
        $sql = "SELECT appointment_time FROM appointments 
                WHERE appointment_date = :date AND status != "cancelled"";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":date" => $date]);
        $bookedSlots = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Generate available time slots
        $availableSlots = [];
        $start = strtotime($hours["start_time"]);
        $end = strtotime($hours["end_time"]);
        $interval = $duration * 60;
        
        for($time = $start; $time + $interval <= $end; $time += $interval) {
            $slot = date("H:i:s", $time);
            $slotEnd = date("H:i:s", $time + $interval);
            
            // Check if slot is available
            if(!in_array($slot, $bookedSlots)) {
                // Check if there's enough time before next appointment
                $available = true;
                foreach($bookedSlots as $booked) {
                    $bookedTime = strtotime($booked);
                    if($time + $interval > $bookedTime && $time < $bookedTime + $interval) {
                        $available = false;
                        break;
                    }
                }
                if($available) {
                    $availableSlots[] = $slot;
                }
            }
        }
        
        return $availableSlots;
    }
    
    public function createAppointment($data) {
        try {
            // Verify slot is still available
            $availableSlots = $this->getAvailableSlots($data["date"], $data["service_id"]);
            if(!in_array($data["time"], $availableSlots)) {
                throw new Exception("השעה שנבחרה כבר תפוסה");
            }
            
            // Create customer
            $customerId = $this->createCustomer($data);
            
            // Create appointment
            $token = bin2hex(random_bytes(32));
            $sql = "INSERT INTO appointments (customer_id, service_id, appointment_date, appointment_time, 
                    status, token, notes, created_at) 
                    VALUES (:customer_id, :service_id, :date, :time, "pending", :token, :notes, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ":customer_id" => $customerId,
                ":service_id" => $data["service_id"],
                ":date" => $data["date"],
                ":time" => $data["time"],
                ":token" => $token,
                ":notes" => $data["notes"] ?? null
            ]);
            
            $appointmentId = $this->db->lastInsertId();
            
            Logger::appointment("Created appointment #$appointmentId for customer {$data["name"]}");
            
            return ["id" => $appointmentId, "token" => $token];
        } catch(PDOException $e) {
            if($e->errorInfo[1] == 1062) {
                throw new Exception("השעה שנבחרה כבר תפוסה");
            }
            throw $e;
        }
    }
    
    public function getAppointment($id, $token) {
        $sql = "SELECT a.*, c.name, c.phone, c.email, s.name as service_name, s.price, s.duration 
                FROM appointments a 
                JOIN customers c ON a.customer_id = c.id 
                JOIN services s ON a.service_id = s.id 
                WHERE a.id = :id AND a.token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id" => $id, ":token" => $token]);
        return $stmt->fetch();
    }
    
    public function updateAppointment($id, $token, $newDate, $newTime) {
        $appointment = $this->getAppointment($id, $token);
        if(!$appointment) {
            throw new Exception("תור לא נמצא");
        }
        
        // Check if cancellation is allowed
        $appointmentTime = strtotime($appointment["appointment_date"] . " " . $appointment["appointment_time"]);
        if($appointmentTime - time() < MIN_CANCELLATION_HOURS * 3600) {
            throw new Exception("לא ניתן לשנות תור פחות מ-" . MIN_CANCELLATION_HOURS . " שעות לפני הזמן");
        }
        
        // Verify new slot is available
        $availableSlots = $this->getAvailableSlots($newDate, $appointment["service_id"]);
        if(!in_array($newTime, $availableSlots)) {
            throw new Exception("השעה המבוקשת כבר תפוסה");
        }
        
        // Update appointment
        $sql = "UPDATE appointments SET appointment_date = :date, appointment_time = :time 
                WHERE id = :id AND token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":date" => $newDate, ":time" => $newTime, ":id" => $id, ":token" => $token]);
        
        Logger::appointment("Updated appointment #$id to $newDate $newTime");
        
        return true;
    }
    
    public function cancelAppointment($id, $token) {
        $appointment = $this->getAppointment($id, $token);
        if(!$appointment) {
            throw new Exception("תור לא נמצא");
        }
        
        // Check if cancellation is allowed
        $appointmentTime = strtotime($appointment["appointment_date"] . " " . $appointment["appointment_time"]);
        if($appointmentTime - time() < MIN_CANCELLATION_HOURS * 3600) {
            throw new Exception("לא ניתן לבטל תור פחות מ-" . MIN_CANCELLATION_HOURS . " שעות לפני הזמן");
        }
        
        $sql = "UPDATE appointments SET status = "cancelled" WHERE id = :id AND token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id" => $id, ":token" => $token]);
        
        Logger::appointment("Cancelled appointment #$id");
        
        return true;
    }
    
    private function getService($id) {
        $sql = "SELECT * FROM services WHERE id = :id AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch();
    }
    
    private function getBusinessHours($dayOfWeek) {
        $sql = "SELECT * FROM business_hours WHERE day_of_week = :day AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":day" => $dayOfWeek]);
        return $stmt->fetch();
    }
    
    private function createCustomer($data) {
        $sql = "INSERT INTO customers (name, phone, email, created_at) 
                VALUES (:name, :phone, :email, NOW())
                ON DUPLICATE KEY UPDATE name = VALUES(name)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":name" => $data["name"],
            ":phone" => $data["phone"],
            ":email" => $data["email"] ?? null
        ]);
        
        $sql = "SELECT id FROM customers WHERE phone = :phone";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":phone" => $data["phone"]]);
        return $stmt->fetchColumn();
    }
}
