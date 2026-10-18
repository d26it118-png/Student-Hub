# ER Model - StudentHub Practical 8

```text
STUDENTS
---------
student_id (PK)
full_name
email
mobile
course
year
gender
     |
     | 1
     |
     | M
REGISTRATIONS
-------------
registration_id (PK)
student_id (FK)
event_id (FK)
registration_date
     |
     | M
     |
     | 1
EVENTS
------
event_id (PK)
title
category
event_date
venue
organizer
```

## Relationships
- One student can have many registrations.
- One event can have many registrations.
- The registrations table resolves the many-to-many relationship between students and events.

## Normalization
Student information is stored only in `students`, event information only in `events`, and the relationship is stored in `registrations`. This reduces duplication and update anomalies.
