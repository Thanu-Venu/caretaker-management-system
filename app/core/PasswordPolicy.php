<?php

/**
 * Strong password rules for registration and password changes.
 * Do not use on login — legacy accounts may not meet this policy.
 */
class PasswordPolicy
{
    public const MIN_LENGTH = 8;

    public static function validateStrong(string $password): ?string
    {
        if (strlen($password) < self::MIN_LENGTH) {
            return 'Password must be at least 8 characters long.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return 'Password must include at least one uppercase letter.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            return 'Password must include at least one lowercase letter.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            return 'Password must include at least one number.';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return 'Password must include at least one special character.';
        }

        return null;
    }

    public static function formRequirementTitle(): string
    {
        return 'Password must be at least 8 characters and include an uppercase letter, a lowercase letter, a number, and a special character.';
    }
}
