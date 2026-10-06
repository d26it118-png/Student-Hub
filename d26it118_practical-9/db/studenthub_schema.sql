CREATE DATABASE IF NOT EXISTS studenthub;
USE studenthub;

DROP TABLE IF EXISTS registrations;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS students;

CREATE TABLE students (
    student_id VARCHAR(20) PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15),
    course VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    gender VARCHAR(20)
);

CREATE TABLE events (
    event_id INT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(50) NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(150) NOT NULL,
    organizer VARCHAR(100) NOT NULL
);

CREATE TABLE registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    event_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_registration (student_id, event_id),
    CONSTRAINT fk_registration_student
        FOREIGN KEY (student_id) REFERENCES students(student_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_registration_event
        FOREIGN KEY (event_id) REFERENCES events(event_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO students (student_id, full_name, email, mobile, course, year, gender) VALUES
('ST101','Aarav Shah','aarav.shah@example.com','9876500001','B.Tech IT',3,'male'),
('ST102','Diya Patel','diya.patel@example.com','9876500002','B.Tech IT',2,'female'),
('ST103','Rohan Mehta','rohan.mehta@example.com','9876500003','B.Tech CSE',3,'male'),
('ST104','Kavya Desai','kavya.desai@example.com','9876500004','B.Tech IT',1,'female'),
('ST105','Harsh Trivedi','harsh.trivedi@example.com','9876500005','B.Tech CSE',4,'male'),
('ST106','Meera Joshi','meera.joshi@example.com','9876500006','B.Tech IT',2,'female'),
('ST107','Dev Patel','dev.patel@example.com','9876500007','B.Tech IT',3,'male'),
('ST108','Isha Shah','isha.shah@example.com','9876500008','B.Tech CSE',1,'female'),
('ST109','Yash Parmar','yash.parmar@example.com','9876500009','B.Tech IT',4,'male'),
('ST110','Mahi Vora','mahi.vora@example.com','9876500010','B.Tech CSE',2,'female');

INSERT INTO events (event_id, title, category, event_date, venue, organizer) VALUES
(1,'AI Innovation Workshop','Workshop','2026-10-08','CSPIT Computer Lab','IT Department'),
(2,'Web Development Bootcamp','Technical','2026-10-12','Seminar Hall A','WDF Club'),
(3,'CodeSprint Challenge','Competition','2026-10-15','Innovation Center','Coding Club'),
(4,'Cloud Computing Seminar','Seminar','2026-10-19','Auditorium','Cloud Club'),
(5,'Cyber Security Workshop','Workshop','2026-10-22','Computer Lab 2','Cyber Club'),
(6,'Hackathon 2026','Competition','2026-10-25','Innovation Center','CDPC'),
(7,'Java Programming Seminar','Seminar','2026-10-28','Seminar Hall B','Java Club'),
(8,'UI UX Design Workshop','Workshop','2026-11-02','Design Studio','Design Club'),
(9,'Data Structures Contest','Competition','2026-11-05','Computer Lab 1','DSA Club'),
(10,'AI and Machine Learning Seminar','Seminar','2026-11-09','Auditorium','AI Club');

INSERT INTO registrations (student_id, event_id) VALUES
('ST101',1),
('ST102',2),
('ST103',3),
('ST101',5);
