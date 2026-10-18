-- Run this in phpMyAdmin after selecting the studenthub database.
-- Practical 9 already creates the users table. This is safe for a fresh setup.
CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'student',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME NULL
);

-- If the users table already exists from Practical 9, add last_login if needed.
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login DATETIME NULL;

CREATE TABLE IF NOT EXISTS auth_tokens (
    token_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_auth_tokens_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- After creating/registering your account, promote one test account to admin:
-- UPDATE users SET role = 'admin' WHERE email = 'admin@gmail.com';
-- All other accounts should remain role = 'student'.
