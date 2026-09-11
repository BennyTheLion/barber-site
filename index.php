<!doctype html>
<html lang="he" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>LINE. — קביעת תור</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="assets/style.css?v=<?= @filemtime(__DIR__ . '/assets/style.css') ?>">
</head>
<body>
<div id="confetti-layer"></div>

<!-- HEADER -->
<div class="site-header">
  <div class="brand"><span class="logo-dot">●</span> LINE.</div>
  <div>
    <button id="admin-login-btn" class="icon-btn hidden" onclick="openLoginModal()" aria-label="כניסת מנהל" title="כניסת מנהל">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
    </button>
    <a id="admin-panel-btn" href="admin.php" class="icon-btn hidden" aria-label="פאנל ניהול" title="פאנל ניהול" style="text-decoration:none;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.34 1.87l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.7 1.7 0 00-1.87-.34 1.7 1.7 0 00-1 1.55V21a2 2 0 01-4 0v-.09a1.7 1.7 0 00-1-1.55 1.7 1.7 0 00-1.87.34l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.7 1.7 0 00.34-1.87 1.7 1.7 0 00-1.55-1H3a2 2 0 010-4h.09a1.7 1.7 0 001.55-1 1.7 1.7 0 00-.34-1.87l-.06-.06a2 2 0 112.83-2.83l.06.06a1.7 1.7 0 001.87.34H9a1.7 1.7 0 001-1.55V3a2 2 0 014 0v.09a1.7 1.7 0 001 1.55 1.7 1.7 0 001.87-.34l.06-.06a2 2 0 112.83 2.83l-.06.06a1.7 1.7 0 00-.34 1.87V9a1.7 1.7 0 001.55 1H21a2 2 0 010 4h-.09a1.7 1.7 0 00-1.55 1z"/></svg>
    </a>
  </div>
</div>

