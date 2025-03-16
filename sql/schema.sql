-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    status ENUM('pending', 'active', 'suspended', 'deleted') NOT NULL DEFAULT 'pending',
    verification_token VARCHAR(255),
    remember_token VARCHAR(255),
    timezone VARCHAR(100) DEFAULT 'UTC',
    email_notifications BOOLEAN DEFAULT TRUE,
    desktop_notifications BOOLEAN DEFAULT TRUE,
    subscription_plan VARCHAR(50) DEFAULT 'free',
    subscription_status VARCHAR(50),
    subscription_ends_at DATETIME,
    last_login DATETIME,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    deleted_at DATETIME
);

-- Social accounts table
CREATE TABLE social_accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    platform VARCHAR(50) NOT NULL,
    account_id VARCHAR(255) NOT NULL,
    account_name VARCHAR(255) NOT NULL,
    access_token TEXT NOT NULL,
    refresh_token TEXT,
    token_expires DATETIME,
    status ENUM('active', 'inactive', 'revoked') NOT NULL DEFAULT 'active',
    profile_url VARCHAR(255),
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_platform_account (platform, account_id)
);

-- Posts table
CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    media_urls JSON,
    scheduled_time DATETIME,
    status ENUM('draft', 'scheduled', 'published', 'failed') NOT NULL DEFAULT 'draft',
    approval_required BOOLEAN DEFAULT FALSE,
    approval_status ENUM('pending', 'approved', 'rejected'),
    approval_notes TEXT,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    published_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Post platforms table (for cross-posting)
CREATE TABLE post_platforms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    social_account_id INT NOT NULL,
    platform_post_id VARCHAR(255),
    status ENUM('pending', 'published', 'failed') NOT NULL DEFAULT 'pending',
    engagement_data JSON,
    error_message TEXT,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (social_account_id) REFERENCES social_accounts(id)
);

-- Analytics table
CREATE TABLE analytics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    social_account_id INT NOT NULL,
    date DATE NOT NULL,
    metric_type VARCHAR(50) NOT NULL,
    metric_value BIGINT NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (social_account_id) REFERENCES social_accounts(id),
    UNIQUE KEY unique_daily_metric (social_account_id, date, metric_type)
);

-- Password reset tokens table
CREATE TABLE password_reset_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Notifications table
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    action_url VARCHAR(255),
    action_text VARCHAR(50),
    read_at DATETIME,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Audit logs table
CREATE TABLE audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INT NOT NULL,
    metadata JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Contact messages table
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') NOT NULL DEFAULT 'new',
    created_at DATETIME NOT NULL,
    updated_at DATETIME
);

-- Blog posts table
CREATE TABLE blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    author_id INT NOT NULL,
    status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
    published_at DATETIME,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    FOREIGN KEY (author_id) REFERENCES users(id)
);

-- Create indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_social_accounts_user ON social_accounts(user_id);
CREATE INDEX idx_social_accounts_platform ON social_accounts(platform);
CREATE INDEX idx_posts_user ON posts(user_id);
CREATE INDEX idx_posts_status ON posts(status);
CREATE INDEX idx_posts_scheduled ON posts(scheduled_time);
CREATE INDEX idx_analytics_date ON analytics(date);
CREATE INDEX idx_analytics_metric ON analytics(metric_type);
CREATE INDEX idx_notifications_user ON notifications(user_id);
CREATE INDEX idx_audit_logs_user ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_action ON audit_logs(action);
CREATE INDEX idx_blog_posts_slug ON blog_posts(slug);
CREATE INDEX idx_blog_posts_status ON blog_posts(status);

-- Add triggers
DELIMITER //

-- Update timestamp trigger for users
CREATE TRIGGER before_user_update
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    SET NEW.updated_at = NOW();
END//

-- Update timestamp trigger for social_accounts
CREATE TRIGGER before_social_account_update
BEFORE UPDATE ON social_accounts
FOR EACH ROW
BEGIN
    SET NEW.updated_at = NOW();
END//

-- Update timestamp trigger for posts
CREATE TRIGGER before_post_update
BEFORE UPDATE ON posts
FOR EACH ROW
BEGIN
    SET NEW.updated_at = NOW();
    IF NEW.status = 'published' AND OLD.status != 'published' THEN
        SET NEW.published_at = NOW();
    END IF;
END//

-- Update timestamp trigger for post_platforms
CREATE TRIGGER before_post_platform_update
BEFORE UPDATE ON post_platforms
FOR EACH ROW
BEGIN
    SET NEW.updated_at = NOW();
END//

-- Update timestamp trigger for contact_messages
CREATE TRIGGER before_contact_message_update
BEFORE UPDATE ON contact_messages
FOR EACH ROW
BEGIN
    SET NEW.updated_at = NOW();
END//

-- Update timestamp trigger for blog_posts
CREATE TRIGGER before_blog_post_update
BEFORE UPDATE ON blog_posts
FOR EACH ROW
BEGIN
    SET NEW.updated_at = NOW();
    IF NEW.status = 'published' AND OLD.status != 'published' THEN
        SET NEW.published_at = NOW();
    END IF;
END//

DELIMITER ;
