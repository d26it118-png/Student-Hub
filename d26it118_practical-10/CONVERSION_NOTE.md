# Practical 9 → Practical 10 Conversion

## Reused from Practical 9
- StudentHub interface, CSS, images and pages
- `studenthub` database and `users` table
- MySQLi connection (`db/db_connect_mysqli.php`)
- User registration, prepared INSERT, duplicate-email check and `password_hash()`

## Added for Practical 10
- `Pages/login_user.html`
- `php/process_login.php`
- `Pages/auth_check.php`
- `Pages/student_dashboard.php`
- `Pages/admin_dashboard.php`
- `php/logout.php`
- `db/practical10_auth_schema.sql`

## Main behavior changes
- Login uses POST and `password_verify()`.
- Session ID is regenerated after successful login.
- Session stores user ID, name, email, role and last activity; password/hash are not stored in the session.
- Admin/student users are redirected to their correct dashboards.
- Each dashboard enforces authentication, role and a 15-minute idle timeout.
- Logout clears session data, expires the session cookie, destroys the session and revokes the remember-me token.
- Optional remember-me uses a cryptographically random token in an HttpOnly cookie and stores only its SHA-256 hash in MySQL.
- `last_login` is recorded in the database.
