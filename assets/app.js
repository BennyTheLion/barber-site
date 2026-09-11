var state = { serviceId: null, date: null, time: null, services: [] };
var DAY_NAMES = ['ראשון', 'שני', 'שלישי', 'רביעי', 'חמישי', 'שישי', 'שבת'];

function haptic(pattern) { try { if (navigator.vibrate) navigator.vibrate(pattern); } catch (e) {} }

function api(url, opts) {
  opts = opts || {};
  opts.headers = Object.assign({ 'Content-Type': 'application/json' }, opts.headers || {});
  return fetch(url, opts).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); });
}

/* ── Session / admin header ─────────────────────────────────────────── */
function checkSession() {
  api('api/session.php').then(function (res) {
    var loginBtn = document.getElementById('admin-login-btn');
    var panelBtn = document.getElementById('admin-panel-btn');
    if (res.data.loggedIn) {
      loginBtn.classList.add('hidden');
      panelBtn.classList.remove('hidden');
    } else {
      loginBtn.classList.remove('hidden');
      panelBtn.classList.add('hidden');
    }
  });
}

function openLoginModal() {
  document.getElementById('login-modal').classList.remove('hidden');
}
function closeLoginModal() {
  document.getElementById('login-modal').classList.add('hidden');
  document.getElementById('login-error').textContent = '';
}
function submitLogin() {
  var username = document.getElementById('login-username').value.trim();
  var password = document.getElementById('login-password').value;
  api('api/login.php', { method: 'POST', body: JSON.stringify({ username: username, password: password }) })
    .then(function (res) {
      if (res.ok) { window.location.href = 'admin.php'; }
      else { document.getElementById('login-error').textContent = res.data.error || 'שגיאה'; }
    });
}

/* ── Settings: footer + working hours + whatsapp ───────────────────── */
function loadSettings() {
  api('api/settings.php').then(function (res) {
    var s = res.data;
    document.getElementById('owner-name').textContent = s.owner_name || '';
    document.getElementById('owner-tagline').textContent = s.tagline || '';

    var addrPhone = [];
    if (s.address) addrPhone.push(s.address);
    if (s.phone) addrPhone.push(s.phone);
    document.getElementById('footer-contact').textContent = addrPhone.join(' · ') || '';
    if (s.email) {
      var emailEl = document.getElementById('footer-email');
      emailEl.href = 'mailto:' + s.email; emailEl.textContent = s.email; emailEl.classList.remove('hidden');
    }

    var socials = document.getElementById('footer-socials');
    socials.innerHTML = '';
    [['instagram_url', 'אינסטגרם'], ['facebook_url', 'פייסבוק'], ['tiktok_url', 'טיקטוק']].forEach(function (pair) {
      if (s[pair[0]]) {
        var a = document.createElement('a');
        a.href = s[pair[0]]; a.target = '_blank'; a.rel = 'noopener'; a.textContent = pair[1];
        socials.appendChild(a);
      }
    });

    if (s.whatsapp_phone) {
      var wa = document.getElementById('whatsapp-fab');
      wa.href = 'https://wa.me/' + s.whatsapp_phone.replace(/[^0-9]/g, '');
      wa.classList.remove('hidden');
    }

    renderWorkingHours(s.working_hours || {});
  });
}

function renderWorkingHours(hours) {
  var el = document.getElementById('working-hours-list');
  el.innerHTML = '';
  var todayIdx = new Date().getDay();
  for (var i = 0; i < 7; i++) {
    var d = hours[String(i)] || {};
    var row = document.createElement('div');
    row.className = 'hours-row' + (i === todayIdx ? ' today' : '');
    var timeText = (!d.open || !d.close || d.closed) ? 'סגור' : (d.open + ' – ' + d.close);
    row.innerHTML = '<span class="day">' + DAY_NAMES[i] + '</span><span class="time">' + timeText + '</span>';
    el.appendChild(row);
  }
}

