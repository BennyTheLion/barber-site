<?php
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
 
 if(!$isAjax) {
     // views/admin/appointments.php
    require_once __DIR__ . "/../layout/admin_header.php";
 }

// views/admin/dashboard.php
// ✅ אין כאן include של header או footer - הם כבר ב-header

error_log("=== DASHBOARD LOADED ===");

// ============= סטטיסטיקות =============
$db = Database::getInstance()->getConnection();

// 1. תורים היום
$stmt = $db->query("SELECT COUNT(*) as count FROM appointments WHERE appointment_date = CURDATE() AND status != 'cancelled'");
$todayAppointments = $stmt->fetch()['count'];

// 2. תורים ממתינים
$stmt = $db->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'pending' AND appointment_date >= CURDATE()");
$pendingAppointments = $stmt->fetch()['count'];

// 3. תורים השבוע
$stmt = $db->query("SELECT COUNT(*) as count FROM appointments WHERE YEARWEEK(appointment_date) = YEARWEEK(CURDATE()) AND status != 'cancelled'");
$weekAppointments = $stmt->fetch()['count'];

// 4. לקוחות חדשים החודש
$stmt = $db->query("SELECT COUNT(*) as count FROM customers WHERE MONTH(created_at) = MONTH(CURDATE())");
$newCustomers = $stmt->fetch()['count'];

// 5. סה"כ לקוחות
$stmt = $db->query("SELECT COUNT(*) as count FROM customers");
$totalCustomers = $stmt->fetch()['count'];

// 6. הכנסות החודש (תורים שהושלמו)
$stmt = $db->query("SELECT SUM(s.price) as total FROM appointments a JOIN services s ON a.service_id = s.id WHERE MONTH(a.appointment_date) = MONTH(CURDATE()) AND a.status = 'completed'");
$monthlyRevenue = $stmt->fetch()['total'] ?? 0;

// 7. תורים שבוטלו החודש
$stmt = $db->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'cancelled' AND MONTH(appointment_date) = MONTH(CURDATE())");
$cancelledCount = $stmt->fetch()['count'];