<div class="wrap">

  <!-- HERO -->
  <div class="reveal" style="display:flex; flex-direction:column; align-items:flex-start; gap:16px; padding:20px 20px 28px; animation-delay:.1s;">
    <h1 style="font-size:32px; line-height:1.2; font-weight:800; margin:0;" id="hero-title"><span id="owner-name">איתי וקנין</span><br><span id="owner-tagline" style="font-weight:600;">ספר מעולם אחר</span></h1>

    <!-- animated haircut illustration (hand-drawn cartoon character) -->
    <div class="hero-art" style="width:100%; aspect-ratio:4/3; border-radius:20px; position:relative; overflow:hidden; background:radial-gradient(circle at 50% 38%, oklch(0.24 0.02 260) 0%, oklch(0.13 0.006 260) 72%);">
      <div class="hero-glow"></div>
      <svg viewBox="0 0 160 160" style="position:absolute; inset:0; width:100%; height:100%;">
        <g class="hero-sparkle" style="animation-delay:0s;">
          <path d="M30 34 L32 40 L38 42 L32 44 L30 50 L28 44 L22 42 L28 40 Z" fill="oklch(0.85 0.19 145 / 0.8)"/>
        </g>
        <g class="hero-sparkle" style="animation-delay:.6s;">
          <path d="M128 100 L129.5 104 L133.5 105.5 L129.5 107 L128 111 L126.5 107 L122.5 105.5 L126.5 104 Z" fill="oklch(0.94 0.006 260 / 0.7)"/>
        </g>
        <path class="hero-clip" style="transform-origin:112px 42px; animation-delay:0s;" d="M112 42 Q118 38 123 41 Q119 46 112 42 Z" fill="oklch(0.85 0.19 145 / 0.8)"/>
        <path class="hero-clip" style="transform-origin:108px 38px; animation-delay:.35s;" d="M108 38 Q113 33 118 36 Q114 41 108 38 Z" fill="oklch(0.85 0.19 145 / 0.6)"/>
        <path class="hero-clip" style="transform-origin:116px 48px; animation-delay:.7s;" d="M116 48 Q121 45 125 48 Q121 52 116 48 Z" fill="oklch(0.94 0.006 260 / 0.5)"/>
        <g class="hero-char">
          <path d="M44 158 L58 122 L102 122 L116 158 Z" fill="oklch(0.21 0.01 260)" stroke="oklch(0.85 0.19 145 / 0.5)" stroke-width="2"/>
          <path d="M58 122 L102 122 L98 132 L62 132 Z" fill="oklch(0.85 0.19 145 / 0.25)"/>
          <rect x="72" y="106" width="16" height="18" rx="6" fill="oklch(0.78 0.07 55)"/>
          <circle cx="80" cy="82" r="36" fill="oklch(0.83 0.06 55)"/>
          <circle cx="45" cy="84" r="7" fill="oklch(0.83 0.06 55)"/>
          <circle cx="115" cy="84" r="7" fill="oklch(0.83 0.06 55)"/>
          <path d="M42 78 Q38 105 52 118 Q46 96 50 78 Z" fill="oklch(0.85 0.19 145)"/>
          <path d="M118 78 Q122 105 108 118 Q114 96 110 78 Z" fill="oklch(0.85 0.19 145)"/>
          <ellipse cx="58" cy="92" rx="6" ry="3.5" fill="oklch(0.7 0.16 20 / 0.55)"/>
          <ellipse cx="102" cy="92" rx="6" ry="3.5" fill="oklch(0.7 0.16 20 / 0.55)"/>
          <g><ellipse cx="66" cy="80" rx="6.5" ry="8.5" fill="oklch(0.16 0.008 260)"/><circle cx="68" cy="76.5" r="2.2" fill="oklch(0.98 0.01 85)"/></g>
          <g><ellipse cx="94" cy="80" rx="6.5" ry="8.5" fill="oklch(0.16 0.008 260)"/><circle cx="96" cy="76.5" r="2.2" fill="oklch(0.98 0.01 85)"/></g>
          <path d="M70 98 Q80 105 90 98" fill="none" stroke="oklch(0.3 0.03 40)" stroke-width="2.4" stroke-linecap="round"/>
          <path d="M44 62 Q42 40 58 34 Q56 24 68 22 Q68 12 80 16 Q90 10 94 22 Q106 22 106 34 Q120 38 116 60 Q104 46 98 56 Q90 42 82 54 Q74 40 66 54 Q56 44 44 62 Z"
                fill="oklch(0.85 0.19 145)" stroke="oklch(0.65 0.15 145)" stroke-width="1.5" stroke-linejoin="round"/>
        </g>
        <g class="hero-scissors" style="transform-origin:112px 42px;">
          <circle cx="102" cy="50" r="4.5" fill="none" stroke="oklch(0.94 0.006 260)" stroke-width="2.5"/>
          <g class="hero-blade-top" style="transform-origin:112px 42px;"><line x1="112" y1="42" x2="132" y2="24" stroke="oklch(0.94 0.006 260)" stroke-width="2.5" stroke-linecap="round"/></g>
          <g class="hero-blade-bottom" style="transform-origin:112px 42px;">
            <line x1="112" y1="42" x2="102" y2="50" stroke="oklch(0.94 0.006 260)" stroke-width="2.5" stroke-linecap="round"/>
            <line x1="112" y1="42" x2="133" y2="52" stroke="oklch(0.94 0.006 260)" stroke-width="2.5" stroke-linecap="round"/>
          </g>
        </g>
      </svg>
    </div>

    <div style="font-size:15.5px; line-height:1.6; color:oklch(0.94 0.006 260 / 0.65);">בוחרים שירות וזמן פנוי — אישור מיידי ותזכורת לפני התור.</div>
    <button id="hero-cta" class="btn btn-primary btn-auto hero-shake" style="align-self:center;" onclick="revealBooking()">קביעת תור</button>
    <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:oklch(0.94 0.006 260 / 0.5);">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="oklch(0.85 0.19 145)" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/></svg>
      זמינות בזמן אמת · ללא הרשמה לצפייה
    </div>
  </div>

  <!-- GALLERY (photos) -->
  <div id="gallery-section" class="observe hidden" style="margin-bottom:28px;">
    <div class="section-title" style="padding:0 20px;">גלריה</div>
    <div class="gallery-wrap">
      <div class="gallery-scroll" id="gallery-scroll"></div>
      <button class="gallery-arrow gallery-arrow-left" id="gallery-arrow-left" aria-label="תמונה הבאה">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 6l-6 6 6 6"/></svg>
      </button>
      <button class="gallery-arrow gallery-arrow-right" id="gallery-arrow-right" aria-label="תמונה קודמת">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 6l6 6-6 6"/></svg>
      </button>
    </div>
    <div class="gallery-dots" id="gallery-dots"></div>
  </div>

  <!-- GALLERY (videos) -->
  <div id="video-section" class="observe hidden" style="margin-bottom:28px;">
    <div class="section-title" style="padding:0 20px;">סרטונים</div>
    <div class="gallery-scroll" id="video-scroll"></div>
  </div>

  <!-- WORKING HOURS -->
  <div class="observe card" style="margin:0 20px 28px; padding:18px;">
    <div class="section-title">שעות פעילות</div>
    <div id="working-hours-list"></div>
  </div>

  <!-- BOOKING CARD -->
  <div id="booking-card" class="card booking-hidden" style="margin:0 20px 32px; padding:22px 18px; flex-direction:column; gap:20px;">

    <div id="confirmed-view" class="hidden" style="display:flex; flex-direction:column; align-items:center; gap:14px; padding:12px 4px 4px; text-align:center;">
      <div class="check-circle" style="width:56px; height:56px; border-radius:50%; background:oklch(0.85 0.19 145 / 0.15); display:flex; align-items:center; justify-content:center;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="oklch(0.85 0.19 145)" stroke-width="2.4"><circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/></svg>
      </div>
      <div style="font-size:18px; font-weight:800;">התור נקבע בהצלחה!</div>
      <div id="summary-text" style="font-size:14.5px; color:oklch(0.94 0.006 260 / 0.65); line-height:1.6;"></div>
      <button class="btn btn-ghost" style="margin-top:6px;" onclick="resetBooking()">קביעת תור נוסף</button>
    </div>

    <div id="booking-form">
      <div style="font-size:14px; font-weight:600; color:oklch(0.94 0.006 260 / 0.6); margin-bottom:20px;">קביעת תור מהירה</div>

      <div style="margin-bottom:20px;">
        <div class="section-title">שירות</div>
        <div id="service-list" style="display:flex; flex-direction:column; gap:8px;"></div>
      </div>

      <div class="field">
        <label>תאריך</label>
        <input type="date" id="date-input">
      </div>

      <div style="margin-bottom:20px;">
        <div class="section-title">שעות פנויות</div>
        <div id="slot-list" style="display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:8px;"></div>
      </div>

      <div class="field"><label>שם מלא</label><input type="text" id="cust-name" placeholder="השם שלך"></div>
      <div class="field"><label>טלפון</label><input type="tel" id="cust-phone" placeholder="050-0000000"></div>
      <div class="field"><label>אימייל (לקבלת אישור, אופציונלי)</label><input type="email" id="cust-email" placeholder="you@example.com"></div>

      <button id="confirm-btn" class="btn btn-primary" disabled onclick="confirmBooking()">אישור התור</button>
    </div>
  </div>

  <!-- MANAGE EXISTING BOOKING (cancel / reschedule, self-service by phone) -->
  <div id="manage-card" class="observe card" style="margin:0 20px 32px; padding:22px 18px;">
    <div class="section-title">ניהול תור קיים</div>
    <div style="font-size:13px; color:oklch(0.94 0.006 260 / 0.55); margin-bottom:14px; line-height:1.6;">
      הזינו את הטלפון שאיתו קבעתם את התור, כדי לעדכן מועד או לבטל.
    </div>
    <div style="display:flex; gap:8px; align-items:flex-end;">
      <div class="field" style="margin-bottom:0; flex:1;"><label>טלפון</label><input type="tel" id="manage-phone" placeholder="050-0000000"></div>
      <button class="btn btn-ghost btn-auto" onclick="lookupMyBookings()">חיפוש</button>
    </div>
    <div id="manage-list" style="margin-top:16px;"></div>
  </div>

  <!-- CTA -->
  <div class="observe" style="margin:0 20px 28px; padding:24px 20px; border-radius:18px; background:oklch(0.85 0.19 145); display:flex; flex-direction:column; gap:14px;">
    <div>
      <div style="font-size:19px; font-weight:800; color:oklch(0.14 0.01 260); margin-bottom:6px;">קבעו תור בפחות מדקה</div>
      <div style="font-size:13.5px; color:oklch(0.14 0.01 260 / 0.7);">בלי הרשמה, בלי המתנה בטלפון.</div>
    </div>
    <button class="btn" style="background:oklch(0.14 0.01 260); color:oklch(0.94 0.006 260);" onclick="revealBooking()">קביעת תור</button>
  </div>

  <!-- FOOTER -->
  <div class="site-footer">
    <div>
      <span id="footer-contact"></span>
      <a id="footer-email" href="#" class="hidden" style="margin-inline-start:8px;"></a>
    </div>
    <div class="socials" id="footer-socials"></div>
    <div class="legal">
      <a href="privacy.php">מדיניות פרטיות</a>
      <a href="terms.php">תקנון</a>
    </div>
  </div>

