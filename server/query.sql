-- Database schema for 1XD3-Final-Project
-- Tables: users, tasks, schedules, achievements

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tasks table
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    due_date TIMESTAMP NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Schedules table
CREATE TABLE schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Achievements table
CREATE TABLE achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(250),
    condition_type VARCHAR(50) NOT NULL,
    condition_value INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Sample data for testing

-- Users sample data (password for all is "password")
INSERT INTO users (username, email, password) VALUES 
('testuser', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('johndoe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('janedoe', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Tasks sample data
INSERT INTO tasks (user_id, title, description, status, due_date) VALUES
(1, 'Team Meeting', 'Weekly team sync', 'pending', '2023-11-15 10:00:00'),
(1, 'Project Deadline', 'Submit final project', 'in-progress', '2023-11-15 16:00:00'),
(1, 'Dentist Appointment', 'Regular checkup', 'completed', '2023-11-16 12:00:00'),
(2, 'Client Call', 'Discuss project requirements', 'pending', '2023-11-17 14:00:00'),
(2, 'Gym Session', 'Workout at the gym', 'pending', '2023-11-15 18:00:00'),
(3, 'Grocery Shopping', 'Buy weekly groceries', 'pending', '2023-11-18 10:00:00');

-- Schedules sample data
INSERT INTO schedules (task_id, start_time, end_time) VALUES
(1, '2023-11-15 09:00:00', '2023-11-15 10:00:00'),
(2, '2023-11-15 14:00:00', '2023-11-15 16:00:00'),
(3, '2023-11-16 11:00:00', '2023-11-16 12:00:00'),
(4, '2023-11-17 13:30:00', '2023-11-17 14:30:00'),
(5, '2023-11-15 17:30:00', '2023-11-15 19:00:00'),
(6, '2023-11-18 09:30:00', '2023-11-18 11:00:00');

-- Achievements sample data
INSERT INTO achievements (name, description, condition_type, condition_value) VALUES
('Early Bird', 'Complete 5 tasks before 9 AM', 'morning_tasks', 5),
('Night Owl', 'Complete 5 tasks after 8 PM', 'evening_tasks', 5),
('Task Master', 'Complete 20 tasks in total', 'total_tasks', 20),
('Perfect Week', 'Complete all scheduled tasks in a week', 'weekly_completion', 1),
('Marathon', 'Complete tasks for 30 consecutive days', 'streak_days', 30);