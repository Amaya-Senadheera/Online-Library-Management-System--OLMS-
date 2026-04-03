-- =====================================================================================
-- DATABASE SETUP & SAFETY CLEAR
-- =====================================================================================

DROP DATABASE IF EXISTS `olms`;
CREATE DATABASE `olms`;
USE `olms`;

-- --------------------------------------------------------
-- OLMS Master Database Structure
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- 1. Create the `users` table
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','member') DEFAULT 'member',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inject a default Admin user so the admin team can log in.
-- Note: The password hash below is for the word: password123
INSERT INTO `users` (`username`, `email`, `password`, `role`) VALUES
('admin', 'admin@library.com', '$2y$10$ctE9miG4sQ/V4g.YGL23xubgk5qzPs7LVGmw74IMSIPL4T/gcp586', 'admin');

-- ALL users below now have the password: password123
-- ID mapping: 2=Admin_Team, 3=Minu, 4=Thiseni, 5=Amaya, 6=Rakesh, 7=Shehara, 8=Test
INSERT INTO users (username, email, password, role) VALUES
('Minu', 'minu@olms.com', '$2y$10$ctE9miG4sQ/V4g.YGL23xubgk5qzPs7LVGmw74IMSIPL4T/gcp586', 'member'),
('Thiseni', 'thiseni@olms.com', '$2y$10$ctE9miG4sQ/V4g.YGL23xubgk5qzPs7LVGmw74IMSIPL4T/gcp586', 'member'),
('Amaya', 'amaya@olms.com', '$2y$10$ctE9miG4sQ/V4g.YGL23xubgk5qzPs7LVGmw74IMSIPL4T/gcp586', 'member'),
('Rakesh', 'rakesh@olms.com', '$2y$10$ctE9miG4sQ/V4g.YGL23xubgk5qzPs7LVGmw74IMSIPL4T/gcp586', 'member'),
('Shehara', 'shehara@olms.com', '$2y$10$ctE9miG4sQ/V4g.YGL23xubgk5qzPs7LVGmw74IMSIPL4T/gcp586', 'member'),
('Test', 'test@olms.com', '$2y$10$ctE9miG4sQ/V4g.YGL23xubgk5qzPs7LVGmw74IMSIPL4T/gcp586', 'member');


-- 2. Create the `books` table
CREATE TABLE `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT 'default-cover.jpg',
  `total_qty` int(11) NOT NULL DEFAULT 0,
  `available_qty` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Note: available_qty has been adjusted down to account for the 'active' transactions below!
INSERT INTO books (title, author, category, cover_image, total_qty, available_qty) VALUES
('To Kill a Mockingbird', 'Harper Lee', 'Fiction', 'To Kill a Mockingbird.jpg', 5, 4),
('1984', 'George Orwell', 'Dystopian Fiction', '1984.jpg', 4, 3),
('The Great Gatsby', 'F. Scott Fitzgerald', 'Classic Fiction', 'The Great Gatsby.jpg', 4, 3),
('A Brief History of Time', 'Stephen Hawking', 'Science', 'A Brief History of Time.jpg', 3, 2),
('The Diary of a Young Girl', 'Anne Frank', 'Biography', 'The Diary of a Young Girl.jpg', 3, 3);


-- 3. Create the `transactions` table (For borrows and returns)
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `borrow_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `due_date` datetime DEFAULT NULL,
  `returned_date` datetime DEFAULT NULL,
  `status` enum('active','returned') DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dummy Data for Transactions (Testing history and active reads)
INSERT INTO transactions (user_id, book_id, borrow_date, due_date, returned_date, status) VALUES
-- User 8 (Test) History
(8, 1, '2026-03-01 10:00:00', '2026-03-15 10:00:00', '2026-03-12 14:30:00', 'returned'),
(8, 2, '2026-03-10 11:15:00', '2026-03-24 11:15:00', '2026-03-20 09:45:00', 'returned'),
(8, 5, '2026-03-18 09:20:00', '2026-04-01 09:20:00', '2026-03-30 16:10:00', 'returned'),
-- User 8 (Test) Active Reads
(8, 3, '2026-04-01 09:00:00', '2026-04-15 09:00:00', NULL, 'active'),
(8, 4, '2026-04-03 14:20:00', '2026-04-17 14:20:00', NULL, 'active'),
-- User 4 (Thiseni) Active Read
(4, 1, '2026-03-28 16:00:00', '2026-04-11 16:00:00', NULL, 'active'),
-- User 5 (Amaya) History
(5, 5, '2026-02-10 08:30:00', '2026-02-24 08:30:00', '2026-02-22 11:00:00', 'returned'),
-- User 6 (Rakesh) Active Read
(6, 2, '2026-04-02 13:00:00', '2026-04-16 13:00:00', NULL, 'active');


-- 4. Create the `reviews` table
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT 5,
  `comment` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dummy Data for Reviews (Fleshing out the community feel)
INSERT INTO reviews (book_id, user_id, rating, comment, created_at) VALUES
(1, 8, 5, 'An absolute masterpiece. The themes of justice and innocence are handled beautifully. Highly recommend!', '2026-03-13 09:00:00'),
(2, 8, 4, 'Really thought-provoking and slightly terrifying. It makes you look at society differently.', '2026-03-21 10:30:00'),
(5, 5, 5, 'Such a powerful and emotional read. It is heartbreaking but essential for everyone to read.', '2026-02-23 15:45:00'),
(1, 6, 4, 'Great classic. The pacing is a little slow at times, but the payoff is worth it.', '2026-03-15 11:20:00'),
(3, 7, 5, 'The imagery and themes of the roaring twenties are perfectly woven together. A tragic romance.', '2026-04-02 08:15:00'),
(5, 8, 5, 'A sobering reminder of history. Read it in one sitting.', '2026-03-31 09:12:00');

COMMIT;