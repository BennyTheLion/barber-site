<?php
// views/layout/footer.php - With draggable accessibility toolbar
?>

<footer style="
    background: linear-gradient(180deg, #1a1a1a 0%, #0d0d0d 100%);
    color: #ffffff;
    padding: 60px 0 0;
    margin-top: 40px;
    border-top: 4px solid #dc2626;
    position: relative;
    width: 100%;
    font-family: 'Heebo', sans-serif;
">
    <!-- Animated top border -->
    <div style="
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #dc2626, #991b1b, #dc2626);
        background-size: 200% 100%;
        animation: gradientMove 3s ease-in-out infinite;
    "></div>

    <div class="container">
        <div class="footer-content" style="
            display: grid;
            grid-template-columns: 2fr 1.2fr 1.2fr 1.6fr;
            gap: 35px;
            margin-bottom: 40px;
        ">
            <!-- Brand Section -->
            <div class="footer-section brand-section">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                    <i class="fas fa-cut" style="font-size: 35px; color: #dc2626;"></i>
                    <span style="font-size: 1.5rem; font-weight: 800; color: white;"><?php echo SITE_NAME; ?></span>
                </div>
                <p style="color: #999; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;">
                    תספורות גברים וילדים ברמה הגבוהה ביותר
                </p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="#" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.08); border-radius: 50%; color: #fff; font-size: 18px; transition: all 0.3s ease; text-decoration: none;"><i class="fab fa-instagram"></i></a>
                    <a href="#" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.08); border-radius: 50%; color: #fff; font-size: 18px; transition: all 0.3s ease; text-decoration: none;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.08); border-radius: 50%; color: #fff; font-size: 18px; transition: all 0.3s ease; text-decoration: none;"><i class="fab fa-tiktok"></i></a>
                    <a href="#" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.08); border-radius: 50%; color: #fff; font-size: 18px; transition: all 0.3s ease; text-decoration: none;"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="footer-section">
                <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; color: #dc2626; position: relative; padding-bottom: 10px;">
                    <i class="fas fa-link"></i> ניווט מהיר
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>" style="color: #bbb; text-decoration: none; transition: all 0.3s; display: flex; align-items: center; gap: 8px; font-size: 0.95rem;"><i class="fas fa-home" style="width: 18px; font-size: 14px; color: #dc2626;"></i> דף הבית</a></li>
                    <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>/about" style="color: #bbb; text-decoration: none; transition: all 0.3s; display: flex; align-items: center; gap: 8px; font-size: 0.95rem;"><i class="fas fa-info-circle" style="width: 18px; font-size: 14px; color: #dc2626;"></i> אודות</a></li>
                    <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>/services" style="color: #bbb; text-decoration: none; transition: all 0.3s; display: flex; align-items: center; gap: 8px; font-size: 0.95rem;"><i class="fas fa-cut" style="width: 18px; font-size: 14px; color: #dc2626;"></i> שירותים</a></li>
                    <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>/gallery" style="color: #bbb; text-decoration: none; transition: all 0.3s; display: flex; align-items: center; gap: 8px; font-size: 0.95rem;"><i class="fas fa-images" style="width: 18px; font-size: 14px; color: #dc2626;"></i> גלריה</a></li>
                    <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>/contact" style="color: #bbb; text-decoration: none; transition: all 0.3s; display: flex; align-items: center; gap: 8px; font-size: 0.95rem;"><i class="fas fa-envelope" style="width: 18px; font-size: 14px; color: #dc2626;"></i> צור קשר</a></li>
                </ul>
            </div>
            
            <!-- Legal Pages -->
            <div class="footer-section">
                <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; color: #dc2626; position: relative; padding-bottom: 10px;">
                    <i class="fas fa-gavel"></i> מידע משפטי
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>/privacy-policy" style="color: #bbb; text-decoration: none; transition: all 0.3s; display: flex; align-items: center; gap: 8px; font-size: 0.9rem;"><i class="fas fa-shield-alt" style="width: 18px; font-size: 14px; color: #dc2626;"></i> מדיניות פרטיות</a></li>
                    <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>/terms-of-service" style="color: #bbb; text-decoration: none; transition: all 0.3s; display: flex; align-items: center; gap: 8px; font-size: 0.9rem;"><i class="fas fa-file-contract" style="width: 18px; font-size: 14px; color: #dc2626;"></i> תנאי שימוש</a></li>
                    <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>/accessibility-statement" style="color: #bbb; text-decoration: none; transition: all 0.3s; display: flex; align-items: center; gap: 8px; font-size: 0.9rem;"><i class="fas fa-universal-access" style="width: 18px; font-size: 14px; color: #dc2626;"></i> הצהרת נגישות</a></li>
                    <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>/cookie-policy" style="color: #bbb; text-decoration: none; transition: all 0.3s; display: flex; align-items: center; gap: 8px; font-size: 0.9rem;"><i class="fas fa-cookie-bite" style="width: 18px; font-size: 14px; color: #dc2626;"></i> מדיניות עוגיות</a></li>
                </ul>
            </div>
            
            <!-- Contact Section -->
            <div class="footer-section">
                <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; color: #dc2626; position: relative; padding-bottom: 10px;">
                    <i class="fas fa-phone"></i> צור קשר
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 10px;">
                        <span style="color: #bbb; display: flex; align-items: center; gap: 8px; font-size: 0.95rem;">
                            <i class="fas fa-map-marker-alt" style="width: 18px; font-size: 14px; color: #dc2626;"></i>
                            <span>[כתובת העסק]</span>
                        </span>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <span style="color: #bbb; display: flex; align-items: center; gap: 8px; font-size: 0.95rem;">
                            <i class="fas fa-phone" style="width: 18px; font-size: 14px; color: #dc2626;"></i>
                            <span><?php echo CONTACT_PHONE ?? '050-0000000'; ?></span>
                        </span>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <span style="color: #bbb; display: flex; align-items: center; gap: 8px; font-size: 0.95rem;">
                            <i class="fas fa-envelope" style="width: 18px; font-size: 14px; color: #dc2626;"></i>
                            <span><?php echo CONTACT_EMAIL ?? 'info@example.com'; ?></span>
                        </span>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <span style="color: #bbb; display: flex; align-items: center; gap: 8px; font-size: 0.95rem;">
                            <i class="fas fa-clock" style="width: 18px; font-size: 14px; color: #dc2626;"></i>
                            <span>ראשון-חמישי: 09:00-20:00</span>
                        </span>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <span style="color: #bbb; display: flex; align-items: center; gap: 8px; font-size: 0.95rem;">
                            <i class="fas fa-clock" style="width: 18px; font-size: 14px; color: #dc2626;"></i>
                            <span>שישי: 09:00-14:00</span>
                        </span>
                    </li>
                </ul>
                
                <!-- Accessibility Badge -->
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.08);">
                    <a href="<?php echo SITE_URL; ?>/accessibility-statement" style="
                        display: flex;
                        align-items: center;
                        gap: 8px;
                        color: #bbb;
                        text-decoration: none;
                        transition: all 0.3s;
                        font-size: 0.9rem;
                        padding: 4px 0;
                    ">
                        <i class="fas fa-universal-access" style="width: 18px; font-size: 14px; color: #dc2626;"></i>
                        <span>הצהרת נגישות</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div style="border-top: 1px solid rgba(255,255,255,0.08); padding: 20px 0; margin-top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div style="color: #6c757d; font-size: 0.85rem;">
                    <p style="margin: 0;">&copy; <?= date('Y') ?> <?php echo SITE_NAME; ?>. כל הזכויות שמורות.</p>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <a href="<?php echo SITE_URL; ?>/privacy-policy" style="color: #888; text-decoration: none; font-size: 0.8rem; transition: color 0.3s;">פרטיות</a>
                    <span style="color: #444;">|</span>
                    <a href="<?php echo SITE_URL; ?>/terms-of-service" style="color: #888; text-decoration: none; font-size: 0.8rem; transition: color 0.3s;">תנאים</a>
                    <span style="color: #444;">|</span>
                    <a href="<?php echo SITE_URL; ?>/accessibility-statement" style="color: #888; text-decoration: none; font-size: 0.8rem; transition: color 0.3s;">נגישות</a>
                    <span style="color: #444;">|</span>
                    <a href="<?php echo SITE_URL; ?>/cookie-policy" style="color: #888; text-decoration: none; font-size: 0.8rem; transition: color 0.3s;">עוגיות</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- ========== DRAGGABLE ACCESSIBILITY TOOLBAR ========== -->