/* ── Gallery ─────────────────────────────────────────────────────────── */
function loadGallery() {
  api('api/gallery.php?type=image').then(function (res) {
    var items = res.data || [];
    var gallerySection = document.getElementById('gallery-section');
    if (!items.length) { gallerySection.classList.add('hidden'); return; }
    gallerySection.classList.remove('hidden');

    var scroll = document.getElementById('gallery-scroll');
    var dots = document.getElementById('gallery-dots');
    scroll.innerHTML = ''; dots.innerHTML = '';
    items.forEach(function (item, i) {
      var div = document.createElement('div');
      div.className = 'gallery-item';
      div.innerHTML = '<img src="' + item.url + '" alt="גלריה" loading="lazy">';
      scroll.appendChild(div);
      var dot = document.createElement('div');
      dot.className = 'gallery-dot' + (i === 0 ? ' active' : '');
      dots.appendChild(dot);
    });
    setupGalleryNav(scroll, dots, document.getElementById('gallery-arrow-left'), document.getElementById('gallery-arrow-right'));
  });

  api('api/gallery.php?type=video').then(function (res) {
    var items = res.data || [];
    var section = document.getElementById('video-section');
    if (!items.length) { section.classList.add('hidden'); return; }
    section.classList.remove('hidden');
    var scroll = document.getElementById('video-scroll');
    scroll.innerHTML = '';
    items.forEach(function (item) {
      var div = document.createElement('div');
      div.className = 'gallery-item';
      div.innerHTML = '<video src="' + item.url + '" controls playsinline preload="metadata"></video>';
      scroll.appendChild(div);
    });
  });
}

// Tracks which slide is centered using IntersectionObserver instead of scrollLeft math,
// since scrollLeft sign/behaviour for RTL containers differs across browsers.
// Also wires up the left/right arrow buttons. Because the page is RTL, the reading/swipe
// direction runs right-to-left, so the LEFT-side arrow moves forward (higher index) and the
// RIGHT-side arrow moves backward (lower index) — matching the direction a swipe already goes.
function setupGalleryNav(scroll, dots, leftBtn, rightBtn) {
  var count = scroll.children.length;
  var currentIndex = 0;

  function updateArrows() {
    if (leftBtn) leftBtn.classList.toggle('gallery-arrow-hidden', currentIndex >= count - 1);
    if (rightBtn) rightBtn.classList.toggle('gallery-arrow-hidden', currentIndex <= 0);
  }

  function goTo(index) {
    if (index < 0 || index >= count) return;
    scroll.children[index].scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' });
  }

  if ('IntersectionObserver' in window) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting && entry.intersectionRatio >= 0.6) {
          currentIndex = Array.prototype.indexOf.call(scroll.children, entry.target);
          Array.prototype.forEach.call(dots.children, function (d, i) { d.classList.toggle('active', i === currentIndex); });
          updateArrows();
        }
      });
    }, { root: scroll, threshold: [0.6] });
    Array.prototype.forEach.call(scroll.children, function (child) { observer.observe(child); });
  }

  if (leftBtn) leftBtn.onclick = function () { haptic(8); goTo(currentIndex + 1); };
  if (rightBtn) rightBtn.onclick = function () { haptic(8); goTo(currentIndex - 1); };
  updateArrows();
}

/* ── Booking flow ────────────────────────────────────────────────────── */
function loadServices() {
  api('api/services.php').then(function (res) {
    state.services = res.data || [];
    renderServices();
  });
}

function renderServices() {
  var el = document.getElementById('service-list');
  el.innerHTML = '';
  state.services.forEach(function (s) {
    var div = document.createElement('div');
    div.className = 'chip' + (state.serviceId === s.id ? ' selected' : '');
    div.innerHTML = '<span>' + s.name + ' · ' + s.duration_minutes + ' דק׳</span><span style="font-weight:700;">' + (s.price || '') + '</span>';
    div.onclick = function () { haptic(8); state.serviceId = s.id; state.time = null; renderAll(); loadSlots(); };
    el.appendChild(div);
  });
}

function loadSlots() {
  var slotList = document.getElementById('slot-list');
  if (!state.serviceId || !state.date) { slotList.innerHTML = ''; return; }
  slotList.innerHTML = '<div style="grid-column:1/-1;font-size:13px;color:oklch(0.94 0.006 260 / 0.5);padding:10px 0;">טוען שעות...</div>';
  api('api/availability.php?date=' + state.date + '&service_id=' + state.serviceId).then(function (res) {
    var slots = (res.data && res.data.slots) || [];
    slotList.innerHTML = '';
    if (!slots.length) {
      slotList.innerHTML = '<div style="grid-column:1/-1;font-size:13.5px;color:oklch(0.94 0.006 260 / 0.5);padding:10px 0;">אין שעות פנויות בתאריך זה</div>';
      return;
    }
    slots.forEach(function (s) {
      var div = document.createElement('div');
      div.className = 'slot' + (state.time === s.time ? ' selected' : '');
      div.textContent = s.time;
      div.onclick = function () {
        haptic(8);
        state.time = s.time;
        Array.prototype.forEach.call(slotList.children, function (el) { el.classList.remove('selected'); });
        div.classList.add('selected');
        renderConfirmButton();
      };
      slotList.appendChild(div);
    });
  });
}

