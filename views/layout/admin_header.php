<?php
// views/layout/admin_header.php
error_log('admin_header.php loaded');

// Check if user is logged in
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if(!$isLoggedIn) {
    header("Location: " . SITE_URL . "/index.php?url=admin/login");
    exit;
}

// Get current page from URL
$currentPage = 'dashboard';
if(isset($_GET['url'])) {
    $url = $_GET['url'];
    if(strpos($url, '/') !== false) {
        $parts = explode('/', $url);
        $currentPage = $parts[1] ?? 'dashboard';
    } else {
        $currentPage = $url;
    }
} elseif(isset($_GET['action'])) {
    $currentPage = $_GET['action'];
}

// Check if we're on dashboard
$isDashboardPage = ($currentPage == 'dashboard');
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="theme-color" content="#dc2626">
    <title>ניהול - ספר בראש צעיר</title>
    <script>window.BASE_URL = "<?php echo SITE_URL; ?>";</script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Heebo', sans-serif;
            background: #f5f5f5;
            overflow-x: hidden;
            min-height: 100vh;
        }
        
        /* ===== SIDEBAR ===== */
        .sidebar {
            background: #1a1a1a;
            height: 100vh;
            color: white;
            position: fixed;
            right: 0;
            top: 0;
            width: 280px;
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            box-shadow: -2px 0 10px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            -webkit-overflow-scrolling: touch;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        @media (min-width: 769px) {
            .sidebar {
                transform: translateX(0);
            }
            .main-content-wrapper {
                margin-right: 280px;
            }
        }
        
        @media (max-width: 768px) {
            .main-content-wrapper {
                margin-right: 0;
            }
        }
        
        .sidebar .brand {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }
        
        .sidebar .brand i {
            font-size: 45px;
            color: #dc2626;
            margin-bottom: 10px;
        }
        
        .sidebar .brand h5 {
            color: white;
            margin: 0;
            font-weight: 700;
            font-size: 18px;
        }
        
        .sidebar .brand small {
            color: #999;
            font-size: 12px;
        }
        
        .sidebar .nav {
            padding: 10px 0;
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .sidebar .nav-link {
            color: #ccc;
            padding: 14px 20px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
            border-right: 3px solid transparent;
            text-decoration: none;
            cursor: pointer;
            font-size: 15px;
            min-height: 50px;
            -webkit-tap-highlight-color: transparent;
        }
        
        .sidebar .nav-link:active {
            background: rgba(220, 38, 38, 0.2);
        }
        
        .sidebar .nav-link:hover {
            background: rgba(220, 38, 38, 0.1);
            color: white;
        }
        
        .sidebar .nav-link.active {
            background: rgba(220, 38, 38, 0.15);
            color: #dc2626;
            border-right-color: #dc2626;
        }
        
        .sidebar .nav-link i {
            width: 24px;
            font-size: 18px;
            text-align: center;
            flex-shrink: 0;
        }
        
        .sidebar .nav-link span {
            font-size: 14px;
            white-space: nowrap;
        }
        
        .sidebar .nav-divider {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin: 10px 20px;
        }
        
        .sidebar .auth-section {
            padding: 15px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }
        
        .sidebar .auth-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            min-height: 50px;
        }
        
        .sidebar .auth-btn:active {
            transform: scale(0.97);
        }
        
        .sidebar .auth-btn-logout {
            background: rgba(220, 38, 38, 0.15);
            color: #dc2626;
        }
        
        .sidebar .auth-btn-logout:hover {
            background: rgba(220, 38, 38, 0.25);
            transform: translateY(-2px);
        }
        
        .sidebar .user-info {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        
        .sidebar .user-info .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            color: white;
            flex-shrink: 0;
        }
        
        .sidebar .user-info .user-details {
            flex: 1;
            min-width: 0;
        }
        
        .sidebar .user-info .user-details .name {
            color: white;
            font-weight: 500;
            font-size: 14px;
        }
        
        .sidebar .user-info .user-details .role {
            color: #999;
            font-size: 12px;
        }
        
        /* ===== MENU TOGGLE ===== */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 1001;
            background: #dc2626;
            border: none;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            font-size: 22px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
            transition: all 0.3s;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
        
        .menu-toggle:active {
            transform: scale(0.9);
        }
        
        .menu-toggle:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.5);
        }
        
        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
                width: 44px;
                height: 44px;
                font-size: 18px;
                top: 12px;
                right: 12px;
                border-radius: 10px;
            }
        }
        
        @media (min-width: 769px) {
            .menu-toggle {
                display: none !important;
            }
        }
        
        /* ===== OVERLAY ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            z-index: 999;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
        
        .sidebar-overlay.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* ===== MAIN CONTENT ===== */
        .main-content-wrapper {
            min-height: 100vh;
            transition: margin-right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .main-content {
            padding: 20px;
        }
        
        /* ===== TOP BAR ===== */
        .top-bar {
            background: white;
            padding: 15px 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid #e5e7eb;
            min-height: 70px;
        }
        
        .top-bar .greeting {
            font-size: 16px;
            font-weight: 500;
            color: #1a1a1a;
        }
        
        .top-bar .greeting i {
            color: #dc2626;
            margin-left: 8px;
        }
        
        .top-bar .datetime {
            color: #6c757d;
            font-size: 14px;
        }
        
        .top-bar .datetime i {
            margin-left: 6px;
        }
        
        .top-bar .top-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .top-bar .logout-btn-top {
            background: none;
            border: none;
            color: #dc2626;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
            -webkit-tap-highlight-color: transparent;
        }
        
        .top-bar .logout-btn-top:active {
            transform: scale(0.95);
        }
        
        .top-bar .logout-btn-top:hover {
            background: rgba(220, 38, 38, 0.1);
        }
        
        /* ===== PAGE CONTENT ===== */
        #pageContent {
            animation: fadeInContent 0.3s ease;
        }
        
        @keyframes fadeInContent {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .content-loading {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }
        
        .content-loading .spinner {
            display: inline-block;
            animation: spin 1s linear infinite;
            font-size: 30px;
            color: #dc2626;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* ===== MOBILE ===== */
        @media (max-width: 768px) {
            .main-content {
                padding: 80px 12px 20px;
            }
            
            .top-bar {
                padding: 10px 65px 10px 15px;
                flex-direction: column;
                gap: 6px;
                text-align: center;
                position: fixed;
                top: 0;
                right: 0;
                left: 0;
                z-index: 998;
                min-height: 60px;
                box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            }
            
            .top-bar .greeting {
                font-size: 13px;
            }
            
            .top-bar .datetime {
                font-size: 11px;
            }
            
            .top-bar .top-actions {
                gap: 8px;
            }
            
            .top-bar .logout-btn-top {
                font-size: 12px;
                padding: 4px 10px;
            }
            
            .sidebar {
                width: 85%;
                max-width: 320px;
            }
            
            .sidebar .nav-link {
                padding: 12px 16px;
                font-size: 14px;
                min-height: 44px;
            }
            
            .sidebar .nav-link i {
                font-size: 16px;
                width: 20px;
            }
            
            .sidebar .brand {
                padding: 20px 15px;
            }
            
            .sidebar .brand i {
                font-size: 35px;
            }
            
            .sidebar .brand h5 {
                font-size: 16px;
            }
            
            .sidebar .auth-btn {
                padding: 12px;
                font-size: 14px;
                min-height: 44px;
            }
            
            .sidebar .user-info {
                padding: 12px 15px;
            }
            
            .sidebar .user-info .avatar {
                width: 34px;
                height: 34px;
                font-size: 15px;
            }
        }
        
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: #1a1a1a;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: #dc2626;
            border-radius: 10px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #b91c1c;
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <i class="fas fa-cut"></i>
            <h5>ספר בראש צעיר</h5>
            <small>מערכת ניהול</small>
        </div>
        
        <div class="user-info">
            <div class="avatar">
                <?= mb_substr($_SESSION['admin_name'] ?? $_SESSION['admin_username'] ?? 'M', 0, 1) ?>
            </div>
            <div class="user-details">
                <div class="name"><?= htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['admin_username'] ?? 'מנהל') ?></div>
                <div class="role">מנהל מערכת</div>
            </div>
        </div>
        
        <nav class="nav flex-column" id="mainNav">
            
            <a class="nav-link <?= $currentPage == 'dashboard' ? 'active' : '' ?>" data-page="dashboard" data-url="<?php echo SITE_URL; ?>/index.php?url=admin/dashboard">
                <i class="fas fa-chart-pie"></i> <span>דשבורד</span>
            </a>
            <a class="nav-link <?= $currentPage == 'appointments' ? 'active' : '' ?>" data-page="appointments" data-url="<?php echo SITE_URL; ?>/index.php?url=admin/appointments">
                <i class="fas fa-calendar-alt"></i> <span>תורים</span>
            </a>
            <a class="nav-link <?= $currentPage == 'services' ? 'active' : '' ?>" data-page="services" data-url="<?php echo SITE_URL; ?>/index.php?url=admin/services">
                <i class="fas fa-cut"></i> <span>שירותים</span>
            </a>
            <a class="nav-link <?= $currentPage == 'customers' ? 'active' : '' ?>" data-page="customers" data-url="<?php echo SITE_URL; ?>/index.php?url=admin/customers">
                <i class="fas fa-users"></i> <span>לקוחות</span>
            </a>
            <a class="nav-link <?= $currentPage == 'schedule' ? 'active' : '' ?>" data-page="schedule" data-url="<?php echo SITE_URL; ?>/index.php?url=admin/schedule">
                <i class="fas fa-clock"></i> <span>שעות פעילות</span>
            </a>
            <a class="nav-link <?= $currentPage == 'settings' ? 'active' : '' ?>" data-page="settings" data-url="<?php echo SITE_URL; ?>/index.php?url=admin/settings">
                <i class="fas fa-cog"></i> <span>הגדרות</span>
            </a>
            <a class="nav-link <?= $currentPage == 'logs' ? 'active' : '' ?>" data-page="logs" data-url="<?php echo SITE_URL; ?>/index.php?url=admin/logs">
                <i class="fas fa-history"></i> <span>לוגים</span>
            </a>
            
            <div class="nav-divider"></div>
        </nav>
        
        <div class="auth-section">
            <a href="<?php echo SITE_URL; ?>/index.php?url=admin/logout" class="auth-btn auth-btn-logout" onclick="return confirm('האם אתה בטוח שברצונך להתנתק?')">
                <i class="fas fa-sign-out-alt"></i>
                <span>התנתק</span>
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content-wrapper">
        <div class="top-bar">
            <div class="greeting">
                <i class="fas fa-user-circle"></i>
                שלום, <?= htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['admin_username'] ?? 'מנהל') ?>
            </div>
            <div class="top-actions">
                <div class="datetime">
                    <i class="fas fa-calendar-alt"></i>
                    <?= date('d/m/Y') ?> | 
                    <i class="fas fa-clock"></i>
                    <?= date('H:i') ?>
                </div>
                <button class="logout-btn-top" onclick="if(confirm('האם אתה בטוח שברצונך להתנתק?')) window.location.href='<?php echo SITE_URL; ?>/index.php?url=admin/logout'">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>התנתק</span>
                </button>
            </div>
        </div>
        
       <!-- Page Content -->
        <div class="main-content" id="pageContent">
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ========== MOBILE MENU ==========
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        function openMenu() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeMenu() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        if(menuToggle) {
            menuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                if(sidebar.classList.contains('open')) {
                    closeMenu();
                } else {
                    openMenu();
                }
            });
        }
        
        if(overlay) {
            overlay.addEventListener('click', closeMenu);
        }
        
        // Close menu on link click (mobile)
        document.querySelectorAll('#mainNav .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if(window.innerWidth <= 768) {
                    setTimeout(closeMenu, 300);
                }
            });
        });
        
        // ========== LOAD PAGE CONTENT ==========
        const pageContent = document.getElementById('pageContent');
        const navLinks = document.querySelectorAll('#mainNav .nav-link');
        
        // ✅ הוסף משתנה למניעת לופ
        let isLoadingPage = false;
        
        window.loadPage = function(url, pageName) {
            // ✅ מניעת קריאות כפולות
            if (isLoadingPage) {
                console.log('loadPage already in progress, skipping...');
                return;
            }
            
            
            isLoadingPage = true;
            
            // הצג לודר
            pageContent.innerHTML = `
                <div class="content-loading">
                    <div class="spinner">⟳</div>
                    <p>טוען ${pageName}...</p>
                </div>
            `;
            
            // עדכן ניווט
            navLinks.forEach(link => link.classList.remove('active'));
            navLinks.forEach(link => {
                if(link.dataset.page === pageName) {
                    link.classList.add('active');
                }
            });
            
            if (history.pushState) {
                const newUrl = (window.BASE_URL || '') + '/index.php?url=admin/' + pageName;
                history.pushState({page: pageName}, '', newUrl);
            }
            
            // שלח בקשה
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                // חלץ רק את תוכן ה-pageContent
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                const newContent = tempDiv.querySelector('#pageContent');
                
                if (newContent) {
                    pageContent.innerHTML = newContent.innerHTML;
                } else {
                    pageContent.innerHTML = html;
                }
                
                if (window.innerWidth <= 768) {
                    closeMenu();
                }
                
                // ✅ הפעל מחדש את ה-appointments JS
                if (pageName === 'appointments') {
                    console.log('Appointments page loaded, re-initializing...');
                    setTimeout(function() {
                        if (typeof initAppointments === 'function') {
                            initAppointments();
                        }
                    }, 300);
                }
            })
            .catch(error => {
                console.error('Error loading page:', error);
                pageContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        שגיאה בטעינת העמוד. אנא נסה שוב.
                        <br><small>${error.message}</small>
                    </div>
                `;
            })
            .finally(function() {
                // ✅ שחרר את הנעילה אחרי 500ms
                setTimeout(function() {
                    isLoadingPage = false;
                }, 500);
            });
        };
        
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.dataset.url;
                const page = this.dataset.page;
                if (url) {
                    window.loadPage(url, page);
                }
            });
        });
        
        // ========== LOAD DEFAULT PAGE ==========
        const isDashboardPage = <?= json_encode($isDashboardPage) ?>;
        const defaultLink = document.querySelector('#mainNav .nav-link.active');
        
        if (defaultLink && !isDashboardPage) {
            window.loadPage(defaultLink.dataset.url, defaultLink.dataset.page);
        }
        
        // ========== HANDLE BROWSER BACK/FORWARD ==========
        window.addEventListener('popstate', function(event) {
            if (event.state && event.state.page) {
                const page = event.state.page;
                const link = document.querySelector(`#mainNav .nav-link[data-page="${page}"]`);
                if (link) {
                    window.loadPage(link.dataset.url, page);
                }
            }
        });
    });
    
    // ========== GLOBAL SERVICE FUNCTIONS ==========
    function resetServiceForm() {
        console.log('resetServiceForm called');
        try {
            document.getElementById('modalTitle').textContent = 'שירות חדש';
            document.getElementById('serviceId').value = '0';
            document.getElementById('serviceName').value = '';
            document.getElementById('serviceDescription').value = '';
            document.getElementById('servicePrice').value = '';
            document.getElementById('serviceDuration').value = '30';
            document.getElementById('serviceActive').checked = true;
            document.getElementById('existingImage').value = '';
            document.getElementById('currentImagePreview').style.display = 'none';
            document.getElementById('currentImagePreview').src = '';
            document.getElementById('noImagePlaceholder').style.display = 'block';
            document.getElementById('serviceImage').value = '';
            document.getElementById('imageFileName').textContent = '';
        } catch(e) {
            console.error('resetServiceForm error:', e);
        }
    }

    function editService(service) {
        console.log('editService called with:', service);
        
        if (typeof service === 'string') {
            try {
                service = JSON.parse(service);
            } catch(e) {
                console.error('Error parsing service data:', e);
                alert('שגיאה בטעינת נתוני השירות');
                return;
            }
        }
        
        try {
            const modalElement = document.getElementById('serviceModal');
            if (!modalElement) {
                console.error('Service modal not found in DOM');
                alert('המודל לא נמצא. אנא רענן את העמוד.');
                return;
            }
            
            document.getElementById('modalTitle').textContent = 'עריכת שירות';
            document.getElementById('serviceId').value = service.id || 0;
            document.getElementById('serviceName').value = service.name || '';
            document.getElementById('serviceDescription').value = service.description || '';
            document.getElementById('servicePrice').value = service.price || 0;
            document.getElementById('serviceDuration').value = service.duration || 30;
            document.getElementById('serviceActive').checked = service.is_active == 1;
            document.getElementById('existingImage').value = service.image || '';
            
            const preview = document.getElementById('currentImagePreview');
            const noImage = document.getElementById('noImagePlaceholder');
            const fileName = document.getElementById('imageFileName');
            
            if (service.image) {
                preview.src = '<?php echo SITE_URL; ?>/uploads/services/' + service.image;
                preview.style.display = 'block';
                noImage.style.display = 'none';
                fileName.textContent = 'תמונה נוכחית: ' + service.image;
            } else {
                preview.style.display = 'none';
                preview.src = '';
                noImage.style.display = 'block';
                fileName.textContent = '';
            }
            
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            
        } catch(e) {
            console.error('editService error:', e);
            alert('שגיאה בטעינת המודל: ' + e.message);
        }
    }

    function showModal() {
        const modalElement = document.getElementById('serviceModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            console.error('Modal element not found');
        }
    }

    // ========== IMAGE PREVIEW ==========
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('serviceImage');
        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const preview = document.getElementById('currentImagePreview');
                        if (preview) {
                            preview.src = event.target.result;
                            preview.style.display = 'block';
                            document.getElementById('noImagePlaceholder').style.display = 'none';
                            document.getElementById('imageFileName').textContent = 'קובץ נבחר: ' + file.name;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
    
    console.log('editService function exists:', typeof editService === 'function');
    console.log('resetServiceForm function exists:', typeof resetServiceForm === 'function');
    </script>
    
    <!-- ========== SERVICE MODAL ========== -->
    <div class="modal fade" id="serviceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-cut text-danger"></i> <span id="modalTitle">שירות חדש</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?php echo SITE_URL; ?>/index.php?url=admin/services" enctype="multipart/form-data" id="serviceForm">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="serviceId" value="0">
                        <input type="hidden" name="existing_image" id="existingImage" value="">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">שם השירות <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="serviceName" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">תיאור</label>
                                    <textarea name="description" id="serviceDescription" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">מחיר (₪) <span class="text-danger">*</span></label>
                                        <input type="number" name="price" id="servicePrice" class="form-control" step="0.01" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">משך (דקות) <span class="text-danger">*</span></label>
                                        <input type="number" name="duration" id="serviceDuration" class="form-control" value="30" required>
                                    </div>
                                </div>
                                <div class="mb-3 mt-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_active" id="serviceActive" class="form-check-input" value="1" checked>
                                        <label class="form-check-label">שירות פעיל</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <small class="text-muted">תמונת שירות</small>
                                    </div>
                                    <div class="card-body text-center">
                                        <div id="imagePreview" class="mb-3">
                                            <img id="currentImagePreview" src="" alt="תמונת שירות" 
                                                 style="max-width: 100%; max-height: 150px; display: none; border-radius: 8px;">
                                            <div id="noImagePlaceholder" class="text-muted">
                                                <i class="fas fa-image fa-3x"></i>
                                                <p class="small">אין תמונה</p>
                                            </div>
                                        </div>
                                        <input type="file" name="image" id="serviceImage" class="form-control form-control-sm" accept="image/*">
                                        <small class="text-muted">JPEG, PNG, GIF, WEBP (מקסימום 2MB)</small>
                                        <div id="imageFileName" class="small text-muted mt-1"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ביטול</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> שמור שירות
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>