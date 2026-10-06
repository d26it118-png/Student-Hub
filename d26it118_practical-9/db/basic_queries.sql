-- Basic MySQL queries used in Practical 8

USE studenthub;

-- SELECT
SELECT * FROM students;
SELECT * FROM events;

-- INSERT
INSERT INTO events (event_id, title, category, event_date, venue, organizer)
VALUES (20, 'Database Workshop', 'Workshop', '2026-12-01', 'Lab 1', 'DBMS Club');

-- UPDATE
UPDATE students SET full_name = 'Updated Student'
WHERE student_id = 'ST101';

-- DELETE
DELETE FROM registrations WHERE registration_id = 1;

-- INNER JOIN
SELECT s.full_name, e.title, e.event_date
FROM registrations r
INNER JOIN students s ON r.student_id = s.student_id
INNER JOIN events e ON r.event_id = e.event_id;