function renderConfirmButton() {
  var btn = document.getElementById('confirm-btn');
  btn.disabled = !(state.serviceId && state.date && state.time);
}

function renderAll() { renderServices(); renderConfirmButton(); }

function onDateChange(value) {
  state.date = value; state.time = null; loadSlots(); renderConfirmButton();
}

function confirmBooking() {
  if (!(state.serviceId && state.date && state.time)) return;
  var name = document.getElementById('cust-name').value.trim();
  var phone = document.getElementById('cust-phone').value.trim();
  var email = document.getElementById('cust-email').value.trim();
  if (!name || !phone) { alert('נא למלא שם וטלפון'); return; }

  api('api/booking.php', {
    method: 'POST',
    body: JSON.stringify({ service_id: state.serviceId, date: state.date, time: state.time, name: name, phone: phone, email: email }),
  }).then(function (res) {
    if (!res.ok) { alert(res.data.error || 'שגיאה בקביעת התור'); loadSlots(); return; }
    haptic([12, 40, 12, 40, 20]);
    document.getElementById('summary-text').textContent = res.data.summary;
    document.getElementById('booking-form').classList.add('hidden');
    var confirmedView = document.getElementById('confirmed-view');
    confirmedView.classList.remove('hidden');
    confirmedView.classList.remove('swap-in');
    void confirmedView.offsetWidth;
    confirmedView.classList.add('swap-in');
    fireConfetti();
  });
}

function resetBooking() {
  haptic(8);
  state.serviceId = null; state.date = null; state.time = null;
  document.getElementById('date-input').value = '';
  document.getElementById('cust-name').value = '';
  document.getElementById('cust-phone').value = '';
  document.getElementById('cust-email').value = '';
  document.getElementById('slot-list').innerHTML = '';
  renderAll();
  document.getElementById('confirmed-view').classList.add('hidden');
  var form = document.getElementById('booking-form');
  form.classList.remove('hidden'); form.classList.remove('swap-in');
  void form.offsetWidth; form.classList.add('swap-in');
}

var bookingRevealed = false;
function scrollToBooking() {
  var el = document.getElementById('booking-card');
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
function revealBooking() {
  haptic(8);
  if (bookingRevealed) { scrollToBooking(); return; }
  bookingRevealed = true;
  var heroCta = document.getElementById('hero-cta');
  if (heroCta && !heroCta.classList.contains('booking-hidden')) {
    heroCta.classList.add('btn-exit');
    setTimeout(function () { heroCta.classList.add('booking-hidden'); }, 220);
  }
  var card = document.getElementById('booking-card');
  card.classList.remove('booking-hidden');
  void card.offsetWidth;
  card.classList.add('reveal');
  setTimeout(scrollToBooking, 160);
}

function fireConfetti() {
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  var layer = document.getElementById('confetti-layer');
  var colors = ['oklch(0.85 0.19 145)', 'oklch(0.94 0.006 260)', 'oklch(0.75 0.15 145)', 'oklch(0.6 0.2 30)'];
  var rect = document.getElementById('booking-card').getBoundingClientRect();
  var originX = rect.left + rect.width / 2;
  var originY = rect.top + 40;
  for (var i = 0; i < 28; i++) {
    (function () {
      var piece = document.createElement('div');
      piece.className = 'confetti-piece';
      var size = 5 + Math.random() * 5;
      piece.style.width = size + 'px';
      piece.style.height = (size * 0.4) + 'px';
      piece.style.background = colors[Math.floor(Math.random() * colors.length)];
      piece.style.left = (originX + (Math.random() - 0.5) * 160) + 'px';
      piece.style.top = originY + 'px';
      var duration = 0.9 + Math.random() * 0.7;
      piece.style.animationDuration = duration + 's';
      piece.style.animationDelay = (Math.random() * 0.15) + 's';
      layer.appendChild(piece);
      setTimeout(function () { piece.remove(); }, (duration + 0.2) * 1000);
    })();
  }
}

function setupScrollReveal() {
  var targets = document.querySelectorAll('.observe');
  if (!('IntersectionObserver' in window)) { targets.forEach(function (t) { t.classList.add('in-view'); }); return; }
  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry, i) {
      if (entry.isIntersecting) {
        setTimeout(function () { entry.target.classList.add('in-view'); }, i * 60);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });
  targets.forEach(function (t) { observer.observe(t); });
}

