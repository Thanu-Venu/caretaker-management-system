<?php
require_once APPROOT . '/core/Database.php';
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

    // 🔹 Get all users
    public function getAllUsers()
    {
        $result = $this->conn->query("SELECT * FROM users ORDER BY id DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // 🔹 Add user
    public function addUser($data)
    {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        if (!$this->hasAccountLinking()) {
            $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $data['username'], $data['email'], $hashedPassword, $data['role'], $data['status']);
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

    // 🔹 Update user
    public function updateUser($id, $data)
    {
        if (!$this->hasAccountLinking()) {
            $stmt = $this->conn->prepare("UPDATE users SET username=?, email=?, role=?, status=? WHERE id=?");
            $stmt->bind_param("ssssi", $data['username'], $data['email'], $data['role'], $data['status'], $id);
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

            $stmt = $this->conn->prepare("UPDATE users SET username=?, email=?, role=?, status=? WHERE id=?");
            $stmt->bind_param("ssssi", $data['username'], $data['email'], $roleLegacy, $data['status'], $id);
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
    // In UserModel
    public function updateUserProfile($id, $data)
    {
        $stmt = $this->conn->prepare("UPDATE users SET username=?, phone=?, profile_pic=COALESCE(?, profile_pic) WHERE id=?");
        $stmt->bind_param("sssi", $data['username'], $data['phone'], $data['profile_pic'], $id);
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
