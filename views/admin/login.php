<?php
// views/admin/login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="theme-color" content="#dc2626">
    <title>התחברות למערכת ניהול - ספר בראש צעיר</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <style>
        /* ========== RESET & BASE ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #000 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Heebo', sans-serif;
            padding: 20px;
        }
        
        /* ========== LOGIN CARD ========== */
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            animation: slideUp 0.5s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* ========== HEADER ========== */
        .login-header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 35px 20px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            transform: rotate(45deg);
        }
        
        .login-header i {
            font-size: 60px;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
            text-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .login-header h2 {
            font-size: 1.5rem;
            margin-bottom: 5px;
            font-weight: 800;
            position: relative;
            z-index: 1;
        }
        
        .login-header p {
            margin: 0;
            opacity: 0.9;
            font-weight: 300;
            position: relative;
            z-index: 1;
        }
        
        /* ========== BODY ========== */
        .login-body {
            padding: 35px 30px 30px;
        }
        
        /* ========== FORM ========== */
        .form-label {
            font-weight: 600;
            font-size: 14px;
            color: #374151;
            margin-bottom: 6px;
        }
        
        .form-control {
            padding: 12px 16px;
            font-size: 16px;
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            transition: all 0.3s;
            background: #f9fafb;
            height: 50px;
        }
        
        .form-control:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
            background: white;
        }
        
        .form-control.error {
            border-color: #dc2626;
            background-color: #fef2f2;
        }
        
        .form-control.success {
            border-color: #16a34a;
            background-color: #f0fdf4;
        }
        
        .form-control::placeholder {
            color: #9ca3af;
            font-size: 14px;
        }
        
        /* ========== PASSWORD WRAPPER ========== */
        .password-wrapper {
            position: relative;
        }

        .password-wrapper .form-control {
            padding-left: 50px;
        }

        .password-toggle {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 8px;
            font-size: 18px;
            transition: all 0.3s ease;
            z-index: 10;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: #dc2626;
            background: rgba(220, 38, 38, 0.08);
        }

        .password-toggle:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2);
        }

        .password-toggle:active {
            transform: translateY(-50%) scale(0.9);
        }
        
        .password-toggle .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }
        
        /* ========== LOGIN BUTTON ========== */
        .btn-login {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 14px;
            border-radius: 10px;
            width: 100%;
            font-weight: 700;
            font-size: 16px;
            border: none;
            transition: all 0.3s;
            cursor: pointer;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
        }
        
        .btn-login:hover::before {
            opacity: 1;
        }
        
        .btn-login:active {
            transform: translateY(0) scale(0.98);
        }
        
        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-login i {
            font-size: 18px;
        }
        
        /* ========== ALERTS ========== */
        #alertContainer {
            margin-bottom: 20px;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 12px 16px;
            animation: slideDown 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border-right: 4px solid #dc2626;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border-right: 4px solid #16a34a;
        }
        
        .alert i {
            font-size: 18px;
        }
        
        .alert .btn-close {
            margin-right: auto;
            padding: 8px;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* ========== SPINNER ========== */
        .spinner {
            display: inline-block;
            animation: spin 1s linear infinite;
            font-size: 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* ========== SHAKE ANIMATION ========== */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-8px); }
            20%, 40%, 60%, 80% { transform: translateX(8px); }
        }
        
        .shake {
            animation: shake 0.5s ease;
        }
        
        /* ========== DIVIDER ========== */
        .login-divider {
            display: flex;
            align-items: center;
            margin: 25px 0 20px;
        }
        
        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }
        
        .login-divider span {
            padding: 0 15px;
            color: #9ca3af;
            font-size: 13px;
        }
        
        /* ========== FOOTER ========== */
        .login-footer {
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
        }
        
        .login-footer i {
            color: #dc2626;
            margin-left: 5px;
        }
        
        /* ========== KEYBOARD SHORTCUT HINT ========== */
        .shortcut-hint {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
            color: #9ca3af;
        }
        
        .shortcut-hint kbd {
            background: #f3f4f6;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            border: 1px solid #d1d5db;
            font-family: inherit;
        }
        
        /* ========== RESPONSIVE ========== */
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .login-header {
                padding: 25px 15px 20px;
            }
            
            .login-header i {
                font-size: 40px;
            }
            
            .login-header h2 {
                font-size: 1.2rem;
            }
            
            .login-header p {
                font-size: 14px;
            }
            
            .login-body {
                padding: 25px 16px 20px;
            }
            
            .form-control {
                font-size: 15px;
                height: 44px;
                padding: 10px 14px;
            }

            .password-toggle {
                left: 8px;
                width: 36px;
                height: 36px;
                font-size: 16px;
            }

            .password-wrapper .form-control {
                padding-left: 44px;
            }
            
            .btn-login {
                height: 44px;
                font-size: 15px;
            }
        }
        
        @media (max-width: 380px) {
            .login-header i {
                font-size: 32px;
            }
            
            .login-header h2 {
                font-size: 1rem;
            }
            
            .login-body {
                padding: 20px 12px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <i class="fas fa-cut"></i>
            <h2>ספר בראש צעיר</h2>
            <p>מערכת ניהול</p>
        </div>
        
        <!-- Body -->
        <div class="login-body">
            <!-- Alert Container -->
            <div id="alertContainer">
                <?php if(isset($_SESSION['admin_error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= htmlspecialchars($_SESSION['admin_error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['admin_error']); ?>
                <?php endif; ?>
            </div>
            
            <!-- Login Form -->
            <form method="POST" action="<?php echo SITE_URL; ?>/index.php?url=admin/login" id="loginForm" autocomplete="off">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user" style="color: #dc2626; margin-left: 6px;"></i>
                        שם משתמש
                    </label>
                    <input type="text" name="username" id="username" class="form-control" 
                           required autofocus placeholder="הזן שם משתמש">
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock" style="color: #dc2626; margin-left: 6px;"></i>
                        סיסמה
                    </label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" class="form-control" 
                               required placeholder="הזן סיסמה">
                        <button type="button" class="password-toggle" id="togglePassword" 
                                aria-label="הצג/הסתר סיסמה" tabindex="-1">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                            <span class="sr-only">הצג סיסמה</span>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> 
                    <span>התחבר</span>
                </button>
            </form>
            
            <!-- Divider -->
            <div class="login-divider">
                <span>מערכת מאובטחת</span>
            </div>
            
            <!-- Footer -->
            <div class="login-footer">
                <i class="fas fa-shield-alt"></i>
                <span>כל הזכויות שמורות &copy; <?= date('Y') ?></span>
            </div>
            
            <!-- Keyboard Shortcut Hint -->
            <div class="shortcut-hint">
                <kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>P</kbd> להצגת סיסמה
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ========== PASSWORD TOGGLE ==========
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                toggleIcon.classList.toggle('fa-eye');
                toggleIcon.classList.toggle('fa-eye-slash');
                
                const isVisible = type === 'text';
                this.setAttribute('aria-label', isVisible ? 'הסתר סיסמה' : 'הצג סיסמה');
                
                const srOnly = this.querySelector('.sr-only');
                if (srOnly) {
                    srOnly.textContent = isVisible ? 'הסתר סיסמה' : 'הצג סיסמה';
                }
            });

            // ✅ Keyboard shortcut: Ctrl+Shift+P
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.shiftKey && (e.key === 'P' || e.key === 'p')) {
                    e.preventDefault();
                    togglePassword.click();
                }
            });
        }

        // ========== LOGIN FORM ==========
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const alertContainer = document.getElementById('alertContainer');
        const usernameInput = document.getElementById('username');
        
        if (loginForm) {
            loginForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Clear previous alerts
                alertContainer.innerHTML = '';
                
                // Remove previous styles
                usernameInput.classList.remove('error', 'success');
                passwordInput.classList.remove('error', 'success');
                
                // Show loading
                loginBtn.disabled = true;
                loginBtn.innerHTML = '<span class="spinner">⟳</span> מתחבר...';
                
                const formData = new FormData(this);
                
                try {
                    const response = await fetch('<?php echo SITE_URL; ?>/index.php?url=admin/login', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // ✅ Success
                        usernameInput.classList.add('success');
                        passwordInput.classList.add('success');
                        showAlert('success', '✅ ' + result.message);
                        
                        // Redirect after 1.5 seconds
                        setTimeout(() => {
                            window.location.href = result.redirect || '<?php echo SITE_URL; ?>/index.php?url=admin/dashboard';
                        }, 1500);
                    } else {
                        // ❌ Error
                        usernameInput.classList.add('error');
                        passwordInput.classList.add('error');
                        showAlert('danger', '❌ ' + result.message);
                        
                        // Reset button
                        loginBtn.disabled = false;
                        loginBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> <span>התחבר</span>';
                        
                        // Shake animation
                        document.querySelector('.login-card').classList.add('shake');
                        setTimeout(() => {
                            document.querySelector('.login-card').classList.remove('shake');
                        }, 500);
                        
                        // Focus on username
                        usernameInput.focus();
                    }
                } catch (error) {
                    console.error('Login error:', error);
                    showAlert('danger', '❌ שגיאת רשת. אנא נסה שוב.');
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> <span>התחבר</span>';
                }
            });
        }
        
        // ========== SHOW ALERT ==========
        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            alertContainer.appendChild(alertDiv);
            
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 300);
                }
            }, 5000);
        }
        
        // ========== ENTER KEY ==========
        // Pressing Enter on username field moves to password
        usernameInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                passwordInput.focus();
            }
        });
    });
    </script>
</body>
</html>