/* ── Manage existing booking (self-service cancel/reschedule by phone) ─ */
var manageState = { phone: '' };

function toast(msg) {
  var t = document.createElement('div');
  t.className = 'toast';
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(function () { t.remove(); }, 2200);
}

function lookupMyBookings() {
  var phone = document.getElementById('manage-phone').value.trim();
  if (!phone) { alert('נא להזין מספר טלפון'); return; }
  manageState.phone = phone;
  api('api/booking.php?phone=' + encodeURIComponent(phone)).then(function (res) {
    renderMyBookings(res.ok ? (res.data || []) : []);
  });
}

function renderMyBookings(rows) {
  var el = document.getElementById('manage-list');
  el.innerHTML = '';
  if (!rows.length) {
    el.innerHTML = '<div style="font-size:13.5px;color:oklch(0.94 0.006 260 / 0.5);">לא נמצאו תורים פעילים למספר זה</div>';
    return;
  }
  rows.forEach(function (b) {
    var row = document.createElement('div');
    row.className = 'card';
    row.style.cssText = 'padding:14px;margin-bottom:10px;';
    row.innerHTML =
      '<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">' +
      '<div style="font-size:13.5px;line-height:1.6;"><div style="font-weight:700;">' + b.service_name + '</div>' +
      '<div>' + b.customer_name + '</div></div>' +
      '<div style="text-align:left;font-size:13px;white-space:nowrap;">' + b.booking_date + '<br>' + b.booking_time.slice(0, 5) + '</div>' +
      '</div>' +
      '<div style="display:flex;gap:6px;margin-top:12px;">' +
      '<button class="btn btn-sm btn-ghost" data-resched="' + b.id + '">עדכון מועד</button>' +
      '<button class="btn btn-sm btn-danger" data-cancel="' + b.id + '">ביטול תור</button>' +
      '</div>' +
      '<div class="hidden" id="my-resched-' + b.id + '" style="margin-top:14px;"></div>';
    el.appendChild(row);
    row.querySelector('[data-cancel]').onclick = function () { cancelMyBooking(b.id); };
    row.querySelector('[data-resched]').onclick = function () { toggleMyReschedule(b); };
  });
}

function cancelMyBooking(id) {
  if (!confirm('לבטל תור זה?')) return;
  api('api/booking.php', { method: 'PUT', body: JSON.stringify({ id: id, action: 'cancel', phone: manageState.phone }) })
    .then(function (res) {
      if (res.ok) { toast('התור בוטל'); lookupMyBookings(); } else alert(res.data.error || 'שגיאה');
    });
}

