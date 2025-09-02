-- Book Bytes Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS book_bytes_db;
USE book_bytes_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    reset_token VARCHAR(255) NULL,
    reset_expires DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Books table
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Book sections table
CREATE TABLE book_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    section_title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    order_num INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- Takeaways table
CREATE TABLE takeaways (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    takeaway_text TEXT NOT NULL,
    example_text TEXT NULL,
    order_num INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (section_id) REFERENCES book_sections(id) ON DELETE CASCADE
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@bookbytes.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample books
INSERT INTO books (title, author, description, image) VALUES 
('The Habit of Winning', 'Prakash Iyer', 'A powerful guide to developing winning habits and achieving success in life and career.', 'habit-of-winning.jpg'),
('Atomic Habits', 'James Clear', 'An easy and proven way to build good habits and break bad ones through small changes.', 'atomic-habits.jpg'),
('Think and Grow Rich', 'Napoleon Hill', 'The classic guide to wealth and success, revealing the secrets of achievement.', 'think-grow-rich.jpg'),
('The 7 Habits of Highly Effective People', 'Stephen Covey', 'A holistic approach for solving personal and professional problems.', '7-habits.jpg'),
('Rich Dad Poor Dad', 'Robert Kiyosaki', 'What the rich teach their kids about money that the poor and middle class do not.', 'rich-dad-poor-dad.jpg');

-- Insert sample sections for "The Habit of Winning"
INSERT INTO book_sections (book_id, section_title, content, order_num) VALUES 
(1, 'Core Principles for Success', 'Success is not just about talent or luck. It is about developing the right habits and mindset that lead to consistent winning outcomes.', 1),
(1, 'Vision and Goals', 'Having a clear vision and well-defined goals is the foundation of any successful journey. Without direction, effort becomes meaningless.', 2),
(1, 'Self-Belief', 'Believing in yourself is the first step towards achieving greatness. Self-doubt is the biggest enemy of success.', 3);

-- Insert sample takeaways
INSERT INTO takeaways (section_id, takeaway_text, example_text, order_num) VALUES 
(1, 'The Power of a Clear Goal', 'The book compares a clear goal to a mountain you want to climb. Without a specific mountain in mind, you are just wandering. For instance, a clear goal is not just I want to be rich, but I will earn a seven-figure income by building a successful e-commerce business in five years. This specificity gives you a definite path.', 1),
(1, 'Written Goals and Success', 'A Harvard Business School study is cited to highlight the power of writing goals down. The 3% of students who wrote their goals down had a net worth greater than the other 97% combined 25 years later. This illustrates that the act of writing a goal makes it more concrete and easier to track.', 2),
(2, 'Focus on One Goal', 'Iyer uses the analogy of chasing a white rabbit to explain the importance of focus. If you try to catch too many rabbits at once, you will end up catching none. The lesson is to dedicate all your energy and resources to one primary goal until it is achieved.', 1),
(3, 'Breaking Mental Barriers', 'The story of Roger Bannister is a prime example. For decades, the four-minute mile was considered physically impossible. However, when Bannister broke the barrier, many others followed within a short period. This shows that the true obstacle was not physical, but a mental one that needed to be shattered.', 1),
(3, 'Leveraging Your Strengths', 'The book features the story of Tyrone Muggsy Bogues, the shortest NBA player ever. Instead of trying to become a taller player, he focused on his unique strengths: speed, agility, and exceptional ball-handling skills. He used his unique attributes to thrive in a sport dominated by giants.', 2);