</div>

<!-- FLOATING WHATSAPP -->
<a id="whatsapp-fab" href="#" target="_blank" rel="noopener" class="fab fab-whatsapp hidden" aria-label="שלחו לנו הודעת וואטסאפ">
  <svg width="26" height="26" viewBox="0 0 24 24" fill="white"><path d="M12.04 2c-5.46 0-9.9 4.44-9.9 9.9 0 1.75.46 3.45 1.32 4.95L2 22l5.29-1.39a9.87 9.87 0 004.75 1.21h.01c5.46 0 9.9-4.44 9.9-9.9S17.5 2 12.04 2zm0 18.03h-.01a8.2 8.2 0 01-4.18-1.14l-.3-.18-3.12.82.84-3.05-.2-.31a8.2 8.2 0 01-1.26-4.37c0-4.54 3.7-8.24 8.24-8.24 2.2 0 4.27.86 5.83 2.42a8.19 8.19 0 012.41 5.83c0 4.54-3.7 8.24-8.25 8.24zm4.52-6.16c-.25-.12-1.47-.72-1.7-.81-.23-.08-.4-.12-.56.12-.17.25-.65.81-.79.97-.15.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.24-1.47-1.39-1.72-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.44.12-.15.16-.25.25-.41.08-.17.04-.31-.02-.44-.06-.12-.56-1.36-.77-1.86-.2-.48-.41-.42-.56-.42-.14-.01-.31-.01-.48-.01a.92.92 0 00-.67.31c-.23.25-.87.85-.87 2.08 0 1.23.89 2.42 1.02 2.58.12.17 1.75 2.67 4.24 3.75.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.17-.48-.29z"/></svg>
