-- Migration: 001_initial_schema.sql
-- Description: Initial database schema creation for SQL Server
-- Converted from MySQL to SQL Server

-- Create database
IF DB_ID('contest_db') IS NULL
    CREATE DATABASE contest_db;
GO
USE contest_db;
GO

-- Users table
IF OBJECT_ID('users', 'U') IS NOT NULL DROP TABLE users;
CREATE TABLE users (
    id INT IDENTITY(1,1) PRIMARY KEY,
    username NVARCHAR(50) NOT NULL UNIQUE,
    password NVARCHAR(255) NOT NULL,
    email NVARCHAR(100) NOT NULL UNIQUE,
    fullname NVARCHAR(100),
    phone NVARCHAR(20),
    address NVARCHAR(MAX),
    avatar NVARCHAR(255),
    role NVARCHAR(20) DEFAULT 'user',
    status NVARCHAR(20) DEFAULT 'active',
    last_login DATETIME,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE()
);
GO

-- Contests table
IF OBJECT_ID('contests', 'U') IS NOT NULL DROP TABLE contests;
CREATE TABLE contests (
    id INT IDENTITY(1,1) PRIMARY KEY,
    title NVARCHAR(255) NOT NULL,
    description NVARCHAR(MAX),
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status NVARCHAR(20) DEFAULT 'draft',
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
GO

-- Contestants table
IF OBJECT_ID('contestants', 'U') IS NOT NULL DROP TABLE contestants;
CREATE TABLE contestants (
    id INT IDENTITY(1,1) PRIMARY KEY,
    contest_id INT NOT NULL,
    user_id INT NOT NULL,
    title NVARCHAR(255) NOT NULL,
    description NVARCHAR(MAX),
    image NVARCHAR(255),
    video NVARCHAR(255),
    status NVARCHAR(20) DEFAULT 'pending',
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (contest_id) REFERENCES contests(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
GO

-- Votes table
IF OBJECT_ID('votes', 'U') IS NOT NULL DROP TABLE votes;
CREATE TABLE votes (
    id INT IDENTITY(1,1) PRIMARY KEY,
    contestant_id INT NOT NULL,
    user_id INT NOT NULL,
    ip_address NVARCHAR(45),
    created_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (contestant_id) REFERENCES contestants(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
GO

-- Settings table
IF OBJECT_ID('settings', 'U') IS NOT NULL DROP TABLE settings;
CREATE TABLE settings (
    id INT IDENTITY(1,1) PRIMARY KEY,
    key_name NVARCHAR(50) NOT NULL UNIQUE,
    value NVARCHAR(MAX),
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE()
);
GO

-- Notifications table
IF OBJECT_ID('notifications', 'U') IS NOT NULL DROP TABLE notifications;
CREATE TABLE notifications (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    title NVARCHAR(255) NOT NULL,
    message NVARCHAR(MAX),
    type NVARCHAR(50),
    is_read BIT DEFAULT 0,
    created_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
GO

-- Activity logs table
IF OBJECT_ID('activity_logs', 'U') IS NOT NULL DROP TABLE activity_logs;
CREATE TABLE activity_logs (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    type NVARCHAR(50) NOT NULL,
    description NVARCHAR(MAX),
    data NVARCHAR(MAX),
    created_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
GO

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
GO