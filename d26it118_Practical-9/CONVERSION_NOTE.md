# Practical 8 → Practical 9 Conversion

## Reused from Practical 8
- StudentHub UI and CSS
- Existing XAMPP/MySQL database
- MySQLi connectivity approach
- Prepared-statement approach
- Practical 5 frontend validation patterns

## Added for Practical 9
- `db/db_connect_mysqli.php`
- `db/users_schema.sql`
- `Pages/register_user.html`
- `Pages/process_user_registration.php`

## Security additions
- Backend validation
- Duplicate email SELECT check
- `UNIQUE(email)` database constraint
- `password_hash($password, PASSWORD_DEFAULT)`
- Prepared INSERT with `bind_param("sss", ...)`
- Friendly duplicate/database messages
- Plain password never stored
