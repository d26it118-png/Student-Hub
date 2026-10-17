# StudentHub

StudentHub is a student portal developed as a series of Web Development Frameworks practicals. This repository contains **Practicals 1-11** for D26IT118 at CHARUSAT.

The practicals build the portal step by step, from project planning and accessible web pages to database-backed student features and secure authentication.

## What StudentHub Does

- Provides pages for the home screen, dashboard, attendance, assignments, events, FAQs, profile, and contact information.
- Displays student, event, and FAQ data from JSON files, with JavaScript rendering and interactive search or filtering features.
- Includes registration forms with client-side and server-side validation and clear feedback.
- Demonstrates PHP form processing and CSV/JSON data storage.
- Uses a MySQL database for student, event, and registration data, with schema documentation and prepared database queries.
- Supports account registration with duplicate-email checks and hashed passwords.
- Provides login and logout, student and admin dashboards, role checks, session timeout, and an optional remember-me token.
- Adds admin-only student management with search, filters, pagination, and CRUD operations.

## Practical Index

| Practical | Topic | Folder |
|---|---|---|
| 1 | Project initiation, requirements, sitemap, wireframe, and GitHub setup | [d26it118_practical-1](./d26it118_practical-1/) |
| 2 | Semantic HTML5 pages and accessibility-ready structure | [d26it118_practical-2](./d26it118_practical-2/) |
| 3 | Responsive design with CSS, Grid, and Flexbox | [d26it118_practical-3](./d26it118_practical-3/) |
| 4 | JavaScript DOM manipulation, events, and UI interactivity | [d26it118_practical-4](./d26it118_practical-4/) |
| 5 | Registration forms, frontend validation, and error handling | [d26it118_practical-5](./d26it118_practical-5/) |
| 6 | JSON data, Fetch API, search, and filtering | [d26it118_practical-6](./d26it118_practical-6/) |
| 7 | PHP form processing, server-side validation, and CSV/JSON storage | [d26it118_practical-7](./d26it118_practical-7/) |
| 8 | MySQL schema design, ER model, database connectivity, and prepared statements | [d26it118_practical-8](./d26it118_practical-8/) |
| 9 | Secure account registration, duplicate-email checks, and password hashing | [d26it118_practical-9](./d26it118_practical-9/) |
| 10 | Secure login/logout, sessions, role-based access, timeout, and remember-me | [d26it118_practical-10](./d26it118_practical-10/) |
| 11 | Admin student management with MySQLi CRUD, search, filters, and pagination | [d26it118_practical-11](./d26it118_practical-11/) |

For setup instructions and test cases for Practical 10, see [its README](./d26it118_practical-10/README.md).
For Practical 11, see [its README](./d26it118_practical-11/README_PRACTICAL11.md).

## Technologies

- HTML5 and CSS3
- JavaScript
- PHP
- MySQL and MySQLi
- JSON and CSV
- XAMPP for local PHP and MySQL development

## Run Locally

Static HTML pages can be opened in a browser. To run the PHP and database features:

1. Install and start Apache and MySQL using XAMPP.
2. Copy or clone this repository into `C:\xampp\htdocs\Student-Hub`.
3. Create the `studenthub` database and set it up using the SQL files in `d26it118_practical-10\db\`. Start with `studenthub_schema.sql`, then run `users_schema.sql` and `practical10_auth_schema.sql`.
4. **Database reset warning:** `studenthub_schema.sql` drops and recreates the `students`, `events`, and `registrations` tables. Use it only for a fresh practical database, or review the SQL before running it if you need to preserve existing data.
5. If your local MySQL credentials differ from the project defaults, update the database connection settings in the PHP connection files.
6. Open the Practical 10 login page at [http://localhost/Student-Hub/d26it118_practical-10/Pages/login_user.php](http://localhost/Student-Hub/d26it118_practical-10/Pages/login_user.php).

More detailed database and authentication instructions are in the Practical 10 README.
