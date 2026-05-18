<?php
class UserModel
{
    public function findByEmail(string $email): ?array
    {
        return Database::fetchOne(
            'SELECT * FROM users WHERE email = ? AND active = 1',
            [$email]
        );
    }

    public function findById(int $id): ?array
    {
        return Database::fetchOne('SELECT id, name, email, role FROM users WHERE id = ?', [$id]);
    }

    public function updateLastLogin(int $id): void
    {
        Database::query('UPDATE users SET last_login = NOW() WHERE id = ?', [$id]);
    }

    public function create(string $name, string $email, string $password, string $role = 'manager'): int
    {
        Database::query(
            'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)',
            [$name, $email, password_hash($password, PASSWORD_BCRYPT), $role]
        );
        return (int)Database::lastInsertId();
    }

    public function updatePassword(int $id, string $newPassword): void
    {
        Database::query(
            'UPDATE users SET password = ? WHERE id = ?',
            [password_hash($newPassword, PASSWORD_BCRYPT), $id]
        );
    }
}
