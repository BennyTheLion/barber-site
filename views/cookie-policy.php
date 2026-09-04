<?php
// views/cookie-policy.php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/layout/header.php";
?>

<section class="legal-page">
    <div class="container">
        <div class="legal-content">
            <h1>מדיניות עוגיות (Cookies)</h1>
            <p class="last-updated">עודכן לאחרונה: <?= date('d/m/Y') ?></p>
            
            <div class="legal-section">
                <h2>1. מה זה עוגיות?</h2>
                <p>עוגיות (Cookies) הן קבצים טקסט קטנים שהאתר מאחסן במכשיר שלך (מחשב, טאבלט או טלפון) כדי לשפר את חווית הגלישה שלך.</p>
            </div>
            
            <div class="legal-section">
                <h2>2. סוגי העוגיות שאנו משתמשים בהם</h2>
                
                <h3>2.1 עוגיות הכרחיות</h3>
                <p>עוגיות אלו נחוצות לתפעול בסיסי של האתר:</p>
                <ul>
                    <li><strong>session_id:</strong> לשמירת מצב ההתחברות.</li>
                    <li><strong>csrf_token:</strong> להגנה מפני התקפות CSRF.</li>
                </ul>
                
                <h3>2.2 עוגיות פונקציונליות</h3>
                <p>עוגיות שמאפשרות שיפור חוויית המשתמש:</p>
                <ul>
                    <li><strong>remember_me:</strong> זכירת פרטי התחברות.</li>
                    <li><strong>language:</strong> שפת העדפה.</li>
                </ul>
                
                <h3>2.3 עוגיות אנליטיקה</h3>
                <p>עוגיות לאיסוף סטטיסטיקות על השימוש באתר:</p>
                <ul>
                    <li><strong>_ga:</strong> Google Analytics - לזיהוי משתמשים.</li>
                    <li><strong>_gid:</strong> Google Analytics - למעקב.</li>
                </ul>
                
                <h3>2.4 עוגיות שיווק</h3>
                <p>עוגיות להצגת מודעות רלוונטיות:</p>
                <ul>
                    <li><strong>_fbp:</strong> Facebook Pixel.</li>
                    <li><strong>ads:</strong> מודעות מותאמות.</li>
                </ul>
            </div>
            
            <div class="legal-section">
                <h2>3. שליטה בעוגיות</h2>
                <p>אתה יכול לשלוט בעוגיות ולנהל אותן דרך הגדרות הדפדפן שלך:</p>
                <ul>
                    <li><strong>Chrome:</strong> הגדרות → פרטיות ואבטחה → עוגיות.</li>
                    <li><strong>Firefox:</strong> פרטיות ואבטחה → עוגיות.</li>
                    <li><strong>Safari:</strong> העדפות → פרטיות → ניהול נתוני אתרים.</li>
                    <li><strong>Edge:</strong> הגדרות → עוגיות.</li>
                </ul>
                <p>שים לב: חסימת עוגיות עלולה לפגוע בחוויית השימוש באתר.</p>
            </div>
            
            <div class="legal-section">
                <h2>4. הסכמה לעוגיות</h2>
                <p>בשימוש באתר, אתה מסכים לשימוש בעוגיות לפי מדיניות זו. תוכל לשנות את ההעדפות שלך בכל עת.</p>
            </div>
            
            <div class="legal-section">
                <h2>5. שינויים במדיניות</h2>
                <p>אנו עשויים לעדכן מדיניות זו מעת לעת. מומלץ לבדוק עמוד זה מדי פעם.</p>
            </div>
            
            <div class="legal-section">
                <h2>6. יצירת קשר</h2>
                <p>לשאלות בנוגע למדיניות העוגיות, אנא פנה אלינו:</p>
                <ul>
                    <li><strong>אימייל:</strong> <?php echo CONTACT_EMAIL ?? 'info@example.com'; ?></li>
                    <li><strong>טלפון:</strong> <?php echo CONTACT_PHONE ?? '050-0000000'; ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . "/layout/footer.php"; ?>