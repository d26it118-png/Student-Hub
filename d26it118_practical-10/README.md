# StudentHub — D26IT118 Practical 10

## Practical Title
**Secure Login & Logout with Sessions, Role-Based Access, Timeout and Remember-Me Token**

## Objective
Extend Practical 9 registration so users can sign in with `password_verify()`, get a regenerated PHP session ID, access only the dashboard permitted by their database role, and be logged out after 15 minutes of inactivity.

## What changed from Practical 9
- Kept the existing StudentHub pages, assets, registration flow, and MySQLi database connection.
- Added `Pages/login_user.php` with POST login and optional Remember Me.
- Added `php/process_login.php` for prepared user lookup, password verification, session regeneration, session values, last-login timestamp, and role-based redirect.
- Added `Pages/auth_check.php` to enforce login, 15-minute inactivity timeout, and role checks.
- Added `Pages/student_dashboard.php` and `Pages/admin_dashboard.php`.
- Added `php/logout.php` to revoke the remember-me token, clear the session array, expire cookies, and destroy the session.
- Added `db/practical10_auth_schema.sql` to create the remember-me token table and add `last_login`.


## Requirements
- XAMPP with Apache and MySQL running
- PHP with MySQLi enabled
- Existing `studenthub` database and `users` table from Practical 9

## Database setup
1. Copy `d26it118_Practical-10` into `C:\xampp\htdocs\`.
2. Open `http://localhost/phpmyadmin`.
3. Select database `studenthub`.
4. Import/run `db/practical10_auth_schema.sql`.
5. Ensure users created through Practical 9 have a hashed password and role `student`.
6. To test the admin dashboard, run this SQL after replacing the email with an account you own:
   ```sql
   UPDATE users SET role = 'admin' WHERE email = 'admin@gmail.com';
   ```
   Create/register that account first. Do not let users select their own role in the registration form.

## Run
Open:
`http://localhost/d26it118_Practical-10/Pages/login_user.php`

Login uses the email and password from the `users` table. The stored password must be a `password_hash()` value created by Practical 9.

## Important files
| File | Purpose |
|---|---|
| `Pages/login_user.php` | Login form |
| `php/process_login.php` | Verify credentials, regenerate ID, store session and redirect by role |
| `Pages/auth_check.php` | Session guard, timeout and role enforcement |
| `Pages/student_dashboard.php` | Student-only dashboard |
| `Pages/admin_dashboard.php` | Admin-only dashboard |
| `php/logout.php` | Revoke remember-me token and destroy session |
| `db/practical10_auth_schema.sql` | `last_login` and `auth_tokens` database setup |

## Test cases and screenshots
1. **Login success:** log in as a student; student dashboard and welcome name appear.
2. **Admin login:** log in as admin; admin dashboard and Manage Students link appear.
3. **Failed login:** wrong password; message is `Invalid email or password.`
4. **Unknown email:** use an unregistered email; same error message appears.
5. **Unauthorized access:** log out, then open `Pages/student_dashboard.php`; it redirects to login.
6. **Role protection:** log in as student and open `Pages/admin_dashboard.php`; it redirects to student dashboard.
7. **Timeout:** temporarily change `$timeoutSeconds = 900` in `Pages/auth_check.php` to `10`, log in, wait 10+ seconds without activity, then refresh a protected page. Restore `900` afterward.
8. **Logout:** click Logout, then try opening a dashboard URL again.
9. **Session ID:** inspect `PHPSESSID` in browser DevTools before and after login; it should change after login.
10. **Remember Me:** tick the box, log in, close/reopen the browser, and test within 30 days. This requires the SQL setup above.

## Security notes
- Passwords are checked with `password_verify()`; plain passwords are never stored.
- SQL statements use prepared statements.
- The role comes from the database and is stored server-side in `$_SESSION`, never trusted from a form or cookie.
- Session cookies use `HttpOnly` and `SameSite=Lax`; `Secure` is enabled automatically when HTTPS is used.
- Production deployments must use HTTPS.
- For a real public website, add rate limiting to slow repeated login attempts.

## Viva quick answers
- **Session:** server-side data associated with a session ID.
- **Why regenerate ID?** To prevent session fixation after successful login.
- **Why `password_verify()`?** It safely checks a typed password against the stored hash.
- **How is RBAC enforced?** Every dashboard checks both login status and the role.
- **Timeout:** 900 seconds of inactivity, or 15 minutes.
- **Secure logout:** clear `$_SESSION`, expire the session cookie, then call `session_destroy()`.
- **Remember Me:** use `random_bytes()`, keep only the token hash and expiry in the database, and revoke it on logout.
