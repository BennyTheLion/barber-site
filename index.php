<?php
// index.php - קובץ ראשי של המערכת
session_start();

// הגדרת שגיאות לצורך דיבאג (הסר בפרודקשן)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// קבצי הגדרות בסיסיים
require_once "config/config.php";
require_once "config/database.php";

// Auto-loader פשוט לקבצי מחלקות
spl_autoload_register(function($className) {
    $file = __DIR__ . '/classes/' . $className . '.php';
    if(file_exists($file)) {
        require_once $file;
    }
});

// ===== טיפול ישיר בפרמטרים מ-JavaScript =====
// ✅ Check if controller and action are set directly (from JavaScript)
if(isset($_GET['controller']) && isset($_GET['action'])) {
    $controller = $_GET['controller'];
    $action = $_GET['action'];
    $param = $_GET['param'] ?? null;
    
    if($controller == 'booking') {
        require_once "controllers/AppointmentController.php";
        $appointmentController = new AppointmentController();
        
        if($action == 'getAvailableSlots') {
            $appointmentController->getAvailableSlots();
            exit;
        } elseif($action == 'create') {
            $appointmentController->create();
            exit;
        } elseif($action == 'manage') {
            $appointmentController->manage($_GET['id'] ?? null);
            exit;
        } elseif($action == 'update') {
            $appointmentController->update();
            exit;
        } elseif($action == 'cancel') {
            $appointmentController->cancel();
            exit;
        } else {
            $appointmentController->index();
            exit;
        }
    }
}

// ===== קבלת ה-URL מהשרת =====
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path = rtrim(dirname($script_name), '/');

// הסרת הנתיב הבסיסי מה-URL
$url = str_replace($base_path, '', $request_uri);
$url = ltrim($url, '/');
$url = explode('?', $url)[0]; // הסרת פרמטרים

// ✅ אם יש פרמטר url מה-.htaccess, השתמש בו
if(isset($_GET['url']) && !empty($_GET['url'])) {
    $url = $_GET['url'];
}

// ✅ אם אין URL, נגדיר ברירת מחדל
if(empty($url)) {
    $url = 'home';
}

// פיצול ה-URL
$url_parts = explode('/', $url);
$controller = isset($url_parts[0]) && $url_parts[0] != '' ? $url_parts[0] : 'home';
$action = isset($url_parts[1]) ? $url_parts[1] : 'index';
$param = isset($url_parts[2]) ? $url_parts[2] : null;

// ✅ Override אם יש GET controller/action (מקרה חירום)
$controller = $_GET['controller'] ?? $controller;
$action = $_GET['action'] ?? $action;
$param = $_GET['param'] ?? $param;

// לוג לדיבאג
error_log("=== Request Debug ===");
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("URL from .htaccess: " . ($_GET['url'] ?? 'none'));
error_log("Parsed URL: " . $url);
error_log("Controller: $controller, Action: $action, Param: $param");

