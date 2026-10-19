-- Run this ONCE in phpMyAdmin (database: student_system) on your existing data.
ALTER TABLE users ADD COLUMN IF NOT EXISTS full_name VARCHAR(100) DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT NULL;

-- Subjects a lecturer teaches. Students are matched automatically by program.
CREATE TABLE IF NOT EXISTS courses (
  id INT(11) NOT NULL AUTO_INCREMENT,
  lecturer_id INT(11) NOT NULL,
  course_name VARCHAR(100) NOT NULL,
  program VARCHAR(100) NOT NULL,
  credits INT(11) NOT NULL DEFAULT 3,
  created_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY uniq_course (lecturer_id, course_name, program)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE results ADD COLUMN IF NOT EXISTS course_id INT(11) DEFAULT NULL;
ALTER TABLE results ADD UNIQUE KEY IF NOT EXISTS uniq_result (student_id, course_id);

-- Old manual assignment is no longer used
DROP TABLE IF EXISTS lecturer_students;

-- Student profile contact details
ALTER TABLE students ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT NULL;
ALTER TABLE students ADD COLUMN IF NOT EXISTS email VARCHAR(100) DEFAULT NULL;
