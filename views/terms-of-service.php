<?php
// views/terms-of-service.php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/layout/header.php";
?>

<section class="legal-page">
    <div class="container">
        <div class="legal-content">
            <h1>תנאי שימוש</h1>
            <p class="last-updated">עודכן לאחרונה: <?= date('d/m/Y') ?></p>
            
            <div class="legal-section">
                <h2>1. הסכמה לתנאים</h2>
                <p>בכניסה לשימוש באתר <?php echo SITE_NAME; ?>, אתה מסכים לתנאי שימוש אלה. אם אינך מסכים, אנא אל תשתמש באתר.</p>
            </div>
            
            <div class="legal-section">
                <h2>2. שימוש באתר</h2>
                <p>אתה מתחייב להשתמש באתר רק למטרות חוקיות ובאופן שאינו פוגע בזכויות אחרים. אסור:</p>
                <ul>
                    <li>להשתמש באתר לפעילות בלתי חוקית.</li>
                    <li>לפגוע באבטחת האתר או בניסיון לפרוץ למערכת.</li>
                    <li>להפיץ וירוסים או קוד זדוני.</li>
                    <li>להטריד, לאיים או לפגוע באחרים.</li>
                </ul>
            </div>
            
            <div class="legal-section">
                <h2>3. הזמנת תורים</h2>
                <p>בעת הזמנת תור באתר, אתה מתחייב:</p>
                <ul>
                    <li>לספק מידע נכון ומדויק.</li>
                    <li>להגיע בזמן לתור שנקבע.</li>
                    <li>להודיע על ביטול או שינוי תור לפחות 24 שעות מראש.</li>
                    <li>לכבד את מדיניות הביטולים של העסק.</li>
                </ul>
            </div>
            
            <div class="legal-section">
                <h2>4. ביטולים והחזרים</h2>
                <ul>
                    <li><strong>ביטול תור:</strong> ביטול תור יתאפשר עד 24 שעות לפני מועד התור.</li>
                    <li><strong>אי הגעה:</strong> אי הגעה לתור ללא הודעה מוקדמת עלולה לגרור חיוב.</li>
                    <li><strong>החזרים:</strong> החזרים יינתנו לפי שיקול דעת הנהלת העסק.</li>
                </ul>
            </div>
            
            <div class="legal-section">
                <h2>5. קניין רוחני</h2>
                <p>כל התכנים באתר, כולל טקסטים, תמונות, לוגואים, ועיצוב, הם רכושם של <?php echo SITE_NAME; ?> ומוגנים בזכויות יוצרים. אסור להעתיק, לשכפל או להפיץ תוכן ללא אישור.</p>
            </div>
            
            <div class="legal-section">
                <h2>6. אחריות</h2>
                <p>האתר והשירותים ניתנים "כפי שהם" (AS IS). אנו לא נושאים באחריות לנזקים ישירים או עקיפים הנובעים משימוש באתר.</p>
            </div>
            
            <div class="legal-section">
                <h2>7. שינויים בתנאים</h2>
                <p>אנו עשויים לעדכן תנאים אלה מעת לעת. מומלץ לבדוק עמוד זה מדי פעם.</p>
            </div>
            
            <div class="legal-section">
                <h2>8. חוקים ורגולציה</h2>
                <p>תנאי שימוש אלה מוסדרים על פי חוקי מדינת ישראל.</p>
            </div>
            
            <div class="legal-section">
                <h2>9. פרטי התקשרות</h2>
                <p>לשאלות בנוגע לתנאי שימוש, אנא פנה אלינו:</p>
                <ul>
                    <li><strong>אימייל:</strong> <?php echo CONTACT_EMAIL ?? 'info@example.com'; ?></li>
                    <li><strong>טלפון:</strong> <?php echo CONTACT_PHONE ?? '050-0000000'; ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . "/layout/footer.php"; ?>