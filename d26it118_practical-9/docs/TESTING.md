# Testing Guide - Practical 8

## 1. Database Connection
Open `db/db_test.php`. Expected output:
`Database connection successful!`

## 2. Students
Open `Pages/students.php`. Student rows should come from MySQL.

## 3. Events
Open `Pages/events.php`. Event rows should come from MySQL.

## 4. Student Registration
Open `Pages/register.php`, submit valid data, then check the `students` table in phpMyAdmin.

## 5. Event Registration
Open `Pages/events.php`, click Register, enter a valid student ID such as `ST101`, and submit.

## 6. JOIN
Open `Pages/registrations.php`. It should show student and event information together using an INNER JOIN.

## 7. CRUD
Open `Pages/crud.php` and test update/delete using a student ID.

## Expected Errors
- Duplicate student ID/email: database duplicate error is handled.
- Duplicate student-event registration: duplicate registration is rejected.
- Invalid form input: validation message is shown.