<div id="accessibilityToolbar" style="
    position: fixed;
    bottom: 20px;
    left: 20px;
    background: #1a1a1a;
    padding: 8px 12px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.5);
    display: flex;
    gap: 6px;
    z-index: 999;
    border: 1px solid rgba(220, 38, 38, 0.3);
    cursor: grab;
    touch-action: none;
    user-select: none;
    -webkit-user-select: none;
">
    <!-- Drag Handle -->
    <div style="
        display: flex;
        align-items: center;
        padding-left: 5px;
        cursor: grab;
        color: #666;
        font-size: 14px;
    ">
        <i class="fas fa-grip-vertical"></i>
    </div>
    
    <button onclick="increaseFont()" title="הגדל טקסט" style="
        background: rgba(255,255,255,0.08);
        border: none;
        color: #fff;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 13px;
        font-weight: 500;
    ">
        <i class="fas fa-font"></i> A+
    </button>
    <button onclick="decreaseFont()" title="הקטן טקסט" style="
        background: rgba(255,255,255,0.08);
        border: none;
        color: #fff;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 13px;
        font-weight: 500;
    ">
        <i class="fas fa-font"></i> A-
    </button>
    <button onclick="toggleContrast()" title="ניגודיות גבוהה" style="
        background: rgba(255,255,255,0.08);
        border: none;
        color: #fff;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 13px;
        font-weight: 500;
    ">
        <i class="fas fa-adjust"></i>
    </button>
    <button onclick="resetFontSize()" title="איפוס גודל טקסט" style="
        background: rgba(255,255,255,0.08);
        border: none;
        color: #fff;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 13px;
        font-weight: 500;
    ">
        <i class="fas fa-undo-alt"></i>
    </button>
    <button onclick="closeToolbar()" title="סגור" style="
        background: rgba(255,255,255,0.05);
        border: none;
        color: #666;
        padding: 8px 10px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 13px;
    ">
        <i class="fas fa-times"></i>
    </button>
