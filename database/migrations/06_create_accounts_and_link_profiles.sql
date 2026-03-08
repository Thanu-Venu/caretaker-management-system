-- SmartCare migration: centralize authentication into accounts table
-- Date: 2026-03-08
-- Notes:
-- 1) Run this on a backup/staging DB first.
-- 2) Resolve duplicate emails before adding global unique constraints.
-- 3) This migration does not use triggers.

START TRANSACTION;

-- 1) Create unified authentication table
CREATE TABLE IF NOT EXISTS accounts (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'client', 'caretaker') NOT NULL,
    status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_accounts_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 2) Add account_id references to profile tables
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS account_id INT NULL AFTER id,
    ADD INDEX IF NOT EXISTS idx_users_account_id (account_id),
    ADD CONSTRAINT fk_users_account
        FOREIGN KEY (account_id) REFERENCES accounts(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE;

ALTER TABLE clients
    ADD COLUMN IF NOT EXISTS account_id INT NULL AFTER id,
    ADD INDEX IF NOT EXISTS idx_clients_account_id (account_id),
    ADD CONSTRAINT fk_clients_account
        FOREIGN KEY (account_id) REFERENCES accounts(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE;

ALTER TABLE caretakers
    ADD COLUMN IF NOT EXISTS account_id INT NULL AFTER id,
    ADD INDEX IF NOT EXISTS idx_caretakers_account_id (account_id),
    ADD CONSTRAINT fk_caretakers_account
        FOREIGN KEY (account_id) REFERENCES accounts(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE;

-- 3) Migrate USERS -> ACCOUNTS (admin/manager)
INSERT INTO accounts (name, email, password, role, status, created_at, updated_at)
SELECT
    u.username AS name,
    u.email,
    u.password,
    CASE
        WHEN LOWER(u.role) = 'manager' THEN 'manager'
        ELSE 'admin'
    END AS role,
    COALESCE(u.status, 'Active') AS status,
    COALESCE(u.created_at, NOW()) AS created_at,
    NOW() AS updated_at
FROM users u
LEFT JOIN accounts a ON a.email = u.email
WHERE a.id IS NULL;

UPDATE users u
JOIN accounts a ON a.email = u.email
SET u.account_id = a.id
WHERE u.account_id IS NULL;

-- 4) Migrate CLIENTS -> ACCOUNTS
INSERT INTO accounts (name, email, password, role, status, created_at, updated_at)
SELECT
    c.name,
    c.email,
    c.password,
    'client' AS role,
    'Active' AS status,
    COALESCE(c.created_at, NOW()) AS created_at,
    NOW() AS updated_at
FROM clients c
LEFT JOIN accounts a ON a.email = c.email
WHERE a.id IS NULL;

UPDATE clients c
JOIN accounts a ON a.email = c.email
SET c.account_id = a.id
WHERE c.account_id IS NULL;

-- 5) Migrate CARETAKERS -> ACCOUNTS
INSERT INTO accounts (name, email, password, role, status, created_at, updated_at)
SELECT
    ct.name,
    ct.email,
    ct.password,
    'caretaker' AS role,
    CASE
        WHEN ct.status = 'Inactive' THEN 'Inactive'
        ELSE 'Active'
    END AS status,
    COALESCE(ct.created_at, NOW()) AS created_at,
    NOW() AS updated_at
FROM caretakers ct
LEFT JOIN accounts a ON a.email = ct.email
WHERE a.id IS NULL;

UPDATE caretakers ct
JOIN accounts a ON a.email = ct.email
SET ct.account_id = a.id
WHERE ct.account_id IS NULL;

COMMIT;

-- 6) Verification queries (run manually after commit)
-- SELECT email, COUNT(*) c FROM accounts GROUP BY email HAVING c > 1;
-- SELECT COUNT(*) FROM users WHERE account_id IS NULL;
-- SELECT COUNT(*) FROM clients WHERE account_id IS NULL;
-- SELECT COUNT(*) FROM caretakers WHERE account_id IS NULL;
