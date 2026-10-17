-- Practical 11 StudentHub database dump
-- Import into phpMyAdmin. This keeps the Practical 10 schema compatible.
CREATE DATABASE IF NOT EXISTS studenthub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studenthub;

CREATE TABLE IF NOT EXISTS students (
    student_id VARCHAR(20) PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) DEFAULT NULL,
    course VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    gender VARCHAR(20) DEFAULT NULL
) ENGINE=InnoDB;

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
('ST110','Mahi Vora','mahi.vora@example.com','9876500010','B.Tech CSE',2,'female'),
('ST111','Krish Patel','krish.patel@example.com','9876500011','B.Tech CE',1,'male'),
('ST112','Anaya Shah','anaya.shah@example.com','9876500012','B.Tech CE',2,'female'),
('ST113','Vivaan Mehta','vivaan.mehta@example.com','9876500013','B.Tech CSE',3,'male'),
('ST114','Sara Desai','sara.desai@example.com','9876500014','B.Tech IT',4,'female'),
('ST115','Dhruv Joshi','dhruv.joshi@example.com','9876500015','B.Tech CE',2,'male'),
('ST116','Myra Trivedi','myra.trivedi@example.com','9876500016','B.Tech CSE',1,'female'),
('ST117','Reyansh Vora','reyansh.vora@example.com','9876500017','B.Tech IT',3,'male'),
('ST118','Aadhya Parmar','aadhya.parmar@example.com','9876500018','B.Tech CE',4,'female'),
('ST119','Arjun Rana','arjun.rana@example.com','9876500019','B.Tech CSE',2,'male'),
('ST120','Kiara Patel','kiara.patel@example.com','9876500020','B.Tech IT',1,'female')
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name), mobile=VALUES(mobile), course=VALUES(course), year=VALUES(year), gender=VALUES(gender);

-- Verify seed data: SELECT COUNT(*) FROM students;
