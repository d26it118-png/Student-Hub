# Conversion Note

This project was converted directly from the supplied Practical 7 ZIP.

- Existing StudentHub HTML/CSS/JavaScript design is retained.
- CSV registration storage is replaced by MySQL.
- PDO is NOT used.
- Basic MySQLi (`mysqli_connect`, `mysqli_query`, `mysqli_fetch_assoc`) is used.
- MySQLi prepared statements are used for user-input INSERT/UPDATE operations.
- Practical 8 database tables: students, events, registrations.
- `data/students.csv` and JSON files are retained only as original Practical 7 reference data; active database pages use MySQL.
