<?php
// classes/Mailer.php - Appointment email notifications (customer + admin)

require_once __DIR__ . '/../PHPMailer/Exception.php';
require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class Mailer {

    public static function appointmentCreated(array $appointment) {
        $subject = "אישור קביעת תור - " . SITE_NAME;
        $body = self::buildCustomerBody($appointment, "התור שלך נקבע בהצלחה!");
        self::dispatch($appointment, $subject, $body, "נקבע תור חדש");
    }

    public static function appointmentUpdated(array $appointment) {
        $subject = "התור שלך עודכן - " . SITE_NAME;
        $body = self::buildCustomerBody($appointment, "התור שלך עודכן בהצלחה!");
        self::dispatch($appointment, $subject, $body, "תור עודכן");
    }

    public static function appointmentCancelled(array $appointment) {
        $subject = "התור שלך בוטל - " . SITE_NAME;
        $body = "שלום {$appointment['customer_name']},\n\n"
              . "התור שלך בוטל בהתאם לבקשתך.\n\n"
              . self::detailsBlock($appointment);
        self::dispatch($appointment, $subject, $body, "תור בוטל");
    }

    private static function buildCustomerBody(array $appointment, string $intro) {
        $body = "שלום {$appointment['customer_name']},\n\n";
        $body .= "{$intro}\n\n";
        $body .= self::detailsBlock($appointment);

        if (!empty($appointment['manage_link'])) {
            $body .= "\nלניהול התור (עדכון/ביטול):\n" . $appointment['manage_link'] . "\n\n";
            $body .= "ניתן לבטל או לשנות תור עד " . MIN_CANCELLATION_HOURS . " שעות לפני התור.";
        }

        return $body;
    }

    private static function detailsBlock(array $appointment) {
        $lines = "📋 פרטי התור:\n";
        $lines .= "מספר תור: {$appointment['id']}\n";
        $lines .= "שירות: {$appointment['service_name']}\n";
        $lines .= "תאריך: " . date('d/m/Y', strtotime($appointment['appointment_date'])) . "\n";
        $lines .= "שעה: " . substr($appointment['appointment_time'], 0, 5) . "\n";
        $lines .= "טלפון לקוח: {$appointment['customer_phone']}\n";
        return $lines;
    }

    private static function dispatch(array $appointment, string $subject, string $customerBody, string $adminEventLabel) {
        if (!empty($appointment['customer_email'])) {
            self::send($appointment['customer_email'], $appointment['customer_name'], $subject, $customerBody);
        }

        $adminSubject = "[{$adminEventLabel}] " . SITE_NAME . " - תור #{$appointment['id']}";
        $adminBody = "{$adminEventLabel}:\n\n" . self::detailsBlock($appointment);
        if (!empty($appointment['customer_email'])) {
            $adminBody .= "אימייל לקוח: {$appointment['customer_email']}\n";
        }
        self::send(ADMIN_NOTIFICATION_EMAIL, 'מנהל מערכת', $adminSubject, $adminBody);
    }

    private static function send($toEmail, $toName, $subject, $body) {
        if (empty(SMTP_USERNAME) || empty(SMTP_PASSWORD)) {
            error_log("Mailer: SMTP_USERNAME/SMTP_PASSWORD not configured, skipping email to {$toEmail}");
            return false;
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            return true;
        } catch (PHPMailerException $e) {
            error_log("Mailer error sending to {$toEmail}: " . $mail->ErrorInfo);
            return false;
        }
    }
}
