-- Migration: 001_initial_schema.sql
-- Description: Initial database schema creation
-- Created: 2024-03-20

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS contest_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE contest_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    fullname VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    avatar VARCHAR(255),
    role ENUM('admin', 'user', 'moderator') DEFAULT 'user',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contests table
CREATE TABLE IF NOT EXISTS contests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('draft', 'active', 'ended', 'cancelled') DEFAULT 'draft',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Contestants table
CREATE TABLE IF NOT EXISTS contestants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contest_id INT NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    video VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (contest_id) REFERENCES contests(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Votes table
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contestant_id INT NOT NULL,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contestant_id) REFERENCES contestants(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(50) NOT NULL UNIQUE,
    value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    type VARCHAR(50),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Activity logs table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    description TEXT,
    data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create indexes
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(status);

CREATE INDEX idx_contests_status ON contests(status);
CREATE INDEX idx_contests_dates ON contests(start_date, end_date);

CREATE INDEX idx_contestants_contest ON contestants(contest_id);
CREATE INDEX idx_contestants_user ON contestants(user_id);
CREATE INDEX idx_contestants_status ON contestants(status);

CREATE INDEX idx_votes_contestant ON votes(contestant_id);
CREATE INDEX idx_votes_user ON votes(user_id);
CREATE INDEX idx_votes_date ON votes(created_at);

CREATE INDEX idx_notifications_user ON notifications(user_id);
CREATE INDEX idx_notifications_read ON notifications(is_read);

CREATE INDEX idx_activity_logs_user ON activity_logs(user_id);
CREATE INDEX idx_activity_logs_type ON activity_logs(type);
CREATE INDEX idx_activity_logs_date ON activity_logs(created_at);