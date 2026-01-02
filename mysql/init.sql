CREATE DATABASE tech_portal;
USE tech_portal;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE,
    password VARCHAR(255)
);

CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(200),
    description TEXT
);

CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    course_id INT,
    status VARCHAR(50) DEFAULT 'Enrolled'
);

CREATE TABLE blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT,
    title VARCHAR(255),
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO courses (course_name, description) VALUES
('Web Development','HTML, CSS, JavaScript, PHP, MySQL'),
('Data Science','Python, Pandas, Machine Learning'),
('Cyber Security','Ethical hacking and security basics');

INSERT INTO blogs (course_id, title, content) VALUES
(1,'HTML Basics','Learn structure of web pages'),
(1,'PHP Introduction','Server-side scripting'),
(2,'What is Data Science?','Data analysis concepts');
