// Appointment Booking System
class AppointmentSystem {
    constructor() {
        this.init();
    }
    
    init() {
        // Service selection
        const serviceSelect = document.getElementById("service_id");
        if(serviceSelect) {
            serviceSelect.addEventListener("change", () => this.loadAvailableDates());
        }
        
        // Date selection
        const dateInput = document.getElementById("appointment_date");
        if(dateInput) {
            dateInput.addEventListener("change", () => this.loadAvailableSlots());
        }
        
        // Form submission
        const bookingForm = document.getElementById("booking-form");
        if(bookingForm) {
            bookingForm.addEventListener("submit", (e) => this.submitBooking(e));
        }
        
        // Cancel appointment
        const cancelBtn = document.getElementById("cancel-appointment");
        if(cancelBtn) {
            cancelBtn.addEventListener("click", (e) => this.confirmCancel(e));
        }
        
        // Update appointment
        const updateForm = document.getElementById("update-booking-form");
        if(updateForm) {
            updateForm.addEventListener("submit", (e) => this.updateBooking(e));
        }
    }
    
    async loadAvailableDates() {
        const serviceId = document.getElementById("service_id").value;
        if(!serviceId) return;
        
        const response = await fetch(`${window.BASE_URL || ""}/booking/getAvailableDates?service_id=${serviceId}`);
        const dates = await response.json();
        
        const dateInput = document.getElementById("appointment_date");
        dateInput.min = this.getTomorrowDate();
        dateInput.max = this.getMaxDate();
        
        // Highlight available dates (requires datepicker customization)
    }
    
    async loadAvailableSlots() {
        const serviceId = document.getElementById("service_id").value;
        const date = document.getElementById("appointment_date").value;
        
        if(!serviceId || !date) return;
        
        const response = await fetch(`${window.BASE_URL || ""}/booking/getAvailableSlots?service_id=${serviceId}&date=${date}`);
        const slots = await response.json();
        
        const slotsContainer = document.getElementById("available-slots");
        if(!slotsContainer) return;
        
        if(slots.length === 0) {
            slotsContainer.innerHTML = "<div class="alert alert-warning">היום שנבחר מלא. אנא בחר תאריך אחר.</div>";
            return;
        }
        
        let html = "<div class="row">";
        slots.forEach(slot => {
            html += `
                <div class="col-4 col-md-3 mb-2">
                    <button type="button" class="btn btn-outline-primary time-slot" data-time="${slot}">
                        ${slot.substring(0, 5)}
                    </button>
                </div>
            `;
        });
        html += "</div>";
        
        slotsContainer.innerHTML = html;
        
        // Add click handlers
        document.querySelectorAll(".time-slot").forEach(btn => {
            btn.addEventListener("click", () => this.selectTimeSlot(btn));
        });
    }
    
    selectTimeSlot(btn) {
        document.querySelectorAll(".time-slot").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        document.getElementById("appointment_time").value = btn.dataset.time;
    }
    
    async submitBooking(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const csrfToken = document.querySelector("[name=csrf_token]").value;
        
        const response = await fetch((window.BASE_URL || "") + "/booking/create", {
            method: "POST",
            headers: {
                "X-CSRF-Token": csrfToken
            },
            body: formData
        });
        
        const result = await response.json();
        
        if(result.success) {
            window.location.href = `${window.BASE_URL || ""}/booking/manage?id=${result.appointment_id}&token=${result.token}`;
        } else {
            alert(result.error || "אירעה שגיאה. אנא נסה שוב.");
        }
    }
    
    async confirmCancel(e) {
        if(confirm("האם אתה בטוח שברצונך לבטל את התור?")) {
            const appointmentId = document.getElementById("appointment_id").value;
            const token = document.getElementById("token").value;
            
            const response = await fetch((window.BASE_URL || "") + "/booking/cancel", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({id: appointmentId, token: token})
            });
            
            const result = await response.json();
            
            if(result.success) {
                alert("התור בוטל בהצלחה");
                window.location.href = (window.BASE_URL || "/") ;
            } else {
                alert(result.error || "אירעה שגיאה בביטול התור");
            }
        }
    }
    
    async updateBooking(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        
        const response = await fetch((window.BASE_URL || "") + "/booking/update", {
            method: "POST",
            body: formData
        });
        
        const result = await response.json();
        
        if(result.success) {
            alert("התור עודכן בהצלחה");
            window.location.reload();
        } else {
            alert(result.error || "אירעה שגיאה בעדכון התור");
        }
    }
    
    getTomorrowDate() {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        return tomorrow.toISOString().split("T")[0];
    }
    
    getMaxDate() {
        const maxDate = new Date();
        maxDate.setDate(maxDate.getDate() + 30);
        return maxDate.toISOString().split("T")[0];
    }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    new AppointmentSystem();
    
    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px"
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                entry.target.classList.add("animate");
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    document.querySelectorAll(".service-card, .gallery-item").forEach(el => {
        observer.observe(el);
    });
});
