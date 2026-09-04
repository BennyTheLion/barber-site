<?php
// views/accessibility-statement.php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/layout/header.php";
?>

<section class="legal-page">
    <div class="container">
        <div class="legal-content">
            <h1>הצהרת נגישות</h1>
            <p class="last-updated">עודכן לאחרונה: <?= date('d/m/Y') ?></p>
            
            <div class="legal-section">
                <h2>1. מחויבות לנגישות</h2>
                <p>ב<?php echo SITE_NAME; ?> אנו מאמינים שכל אדם זכאי לשירות שוויוני ונגיש. אנו פועלים להנגיש את האתר והשירותים שלנו לכלל האוכלוסייה, כולל אנשים עם מוגבלויות.</p>
            </div>
            
            <div class="legal-section">
                <h2>2. התאמות נגישות באתר</h2>
                <p>האתר תוכנן תוך התחשבות בהנחיות הנגישות לתכני האינטרנט (WCAG) ברמה AA. בין ההתאמות שביצענו:</p>
                <ul>
                    <li><strong>התאמה לטכנולוגיות מסייעות:</strong> האתר תומך בקוראי מסך וכלי עזר נוספים.</li>
                    <li><strong>ניגודיות צבעים:</strong> שימוש בצבעים בעלי ניגודיות גבוהה לקריאות מיטבית.</li>
                    <li><strong>גודל טקסט:</strong> ניתן להגדיל את גודל הטקסט באמצעות הדפדפן.</li>
                    <li><strong>ניווט מקלדת:</strong> ניתן לנווט באתר באמצעות מקלדת בלבד.</li>
                    <li><strong>תיאורי תמונות:</strong> כל התמונות באתר כוללות טקסט חלופי.</li>
                    <li><strong>מבנה ברור:</strong> כותרות ותגיות מסודרות לניווט נוח.</li>
                    <li><strong>שפת תכנים:</strong> תוכן ברור, פשוט וקריא.</li>
                </ul>
            </div>
            
            <div class="legal-section">
                <h2>3. התאמות פיזיות</h2>
                <p>במתחם העסק ביצענו התאמות נגישות פיזיות:</p>
                <ul>
                    <li>כניסה נגישה לבעלי מוגבלות תנועה.</li>
                    <li>שירותי לקוחות מותאמים.</li>
                    <li>אפשרות לתיאום מראש לסיוע מיוחד.</li>
                </ul>
            </div>
            
            <div class="legal-section">
                <h2>4. בקשות והצעות</h2>
                <p>אנו פתוחים להצעות ולבקשות לשיפור הנגישות. אם נתקלת בבעיית נגישות או שיש לך רעיון לשיפור, אנא פנה אלינו.</p>
            </div>
            
            <div class="legal-section">
                <h2>5. פרטי רכז נגישות</h2>
                <ul>
                    <li><strong>שם:</strong> רכז נגישות <?php echo SITE_NAME; ?></li>
                    <li><strong>אימייל:</strong> <?php echo CONTACT_EMAIL ?? 'accessibility@example.com'; ?></li>
                    <li><strong>טלפון:</strong> <?php echo CONTACT_PHONE ?? '050-0000000'; ?></li>
                </ul>
            </div>
            
            <div class="legal-section">
                <h2>6. תאימות לדפדפנים</h2>
                <p>האתר תומך בדפדפנים מודרניים ובטכנולוגיות מסייעות נפוצות. אנו פועלים לשמור על תאימות מרבית.</p>
            </div>
            
            <div class="legal-section">
                <h2>7. הצהרת נגישות מלאה</h2>
                <p>אנו מחויבים להנגיש את האתר לכלל האוכלוסייה. הנגשת האתר בוצעה בהתאם לתקנות שוויון זכויות לאנשים עם מוגבלות, התשע"ג-2013.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . "/layout/footer.php"; ?>