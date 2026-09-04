<?php
require_once __DIR__ . "/layout/header.php";

// הצג הודעת הצלחה אם קיימת
if(isset($_SESSION['booking_success']) && isset($_SESSION['booking_message'])): ?>
<div class="container mt-3">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <h4 class="alert-heading">
            <i class="fas fa-check-circle"></i> התור נקבע בהצלחה!
        </h4>
        <pre style="white-space: pre-wrap; font-family: inherit;"><?= htmlspecialchars($_SESSION['booking_message']) ?></pre>
        <hr>
        <p class="mb-0">
            <i class="fas fa-info-circle"></i> 
            <strong>שמור את הקישור!</strong> תוכל להשתמש בו כדי לבטל או לשנות את התור.
        </p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php 
unset($_SESSION['booking_message']);
unset($_SESSION['booking_success']);
endif;

$db = Database::getInstance()->getConnection();
$services = $db->query("SELECT * FROM services WHERE is_active = 1")->fetchAll();
?>

<div class="container" style="padding: 100px 0;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0"><i class="fas fa-cut"></i> קביעת תור</h3>
                </div>
                <div class="card-body">
                    <form id="bookingForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold">בחר שירות:</label>
                            <select name="service_id" id="service_id" class="form-select" required>
                                <option value="">-- בחר שירות --</option>
                                <?php foreach($services as $s): ?>
                                <option value="<?= $s['id'] ?>" data-duration="<?= $s['duration'] ?>">
                                    <?= htmlspecialchars($s['name']) ?> - ₪<?= number_format($s['price'], 2) ?> (<?= $s['duration'] ?> דקות)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">בחר תאריך:</label>
                            <input type="date" name="appointment_date" id="appointment_date" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">בחר שעה:</label>
                            <div id="slotsContainer" class="row g-2">
                                <div class="col-12 text-muted text-center p-3">יש לבחור שירות ותאריך</div>
                            </div>
                            <input type="hidden" name="appointment_time" id="appointment_time">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">שם מלא:</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">טלפון:</label>
                            <input type="tel" name="phone" class="form-control" required placeholder="050-0000000">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">אימייל:</label>
                            <input type="email" name="email" class="form-control" required placeholder="name@example.com">
                            <small class="text-muted">נשלח אליו אישור ועדכונים על התור</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">הערות (אופציונלי):</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-danger w-100 py-2">
                            <i class="fas fa-check-circle"></i> קבע תור
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo SITE_URL; ?>/assets/js/push-client.js"></script>
<script>
// קביעת תאריכים
const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);
document.getElementById('appointment_date').min = tomorrow.toISOString().split('T')[0];

const maxDate = new Date(today);
maxDate.setDate(maxDate.getDate() + 30);
document.getElementById('appointment_date').max = maxDate.toISOString().split('T')[0];

// אלמנטים
const serviceSelect = document.getElementById('service_id');
const dateInput = document.getElementById('appointment_date');
const slotsContainer = document.getElementById('slotsContainer');
const timeInput = document.getElementById('appointment_time');

// טעינת שעות
async function loadSlots() {
    const serviceId = serviceSelect.value;
    const date = dateInput.value;
    
    if(!serviceId || !date) {
        slotsContainer.innerHTML = '<div class="col-12 text-muted text-center p-3">יש לבחור שירות ותאריך</div>';
        return;
    }
    
    slotsContainer.innerHTML = '<div class="col-12 text-center p-3"><div class="spinner-border text-danger"></div><br>טוען שעות...</div>';
    
    try {
       
        const url = `<?php echo SITE_URL; ?>/index.php?controller=booking&action=getAvailableSlots&service_id=${serviceId}&date=${date}`;
         
        const response = await fetch(url);
        const slots = await response.json();
       
        if(slots.error) {
            slotsContainer.innerHTML = `<div class="col-12 alert alert-warning">${slots.error}</div>`;
            return;
        }
        
        if(!slots || slots.length === 0) {
            slotsContainer.innerHTML = '<div class="col-12 alert alert-warning">אין שעות פנויות ביום זה</div>';
            return;
        }
        
        let html = '';
        slots.forEach(slot => {
            const timeOnly = slot.substring(0, 5);
            html += `
                <div class="col-4 col-md-3">
                    <button type="button" class="btn btn-outline-danger w-100 time-slot" data-time="${slot}">
                        ${timeOnly}
                    </button>
                </div>
            `;
        });
        
        slotsContainer.innerHTML = html;
        
        // הוסף מאזינים
        document.querySelectorAll('.time-slot').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.time-slot').forEach(b => {
                    b.classList.remove('active', 'btn-danger');
                    b.classList.add('btn-outline-danger');
                });
                this.classList.remove('btn-outline-danger');
                this.classList.add('active', 'btn-danger');
                timeInput.value = this.dataset.time;
            });
        });
        
    } catch(error) {
        console.error('Error:', error);
        slotsContainer.innerHTML = '<div class="col-12 alert alert-danger">שגיאה בטעינת שעות</div>';
    }
}

// האזנה לשינויים
serviceSelect.addEventListener('change', loadSlots);
dateInput.addEventListener('change', loadSlots);

// שליחת טופס
document.getElementById('bookingForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if(!timeInput.value) {
        alert('אנא בחר שעה');
        return;
    }
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>מזמין תור...';

    // נסה להפעיל התראות פוש (לא חוסם - אם המשתמש דוחה/הדפדפן לא תומך, ממשיכים בלי)
    if (PushClient.isSupported()) {
        try {
            const subscription = await PushClient.subscribe();
            formData.append('push_subscription', JSON.stringify(subscription));
        } catch (pushError) {
            console.log('Push subscribe skipped:', pushError.message);
        }
    }

    try {
        const response = await fetch('<?php echo SITE_URL; ?>/index.php?controller=booking&action=create', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if(result.success) {
            alert('✅ התור נקבע בהצלחה!');
			 const url = `<?php echo SITE_URL; ?>/index.php?controller=booking&action=manage&id=${result.appointment_id}&token=${result.token}`;
    
    // במקום הפניה אוטומטית, הצג קישור
    const message = `
        <div class="alert alert-success">
            <h4>✅ התור נקבע בהצלחה!</h4>
            <p>לחץ כאן לניהול התור:</p>
            <a href="${url}" class="btn btn-primary">ניהול תור</a>
            <button onclick="window.location.href='${url}'" class="btn btn-success">המשך אוטומטית</button>
            <p>שמור את הקישור הזה - תוכל להשתמש בו כדי לבטל או לשנות את התור:</p>
			<div class="input-group mb-3">
				<input type="text" class="form-control" id="bookingLink" value="<?= htmlspecialchars('${url}') ?>" readonly>
				<button class="btn btn-primary" type="button" onclick="copyToClipboard()">
					<i class="fas fa-copy"></i> העתק קישור
				</button>
			</div>
			<small class="text-muted">
				<i class="fas fa-share-alt"></i> 
				תוכל לשתף קישור זה או לשמור אותו לעצמך
			</small>
        </div>
    `;
    
    // הוסף את ההודעה לדף במקום ההפניה
    document.getElementById('bookingForm').innerHTML = message;
} else {
            alert('❌ שגיאה: ' + result.error);
            if(result.error.includes('תפוסה')) {
                loadSlots();
            }
        }
    } catch(error) {
        alert('אירעה שגיאה. אנא נסה שוב.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});
</script>

<?php require_once __DIR__ . "/layout/footer.php"; ?>