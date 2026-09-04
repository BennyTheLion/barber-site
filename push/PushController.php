<?php
// push/PushController.php - HTTP endpoints for the standalone push module.
require_once __DIR__ . '/PushService.php';

class PushController {

    // GET - the public key the front-end needs to call pushManager.subscribe()
    public function vapidKey() {
        header('Content-Type: application/json');
        echo json_encode(['publicKey' => PUSH_VAPID_PUBLIC_KEY]);
    }

    // POST { customer_id, subscription }
    public function subscribeCustomer() {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $customerId = $data['customer_id'] ?? 0;
            $subscription = $data['subscription'] ?? null;

            if (!$customerId || !$subscription) {
                throw new Exception('חסרים פרטים');
            }

            PushService::subscribeCustomer($customerId, $subscription);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // POST { subscription } - admin identified from the session, must be logged in
    public function subscribeAdmin() {
        header('Content-Type: application/json');
        try {
            if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
                throw new Exception('נדרשת התחברות');
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $subscription = $data['subscription'] ?? null;

            if (!$subscription) {
                throw new Exception('חסרים פרטים');
            }

            PushService::subscribeAdmin($_SESSION['admin_id'], $subscription);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // POST { endpoint }
    public function unsubscribe() {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $endpoint = $data['endpoint'] ?? '';

            if (!$endpoint) {
                throw new Exception('חסרים פרטים');
            }

            PushService::unsubscribe($endpoint);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
