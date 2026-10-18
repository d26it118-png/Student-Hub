# Practical 10 → Practical 11 conversion

This folder keeps the StudentHub site and authentication/database setup from Practical 10, and adds **Student Management CRUD with Search, Filter and Pagination** for Practical 11.

Main file: `Pages/student_management.php`
Database dump: `db/practical11_studenthub.sql`
Test cases: `docs/PRACTICAL11_TEST_CASES.md`

The existing schema uses `student_id` (VARCHAR) as the student primary key and stores `course` as text, so this implementation follows the actual Practical 10 schema instead of assuming an `id` or `courses` table.
