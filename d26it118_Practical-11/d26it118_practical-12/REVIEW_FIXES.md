# Practical 10 ZIP review and fixes

## Fixes applied
1. Replaced `php/process_login.php`, which incorrectly contained the login-page HTML, with actual POST login processing.
2. The processor now queries `users` with a prepared statement and verifies the stored hash using `password_verify()`.
3. On successful login it regenerates the session ID, stores the verified user's ID/name/email/role, updates `last_login`, and redirects by database role.
4. Added 30-day Remember Me token creation using `random_bytes()`, storing only the SHA-256 token hash in `auth_tokens`.
5. Fixed authentication timeout and logout redirects to point to the file that actually exists: `Pages/login_user.php` (the ZIP does not contain `Pages/login_user.html`).
6. Updated README URLs to use `login_user.php`.

## Before testing
- Import/run `db/practical10_auth_schema.sql` in phpMyAdmin while using the `studenthub` database. It must create `auth_tokens` and ensure `users.last_login` exists.
- Make sure passwords in `users.password` are hashes created by `password_hash()`. Existing plaintext passwords will not work with `password_verify()`.
- Register an account, then make one test account admin using its real email, e.g. `UPDATE users SET role='admin' WHERE email='your-email@example.com';`.
- Run from XAMPP at `http://localhost/d26it118_Practical-10/Pages/login_user.php`.

## Note
Remember Me restoration in `Pages/auth_check.php` uses the `auth_tokens` table, so that table must exist before testing. For a production site, add rate limiting and require HTTPS.
