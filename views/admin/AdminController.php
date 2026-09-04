// הוסף ל-AdminController.php

public function updateAppointment() {
    $this->checkAuth();
    header('Content-Type: application/json');
    
    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? '';
    
    $stmt = $this->db->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    
    echo json_encode(['success' => true]);
}

public function deleteAppointment($id) {
    $this->checkAuth();
    $stmt = $this->db->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin/appointments");
    exit;
}

public function addBlockedDate() {
    $this->checkAuth();
    $date = $_POST['date'] ?? '';
    $reason = $_POST['reason'] ?? '';
    
    $stmt = $this->db->prepare("INSERT INTO blocked_dates (blocked_date, reason) VALUES (?, ?)");
    $stmt->execute([$date, $reason]);
    
    header("Location: admin/schedule");
    exit;
}

public function deleteBlockedDate($id) {
    $this->checkAuth();
    $stmt = $this->db->prepare("DELETE FROM blocked_dates WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin/schedule");
    exit;
}

public function clearLogs() {
    $this->checkAuth();
    $logFile = __DIR__ . "/../logs/system.log";
    file_put_contents($logFile, "");
    header("Location: admin/logs");
    exit;
}