// 8. תורים קרובים (ל-7 הימים הקרובים)
$stmt = $db->query("SELECT a.*, c.name, c.phone, s.name as service_name 
                    FROM appointments a 
                    JOIN customers c ON a.customer_id = c.id 
                    JOIN services s ON a.service_id = s.id 
                    WHERE a.appointment_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                    AND a.status != 'cancelled'
                    ORDER BY a.appointment_date ASC, a.appointment_time ASC 
                    LIMIT 10");
$upcomingAppointments = $stmt->fetchAll();

// 9. סטטיסטיקות לפי שירות
$stmt = $db->query("SELECT s.name, COUNT(a.id) as count 
                    FROM appointments a 
                    JOIN services s ON a.service_id = s.id 
                    WHERE MONTH(a.appointment_date) = MONTH(CURDATE())
                    AND a.status = 'completed'
                    GROUP BY a.service_id 
                    ORDER BY count DESC 
                    LIMIT 5");
$topServices = $stmt->fetchAll();

// 10. פעילות אחרונה (7 ימים)
$stmt = $db->query("SELECT DATE(appointment_date) as date, COUNT(*) as count 
                    FROM appointments 
                    WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                    GROUP BY DATE(appointment_date)
                    ORDER BY date ASC");
$weeklyActivity = $stmt->fetchAll();
?>
<style>
.stats-card {
    border-radius: 15px;
    border: none;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: default;
    overflow: hidden;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.stats-card .card-body {
    padding: 20px;
}

.stats-card .card-title {
    font-size: 13px;
    letter-spacing: 0.5px;
}

.stats-card .card-text {
    font-size: 28px;
    font-weight: 700;
}

.chart-container {
    position: relative;
}

.progress {
    background-color: #f0f0f0;
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
    transition: width 1s ease;
}

.table th {
    font-weight: 600;
    font-size: 13px;
    border-top: none;
}

.table td {
    font-size: 14px;
    vertical-align: middle;
}

.badge {
    font-size: 12px;
    padding: 5px 10px;
}

@media (max-width: 768px) {
    .stats-card .card-text {
        font-size: 22px;
    }
    
    .stats-card .card-body {
        padding: 15px;
    }
}
</style>
<div class="container-fluid">
    <!-- כותרת -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="display-6">
                <i class="fas fa-chart-pie text-danger"></i> דשבורד
            </h1>
            <p class="text-muted">ברוך הבא חזרה! הנה סיכום הפעילות שלך</p>
        </div>
    </div>
    
    <!-- כרטיסי סטטיסטיקה -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stats-card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">תורים היום</h6>
                            <h2 class="card-text mb-0"><?= $todayAppointments ?></h2>
                        </div>
                        <i class="fas fa-calendar-day fa-2x text-white-50"></i>
                    </div>
                    <small class="text-white-50">תורים פעילים להיום</small>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="card stats-card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">ממתינים</h6>
                            <h2 class="card-text mb-0"><?= $pendingAppointments ?></h2>
                        </div>
                        <i class="fas fa-clock fa-2x text-white-50"></i>
                    </div>
                    <small class="text-white-50">תורים בהמתנה</small>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="card stats-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">הכנסות החודש</h6>
                            <h2 class="card-text mb-0">₪<?= number_format($monthlyRevenue, 0) ?></h2>
                        </div>
                        <i class="fas fa-coins fa-2x text-white-50"></i>
                    </div>
                    <small class="text-white-50"><?= $weekAppointments ?> תורים השבוע</small>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="card stats-card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">לקוחות</h6>
                            <h2 class="card-text mb-0"><?= $totalCustomers ?></h2>
                        </div>
                        <i class="fas fa-users fa-2x text-white-50"></i>
                    </div>
                    <small class="text-white-50">+<?= $newCustomers ?> חדשים החודש</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- שורה שנייה - גרפים ומידע -->
    <div class="row g-3 mb-4">
        <!-- פעילות שבועית -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> פעילות שבועית</h5>
                </div>
                <div class="card-body">
                    <?php if(count($weeklyActivity) > 0): ?>
                        <div class="chart-container" style="height: 200px;">
                            <canvas id="weeklyChart"></canvas>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">אין נתונים להצגה</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- שירותים מובילים -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-trophy"></i> שירותים מובילים</h5>
                </div>
                <div class="card-body">
                    <?php if(count($topServices) > 0): ?>
                        <?php foreach($topServices as $service): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><?= htmlspecialchars($service['name']) ?></span>
                                <span class="badge bg-danger"><?= $service['count'] ?> תורים</span>
                            </div>
                            <div class="progress mb-3" style="height: 8px;">
                                <div class="progress-bar bg-danger" role="progressbar" 
                                     style="width: <?= ($service['count'] / $topServices[0]['count']) * 100 ?>%">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">אין נתונים להצגה</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- שורה שלישית - תורים קרובים ופעולות -->
    <div class="row g-3">
        <!-- תורים קרובים -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> תורים קרובים (7 ימים)</h5>
                    <a href="<?php echo SITE_URL; ?>/index.php?url=admin/appointments" class="btn btn-sm btn-outline-light">ראה הכל</a>
                </div>
                <div class="card-body">
                    <?php if(count($upcomingAppointments) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>לקוח</th>
                                        <th>שירות</th>
                                        <th>תאריך</th>
                                        <th>שעה</th>
                                        <th>סטטוס</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($upcomingAppointments as $appointment): ?>
                                    <tr>
                                        <td><?= $appointment['id'] ?></td>
                                        <td><?= htmlspecialchars($appointment['name']) ?></td>
                                        <td><?= htmlspecialchars($appointment['service_name']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($appointment['appointment_date'])) ?></td>
                                        <td><?= substr($appointment['appointment_time'], 0, 5) ?></td>
                                        <td>
                                            <?php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'confirmed' => 'success',
                                                'completed' => 'info',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusTexts = [
                                                'pending' => '⏳ ממתין',
                                                'confirmed' => '✅ מאושר',
                                                'completed' => '✔️ הושלם',
                                                'cancelled' => '❌ בוטל'
                                            ];
                                            $status = $appointment['status'];
                                            $color = $statusColors[$status] ?? 'secondary';
                                            $text = $statusTexts[$status] ?? $status;
                                            ?>
                                            <span class="badge bg-<?= $color ?>"><?= $text ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">
                            <i class="fas fa-calendar-check fa-2x d-block mb-2"></i>
                            אין תורים קרובים
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- פעולות מהירות -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> פעולות מהירות</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo SITE_URL; ?>/index.php?url=admin/appointments" class="btn btn-primary">
                            <i class="fas fa-calendar-alt"></i> ניהול תורים
                        </a>
                        <a href="<?php echo SITE_URL; ?>/index.php?url=admin/appointments?filter=today" class="btn btn-outline-primary">
                            <i class="fas fa-calendar-day"></i> תורים להיום
                        </a>
                        <a href="<?php echo SITE_URL; ?>/index.php?url=admin/services" class="btn btn-success">
                            <i class="fas fa-cut"></i> ניהול שירותים
                        </a>
                        <a href="<?php echo SITE_URL; ?>/index.php?url=admin/customers" class="btn btn-info">
                            <i class="fas fa-users"></i> ניהול לקוחות
                        </a>
                        <a href="<?php echo SITE_URL; ?>/index.php?url=admin/schedule" class="btn btn-warning">
                            <i class="fas fa-clock"></i> שעות פעילות
                        </a>
                        <a href="<?php echo SITE_URL; ?>/index.php?url=admin/settings" class="btn btn-secondary">
                            <i class="fas fa-cog"></i> הגדרות מערכת
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js - לפעילות שבועית -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // גרף פעילות שבועית
    <?php if(count($weeklyActivity) > 0): ?>
        const ctx = document.getElementById('weeklyChart');
        if(ctx) {
            const labels = <?= json_encode(array_map(function($item) { 
                return date('d/m', strtotime($item['date'])); 
            }, $weeklyActivity)) ?>;
            
            const data = <?= json_encode(array_column($weeklyActivity, 'count')) ?>;
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'תורים',
                        data: data,
                        backgroundColor: 'rgba(220, 38, 38, 0.7)',
                        borderColor: '#dc2626',
                        borderWidth: 2,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    <?php endif; ?>
});
</script>

<?php
if(!$isAjax) {
    require_once __DIR__ . "/../layout/admin_footer.php";
}
?>