<?php
require_once APPROOT . '/core/Database.php';
require_once APPROOT . '/core/PasswordPolicy.php';
require_once APPROOT . '/models/AccountModel.php';

class UserModel
{
    private $conn;
    private $accountLinkChecked = false;
    private $accountLinkEnabled = false;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn; // mysqli object
    }

    private function hasAccountLinking(): bool
    {
        if ($this->accountLinkChecked) {
            return $this->accountLinkEnabled;
        }

        $tables = $this->conn->query("SHOW TABLES LIKE 'accounts'");
        if (!$tables || $tables->num_rows === 0) {
            $this->accountLinkChecked = true;
            $this->accountLinkEnabled = false;
            return false;
        }

        $cols = $this->conn->query("SHOW COLUMNS FROM users LIKE 'account_id'");
        $this->accountLinkChecked = true;
        $this->accountLinkEnabled = (bool)($cols && $cols->num_rows > 0);
        return $this->accountLinkEnabled;
    }

    /**
     * Staff password policy (aligned with admin-form-validation passwordStrong).
     *
     * @return string|null Error message or null if valid.
     */
    public static function validatePasswordPolicy(string $password): ?string
    {
        return PasswordPolicy::validateStrong($password);
    }

    // 🔹 Get all users
    public function getAllUsers()
    {
        $result = $this->conn->query("SELECT * FROM users ORDER BY id DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * WHERE clause for staff list filters (status + role).
     *
     * @return array{0: string, 1: string, 2: list<string>}
     */
    private function usersListWhere(array $filters): array
    {
        $parts = ['1=1'];
        $types = '';
        $params = [];

        $status = trim((string)($filters['status'] ?? ''));
        if ($status === 'Active' || $status === 'Inactive') {
            $parts[] = 'status = ?';
            $types .= 's';
            $params[] = $status;
        }

        $role = trim((string)($filters['role'] ?? ''));
        if ($role === 'Admin' || $role === 'Manager') {
            $parts[] = 'LOWER(TRIM(role)) = ?';
            $types .= 's';
            $params[] = strtolower($role);
        }

        return [implode(' AND ', $parts), $types, $params];
    }

    private function bindUserListParams(\mysqli_stmt $stmt, string $types, array $params): bool
    {
        if ($types === '' || $params === []) {
            return true;
        }
        $refs = [$types];
        foreach ($params as $key => $value) {
            $refs[] = &$params[$key];
        }

        return call_user_func_array([$stmt, 'bind_param'], $refs);
    }

    public function countUsersFiltered(array $filters): int
    {
        [$where, $types, $params] = $this->usersListWhere($filters);
        $sql = 'SELECT COUNT(*) AS cnt FROM users WHERE ' . $where;
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return 0;
        }
        if (!$this->bindUserListParams($stmt, $types, $params)) {
            $stmt->close();

            return 0;
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int)($row['cnt'] ?? 0);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getUsersFilteredPaged(array $filters, int $limit, int $offset): array
    {
        $limit = max(1, min(100, $limit));
        $offset = max(0, $offset);
        [$where, $types, $params] = $this->usersListWhere($filters);
        $sql = 'SELECT * FROM users WHERE ' . $where . ' ORDER BY id DESC LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }
        if (!$this->bindUserListParams($stmt, $types, $params)) {
            $stmt->close();

            return [];
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows ?: [];
    }

    // 🔹 Add user
    public function addUser($data)
    {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        if (!$this->hasAccountLinking()) {
            $phone = $data['phone'] ?? '';
            $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role, status, phone) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $data['username'], $data['email'], $hashedPassword, $data['role'], $data['status'], $phone);
            return $stmt->execute();
        }

        $this->conn->begin_transaction();
        try {
            $roleNormalized = AccountModel::normalizeRole($data['role'] ?? '');
            $roleLegacy = AccountModel::toLegacyRole($roleNormalized);

            $name = $data['username'];
            $email = $data['email'];
            $status = $data['status'] ?? 'Active';

            $stmt = $this->conn->prepare(
                "INSERT INTO accounts (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssss", $name, $email, $hashedPassword, $roleNormalized, $status);
            if (!$stmt->execute()) {
                throw new Exception('Failed to create account for user');
            }
            $accountId = (int)$this->conn->insert_id;
            $stmt->close();

            $phone = $data['phone'] ?? '';
            $stmt = $this->conn->prepare(
                "INSERT INTO users (account_id, username, email, password, role, status, phone) VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("issssss", $accountId, $name, $email, $hashedPassword, $roleLegacy, $status, $phone);
            if (!$stmt->execute()) {
                throw new Exception('Failed to create user profile');
            }
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Throwable $e) {
            $this->conn->rollback();
            error_log('UserModel::addUser transaction failed: ' . $e->getMessage());
            return false;
        }
    }

    // 🔹 Get user by ID
    public function getUserById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Public display name for headers / logs: prefers linked accounts.name (full name),
     * then users.name if present, else username. Staff rows often only have username.
     */
    public function withDisplayNameForProfile(array $user): array
    {
        $out     = $user;
        $display = trim((string) ($user['name'] ?? ''));
        if ($display === '') {
            $display = trim((string) ($user['username'] ?? ''));
        }

        $accountId = (int) ($user['account_id'] ?? 0);
        if ($accountId > 0) {
            $accModel = new AccountModel();
            if ($accModel->isAccountsReady()) {
                $acc = $accModel->findById($accountId);
                if (is_array($acc)) {
                    $accountName = trim((string) ($acc['name'] ?? ''));
                    if ($accountName !== '') {
                        $display = $accountName;
                    }
                }
            }
        }

        $out['name'] = $display;

        return $out;
    }

    // 🔹 Update user
    public function updateUser($id, $data)
    {
        $newPassword = trim((string) ($data['new_password'] ?? ''));
        $passwordHash = $newPassword !== '' ? password_hash($newPassword, PASSWORD_DEFAULT) : null;

        if (!$this->hasAccountLinking()) {
            $phone = $data['phone'] ?? '';
            if ($passwordHash !== null) {
                $stmt = $this->conn->prepare("UPDATE users SET username=?, email=?, role=?, status=?, phone=?, password=? WHERE id=?");
                $stmt->bind_param("ssssssi", $data['username'], $data['email'], $data['role'], $data['status'], $phone, $passwordHash, $id);
            } else {
                $stmt = $this->conn->prepare("UPDATE users SET username=?, email=?, role=?, status=?, phone=? WHERE id=?");
                $stmt->bind_param("sssssi", $data['username'], $data['email'], $data['role'], $data['status'], $phone, $id);
            }

            return $stmt->execute();
        }

        $current = $this->getUserById($id);
        if (!$current) {
            return false;
        }

        $this->conn->begin_transaction();
        try {
            $roleNormalized = AccountModel::normalizeRole($data['role'] ?? '');
            $roleLegacy = AccountModel::toLegacyRole($roleNormalized);

            $phone = $data['phone'] ?? '';
            if ($passwordHash !== null) {
                $stmt = $this->conn->prepare("UPDATE users SET username=?, email=?, role=?, status=?, phone=?, password=? WHERE id=?");
                $stmt->bind_param("ssssssi", $data['username'], $data['email'], $roleLegacy, $data['status'], $phone, $passwordHash, $id);
            } else {
                $stmt = $this->conn->prepare("UPDATE users SET username=?, email=?, role=?, status=?, phone=? WHERE id=?");
                $stmt->bind_param("sssssi", $data['username'], $data['email'], $roleLegacy, $data['status'], $phone, $id);
            }
            if (!$stmt->execute()) {
                throw new Exception('Failed to update user profile');
            }
            $stmt->close();

            $accountId = (int)($current['account_id'] ?? 0);
            if ($accountId > 0) {
                $stmt = $this->conn->prepare("UPDATE accounts SET name=?, email=?, role=?, status=? WHERE id=?");
                $stmt->bind_param("ssssi", $data['username'], $data['email'], $roleNormalized, $data['status'], $accountId);
                if (!$stmt->execute()) {
                    throw new Exception('Failed to update account');
                }
                $stmt->close();
                if ($passwordHash !== null) {
                    $stmt = $this->conn->prepare("UPDATE accounts SET password=? WHERE id=?");
                    $stmt->bind_param("si", $passwordHash, $accountId);
                    if (!$stmt->execute()) {
                        throw new Exception('Failed to update account password');
                    }
                    $stmt->close();
                }
            }

            $this->conn->commit();
            return true;
        } catch (Throwable $e) {
            $this->conn->rollback();
            error_log('UserModel::updateUser transaction failed: ' . $e->getMessage());
            return false;
        }
    }

    // 🔹 Delete user
    public function deleteUser($id)
    {
        if (!$this->hasAccountLinking()) {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        }

        $user = $this->getUserById($id);
        if (!$user) {
            return false;
        }

        $this->conn->begin_transaction();
        try {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete user profile');
            }
            $stmt->close();

            $accountId = (int)($user['account_id'] ?? 0);
            if ($accountId > 0) {
                $stmt = $this->conn->prepare("DELETE FROM accounts WHERE id=?");
                $stmt->bind_param("i", $accountId);
                if (!$stmt->execute()) {
                    throw new Exception('Failed to delete account');
                }
                $stmt->close();
            }

            $this->conn->commit();
            return true;
        } catch (Throwable $e) {
            $this->conn->rollback();
            error_log('UserModel::deleteUser transaction failed: ' . $e->getMessage());
            return false;
        }
    }

    public function login($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    public function updateUserProfile($id, $data)
    {
        $profile_pic = $data['profile_pic'] ?? null;
        $stmt = $this->conn->prepare("UPDATE users SET username=?, phone=?, profile_pic=COALESCE(?, profile_pic) WHERE id=?");
        $stmt->bind_param("sssi", $data['username'], $data['phone'], $profile_pic, $id);
        $ok = $stmt->execute();

        if ($ok && $this->hasAccountLinking()) {
            $user = $this->getUserById($id);
            $accountId = (int)($user['account_id'] ?? 0);
            if ($accountId > 0) {
                $name = $data['username'];
                $email = $user['email'];
                $sync = $this->conn->prepare("UPDATE accounts SET name=?, email=? WHERE id=?");
                $sync->bind_param("ssi", $name, $email, $accountId);
                $sync->execute();
                $sync->close();
            }
        }

        return $ok;
    }

    public function updatePassword($id, $hashedPassword)
    {
        $stmt = $this->conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashedPassword, $id);
        $ok = $stmt->execute();

        if ($ok && $this->hasAccountLinking()) {
            $user = $this->getUserById($id);
            $accountId = (int)($user['account_id'] ?? 0);
            if ($accountId > 0) {
                $sync = $this->conn->prepare("UPDATE accounts SET password=? WHERE id=?");
                $sync->bind_param("si", $hashedPassword, $accountId);
                $sync->execute();
                $sync->close();
            }
        }

        return $ok;
    }

    public function getUserByRole($role)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE role=?");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