function toggleMyReschedule(b) {
  var container = document.getElementById('my-resched-' + b.id);
  if (!container.classList.contains('hidden')) { container.classList.add('hidden'); container.innerHTML = ''; return; }
  container.classList.remove('hidden');
  container.innerHTML =
    '<div class="field"><label>תאריך חדש</label><input type="date" id="my-date-' + b.id + '"></div>' +
    '<div class="section-title">שעות פנויות</div>' +
    '<div id="my-slots-' + b.id + '" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;"></div>' +
    '<button class="btn btn-primary" style="margin-top:12px;" id="my-save-' + b.id + '" disabled>שמירת מועד חדש</button>';

  var dateInput = document.getElementById('my-date-' + b.id);
  var today = new Date();
  dateInput.min = today.toISOString().slice(0, 10);
  dateInput.max = new Date(today.getTime() + 30 * 86400000).toISOString().slice(0, 10);
  dateInput.value = b.booking_date;

  var chosenTime = null;
  var saveBtn = document.getElementById('my-save-' + b.id);

  function loadMySlots() {
    var slotsEl = document.getElementById('my-slots-' + b.id);
    slotsEl.innerHTML = '<div style="grid-column:1/-1;font-size:13px;color:oklch(0.94 0.006 260 / 0.5);">טוען שעות...</div>';
    chosenTime = null;
    saveBtn.disabled = true;
    api('api/availability.php?date=' + dateInput.value + '&service_id=' + b.service_id + '&exclude_booking_id=' + b.id).then(function (res) {
      var slots = (res.data && res.data.slots) || [];
      slotsEl.innerHTML = '';
      if (!slots.length) {
        slotsEl.innerHTML = '<div style="grid-column:1/-1;font-size:13.5px;color:oklch(0.94 0.006 260 / 0.5);">אין שעות פנויות בתאריך זה</div>';
        return;
      }
      var currentTime = b.booking_time.slice(0, 5);
      slots.forEach(function (s) {
        var isCurrent = dateInput.value === b.booking_date && s.time === currentTime;
        var div = document.createElement('div');
        div.className = 'slot' + (isCurrent ? ' selected' : '');
        div.textContent = s.time;
        div.onclick = function () {
          haptic(8);
          chosenTime = s.time;
          Array.prototype.forEach.call(slotsEl.children, function (el) { el.classList.remove('selected'); });
          div.classList.add('selected');
          saveBtn.disabled = false;
        };
        slotsEl.appendChild(div);
        if (isCurrent) { chosenTime = s.time; saveBtn.disabled = false; }
      });
    });
  }

  dateInput.addEventListener('change', loadMySlots);
  loadMySlots();

  saveBtn.onclick = function () {
    if (!chosenTime) return;
    api('api/booking.php', {
      method: 'PUT',
      body: JSON.stringify({ id: b.id, action: 'reschedule', date: dateInput.value, time: chosenTime, phone: manageState.phone }),
    }).then(function (res) {
      if (res.ok) { toast('התור עודכן'); lookupMyBookings(); } else alert(res.data.error || 'שגיאה');
    });
  };
}

/* ── Accessibility widget ────────────────────────────────────────────── */
function toggleA11yPanel() {
  document.getElementById('a11y-panel').classList.toggle('hidden');
}
function setA11y(mode) {
  var html = document.documentElement;
  if (mode === 'contrast') html.classList.toggle('a11y-contrast');
  if (mode === 'underline') html.classList.toggle('a11y-underline');
  if (mode === 'font-lg') { html.classList.remove('a11y-font-xl'); html.classList.toggle('a11y-font-lg'); }
  if (mode === 'font-xl') { html.classList.remove('a11y-font-lg'); html.classList.toggle('a11y-font-xl'); }
  if (mode === 'reset') { html.className = html.className.replace(/a11y-\S+/g, '').trim(); }
  try {
    localStorage.setItem('a11y-classes', html.className);
  } catch (e) {}
}
function restoreA11y() {
  try {
    var saved = localStorage.getItem('a11y-classes');
    if (saved) document.documentElement.className = saved;
  } catch (e) {}
}

document.addEventListener('DOMContentLoaded', function () {
  restoreA11y();
  checkSession();
  loadSettings();
  loadGallery();
  loadServices();
  renderConfirmButton();
  setupScrollReveal();

  var dateInput = document.getElementById('date-input');
  var today = new Date();
  var min = today.toISOString().slice(0, 10);
  var max = new Date(today.getTime() + 30 * 86400000).toISOString().slice(0, 10);
  dateInput.min = min; dateInput.max = max;
  dateInput.addEventListener('change', function () { onDateChange(this.value); });
  // Some browsers only open the native date picker when the small calendar icon itself
  // is clicked, not the rest of the field — force it open on any click/focus where supported.
  dateInput.addEventListener('click', function () { try { this.showPicker && this.showPicker(); } catch (e) {} });
  dateInput.addEventListener('focus', function () { try { this.showPicker && this.showPicker(); } catch (e) {} });
});

function togglePasswordVisibility(inputId, btn) {
  var input = document.getElementById(inputId);
  var isHidden = input.type === 'password';
  input.type = isHidden ? 'text' : 'password';
  btn.innerHTML = isHidden
    ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-7-11-7a21.6 21.6 0 015.06-6.06M9.9 4.24A10.94 10.94 0 0112 4c7 0 11 7 11 7a21.6 21.6 0 01-3.22 4.34M1 1l22 22"/><path d="M14.12 14.12a3 3 0 11-4.24-4.24"/></svg>'
    : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>';
}
