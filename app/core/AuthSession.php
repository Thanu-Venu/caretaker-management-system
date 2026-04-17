<?php
require_once APPROOT . '/models/AccountModel.php';

class AuthSession
{
    public static function normalizeRole(?string $role): string
    {
        return AccountModel::normalizeRole($role);
    }

    public static function legacyRole(string $normalizedRole): string
    {
        return AccountModel::toLegacyRole($normalizedRole);
    }

    public static function bootstrap(): void
    {
        if (!isset($_SESSION) || !is_array($_SESSION)) {
            return;
        }

        if (!empty($_SESSION['logged_in'])) {
            self::syncLegacyShape();
            return;
        }

        if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) {
            $roleRaw = $_SESSION['role'] ?? ($_SESSION['user']['role'] ?? '');
            $role = self::normalizeRole((string)$roleRaw);
            $_SESSION['account_id'] = isset($_SESSION['account_id'])
                ? (int)$_SESSION['account_id']
                : (int)($_SESSION['user']['account_id'] ?? 0);
            $_SESSION['profile_id'] = isset($_SESSION['profile_id'])
                ? (int)$_SESSION['profile_id']
                : (int)($_SESSION['user']['id'] ?? 0);
            $_SESSION['name'] = $_SESSION['name'] ?? ($_SESSION['user']['name'] ?? ($_SESSION['user']['username'] ?? ''));
            $_SESSION['email'] = $_SESSION['email'] ?? ($_SESSION['user']['email'] ?? '');
            $_SESSION['role'] = $role;
            // Ensure role is also set in the expected format for caretaker
            $_SESSION['user']['role'] = $role;
            $_SESSION['logged_in'] = true;

            self::syncLegacyShape();
        }
    }

    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['logged_in']) && (int)($_SESSION['account_id'] ?? 0) > 0;
    }

    public static function accountId(): int
    {
        return (int)($_SESSION['account_id'] ?? 0);
    }

    public static function profileId(): int
    {
        return (int)($_SESSION['profile_id'] ?? 0);
    }

    public static function role(): string
    {
        return self::normalizeRole((string)($_SESSION['role'] ?? ''));
    }

    public static function hasRole(string $role): bool
    {
        return self::role() === self::normalizeRole($role);
    }

    public static function name(): string
    {
        return (string)($_SESSION['name'] ?? ($_SESSION['user']['name'] ?? ($_SESSION['user']['username'] ?? '')));
    }

    public static function email(): string
    {
        return (string)($_SESSION['email'] ?? ($_SESSION['user']['email'] ?? ''));
    }

    public static function user(): array
    {
        return is_array($_SESSION['user'] ?? null) ? $_SESSION['user'] : [];
    }

    public static function requireLogin(string $loginUrl = ''): void
    {
        if (self::isLoggedIn()) {
            return;
        }

        $target = $loginUrl !== '' ? $loginUrl : (defined('URLROOT') ? URLROOT . '/auth/login' : 'index.php?url=auth/login');
        header('Location: ' . $target);
        exit;
    }

    public static function requireRole(string $role, string $loginUrl = ''): void
    {
        if (self::isLoggedIn() && self::hasRole($role)) {
            return;
        }

        $target = $loginUrl !== '' ? $loginUrl : (defined('URLROOT') ? URLROOT . '/auth/login' : 'index.php?url=auth/login');
        header('Location: ' . $target);
        exit;
    }

    public static function setAuthenticated(array $account, array $profile): void
    {
        $role = self::normalizeRole((string)($account['role'] ?? ''));
        $legacyRole = self::legacyRole($role);

        $_SESSION['account_id'] = (int)($account['id'] ?? 0);
        $_SESSION['profile_id'] = (int)($profile['id'] ?? 0);
        $_SESSION['role'] = $role;
        $_SESSION['legacy_role'] = $legacyRole;
        $_SESSION['name'] = (string)($account['name'] ?? ($profile['name'] ?? ($profile['username'] ?? '')));
        $_SESSION['email'] = (string)($account['email'] ?? ($profile['email'] ?? ''));
        $_SESSION['logged_in'] = true;

        self::syncLegacyShape($profile);
    }

    private static function syncLegacyShape(?array $profileOverride = null): void
    {
        $role = self::normalizeRole((string)($_SESSION['role'] ?? ''));
        $legacyRole = $_SESSION['legacy_role'] ?? self::legacyRole($role);
        $_SESSION['legacy_role'] = $legacyRole;

        $profile = $profileOverride;
        if (!$profile && !empty($_SESSION['user']) && is_array($_SESSION['user'])) {
            $profile = $_SESSION['user'];
        }
        if (!$profile) {
            $profile = [];
        }

        $profile['id'] = (int)($_SESSION['profile_id'] ?? ($profile['id'] ?? 0));
        $profile['account_id'] = (int)($_SESSION['account_id'] ?? ($profile['account_id'] ?? 0));
        $profile['role'] = $legacyRole;
        $profile['name'] = $_SESSION['name'] ?? ($profile['name'] ?? ($profile['username'] ?? ''));
        $profile['email'] = $_SESSION['email'] ?? ($profile['email'] ?? '');

        $_SESSION['user'] = $profile;
    }

    public static function refreshLegacyUser(array $profile): void
    {
        self::syncLegacyShape($profile);
    }
}
