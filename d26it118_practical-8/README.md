# StudentHub - D26IT118 Practical 8

## Practical Title
**MySQL Schema Design, ER Model, MySQLi Connectivity, and Prepared Statements**

## Objective
Design a normalized StudentHub database containing students, events and registrations. Connect PHP to MySQL using basic MySQLi functions and use SQL queries/prepared statements for database operations.

## Technologies
- HTML5
- CSS3
- JavaScript
- PHP
- MySQL
- phpMyAdmin
- XAMPP
- MySQLi

## Main Practical 8 Work
1. Created the `studenthub` MySQL database.
2. Created normalized `students`, `events`, and `registrations` tables.
3. Added primary keys and foreign keys.
4. Added seed/test data.
5. Created `db/db.php` using `mysqli_connect()`.
6. Created `db/db_test.php` to test the connection.
7. Converted student listing to MySQL-backed `students.php`.
8. Converted event listing to MySQL-backed `events.php`.
9. Added event registration using the `registrations` table.
10. Added `registrations.php` using INNER JOIN.
11. Added `crud.php` for basic UPDATE and DELETE operations.
12. Registration uses MySQLi prepared statements. PDO is not used anywhere.

## Database Setup
1. Start Apache and MySQL in XAMPP.
2. Open phpMyAdmin.
3. Import `db/studenthub_schema.sql`.
4. The script creates the `studenthub` database and all three tables.

## Run
Copy the folder into:
`C:\xampp\htdocs\`

Open:
`http://localhost/d26it11practical-8/Pages/index.html`

Test connection:
`http://localhost/d26it11practical-8/db/db_test.php`

Student records:
`http://localhost/d26it11practical-8/Pages/students.php`

Events:
`http://localhost/d26it11practical-8/Pages/events.php`

Registrations:
`http://localhost/d26it11practical-8/Pages/registrations.php`

## Viva Points
- `students.student_id` is the primary key.
- `events.event_id` is the primary key.
- `registrations.registration_id` is the primary key.
- `registrations.student_id` is a foreign key to `students`.
- `registrations.event_id` is a foreign key to `events`.
- The schema avoids repeating student and event information in registrations.
- MySQLi is used instead of PDO.
- Prepared statements are used where user input is inserted/updated.
- `mysqli_query()` is used for basic SELECT queries.

## Submission
Submit:
- SQL dump: `db/studenthub_schema.sql`
- Database connection: `db/db.php`
- Connection test screenshot
- Complete project ZIP


## UI Preservation
The Practical 7 Events page UI is preserved: search, category filter, sorting, event cards, pagination and footer remain the same. Only the Events data source is changed from `events.json` to the MySQL `events` table. The FAQ page and FAQ JavaScript are preserved from Practical 7.