</div>

<!-- Show/Hide Button -->
<button id="showToolbarBtn" onclick="showToolbar()" style="
    position: fixed;
    bottom: 20px;
    left: 20px;
    background: #dc2626;
    color: white;
    border: none;
    padding: 10px 14px;
    border-radius: 50%;
    cursor: pointer;
    z-index: 998;
    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
    display: none;
    font-size: 18px;
    transition: all 0.3s;
">
    <i class="fas fa-universal-access"></i>
</button>

<style>
@keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.footer-section ul li a:hover {
    color: #dc2626 !important;
    transform: translateX(-5px);
}

.footer-section ul li a:hover i {
    color: #dc2626 !important;
    transform: scale(1.2);
}

.footer-social a:hover {
    background: #dc2626 !important;
    transform: translateY(-3px) scale(1.05) !important;
    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4) !important;
}

.footer-bottom a:hover {
    color: #dc2626 !important;
}

#accessibilityToolbar button:hover {
    background: #dc2626 !important;
    transform: scale(1.05) !important;
}

#accessibilityToolbar .drag-handle:hover {
    color: #dc2626 !important;
}

#accessibilityToolbar.dragging {
    cursor: grabbing !important;
    box-shadow: 0 8px 30px rgba(0,0,0,0.6);
    transform: scale(1.02);
}

