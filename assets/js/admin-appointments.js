// /assets/js/admin-appointments.js
// Complete working JavaScript for appointments

console.log('admin-appointments.js loaded');

// ========== PREVENT DOUBLE INIT ==========
let isInitialized = false;
let initTimeout = null;

// ========== INITIALIZE ALL FUNCTIONS ==========
function initAppointments() {
    console.log('initAppointments() called - re-initializing...');
    
    // ✅ מנע קריאות כפולות
    if (initTimeout) {
        clearTimeout(initTimeout);
    }
    
    initTimeout = setTimeout(function() {
        setupStatusSelects();
        initTimeout = null;
    }, 100);
}

// ========== SETUP STATUS SELECT DROPDOWNS ==========
function setupStatusSelects() {
    console.log('Setting up status selects...');
        
        // Save old value on focus
        newSelect.addEventListener('focus', function() {
            this.dataset.oldValue = this.value;
        });
    
}

// ========== EDIT APPOINTMENT ==========
function editAppointment(appointment) {
    console.log('editAppointment called with:', appointment);
    
    if (!appointment || typeof appointment !== 'object') {
        alert('שגיאה: נתוני תור לא תקינים');
        return;
    }
    
    // Check if modal exists
    var modalElement = document.getElementById('editAppointmentModal');
    
    if (!modalElement) {
        createEditModal();
        modalElement = document.getElementById('editAppointmentModal');
        // Wait a moment for modal to be added to DOM
        setTimeout(function() {
            fillAndShowModal(appointment);
        }, 100);
    } else {
        fillAndShowModal(appointment);
    }
}

function fillAndShowModal(appointment) {
    // Fill form fields
    document.getElementById('editAppointmentId').value = appointment.id || '';
    document.getElementById('editCustomerName').value = appointment.name || '';
    document.getElementById('editPhone').value = appointment.phone || '';
    document.getElementById('editServiceId').value = appointment.service_id || '';
    document.getElementById('editAppointmentDate').value = appointment.appointment_date || '';
    document.getElementById('editAppointmentTime').value = appointment.appointment_time || '';
    document.getElementById('editStatus').value = appointment.status || 'pending';
    document.getElementById('editNotes').value = appointment.notes || '';
    
    // Show modal
    var modalElement = document.getElementById('editAppointmentModal');
    var modal = new bootstrap.Modal(modalElement);
    modal.show();
}

function createEditModal() {
    // Remove existing modal if any
    var existingModal = document.getElementById('editAppointmentModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    var modalHTML = `
        <div class="modal fade" id="editAppointmentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit text-warning"></i> עריכת תור</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editAppointmentForm" method="POST" action="${window.BASE_URL || ''}/index.php?url=admin/updateAppointment">
                        <div class="modal-body">
                            <input type="hidden" name="id" id="editAppointmentId">
                            
                            <div class="mb-3">
                                <label class="form-label">לקוח <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="editCustomerName" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">טלפון <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="editPhone" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">שירות <span class="text-danger">*</span></label>
                                <select name="service_id" id="editServiceId" class="form-control" required>
                                    <?php
                                    $db = Database::getInstance()->getConnection();
                                    $stmt = $db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order");
                                    $services = $stmt->fetchAll();
                                    foreach($services as $service):
                                    ?>
                                    <option value="<?= $service['id'] ?>"><?= htmlspecialchars($service['name']) ?> - ₪<?= number_format($service['price'], 2) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">תאריך <span class="text-danger">*</span></label>
                                    <input type="date" name="appointment_date" id="editAppointmentDate" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">שעה <span class="text-danger">*</span></label>
                                    <input type="time" name="appointment_time" id="editAppointmentTime" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3 mt-3">
                                <label class="form-label">סטטוס</label>
                                <select name="status" id="editStatus" class="form-control">
                                    <option value="pending">⏳ ממתין</option>
                                    <option value="confirmed">✅ מאושר</option>
                                    <option value="completed">✔️ הושלם</option>
                                    <option value="cancelled">❌ בוטל</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">הערות</label>
                                <textarea name="notes" id="editNotes" class="form-control" rows="2"></textarea>
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
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Add form submit handler
    var form = document.getElementById('editAppointmentForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            var btn = document.getElementById('editSubmitBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> שומר...';
            btn.disabled = true;
        });
    }
}