</a>

<!-- ACCESSIBILITY -->
<button class="fab fab-a11y" onclick="toggleA11yPanel()" aria-label="נגישות">
  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="4" r="2"/><path d="M4 8h16M12 8v6M8 22l2-8M16 22l-2-8M8 12l4 2 4-2"/></svg>
</button>
<div id="a11y-panel" class="a11y-panel hidden">
  <button onclick="setA11y('font-lg')">הגדלת טקסט</button>
  <button onclick="setA11y('font-xl')">הגדלת טקסט מקסימלית</button>
  <button onclick="setA11y('contrast')">ניגודיות גבוהה</button>
  <button onclick="setA11y('underline')">קו תחתון לקישורים</button>
  <button onclick="setA11y('reset')">איפוס</button>
</div>

<!-- ADMIN LOGIN MODAL -->
<div id="login-modal" class="modal-backdrop hidden" onclick="if(event.target===this) closeLoginModal()">
  <div class="modal-sheet">
    <div style="font-size:17px; font-weight:800; margin-bottom:16px;">כניסת מנהל</div>
    <div class="field"><label>שם משתמש</label><input type="text" id="login-username"></div>
    <div class="field">
      <label>סיסמה</label>
      <div class="password-wrap">
        <input type="password" id="login-password">
        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('login-password', this)" aria-label="הצג/הסתר סיסמה">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
      </div>
    </div>
    <div id="login-error" style="color:oklch(0.6 0.19 25); font-size:13px; margin-bottom:10px;"></div>
    <button class="btn btn-primary" onclick="submitLogin()">כניסה</button>
    <button class="btn btn-ghost" style="margin-top:10px;" onclick="closeLoginModal()">ביטול</button>
  </div>
</div>

<script src="assets/app.js?v=<?= @filemtime(__DIR__ . '/assets/app.js') ?>"></script>
</body>
</html>
