-- Resetting the users table for a fresh start
TRUNCATE TABLE users;

-- ALL users below now have the password: password123
INSERT INTO users (username, email, password, role) VALUES
('Admin', 'admin@olms.com', '$2y$10$ctE9miG4sQ/V4g.YGL23xubgk5qzPs7LVGmw74IMSIPL4T/gcp586', 'admin'),
('Minu', 'minu@olms.com', '$2y$10$ctE9miG4sQ/V4g.YGL23xubgk5qzPs7LVGmw74IMSIPL4T/gcp586', 'member'),
('Thiseni', 'thiseni@olms.com', '$2y$10$ctE9miG4sQ/V4g.YGL23xubgk5qzPs7LVGmw74IMSIPL4T/gcp586', 'member'),
('Amaya', 'amaya@olms.com', '$2y$10$ctE9miG4sQ/V4g.YGL23xubgk5qzPs7LVGmw74IMSIPL4T/gcp586', 'member'),
('Rakesh', 'rakesh@olms.com', '$2y$10$ctE9miG4sQ/V4g.YGL23xubgk5qzPs7LVGmw74IMSIPL4T/gcp586', 'member'),
('Shehara', 'shehara@olms.com', '$2y$10$ctE9miG4sQ/V4g.YGL23xubgk5qzPs7LVGmw74IMSIPL4T/gcp586', 'member'),
('Test', 'test@olms.com', '$2y$10$ctE9miG4sQ/V4g.YGL23xubgk5qzPs7LVGmw74IMSIPL4T/gcp586', 'member');