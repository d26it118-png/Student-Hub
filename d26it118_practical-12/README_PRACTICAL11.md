# StudentHub — Practical 11

## Features
- Create, read, update and delete student records using MySQLi prepared statements.
- Search by name, email or course; filter by course and academic year.
- Pagination (10 records per page) preserves active search/filter values.
- Clear success/failure messages, delete confirmation, admin role guard, CSRF token and HTML escaping.
- SQL dump seeds 20 students.

## Setup in XAMPP
1. Copy the folder `d26it118_Practical-12` into `C:\xampp\htdocs\`.
2. Start Apache and MySQL in XAMPP.
3. Open `http://localhost/phpmyadmin/`, select **Import**, and import `db/practical11_studenthub.sql`. If your Practical 10 database already exists, this SQL adds/updates the 20 sample students without deleting the other StudentHub tables.
4. Check `db/db_connect_mysqli.php` for your local MySQL username/password (default XAMPP is often `root` with an empty password).
5. Open `http://localhost/d26it118_Practical-12/Pages/login_user.php`, sign in with an admin account created in Practical 10, and use **Manage Students** on the Admin Dashboard.
6. Direct page: `http://localhost/d26it118_Practical-12/Pages/student_management.php` (requires admin session).

## Important schema compatibility
The Practical 10 database has `student_id` as a VARCHAR primary key and `course` as a text field. The Practical 11 module intentionally uses those real column names and does not assume an integer `id` or a separate `courses` table.

## Submission checklist
- `db/practical11_studenthub.sql` (SQL dump, 20 seeded records)
- Screenshot of table/list, add, edit, delete confirmation, search result, no-result search and pagination
- `docs/PRACTICAL11_TEST_CASES.md` with results updated after local testing
