<?php
// views/manage_booking.php - User manages their booking
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/layout/header.php";

$db = Database::getInstance()->getConnection();

// Get appointment data
$appointment = $appointment ?? null;

// Get services for dropdown
$stmt = $db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order");
$services = $stmt->fetchAll();

// Get available dates (next 30 days)
$availableDates = [];
for($i = 1; $i <= 30; $i++) {
    $date = date('Y-m-d', strtotime("+$i days"));
    $dayOfWeek = date('N', strtotime($date));
    
    $stmt = $db->prepare("SELECT * FROM blocked_dates WHERE blocked_date = ?");
    $stmt->execute([$date]);
    $isBlocked = $stmt->fetch();
    
    $stmt = $db->prepare("SELECT * FROM business_hours WHERE day_of_week = ? AND is_active = 1");
    $stmt->execute([$dayOfWeek]);
    $businessHours = $stmt->fetch();
    
    if(!$isBlocked && $businessHours) {
        $availableDates[] = $date;
    }
}
?>

<section class="booking-manage">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="booking-card">
                    <div class="booking-header">
                        <h4><i class="fas fa-calendar-alt"></i> ניהול תור</h4>
                        <p>צפה, ערוך או בטל את התור שלך</p>
                    </div>
                    <div class="booking-body">
                        <?php if(isset($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>
                        
                        <?php if(isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['error'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>
                        
                        <?php if($appointment): ?>
                            <!-- Appointment Details -->
                            <div class="appointment-details">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label>לקוח</label>
                                            <p><?= htmlspecialchars($appointment['customer_name'] ?? '') ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <label>טלפון</label>
                                            <p><?= htmlspecialchars($appointment['phone'] ?? '') ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <label>שירות</label>
                                            <p><?= htmlspecialchars($appointment['service_name'] ?? '') ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label>תאריך</label>
                                            <p><?= date('d/m/Y', strtotime($appointment['appointment_date'])) ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <label>שעה</label>
                                            <p><?= substr($appointment['appointment_time'] ?? '', 0, 5) ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <label>סטטוס</label>
                                            <p>
                                                <span class="status-badge <?= $appointment['status'] == 'cancelled' ? 'status-cancelled' : 'status-active' ?>">
                                                    <?= $appointment['status'] == 'cancelled' ? '❌ בוטל' : '✅ מאושר' ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <?php if(!empty($appointment['notes'])): ?>
                                    <div class="detail-item">
                                        <label>הערות</label>
                                        <p><?= htmlspecialchars($appointment['notes']) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if($appointment['status'] != 'cancelled'): ?>
                                <div class="action-buttons">
                                    <button class="btn btn-edit" onclick="userEditAppointment()">
                                        <i class="fas fa-edit"></i> ערוך תור
                                    </button>
                                    <button class="btn btn-cancel" onclick="userCancelAppointment(<?= $appointment['id'] ?>, '<?= $appointment['token'] ?>')">
                                        <i class="fas fa-times-circle"></i> בטל תור
                                    </button>
                                </div>
                                <p class="text-muted text-center small mt-2">
                                    <i class="fas fa-info-circle"></i> שינויים ישלחו הודעה לעסק
                                </p>
                            <?php else: ?>
                                <div class="alert alert-warning text-center">
                                    <i class="fas fa-info-circle"></i> תור זה בוטל. לא ניתן לבצע שינויים.
                                </div>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                                <h3>תור לא נמצא</h3>
                                <p class="text-muted">הקישור אינו תקף או שהתור לא קיים</p>
                                <a href="<?= SITE_URL ?>" class="btn btn-primary mt-3">חזור לדף הבית</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== EDIT APPOINTMENT MODAL ========== -->
<div class="modal fade" id="editAppointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit text-warning"></i> עריכת תור
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAppointmentForm" method="POST" action="<?php echo SITE_URL; ?>/index.php?controller=booking&action=update">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId" value="<?= $appointment['id'] ?? '' ?>">
                    <input type="hidden" name="token" id="editToken" value="<?= $appointment['token'] ?? '' ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">שם מלא <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="editName" class="form-control" 
                                       value="<?= htmlspecialchars($appointment['customer_name'] ?? '') ?>" 
                                       placeholder="הזן את שמך" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">טלפון <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" id="editPhone" class="form-control"
                                       value="<?= htmlspecialchars($appointment['phone'] ?? '') ?>"
                                       placeholder="050-1234567" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">אימייל <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="editEmail" class="form-control"
                               value="<?= htmlspecialchars($appointment['customer_email'] ?? '') ?>"
                               placeholder="name@example.com" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">שירות <span class="text-danger">*</span></label>
                        <select name="service_id" id="editService" class="form-control" required>
                            <?php foreach($services as $service): ?>
                            <option value="<?= $service['id'] ?>" <?= ($service['id'] == ($appointment['service_id'] ?? 0)) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($service['name']) ?> - ₪<?= number_format($service['price'], 2) ?> (<?= $service['duration'] ?> דקות)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">תאריך <span class="text-danger">*</span></label>
                                <!-- ✅ Same date picker as booking form -->
                                <select name="appointment_date" id="editDate" class="form-control" required>
                                    <option value="">בחר תאריך...</option>
                                    <?php foreach($availableDates as $date): ?>
                                    <option value="<?= $date ?>" <?= ($date == ($appointment['appointment_date'] ?? '')) ? 'selected' : '' ?>>
                                        <?= date('d/m/Y', strtotime($date)) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">שעה <span class="text-danger">*</span></label>
                                <!-- ✅ Same time picker as booking form -->
                                <select name="appointment_time" id="editTime" class="form-control" required>
                                    <option value="">בחר שעה...</option>
                                    <?php
                                    $times = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30'];
                                    foreach($times as $time):
                                    ?>
                                    <option value="<?= $time ?>" <?= ($time == substr($appointment['appointment_time'] ?? '', 0, 5)) ? 'selected' : '' ?>>
                                        <?= $time ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">הערות (אופציונלי)</label>
                        <textarea name="notes" id="editNotes" class="form-control" rows="2" 
                                  placeholder="הערות נוספות..."><?= htmlspecialchars($appointment['notes'] ?? '') ?></textarea>
                    </div>
                    
                    <!-- ✅ Loading indicator for availability check -->
                    <div id="availabilityCheck" style="display: none;" class="text-center text-muted">
                        <i class="fas fa-spinner fa-spin"></i> בודק זמינות...
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ביטול</button>
                    <button type="submit" class="btn btn-primary" id="editSubmitBtn">
                        <i class="fas fa-save"></i> שמור שינויים
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* ===== CARD ===== */
.booking-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.booking-header {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    padding: 25px 30px;
}

.booking-header h4 {
    font-weight: 700;
    margin: 0;
}

.booking-header p {
    margin: 5px 0 0;
    opacity: 0.9;
    font-size: 0.95rem;
}

.booking-body {
    padding: 30px;
}

/* ===== DETAILS ===== */
.appointment-details {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.detail-item {
    margin-bottom: 12px;
}

.detail-item label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: block;
    margin-bottom: 2px;
}

.detail-item p {
    font-size: 1rem;
    font-weight: 500;
    color: #1a1a1a;
    margin: 0;
}

.status-badge {
    display: inline-block;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

/* ===== BUTTONS ===== */
.action-buttons {
    display: flex;
    gap: 15px;
}

.action-buttons .btn {
    flex: 1;
    padding: 12px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
    font-size: 1rem;
}

.btn-edit {
    background: #ffc107;
    color: #1a1a1a;
}

.btn-edit:hover {
    background: #e0a800;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.4);
}

.btn-cancel {
    background: #dc2626;
    color: white;
}

.btn-cancel:hover {
    background: #b91c1c;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
}

/* ===== FORM ===== */
.form-group {
    margin-bottom: 18px;
}

.form-group .form-label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #374151;
    margin-bottom: 5px;
}

.form-group .form-control {
    padding: 10px 14px;
    border-radius: 10px;
    border: 2px solid #e5e7eb;
    transition: all 0.3s;
    font-size: 1rem;
}

.form-group .form-control:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .booking-body {
        padding: 20px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-buttons .btn {
        width: 100%;
    }
    
    .booking-header {
        padding: 20px;
    }
}
</style>

<script>
// ========== SHOW EDIT MODAL ==========
function userEditAppointment() {
    new bootstrap.Modal(document.getElementById('editAppointmentModal')).show();
}

// ========== CANCEL APPOINTMENT ==========
function userCancelAppointment(id, token) {
    if(!confirm('האם אתה בטוח שברצונך לבטל תור זה?')) {
        return;
    }
    
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> מבטל...';
    btn.disabled = true;
    
    fetch('<?php echo SITE_URL; ?>/index.php?controller=booking&action=cancel&id=' + id + '&token=' + token, {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            window.location.reload();
        } else {
            alert('❌ שגיאה בביטול התור: ' + (data.error || 'נסה שוב'));
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ שגיאה בביטול התור. אנא נסה שוב.');
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    });
}

// ========== CHECK AVAILABILITY ==========
document.getElementById('editService')?.addEventListener('change', checkAvailability);
document.getElementById('editDate')?.addEventListener('change', checkAvailability);

function checkAvailability() {
    const serviceId = document.getElementById('editService').value;
    const date = document.getElementById('editDate').value;
    const timeSelect = document.getElementById('editTime');
    const availabilityCheck = document.getElementById('availabilityCheck');
    
    if(!serviceId || !date) {
        return;
    }
    
    availabilityCheck.style.display = 'block';
    
    fetch('<?php echo SITE_URL; ?>/index.php?controller=booking&action=getAvailableSlots&service_id=' + serviceId + '&date=' + date)
        .then(response => response.json())
        .then(data => {
            availabilityCheck.style.display = 'none';
            
            // Keep current selected time if available
            const currentTime = timeSelect.value;
            
            // Clear existing options (keep first "בחר שעה...")
            while(timeSelect.options.length > 1) {
                timeSelect.remove(1);
            }
            
            // Add available slots
            if(data.length > 0) {
                data.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    if(slot === currentTime) {
                        option.selected = true;
                    }
                    timeSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = '❌ אין שעות פנויות';
                option.disabled = true;
                timeSelect.appendChild(option);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            availabilityCheck.style.display = 'none';
        });
}

// ========== FORM SUBMISSION ==========
document.getElementById('editAppointmentForm')?.addEventListener('submit', function(e) {
    const btn = document.getElementById('editSubmitBtn');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> שומר...';
    btn.disabled = true;
});

// ========== TOAST NOTIFICATION ==========
function showToast(message, type = 'success') {
    const colors = {
        success: '#28a745',
        danger: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    };
    
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: ${colors[type] || '#28a745'};
        color: white;
        padding: 15px 25px;
        border-radius: 10px;
        z-index: 9999;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        animation: slideUp 0.3s ease;
        font-weight: 500;
        max-width: 350px;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.transition = 'opacity 0.3s, transform 0.3s';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Initialize availability check on modal open
document.getElementById('editAppointmentModal')?.addEventListener('shown.bs.modal', function() {
    checkAvailability();
});
</script>

<?php require_once __DIR__ . "/layout/footer.php"; ?>