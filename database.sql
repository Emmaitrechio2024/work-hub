CREATE DATABASE gig_marketplace;

USE gig_marketplace;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(100) NOT NULL,
  role ENUM('client', 'solutionist') NOT NULL
);

CREATE TABLE gigs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  budget DECIMAL(10,2),
  status VARCHAR(50) DEFAULT 'open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
assigned_solutionist INT DEFAULT NULL,
  FOREIGN KEY (client_id) REFERENCES users(id)
);

CREATE TABLE applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  gig_id INT NOT NULL,
  solutionist_id INT NOT NULL,
  message TEXT,
  is_selected BOOLEAN DEFAULT FALSE,
  applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (gig_id) REFERENCES gigs(id),
  FOREIGN KEY (solutionist_id) REFERENCES users(id)
);
