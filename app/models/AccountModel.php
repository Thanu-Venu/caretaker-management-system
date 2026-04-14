<?php
require_once APPROOT . '/core/Database.php';

class AccountModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public static function normalizeRole(?string $role): string
    {
        $value = strtolower(trim((string)$role));
        if ($value === 'hr' || $value === 'manager') {
            return 'manager';
        }
        if ($value === 'caregiver' || $value === 'care_taker') {
            return 'caretaker';
        }
        if ($value === 'administrator') {
            return 'admin';
        }
        return $value;
    }

    public static function toLegacyRole(string $normalizedRole): string
    {
        return $normalizedRole === 'manager' ? 'Manager' : $normalizedRole;
    }

    public function isAccountsReady(): bool
    {
        $result = $this->conn->query("SHOW TABLES LIKE 'accounts'");
        return $result && $result->num_rows > 0;
    }

    public function findByEmail(string $email)
    {
        if (!$this->isAccountsReady()) {
            return false;
        }

        $stmt = $this->conn->prepare("SELECT * FROM accounts WHERE email = ? LIMIT 1");
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: false;
    }

    /**
     * @return array|false Account row or false if missing / invalid id
     */
    public function findById(int $id)
    {
        if (!$this->isAccountsReady() || $id <= 0) {
            return false;
        }

        $stmt = $this->conn->prepare('SELECT * FROM accounts WHERE id = ? LIMIT 1');
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: false;
    }

    public function authenticate(string $email, string $password)
    {
        $account = $this->findByEmail($email);
        if (!$account) {
            return false;
        }

        $accountId = (int)($account['id'] ?? 0);
        if (!$this->verifyAndUpgradePassword($password, (string)($account['password'] ?? ''), $accountId)) {
            return false;
        }

        $status = strtolower((string)($account['status'] ?? 'active'));
        if ($status === 'inactive' || $status === 'disabled') {
            return false;
        }

        $account['role'] = self::normalizeRole($account['role'] ?? '');
        return $account;
    }

    private function verifyAndUpgradePassword(string $plainPassword, string $storedPassword, int $accountId): bool
    {
        // Standard path for bcrypt/argon hashes created by password_hash.
        if (password_verify($plainPassword, $storedPassword)) {
            if ($accountId > 0 && password_needs_rehash($storedPassword, PASSWORD_DEFAULT)) {
                $this->updatePasswordHash($accountId, password_hash($plainPassword, PASSWORD_DEFAULT));
            }
            return true;
        }

        // Compatibility path for old datasets imported with non-password_hash formats.
        $isLegacyMatch = ($storedPassword === $plainPassword)
            || (strlen($storedPassword) === 32 && hash_equals(strtolower($storedPassword), md5($plainPassword)))
            || (strlen($storedPassword) === 40 && hash_equals(strtolower($storedPassword), sha1($plainPassword)));

        if (!$isLegacyMatch) {
            return false;
        }

        if ($accountId > 0) {
            $this->updatePasswordHash($accountId, password_hash($plainPassword, PASSWORD_DEFAULT));
        }

        return true;
    }

    private function updatePasswordHash(int $accountId, string $passwordHash): void
    {
        $stmt = $this->conn->prepare("UPDATE accounts SET password = ? WHERE id = ? LIMIT 1");
        if (!$stmt) {
            return;
        }

        $stmt->bind_param("si", $passwordHash, $accountId);
        $stmt->execute();
        $stmt->close();
    }

    public function getProfileByAccount(int $accountId, string $role)
    {
        if (!$this->isAccountsReady()) {
            return false;
        }

        $table = null;
        if ($role === 'admin' || $role === 'manager') {
            $table = 'users';
        } elseif ($role === 'client') {
            $table = 'clients';
        } elseif ($role === 'caretaker') {
            $table = 'caretakers';
        }

        if (!$table) {
            return false;
        }

        $stmt = $this->conn->prepare("SELECT * FROM {$table} WHERE account_id = ? LIMIT 1");
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $accountId);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $profile ?: false;
    }

    public function createClientAccountAndProfile(array $data): bool
    {
        if (!$this->isAccountsReady()) {
            return false;
        }

        $name = trim((string)($data['name'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $phone = trim((string)($data['phone'] ?? ''));
        $password = (string)($data['password'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $this->conn->begin_transaction();

        try {
            $role = 'client';
            $status = 'Active';

            $stmt = $this->conn->prepare(
                "INSERT INTO accounts (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssss", $name, $email, $passwordHash, $role, $status);
            if (!$stmt->execute()) {
                throw new Exception('Failed to create account.');
            }
            $accountId = (int)$this->conn->insert_id;
            $stmt->close();

            $legacyRole = 'client';
            $stmt = $this->conn->prepare(
                "INSERT INTO clients (account_id, name, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("isssss", $accountId, $name, $email, $phone, $passwordHash, $legacyRole);
            if (!$stmt->execute()) {
                throw new Exception('Failed to create client profile.');
            }
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Throwable $e) {
            $this->conn->rollback();
            error_log('createClientAccountAndProfile error: ' . $e->getMessage());
            return false;
        }
    }
}
