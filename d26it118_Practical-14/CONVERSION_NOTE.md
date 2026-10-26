# Conversion Note — Practical 13 to Practical 14

This folder is based on the supplied StudentHub Practical 13 project and adds the Practical 14 admin dashboard, role-based menu, and audit logging.

## Main Practical 14 changes
- Rebuilt `Pages/admin_dashboard.php` with live SQL counts for users, students, events, and registrations.
- Added a latest registrations table and a recent audit log table.
- Added `php/audit_log.php` to record administrator actions.
- Added `db/practical14_audit_logs.sql` to create the required `audit_logs` table.
- Updated event and student management CRUD handlers to record successful create, update, and delete actions.
- Updated `Pages/student_dashboard.php` to display student-specific navigation.
- Added `docs/PRACTICAL14_README.md` with setup, testing, and screenshot instructions.

## Database changes required
Run only `db/practical14_audit_logs.sql` against the existing `studenthub` database. The supplied schema already has the `users`, `students`, `events`, and `registrations` tables and the `users.role` field. Do not re-import the entire database dump.

## Validation
All PHP files in this package passed `php -l` syntax checks. Database execution and browser behavior still need to be verified locally in XAMPP.
