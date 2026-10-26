# Practical 14 — Admin Dashboard, RBAC and Audit Log

## What was added/updated
- `Pages/admin_dashboard.php`: admin-only dashboard with dynamic SQL counts for users, students, events and registrations; latest registrations; and recent audit log rows.
- `php/audit_log.php`: reusable helper to record administrator actions.
- `db/practical14_audit_logs.sql`: migration that creates the `audit_logs` table without replacing existing data.
- `Pages/event_management.php`: logs successful event create/update/delete actions.
- `Pages/student_management.php`: logs successful student create/update/delete actions.
- `Pages/student_dashboard.php`: student-specific menu; administrator management links are not shown to student users.
- Existing `php/auth_check.php` and `require_role("admin")` protect the admin dashboard and management pages.

## Database migration (required)
1. Open `http://localhost/phpmyadmin/`.
2. Select the existing `studenthub` database.
3. Open the **SQL** tab.
4. Paste/run the contents of `db/practical14_audit_logs.sql`.
5. Confirm that the `audit_logs` table appears. Do not import the full database dump again; this migration only adds the new table.

The existing database already contains `users` (including a `role` column), `students`, `events`, and `registrations`. No other schema changes are required for this practical.

## Run
With Apache and MySQL started, open:
`http://localhost/CHARUSAT/d26it118_practical-14/Pages/login_user.php`

If your folder name or capitalization differs, match the URL to the actual folder under `C:\xampp\htdocs\CHARUSAT\`.

Log in with the admin account already present in your database, then open the Admin Dashboard. The login handler supports the existing legacy password and upgrades it to a password hash after a successful login.

## Test cases
1. Admin login: dashboard shows counts, latest registrations and audit log.
2. Student login: student dashboard appears with student menu, not admin links.
3. Direct access: while logged in as a student, try opening `Pages/admin_dashboard.php`; it should redirect to the student dashboard.
4. Create/update/delete an event through Event Management; verify a corresponding row appears in `audit_logs`.
5. Create/update/delete a student through Student Management; verify a corresponding row appears in `audit_logs`.
6. Refresh the admin dashboard and confirm the statistics change when rows are added or deleted.
7. In phpMyAdmin, run `SELECT * FROM audit_logs ORDER BY log_id DESC;` to verify audit records.

## Recommended report screenshots
- Admin dashboard with all four live statistic cards.
- Latest registrations table.
- Recent Admin Audit Log table.
- Student dashboard showing student-only menu.
- Attempt to open admin dashboard while logged in as student (redirect/access restriction).
- phpMyAdmin `audit_logs` table with create/update/delete entries.
- Event or student management success message and matching audit log row.
