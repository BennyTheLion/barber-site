<?php
// views/layout/header.php - Fixed Mobile Menu
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../config/config.php";

if(file_exists(__DIR__ . "/../../config/database.php")) {
    require_once (__DIR__ . "/../../config/database.php");
}

// Check if user is logged in properly
$isLoggedIn = false;
$adminName = '';

if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $isLoggedIn = true;
    $adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_username'] ?? 'מנהל';
}
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="theme-color" content="#dc2626">
    <meta name="description" content="ספר בראש צעיר - תספורות גברים וילדים ברמה גבוהה">
    <title><?php echo SITE_NAME; ?> | תספורות מקצועיות</title>
    <script>window.BASE_URL = "<?php echo SITE_URL; ?>";</script>

    <!-- ✅ Bootstrap 5 RTL - CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    
    <style>
        /* ========== NAVBAR ========== */
        .navbar {
            padding: 12px 0;
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            min-height: 70px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        .navbar-brand {
            font-size: 20px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .navbar-brand i {
            font-size: 28px;
            color: #dc2626;
        }
        
        .navbar-brand span {
            color: white;
        }
        
        /* ========== NAV LINKS ========== */
        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
            padding: 8px 16px !important;
            transition: all 0.3s;
            font-size: 15px;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #dc2626 !important;
        }
        
        /* ========== BOOK APPOINTMENT BUTTON ========== */
        .btn-book-appointment {
            background: #dc2626;
            color: white;
            padding: 10px 24px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            border: none;
            cursor: pointer;
            white-space: nowrap;
        }
        
        .btn-book-appointment:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
            color: white;
        }
        
        .btn-book-appointment:active {
            transform: scale(0.97);
        }
        
        /* ========== ADMIN CONTROLS ========== */
        .navbar .admin-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .navbar .admin-controls .admin-badge {
            background: rgba(255, 255, 255, 0.1);
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            border: 1px solid rgba(255,255,255,0.05);
        }
        
        .navbar .admin-controls .admin-badge i {
            color: #dc2626;
        }
        
        .navbar .btn-admin-login {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.25);
            color: white;
            padding: 6px 16px;
            border-radius: 25px;
            font-size: 13px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            cursor: pointer;
        }
        
        .navbar .btn-admin-login:hover {
            background: white;
            color: #dc2626;
            border-color: white;
            transform: translateY(-2px);
        }
        
        .navbar .btn-admin-login:active {
            transform: scale(0.95);
        }
        
        .navbar .btn-admin-logout {
            background: rgba(220, 38, 38, 0.15);
            border: 2px solid rgba(220, 38, 38, 0.3);
            color: white;
            padding: 6px 16px;
            border-radius: 25px;
            font-size: 13px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            cursor: pointer;
        }
        
        .navbar .btn-admin-logout:hover {
            background: #dc2626;
            border-color: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
        }
        
        .navbar .btn-admin-logout:active {
            transform: scale(0.95);
        }
        
        .navbar .btn-admin-dashboard {
            background: #dc2626;
            border: none;
            color: white;
            padding: 6px 16px;
            border-radius: 25px;
            font-size: 13px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        
        .navbar .btn-admin-dashboard:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
        }
        
        .navbar .btn-admin-dashboard:active {
            transform: scale(0.95);
        }
        
        /* ========== TOGGLER ========== */
        .navbar-toggler {
            border: 2px solid rgba(255,255,255,0.2);
            padding: 8px 12px;
            border-radius: 8px;
            -webkit-tap-highlight-color: transparent;
            z-index: 1001;
        }
        
        .navbar-toggler:focus {
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.3);
        }
        
        .navbar-toggler-icon {
            width: 24px;
            height: 24px;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        }
        
        /* ========== MOBILE ========== */
        @media (max-width: 991px) {
            .navbar {
                min-height: 60px;
                padding: 8px 0;
            }
            
            .navbar-brand {
                font-size: 17px;
            }
            
            .navbar-brand i {
                font-size: 22px;
            }
            
            .navbar-collapse {
                background: rgba(26, 26, 26, 0.98);
                padding: 15px 20px 20px;
                border-radius: 12px;
                margin-top: 10px;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                max-height: 80vh;
                overflow-y: auto;
                border: 1px solid rgba(255,255,255,0.05);
                position: absolute;
                top: 100%;
                right: 0;
                left: 0;
                z-index: 1000;
                width: 100%;
            }
            
            .navbar-collapse.show {
                display: block !important;
            }
            
            .navbar-nav .nav-link {
                padding: 10px 0 !important;
                font-size: 16px;
                border-bottom: 1px solid rgba(255,255,255,0.05);
            }
            
            .navbar-nav .nav-link:last-child {
                border-bottom: none;
            }
            
            /* Mobile admin controls */
            .navbar .admin-controls {
                flex-wrap: wrap;
                justify-content: center;
                width: 100%;
                margin: 12px 0;
                gap: 8px;
                padding: 10px;
                background: rgba(255,255,255,0.03);
                border-radius: 10px;
            }
            
            .navbar .admin-controls .admin-badge {
                width: 100%;
                text-align: center;
                justify-content: center;
                padding: 6px;
                font-size: 13px;
            }
            
            .navbar .btn-admin-login,
            .navbar .btn-admin-logout,
            .navbar .btn-admin-dashboard {
                flex: 1;
                justify-content: center;
                min-width: 80px;
                padding: 8px 12px;
                font-size: 13px;
            }
            
            .btn-book-appointment {
                width: 100%;
                justify-content: center;
                padding: 12px;
                font-size: 16px;
                margin-top: 5px;
            }
        }
        
        @media (max-width: 480px) {
            .navbar-brand {
                font-size: 15px;
            }
            
            .navbar-brand i {
                font-size: 18px;
            }
            
            .navbar .btn-admin-login,
            .navbar .btn-admin-logout,
            .navbar .btn-admin-dashboard {
                font-size: 12px;
                padding: 6px 10px;
                min-width: 60px;
            }
            
            .navbar .admin-controls .admin-badge {
                font-size: 11px;
                padding: 4px 10px;
            }
            
            .btn-book-appointment {
                font-size: 14px;
                padding: 10px;
            }
        }
        
        @media (min-width: 992px) {
            .navbar-nav .nav-link {
                padding: 8px 20px !important;
            }
        }
        
        /* Safe area for notched phones */
        @supports (padding: max(0px)) {
            .navbar {
                padding-top: max(12px, env(safe-area-inset-top));
                padding-bottom: max(12px, env(safe-area-inset-bottom));
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                <i class="fas fa-cut"></i>
                <span><?php echo SITE_NAME; ?></span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'home' ? 'active' : '' ?>" href="<?php echo SITE_URL; ?>">
                            דף הבית
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'about' ? 'active' : '' ?>" href="<?php echo SITE_URL; ?>/about">
                            אודות
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'services' ? 'active' : '' ?>" href="<?php echo SITE_URL; ?>/services">
                            שירותים
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'gallery' ? 'active' : '' ?>" href="<?php echo SITE_URL; ?>/gallery">
                            גלריה
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'contact' ? 'active' : '' ?>" href="<?php echo SITE_URL; ?>/contact">
                            צור קשר
                        </a>
                    </li>
                </ul>
                
                <!-- ADMIN CONTROLS -->
                <div class="admin-controls">
                    <?php if($isLoggedIn): ?>
                        <span class="admin-badge">
                            <i class="fas fa-user-shield"></i>
                            <?= htmlspecialchars($adminName) ?>
                        </span>
                        <a href="<?php echo SITE_URL; ?>/admin/dashboard" class="btn-admin-dashboard">
                            <i class="fas fa-tachometer-alt"></i> 
                            <span class="d-none d-sm-inline">ניהול</span>
                            <span class="d-inline d-sm-none"><i class="fas fa-cog"></i></span>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/admin/logout" class="btn-admin-logout" 
                           onclick="return confirm('האם אתה בטוח שברצונך להתנתק?')">
                            <i class="fas fa-sign-out-alt"></i> 
                            <span class="d-none d-sm-inline">התנתק</span>
                            <span class="d-inline d-sm-none"><i class="fas fa-times"></i></span>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/admin/login" class="btn-admin-login">
                            <i class="fas fa-lock"></i> 
                            <span class="d-none d-sm-inline">כניסת מנהל</span>
                            <span class="d-inline d-sm-none"><i class="fas fa-user-shield"></i></span>
                        </a>
                    <?php endif; ?>
                </div>
                
                <a href="<?php echo SITE_URL; ?>/views/booking.php" class="btn-book-appointment">
                    <i class="fas fa-calendar-plus"></i> קבע תור
                </a>
            </div>
        </div>
    </nav>
    <main>

<!-- ✅ Bootstrap JavaScript - Load at the end of body -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ========== FIX MOBILE MENU ==========
document.addEventListener('DOMContentLoaded', function() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarToggler && navbarCollapse) {
        // Manual toggle for mobile
        navbarToggler.addEventListener('click', function(e) {
            e.stopPropagation();
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            navbarCollapse.classList.toggle('show');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            const isClickInside = navbarToggler.contains(e.target) || navbarCollapse.contains(e.target);
            if (!isClickInside && navbarCollapse.classList.contains('show')) {
                navbarCollapse.classList.remove('show');
                navbarToggler.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Close menu when clicking a link (mobile)
        navbarCollapse.querySelectorAll('.nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 991) {
                    navbarCollapse.classList.remove('show');
                    navbarToggler.setAttribute('aria-expanded', 'false');
                }
            });
        });
        
        // Close menu on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 991 && navbarCollapse.classList.contains('show')) {
                navbarCollapse.classList.remove('show');
                navbarToggler.setAttribute('aria-expanded', 'false');
            }
        });
    }
});

