# Barber Site — Project Overview

A Hebrew-language barbershop website ("ספר בראש צעיר") with public marketing pages, an online appointment-booking system, and an admin panel. Built in plain PHP (no framework) with a custom front-controller router.

## Stack

- **Language:** PHP (procedural front controller + a few classes, no framework)
- **Database:** MySQL (see `sql/install.sql`, `sql/berber.sql`)
- **Dependencies (Composer):** `green-api/whatsapp-api-client-php` (WhatsApp notifications), `PHPMailer` (vendored directly under `/PHPMailer`, not via Composer)
- **Frontend:** plain CSS/JS (`assets/css/style.css`, `assets/js/main.js`, `assets/js/admin-appointments.js`), server-rendered PHP views
- **Web server:** Apache/XAMPP, routing rewritten via `.htaccess`

## Routing

`index.php` is the single entry point. It:
1. Loads `config/config.php` and `config/database.php`.
2. Autoloads classes from `classes/` by class name.
3. Parses `controller/action/param` from the URL (via `.htaccess` rewrite into `?url=...`, with a `?controller=&action=` override path used for AJAX calls from JS).
4. Dispatches to one of three controllers: `HomeController`, `AppointmentController`, `AdminController`.

Debug mode is currently **on** in both `index.php` and `config/config.php` (`display_errors`, full `error_reporting(E_ALL)`) — should be disabled before production.

## Controllers

| Controller | Responsibility |
|---|---|
| `controllers/HomeController.php` (997 lines) | Public pages: home, about, services, gallery, contact, privacy/terms/cookies/accessibility statements |
| `controllers/AppointmentController.php` (416 lines) | Booking flow: available slots, create/update/cancel appointment, manage-by-token lookup |
| `controllers/AdminController.php` (690 lines) | Admin auth, dashboard, appointments/customers/services/schedule/settings management, activity logs |

## Core Classes (`classes/`)

- `Appointment.php` — appointment domain logic/model (188 lines)
- `Database.php` — DB connection wrapper (49 lines)
- `CSRF.php` — CSRF token generation/validation (21 lines)
- `RateLimiter.php` — login/booking rate limiting (49 lines)
- `Validation.php` — input validation helpers (14 lines)
- `Logger.php` — writes to `logs/error.log` / `logs/system.log` (23 lines)

## Views (`views/`)

- Public: `booking.php`, `manage_booking.php` (545 lines — view/edit/cancel an existing appointment by token), legal pages (`privacy-policy.php`, `terms-of-service.php`, `cookie-policy.php`, `accessibility-statement.php`)
- Admin (`views/admin/`): `login.php` (650 lines), `dashboard.php` (417 lines), `appointments.php`, `customers.php`, `customer_details.php`, `services.php`, `schedule.php`, `settings.php`, `logs.php`
- Shared layout: `views/layout/header.php`, `footer.php`, `admin_header.php`, `admin_footer.php`
- Note: `views/admin/AdminController.php` also exists (49 lines) — unusual naming for a view file, worth checking whether it's a leftover/mistaken file.

## Database Schema (`sql/install.sql`)

Tables: `admin_users`, `services`, `customers`, `appointments`, `business_hours`, `blocked_dates`, `settings`, `rate_limits`, `activity_logs`.

## Configuration (`config/`)

- `config.php` — site name, contact info, appointment rules (30-min slots, 12h min cancellation notice, 30-day advance booking limit), upload limits, rate-limit thresholds, error logging path. `SITE_URL`/`BASE_PATH` are now auto-detected from the project's location on disk relative to the web server's document root, so the same codebase works unmodified whether it's deployed at a domain root (Hostinger) or under a subfolder like `/barber-site` (local XAMPP) — all internal links, redirects, asset/image paths, and AJAX calls in the views/JS route through `SITE_URL` (PHP) or `window.BASE_URL` (static JS) instead of hardcoded absolute paths.
- `database.php` — DB connection settings
- `security.php` — security-related settings

## Integrations

- **WhatsApp notifications** via `green-api/whatsapp-api-client-php` (vendored in `vendor/green-api/`)
- **Email** via a vendored copy of PHPMailer (`/PHPMailer/`) — likely for booking confirmations/admin notices

## Other Notable Files

- `HashPass.php` — standalone utility script (likely for generating admin password hashes) sitting at the project root; should probably not be web-accessible in production
- `uploads/Images/`, `uploads/services/` — user/admin-uploaded images (barbershop gallery, service photos), protected by an `.htaccess`
- `robots.txt`, `sitemap.xml` — SEO basics
- `logs/error.log`, `logs/system.log` — runtime logs (should not be web-accessible; confirm `.htaccess`/permissions)

## Things Worth Cleaning Up

1. Debug/error display is enabled (`index.php`, `config/config.php`) — disable for production.
2. Stray file: `views/admin/AdminController.php` (unusual naming for a view).
3. `HashPass.php` at the web root — confirm it's not publicly reachable, or remove after use.
4. Hardcoded placeholder contact info in `config/config.php` (`admin@example.com`, etc.).
