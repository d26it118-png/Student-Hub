# StudentHub — D26IT118 Practical 14

## Practical Title
**Admin Dashboard with Role-Based Access Control, Dynamic Menu, and Audit Log**

## Objective
Create an administrator-only dashboard showing live user/student/event/registration statistics, latest registrations, role-specific navigation and an audit trail of important administrator actions.

## Database
Keep the existing `studenthub` database. It already contains `users`, `students`, `events`, and `registrations`; the `users.role` field distinguishes `admin` and `student` accounts. Run only `db/practical14_audit_logs.sql` in phpMyAdmin to add the new `audit_logs` table. Do not re-import the full SQL dump.

## Run
1. Copy this folder into `C:\xampp\htdocs\CHARUSAT\d26it118_practical-14\`.
2. Start Apache and MySQL in XAMPP.
3. In phpMyAdmin, select `studenthub`, open SQL and run `db/practical14_audit_logs.sql`.
4. Open `http://localhost/CHARUSAT/d26it118_practical-14/Pages/login_user.php`.
5. Log in with the admin account. The login handler uses the existing account in `users`; its legacy plaintext password is converted to a password hash after successful login.
6. Open the Admin Dashboard and test student/event management. Refresh the dashboard to see live counts and audit entries.

## Main Practical 14 files
- `Pages/admin_dashboard.php` — live dashboard stats, latest registrations, recent audit logs; admin-only access.
- `Pages/student_dashboard.php` — student-specific navigation.
- `php/audit_log.php` — audit logging helper.
- `db/practical14_audit_logs.sql` — safe migration for the audit log table.
- `Pages/event_management.php` and `Pages/student_management.php` — successful create/update/delete actions are recorded in `audit_logs`.
- `php/auth_check.php` — existing session and role checks.

See `docs/PRACTICAL14_README.md` for test cases, report screenshot checklist and viva verification steps.
