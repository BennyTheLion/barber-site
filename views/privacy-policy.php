<?php
// views/privacy-policy.php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/layout/header.php";
?>

<section class="legal-page">
    <div class="container">
        <div class="legal-content">
            <h1>מדיניות פרטיות</h1>
            <p class="last-updated">עודכן לאחרונה: <?= date('d/m/Y') ?></p>
            
            <div class="legal-section">
                <h2>1. מבוא</h2>
                <p>אנו ב"<?php echo SITE_NAME; ?>" מחויבים להגן על פרטיות המשתמשים שלנו. מדיניות פרטיות זו מסבירה כיצד אנו אוספים, משתמשים, מאחסנים ומגינים על המידע האישי שלך.</p>
            </div>
            
            <div class="legal-section">
                <h2>2. איזה מידע אנו אוספים</h2>
                <p>אנו עשויים לאסוף את סוגי המידע הבאים:</p>
                <ul>
                    <li><strong>מידע אישי:</strong> שם, מספר טלפון, כתובת אימייל.</li>
                    <li><strong>מידע על תורים:</strong> תאריך, שעה, סוג שירות, הערות.</li>
                    <li><strong>מידע טכני:</strong> כתובת IP, סוג דפדפן, מערכת הפעלה.</li>
                    <li><strong>עוגיות (Cookies):</strong> אנו משתמשים בעוגיות לשיפור חוויית המשתמש.</li>
                </ul>
            </div>
            
            <div class="legal-section">
                <h2>3. כיצד אנו משתמשים במידע</h2>
                <p>המידע שאנו אוספים משמש למטרות הבאות:</p>
                <ul>
                    <li>ניהול תורים ומתן שירותים.</li>
                    <li>תקשורת עם לקוחות (אישורים, תזכורות, עדכונים).</li>
                    <li>שיפור השירותים והחוויה באתר.</li>
                    <li>ניתוח סטטיסטי ומחקרי שוק.</li>
                    <li>עמידה בדרישות חוקיות ורגולטוריות.</li>
                </ul>
            </div>
            
            <div class="legal-section">
                <h2>4. שיתוף מידע עם צדדים שלישיים</h2>
                <p>אנו לא מוכרים, משכירים או מעבירים את המידע האישי שלך לצדדים שלישיים, למעט במקרים הבאים:</p>
                <ul>
                    <li>כאשר נדרש על פי חוק.</li>
                    <li>לצורך אספקת שירותים (כגון ספקי תשלום).</li>
                    <li>כדי להגן על זכויותינו או לבצע אכיפה של תנאי השימוש.</li>
                </ul>
            </div>
            
            <div class="legal-section">
                <h2>5. אבטחת מידע</h2>
                <p>אנו נוקטים באמצעי אבטחה מתקדמים כדי להגן על המידע האישי שלך מפני גישה לא מורשית, שינוי, חשיפה או השמדה. אלה כוללים:</p>
                <ul>
                    <li>הצפנת SSL לכל התקשורת באתר.</li>
                    <li>גישה מוגבלת למידע אישי.</li>
                    <li>בדיקות אבטחה שוטפות.</li>
                </ul>
            </div>
            
            <div class="legal-section">
                <h2>6. זכויות המשתמש</h2>
                <p>לפי חוק הגנת הפרטיות, יש לך את הזכויות הבאות:</p>
                <ul>
                    <li>עיון במידע האישי שנאסף עליך.</li>
                    <li>תיקון מידע שגוי או לא עדכני.</li>
                    <li>מחיקת מידע בתנאים מסוימים.</li>
                    <li>הגבלת השימוש במידע.</li>
                    <li>התנגדות לעיבוד מידע.</li>
                    <li>ניידות מידע.</li>
                </ul>
                <p>למימוש זכויות אלו, אנא צור קשר באמצעות הפרטים בתחתית עמוד זה.</p>
            </div>
            
            <div class="legal-section">
                <h2>7. עוגיות (Cookies)</h2>
                <p>אנו משתמשים בעוגיות כדי לשפר את חוויית המשתמש באתר. למידע נוסף, עיין ב<a href="<?php echo SITE_URL; ?>/cookie-policy">מדיניות העוגיות</a> שלנו.</p>
            </div>
            
            <div class="legal-section">
                <h2>8. שינויים במדיניות</h2>
                <p>אנו עשויים לעדכן מדיניות זו מעת לעת. כל שינוי יפורסם בעמוד זה עם תאריך עדכון חדש.</p>
            </div>
            
            <div class="legal-section">
                <h2>9. פרטי התקשרות</h2>
                <p>לשאלות או בירורים בנוגע למדיניות פרטיות זו, אנא פנה אלינו:</p>
                <ul>
                    <li><strong>שם העסק:</strong> <?php echo SITE_NAME; ?></li>
                    <li><strong>אימייל:</strong> <?php echo CONTACT_EMAIL ?? 'info@example.com'; ?></li>
                    <li><strong>טלפון:</strong> <?php echo CONTACT_PHONE ?? '050-0000000'; ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<style>
.legal-page {
    padding: 120px 0 60px;
    background: #f8f9fa;
    min-height: 100vh;
}

.legal-content {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    padding: 40px 50px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.legal-content h1 {
    font-size: 2.2rem;
    font-weight: 800;
    color: #1a1a1a;
    margin-bottom: 5px;
    text-align: center;
}

.legal-content .last-updated {
    text-align: center;
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 30px;
}

.legal-section {
    margin-bottom: 30px;
}

.legal-section h2 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #dc2626;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 2px solid #f0f0f0;
}

.legal-section p {
    color: #333;
    line-height: 1.8;
    margin-bottom: 10px;
}

.legal-section ul {
    padding-right: 20px;
    margin-bottom: 10px;
}

.legal-section ul li {
    color: #333;
    line-height: 1.8;
    margin-bottom: 5px;
}

.legal-section ul li strong {
    color: #1a1a1a;
}

.legal-section a {
    color: #dc2626;
    text-decoration: none;
}

.legal-section a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .legal-content {
        padding: 25px 20px;
    }
    
    .legal-content h1 {
        font-size: 1.8rem;
    }
    
    .legal-section h2 {
        font-size: 1.1rem;
    }
}
</style>

<?php require_once __DIR__ . "/layout/footer.php"; ?>