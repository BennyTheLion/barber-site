<?php
class RateLimiter {
    private $table = "rate_limits";
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->createTable();
    }
    
    private function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            action VARCHAR(50) NOT NULL,
            attempt_time DATETIME NOT NULL,
            INDEX idx_ip_action (ip_address, action)
        )";
        $this->db->exec($sql);
    }
    
    public function checkLimit($action, $limit = 5, $window = 900) {
        $ip = $_SERVER["REMOTE_ADDR"];
        $cutoff = date("Y-m-d H:i:s", time() - $window);
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE ip_address = :ip AND action = :action AND attempt_time > :cutoff";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":ip" => $ip, ":action" => $action, ":cutoff" => $cutoff]);
        $result = $stmt->fetch();
        
        if($result["count"] >= $limit) {
            return false;
        }
        
        $sql = "INSERT INTO {$this->table} (ip_address, action, attempt_time) VALUES (:ip, :action, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":ip" => $ip, ":action" => $action]);
        
        return true;
    }
    
    public function clearAttempts($action) {
        $ip = $_SERVER["REMOTE_ADDR"];
        $sql = "DELETE FROM {$this->table} WHERE ip_address = :ip AND action = :action";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":ip" => $ip, ":action" => $action]);
    }
}