#accessibilityToolbar.dragging button {
    pointer-events: none;
}

#showToolbarBtn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(220, 38, 38, 0.5);
}

/* Mobile Responsive */
@media (max-width: 992px) {
    .footer-content {
        grid-template-columns: 1fr 1fr !important;
        gap: 30px !important;
    }
}

@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr !important;
        gap: 25px !important;
        text-align: center !important;
    }
    
    .brand-section .footer-logo {
        justify-content: center !important;
    }
    
    .footer-social {
        justify-content: center !important;
    }
    
    .footer-section ul li a,
    .footer-section ul li span {
        justify-content: center !important;
    }
    
    .accessibility-link {
        justify-content: center !important;
    }
    
    #accessibilityToolbar {
        padding: 6px 10px !important;
        gap: 4px !important;
        bottom: 10px;
        left: 10px;
    }
    
    #accessibilityToolbar button {
        padding: 6px 10px !important;
        font-size: 11px !important;
    }
}

@media (max-width: 576px) {
    .footer-social a {
        width: 35px !important;
        height: 35px !important;
        font-size: 15px !important;
    }
}
</style>

<script>
// ========== ACCESSIBILITY FUNCTIONS ==========
function increaseFont() {
    const body = document.body;
    const currentSize = parseFloat(getComputedStyle(body).fontSize);
    if (currentSize < 28) {
        body.style.fontSize = (currentSize * 1.1) + 'px';
        localStorage.setItem('fontSize', body.style.fontSize);
    }
}

function decreaseFont() {
    const body = document.body;
    const currentSize = parseFloat(getComputedStyle(body).fontSize);
    if (currentSize > 12) {
        body.style.fontSize = (currentSize / 1.1) + 'px';
        localStorage.setItem('fontSize', body.style.fontSize);
    }
}

function resetFontSize() {
    document.body.style.fontSize = '16px';
    localStorage.removeItem('fontSize');
}

function toggleContrast() {
    const body = document.body;
    if (body.classList.contains('high-contrast')) {
        body.classList.remove('high-contrast');
        localStorage.setItem('contrast', 'off');
    } else {
        body.classList.add('high-contrast');
        localStorage.setItem('contrast', 'on');
    }
}

function closeToolbar() {
    document.getElementById('accessibilityToolbar').style.display = 'none';
    document.getElementById('showToolbarBtn').style.display = 'block';
}

function showToolbar() {
    document.getElementById('accessibilityToolbar').style.display = 'flex';
    document.getElementById('showToolbarBtn').style.display = 'none';
}