// ========== DELETE APPOINTMENT ==========
function deleteAppointment(id) {
    console.log('deleteAppointment called with ID:', id);
    
    if (!id) {
        alert('שגיאה: חסר מזהה תור');
        return;
    }
    
    if(!confirm('האם אתה בטוח שברצונך למחוק תור זה?')) {
        return;
    }
    
    // Find the button
    var btn = event && event.target ? event.target.closest('button') : null;
    if (!btn) {
        // Try to find the button by onclick attribute
        var buttons = document.querySelectorAll('button[onclick*="deleteAppointment(' + id + ')"]');
        if (buttons.length > 0) {
            btn = buttons[0];
        }
    }
    
    var originalHtml = '';
    if (btn) {
        originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
    }
    
    fetch((window.BASE_URL || '') + '/index.php?url=admin/deleteAppointment/' + id, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        console.log('Delete response:', data);
        if(data.success) {
            // Find and remove the row
            var rows = document.querySelectorAll('table tbody tr');
            var rowToRemove = null;
            rows.forEach(function(row) {
                var firstCell = row.querySelector('td:first-child');
                if (firstCell && firstCell.textContent.trim() == id) {
                    rowToRemove = row;
                }
            });
            
            if (rowToRemove) {
                rowToRemove.style.transition = 'all 0.3s';
                rowToRemove.style.opacity = '0';
                rowToRemove.style.transform = 'translateX(-20px)';
                setTimeout(function() {
                    rowToRemove.remove();
                    var tbody = document.querySelector('table tbody');
                    if(tbody && tbody.children.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="8" class="text-center py-4">אין תורים להצגה</td>
                            </tr>
                        `;
                    }
                }, 300);
            } else {
                // Reload if row not found
                location.reload();
            }
            showToast('✅ תור נמחק בהצלחה!', 'success');
        } else {
            alert('❌ שגיאה במחיקת התור: ' + (data.message || 'נסה שוב'));
            if (btn) {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        alert('❌ שגיאה במחיקת התור');
        if (btn) {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    });
}
    
// ========== STATUS UPDATE ==========
// ========== SIMPLE DUPLICATE PREVENTION ==========
var isUpdating = false;

function updateAppointmentStatus(id, status) {
    console.log('updateAppointmentStatus called - ID:', id, 'Status:', status);
    
    // ✅ מניעת קריאות כפולות
    if (isUpdating) {
        console.log('⚠️ Update already in progress, skipping...');
        return;
    }
    isUpdating = true;
    
    if (!id) {
        alert('שגיאה: חסר מזהה תור');
        isUpdating = false;
        return;
    }
    
    // Find the select element
    var select = document.querySelector('.status-select[data-id="' + id + '"]');
    var oldValue = select ? select.value : 'pending';
    
    if (select) {
        select.disabled = true;
        select.style.opacity = '0.6';
    }
    
    fetch((window.BASE_URL || '') + '/index.php?url=admin/updateAppointment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'id=' + encodeURIComponent(id) + '&status=' + encodeURIComponent(status)
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(function(data) {
        console.log('Status update response:', data);
        
        if (select) {
            select.disabled = false;
            select.style.opacity = '1';
        }
        
        if(data.success) {
            if (select) {
                select.dataset.oldValue = status;
                var colors = {
                    'pending': '#ffc107',
                    'confirmed': '#28a745',
                    'completed': '#17a2b8',
                    'cancelled': '#dc3545'
                };
                select.style.backgroundColor = colors[status] || '';
                setTimeout(function() {
                    select.style.backgroundColor = '';
                }, 2000);
            }
            showToast('✅ סטטוס תור עודכן בהצלחה!', 'success');
        } else {
            alert('❌ שגיאה בעדכון סטטוס: ' + (data.message || 'נסה שוב'));
            if (select) {
                select.value = oldValue;
            }
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        alert('❌ שגיאה בעדכון הסטטוס: ' + error.message);
        if (select) {
            select.disabled = false;
            select.style.opacity = '1';
            select.value = oldValue;
        }
    })
    .finally(function() {
        // ✅ שחרר את הנעילה אחרי 500ms
        setTimeout(function() {
            isUpdating = false;
        }, 500);
    });
}

// ========== TOAST NOTIFICATION ==========
function showToast(message, type) {
    type = type || 'success';
    
    var existingToasts = document.querySelectorAll('.custom-toast');
    existingToasts.forEach(function(t) {
        t.remove();
    });
    
    var colors = {
        success: '#28a745',
        danger: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    };
    
    var toast = document.createElement('div');
    toast.className = 'custom-toast';
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
    
    setTimeout(function() {
        toast.style.transition = 'opacity 0.3s, transform 0.3s';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
        setTimeout(function() {
            toast.remove();
        }, 300);
    }, 3000);
}

// ========== TOAST STYLE ==========
var toastStyle = document.createElement('style');
toastStyle.textContent = `
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(toastStyle);

// ========== INIT ON PAGE LOAD ==========
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing appointments...');
    setTimeout(function() {
        initAppointments();
    }, 100);
});

// ========== RE-INITIALIZE AFTER AJAX LOAD ==========
// Listen for AJAX completion
document.addEventListener('ajaxComplete', function() {
    console.log('AJAX complete - re-initializing...');
    setTimeout(function() {
        initAppointments();
    }, 200);
});


// ✅ EXPOSE FUNCTIONS GLOBALLY
window.initAppointments = initAppointments;
window.editAppointment = editAppointment;
window.deleteAppointment = deleteAppointment;
window.updateAppointmentStatus = updateAppointmentStatus;
window.showToast = showToast;

console.log('editAppointment function:', typeof editAppointment);
console.log('deleteAppointment function:', typeof deleteAppointment);
console.log('updateAppointmentStatus function:', typeof updateAppointmentStatus);
console.log('initAppointments function:', typeof initAppointments);