<?php
// push/PushService.php - standalone Web Push module.
//
// Self-contained: owns its own config (push/config.php), its own DB table
// (push_subscriptions) and its own sending logic. The rest of the app only
// ever calls the static methods below - it never touches WebPush/VAPID
// directly.

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushService {

    // ---------- subscribing ----------

    // Save (or refresh) a browser's push subscription for a customer.
    public static function subscribeCustomer($customerId, array $subscription) {
        return self::saveSubscription('customer', (int)$customerId, null, $subscription);
    }

    // Save (or refresh) a browser's push subscription for a logged-in admin.
    public static function subscribeAdmin($adminId, array $subscription) {
        return self::saveSubscription('admin', null, (int)$adminId, $subscription);
    }

    public static function unsubscribe($endpoint) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM push_subscriptions WHERE endpoint = ?");
        $stmt->execute([$endpoint]);
    }

    private static function saveSubscription($type, $customerId, $adminId, array $subscription) {
        $endpoint = $subscription['endpoint'] ?? '';
        $keys = $subscription['keys'] ?? [];
        $p256dh = $keys['p256dh'] ?? '';
        $auth = $keys['auth'] ?? '';

        if (!$endpoint || !$p256dh || !$auth) {
            throw new Exception('מנוי פוש לא תקין');
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id FROM push_subscriptions WHERE endpoint = ?");
        $stmt->execute([$endpoint]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $db->prepare("UPDATE push_subscriptions SET subscriber_type = ?, customer_id = ?, admin_id = ?, p256dh = ?, auth = ? WHERE endpoint = ?");
            $stmt->execute([$type, $customerId, $adminId, $p256dh, $auth, $endpoint]);
            return (int)$existing['id'];
        }

        $stmt = $db->prepare("INSERT INTO push_subscriptions (subscriber_type, customer_id, admin_id, endpoint, p256dh, auth, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$type, $customerId, $adminId, $endpoint, $p256dh, $auth]);
        return (int)$db->lastInsertId();
    }

    // ---------- sending ----------

    // $data: ['title' => ..., 'body' => ..., 'url' => (optional link to open on click)]
    public static function notifyCustomer($customerId, array $data) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM push_subscriptions WHERE subscriber_type = 'customer' AND customer_id = ?");
        $stmt->execute([$customerId]);
        self::sendToRows($stmt->fetchAll(), $data);
    }

    public static function notifyAdmins(array $data) {
        $db = Database::getInstance()->getConnection();
        $rows = $db->query("SELECT * FROM push_subscriptions WHERE subscriber_type = 'admin'")->fetchAll();
        self::sendToRows($rows, $data);
    }

    private static function sendToRows(array $rows, array $data) {
        if (empty($rows)) {
            return;
        }

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => PUSH_VAPID_SUBJECT,
                    'publicKey' => PUSH_VAPID_PUBLIC_KEY,
                    'privateKey' => PUSH_VAPID_PRIVATE_KEY,
                ],
            ]);
        } catch (Exception $e) {
            error_log('PushService: failed to init WebPush - ' . $e->getMessage());
            return;
        }

        $payload = json_encode([
            'title' => $data['title'] ?? SITE_NAME,
            'body' => $data['body'] ?? '',
            'url' => $data['url'] ?? SITE_URL,
        ]);

        // A single malformed/corrupted subscription must never take down the
        // appointment create/update/cancel flow that triggered this notify -
        // push is a best-effort side channel, so every failure here is
        // logged and swallowed rather than allowed to bubble up.
        try {
            foreach ($rows as $row) {
                $subscription = Subscription::create([
                    'endpoint' => $row['endpoint'],
                    'publicKey' => $row['p256dh'],
                    'authToken' => $row['auth'],
                ]);
                $webPush->queueNotification($subscription, $payload);
            }

            $db = Database::getInstance()->getConnection();
            foreach ($webPush->flush() as $report) {
                if (!$report->isSuccess() && $report->isSubscriptionExpired()) {
                    $stmt = $db->prepare("DELETE FROM push_subscriptions WHERE endpoint = ?");
                    $stmt->execute([$report->getEndpoint()]);
                } elseif (!$report->isSuccess()) {
                    error_log('PushService: send failed for ' . $report->getEndpoint() . ' - ' . $report->getReason());
                }
            }
        } catch (\Throwable $e) {
            error_log('PushService: send failed - ' . $e->getMessage());
        }
    }
}