// ============ ROUTING ============
try {
    switch($controller) {
        // ============ HOME PAGES ============
        case 'home':
        case '':
            if(file_exists("controllers/HomeController.php")) {
                require_once "controllers/HomeController.php";
                $home = new HomeController();
                $home->index();
            } else {
                echo "<h1>ברוכים הבאים לספר בראש צעיר</h1>";
                echo "<p>המערכת בהתקנה. יש להריץ את install.sql</p>";
            }
            break;
            
        case 'about':
            if(file_exists("controllers/HomeController.php")) {
                require_once "controllers/HomeController.php";
                $home = new HomeController();
                $home->about();
            }
            break;
            
        case 'services':
            if(file_exists("controllers/HomeController.php")) {
                require_once "controllers/HomeController.php";
                $home = new HomeController();
                $home->services();
            }
            break;
            
        case 'gallery':
            if(file_exists("controllers/HomeController.php")) {
                require_once "controllers/HomeController.php";
                $home = new HomeController();
                $home->gallery();
            }
            break;
            
        case 'contact':
            if(file_exists("controllers/HomeController.php")) {
                require_once "controllers/HomeController.php";
                $home = new HomeController();
                $home->contact();
            }
            break;
           
          case 'privacy-policy':
            if(file_exists("controllers/HomeController.php")) {
                require_once "controllers/HomeController.php";
                $home = new HomeController();
                $home->privacyPolicy();
            }
            break;
        
        case 'terms-of-service':
            if(file_exists("controllers/HomeController.php")) {
                require_once "controllers/HomeController.php";
                $home = new HomeController();
                $home->termsOfService();
            }
            break;
        
        case 'accessibility-statement':
            if(file_exists("controllers/HomeController.php")) {
                require_once "controllers/HomeController.php";
                $home = new HomeController();
                $home->accessibilityStatement();
            }
            break;
        
        case 'cookie-policy':
            if(file_exists("controllers/HomeController.php")) {
                require_once "controllers/HomeController.php";
                $home = new HomeController();
                $home->cookiePolicy();
            }
            break; 
        // ============ BOOKING SYSTEM ============
        case 'booking':
            if(file_exists("controllers/AppointmentController.php")) {
                require_once "controllers/AppointmentController.php";
                $appointmentController = new AppointmentController();
                
                switch($action) {
                    case 'getAvailableSlots':
                        $appointmentController->getAvailableSlots();
                        break;
                    case 'create':
                        $appointmentController->create();
                        break;
                    case 'manage':
                        $appointmentController->manage($param);
                        break;
                    case 'update':
                        $appointmentController->update();
                        break;
                    case 'cancel':
                        $appointmentController->cancel();
                        break;
                    default:
                        $appointmentController->index();
                }
            } else {
                echo "AppointmentController.php not found";
            }
            break;
            
        // ============ ADMIN PANEL ============
        case 'admin':
            if(file_exists("controllers/AdminController.php")) {
                require_once "controllers/AdminController.php";
                $admin = new AdminController();
                
                switch($action) {
                    case 'login':
                        $admin->login();
                        break;
                    case 'logout':
                        $admin->logout();
                        break;
                    case 'dashboard':
                        $admin->dashboard();
                        break;
                        
                    case 'appointments':
                        $admin->appointments();
                        break;
                
                    case 'services':
                        $admin->services();
                        break;
                   
                    case 'deleteService':
                        $admin->deleteService($param);
                        break;
                    case 'customers':
                        $admin->customers();
                        break;
                    
                    case 'customerDetails':
                        $admin->customerDetails($param);
                        break;
                    case 'schedule':
                        $admin->schedule();
                        break;
                    
                    case 'settings':
                        $admin->settings();
                        break;
                    
                    case 'logs':
                        $admin->logs();
                        break;
                    
                    case 'clearLogs':
                        $admin->clearLogs();
                        break;
                    case 'updateAppointment':
                        $admin->updateAppointment();
                        break;
                    case 'deleteAppointment':
                        $admin->deleteAppointment($param);
                        break;
                    case 'addBlockedDate':
                        $admin->addBlockedDate();
                        break;
                    case 'deleteBlockedDate':
                        $admin->deleteBlockedDate($param);
                        break;
                    default:
                        $admin->dashboard();
                }
            } else {
                echo "AdminController.php not found";
            }
            break;
            
        // ============ DEFAULT ============
        default:
            // ניסיון לטעון דף סטטי או הפניה לדף הבית
            if(file_exists("controllers/HomeController.php")) {
                require_once "controllers/HomeController.php";
                $home = new HomeController();
                $home->index();
            } else {
                echo "<h1>דף לא נמצא</h1>";
                echo "<p>הדף שחיפשת אינו קיים.</p>";
                echo "<a href='index.php'>חזרה לדף הבית</a>";
            }
    }
} catch(Exception $e) {
    error_log("Fatal error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    echo "<h1>שגיאה</h1>";
    echo "<p>אירעה שגיאה בטעינת העמוד. אנא נסה שוב מאוחר יותר.</p>";
    if(defined('DEBUG_MODE') && DEBUG_MODE) {
        echo "<p>פרטים טכניים: " . $e->getMessage() . "</p>";
    }
}
?>