// ========== DRAG FUNCTIONALITY ==========
document.addEventListener('DOMContentLoaded', function() {
    const toolbar = document.getElementById('accessibilityToolbar');
    let isDragging = false;
    let startX, startY, initialX, initialY;
    
    // Restore position from localStorage
    const savedPosition = localStorage.getItem('accessibilityToolbarPosition');
    if (savedPosition) {
        const pos = JSON.parse(savedPosition);
        if (pos.x && pos.y) {
            toolbar.style.left = pos.x + 'px';
            toolbar.style.top = pos.y + 'px';
            toolbar.style.bottom = 'auto';
            toolbar.style.right = 'auto';
        }
    }
    
    // Save position function
    function savePosition(x, y) {
        localStorage.setItem('accessibilityToolbarPosition', JSON.stringify({ x, y }));
    }
    
    // Start drag
    toolbar.addEventListener('mousedown', function(e) {
        // Don't drag if clicking on a button
        if (e.target.closest('button')) return;
        
        isDragging = true;
        toolbar.classList.add('dragging');
        
        const rect = toolbar.getBoundingClientRect();
        startX = e.clientX;
        startY = e.clientY;
        initialX = rect.left;
        initialY = rect.top;
        
        document.addEventListener('mousemove', onDrag);
        document.addEventListener('mouseup', stopDrag);
        
        e.preventDefault();
    });
    
    // Touch events for mobile
    toolbar.addEventListener('touchstart', function(e) {
        if (e.target.closest('button')) return;
        
        isDragging = true;
        toolbar.classList.add('dragging');
        
        const touch = e.touches[0];
        const rect = toolbar.getBoundingClientRect();
        startX = touch.clientX;
        startY = touch.clientY;
        initialX = rect.left;
        initialY = rect.top;
        
        document.addEventListener('touchmove', onTouchDrag, { passive: false });
        document.addEventListener('touchend', stopTouchDrag);
        
        e.preventDefault();
    }, { passive: false });
    
    function onDrag(e) {
        if (!isDragging) return;
        
        const deltaX = e.clientX - startX;
        const deltaY = e.clientY - startY;
        
        let newX = initialX + deltaX;
        let newY = initialY + deltaY;
        
        // Keep within viewport
        const toolbarWidth = toolbar.offsetWidth;
        const toolbarHeight = toolbar.offsetHeight;
        const maxX = window.innerWidth - toolbarWidth - 10;
        const maxY = window.innerHeight - toolbarHeight - 10;
        
        newX = Math.max(10, Math.min(newX, maxX));
        newY = Math.max(10, Math.min(newY, maxY));
        
        toolbar.style.left = newX + 'px';
        toolbar.style.top = newY + 'px';
        toolbar.style.bottom = 'auto';
        toolbar.style.right = 'auto';
    }
    
    function onTouchDrag(e) {
        if (!isDragging) return;
        const touch = e.touches[0];
        
        const deltaX = touch.clientX - startX;
        const deltaY = touch.clientY - startY;
        
        let newX = initialX + deltaX;
        let newY = initialY + deltaY;
        
        const toolbarWidth = toolbar.offsetWidth;
        const toolbarHeight = toolbar.offsetHeight;
        const maxX = window.innerWidth - toolbarWidth - 10;
        const maxY = window.innerHeight - toolbarHeight - 10;
        
        newX = Math.max(10, Math.min(newX, maxX));
        newY = Math.max(10, Math.min(newY, maxY));
        
        toolbar.style.left = newX + 'px';
        toolbar.style.top = newY + 'px';
        toolbar.style.bottom = 'auto';
        toolbar.style.right = 'auto';
        
        e.preventDefault();
    }
    
    function stopDrag() {
        if (isDragging) {
            const rect = toolbar.getBoundingClientRect();
            savePosition(rect.left, rect.top);
        }
        isDragging = false;
        toolbar.classList.remove('dragging');
        document.removeEventListener('mousemove', onDrag);
        document.removeEventListener('mouseup', stopDrag);
    }
    
    function stopTouchDrag() {
        if (isDragging) {
            const rect = toolbar.getBoundingClientRect();
            savePosition(rect.left, rect.top);
        }
        isDragging = false;
        toolbar.classList.remove('dragging');
        document.removeEventListener('touchmove', onTouchDrag);
        document.removeEventListener('touchend', stopTouchDrag);
    }
    
    // Restore preferences
    const savedFontSize = localStorage.getItem('fontSize');
    if (savedFontSize) {
        document.body.style.fontSize = savedFontSize;
    }
    
    const savedContrast = localStorage.getItem('contrast');
    if (savedContrast === 'on') {
        document.body.classList.add('high-contrast');
    }
});

// ========== KEYBOARD SHORTCUTS ==========
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.shiftKey && (e.key === 'A' || e.key === 'a')) {
        e.preventDefault();
        toggleContrast();
    }
    if (e.ctrlKey && e.shiftKey && (e.key === '+' || e.key === '=')) {
        e.preventDefault();
        increaseFont();
    }
    if (e.ctrlKey && e.shiftKey && (e.key === '-' || e.key === '_')) {
        e.preventDefault();
        decreaseFont();
    }
    if (e.ctrlKey && e.shiftKey && e.key === '0') {
        e.preventDefault();
        resetFontSize();
    }
});
</script>

</body>
</html>