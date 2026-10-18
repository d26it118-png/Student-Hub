# StudentHub ER Model
Students 1:M Registrations M:1 Events. Thus Students and Events have a many-to-many relationship through Registrations.
- students: id PK, student_id UK, name, email UK, mobile, course, year, gender
- events: event_id PK, title, category, event_date, venue, organizer
- registrations: registration_id PK, student_id FK, event_id FK, registered_at; UNIQUE(student_id,event_id)
