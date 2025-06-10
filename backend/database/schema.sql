-- Tạo database
IF NOT EXISTS (SELECT * FROM sys.databases WHERE name = 'contest_db')
BEGIN
    CREATE DATABASE contest_db;
END
GO

USE contest_db;
GO

-- Tạo bảng users
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'users')
BEGIN
    CREATE TABLE users (
        id INT IDENTITY(1,1) PRIMARY KEY,
        username NVARCHAR(50) NOT NULL UNIQUE,
        email NVARCHAR(255) NOT NULL UNIQUE,
        password NVARCHAR(100) NOT NULL,
        full_name NVARCHAR(100),
        phone NVARCHAR(20),
        address NVARCHAR(255),
        role NVARCHAR(20) DEFAULT 'user',
        status NVARCHAR(20) DEFAULT 'active',
        created_at DATETIME DEFAULT GETDATE(),
        updated_at DATETIME DEFAULT GETDATE()
);
END
GO

-- Tạo bảng contests
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'contests')
BEGIN
    CREATE TABLE contests (
        id INT IDENTITY(1,1) PRIMARY KEY,
        title NVARCHAR(255) NOT NULL,
        description NVARCHAR(MAX),
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
        status NVARCHAR(20) DEFAULT 'draft',
        created_at DATETIME DEFAULT GETDATE(),
        updated_at DATETIME DEFAULT GETDATE(),
        created_by INT,
        CONSTRAINT FK_Contests_Users FOREIGN KEY (created_by) REFERENCES users(id)
);
END
GO

-- Tạo bảng contestants
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'contestants')
BEGIN
    CREATE TABLE contestants (
        id INT IDENTITY(1,1) PRIMARY KEY,
    contest_id INT NOT NULL,
        name NVARCHAR(255) NOT NULL,
        description NVARCHAR(MAX),
        image NVARCHAR(255),
        status NVARCHAR(20) DEFAULT 'pending',
        created_at DATETIME DEFAULT GETDATE(),
        updated_at DATETIME DEFAULT GETDATE(),
        CONSTRAINT FK_Contestants_Contests FOREIGN KEY (contest_id) REFERENCES contests(id),
        CONSTRAINT FK_Contestants_Users FOREIGN KEY (created_by) REFERENCES users(id)
);
END
GO

-- Tạo bảng votes
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'votes')
BEGIN
    CREATE TABLE votes (
        id INT IDENTITY(1,1) PRIMARY KEY,
        contest_id INT NOT NULL,
    contestant_id INT NOT NULL,
    user_id INT NOT NULL,
        created_at DATETIME DEFAULT GETDATE(),
        CONSTRAINT FK_Votes_Contests FOREIGN KEY (contest_id) REFERENCES contests(id),
        CONSTRAINT FK_Votes_Contestants FOREIGN KEY (contestant_id) REFERENCES contestants(id),
        CONSTRAINT FK_Votes_Users FOREIGN KEY (user_id) REFERENCES users(id)
);
END
GO

-- Tạo bảng settings
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'settings')
BEGIN
    CREATE TABLE settings (
        id INT IDENTITY(1,1) PRIMARY KEY,
        setting_key NVARCHAR(50) NOT NULL UNIQUE,
        value NVARCHAR(MAX),
        created_at DATETIME DEFAULT GETDATE(),
        updated_at DATETIME DEFAULT GETDATE()
);
END
GO

-- Tạo bảng notifications
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'notifications')
BEGIN
    CREATE TABLE notifications (
        id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
        message NVARCHAR(255) NOT NULL,
        is_read BIT DEFAULT 0,
        created_at DATETIME DEFAULT GETDATE(),
        CONSTRAINT FK_Notifications_Users FOREIGN KEY (user_id) REFERENCES users(id)
);
END
GO

-- Tạo bảng activity_logs
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'activity_logs')
BEGIN
    CREATE TABLE activity_logs (
        id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
        action NVARCHAR(50) NOT NULL,
        details NVARCHAR(MAX),
        created_at DATETIME DEFAULT GETDATE(),
        CONSTRAINT FK_ActivityLogs_Users FOREIGN KEY (user_id) REFERENCES users(id)
);
END
GO

-- Tạo indexes
CREATE INDEX IX_Users_Username ON users(username);
CREATE INDEX IX_Users_Email ON users(email);
CREATE INDEX IX_Contests_Status ON contests(status);
CREATE INDEX IX_Contestants_ContestId ON contestants(contest_id);
CREATE INDEX IX_Votes_ContestId ON votes(contest_id);
CREATE INDEX IX_Votes_ContestantId ON votes(contestant_id);
CREATE INDEX IX_Votes_UserId ON votes(user_id);
CREATE INDEX IX_Notifications_UserId ON notifications(user_id);
CREATE INDEX IX_ActivityLogs_UserId ON activity_logs(user_id);
GO