console.log('Mobile menu fixed!');

function copyToClipboard() {
    // Get the site URL
    const siteUrl = window.location.origin;
    
    // Get the booking link element
    const linkInput = document.getElementById('bookingLink');
    
    // Get the current value
    let linkPath = linkInput.value;
    
    // Check if the link already has http:// or https://
    if (!linkPath.startsWith('http://') && !linkPath.startsWith('https://')) {
        // Add leading slash if missing
        if (!linkPath.startsWith('/')) {
            linkPath = '/' + linkPath;
        }
        // Add the site URL
        linkPath = siteUrl + linkPath;
    }
    
    // Set the value and copy
    linkInput.value = linkPath;
    linkInput.select();
    linkInput.setSelectionRange(0, 99999);
    
    try {
        document.execCommand('copy');
        // Show success message
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> הועתק!';
        setTimeout(() => {
            btn.innerHTML = originalText;
        }, 2000);
        
        alert('✅ הקישור הועתק ללוח!\n' + linkPath);
    } catch(err) {
        alert('❌ לא ניתן להעתיק. העתק ידנית.\n' + linkPath);
    }
}

// אפשרות נוספת - יצירת QR code
function generateQRCode() {
    const url = document.getElementById('bookingLink').value;
    // דורש ספריית QRCode
    // https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js
    if(typeof QRCode !== 'undefined') {
        document.getElementById('qrcode').innerHTML = '';
        new QRCode(document.getElementById('qrcode'), url);
    }
}

</script>