-- Practical 12: Event Management CRUD with Poster Upload and File Validation
-- Database: studenthub
-- Import this script once in phpMyAdmin after selecting/importing the Practical 11 database.
USE studenthub;

-- If you already added either column manually, remove that ADD COLUMN clause before importing.
ALTER TABLE events
    ADD COLUMN poster VARCHAR(255) NULL AFTER organizer,
    ADD COLUMN status ENUM('Upcoming','Completed','Cancelled') NOT NULL DEFAULT 'Upcoming' AFTER poster;

-- Sample event records (existing IDs are left unchanged).
INSERT IGNORE INTO events (event_id, title, category, event_date, venue, organizer, status) VALUES
(901, 'Student Innovation Expo', 'Technical', '2026-11-10', 'CSPIT Main Hall', 'IT Department', 'Upcoming'),
(902, 'Web Design Workshop', 'Workshop', '2026-11-18', 'Computer Lab 2', 'Web Development Club', 'Upcoming'),
(903, 'CodeSprint Challenge', 'Competition', '2026-12-02', 'Innovation Center', 'Coding Club', 'Upcoming'),
(904, 'Cloud Computing Seminar', 'Seminar', '2026-12-12', 'Seminar Hall A', 'IT Department', 'Upcoming');

-- Verify:
-- SELECT event_id, title, poster, status FROM events ORDER BY event_id;
