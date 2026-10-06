# StudentHub - D26IT118 Practical 9

## Practical Title
**Secure User Registration with Database Insert, Duplicate Email Check, and Password Hashing**

## Objective
Implement secure user registration using PHP, MySQLi, XAMPP and MySQL. The registration flow validates input on both frontend and backend, checks duplicate email addresses, hashes passwords using `password_hash()`, and inserts the account using a prepared statement.

## Technologies
- HTML5
- CSS3
- JavaScript
- PHP
- MySQL
- phpMyAdmin
- XAMPP
- MySQLi

## Converted from Practical 8
Practical 8 already used MySQLi and prepared statements. Practical 9 adds a separate `users` account table and secure account-registration flow without removing the existing StudentHub student/event database work.

## Practical 9 Files
1. `Pages/register_user.html` - secure registration form and frontend validation.
2. `db/db_connect_mysqli.php` - MySQLi database connection.
3. `Pages/process_user_registration.php` - backend validation, duplicate email check, password hashing and prepared INSERT.
4. `db/users_schema.sql` - creates the `users` table.

## Database Setup
1. Start Apache and MySQL in XAMPP.
2. Open phpMyAdmin.
3. Select the `studenthub` database.
4. Run `db/users_schema.sql`.
5. Do not delete the existing Practical 8 `students`, `events`, or `registrations` tables.

## Run
Copy the folder into:
`C:\xampp\htdocs\`

Open:
`http://localhost/d26it118_Practical-9/Pages/register_user.html`

## Secure Registration Flow
POST check → frontend validation → backend validation → duplicate email check → `password_hash()` → MySQLi prepared INSERT → success/failure message.

## Key Viva Points
- `password_hash()` stores a one-way password hash, not the original password.
- `SELECT id FROM users WHERE email = ?` checks for duplicates.
- `UNIQUE(email)` provides a database-level duplicate safeguard.
- `prepare()`, `bind_param()` and `execute()` protect the INSERT from SQL injection.
- Frontend validation improves user experience, but backend validation is the real security gate.
- `VARCHAR(255)` is used for the password hash column.

## Post-Laboratory Test Cases
- Valid registration → row inserted.
- Duplicate email → no row inserted.
- Empty fields → validation error.
- Invalid email → validation error.
- Short/weak password → validation error.
- SQL injection-style input → treated as data.
- phpMyAdmin password column → unreadable hash, not plain text.
