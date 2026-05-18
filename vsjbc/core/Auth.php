<?php
class Auth
{
    public static function login(int $id, string $name, string $email, string $role): void
    {
        session_regenerate_id(true);
        $_SESSION['_user_id']        = $id;
        $_SESSION['_user_name']      = $name;
        $_SESSION['_user_email']     = $email;
        $_SESSION['_user_role']      = $role;
        $_SESSION['_last_activity']  = time();
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        if (empty($_SESSION['_user_id'])) {
            return false;
        }
        if (time() - ($_SESSION['_last_activity'] ?? 0) > SESSION_TIMEOUT) {
            self::logout();
            return false;
        }
        $_SESSION['_last_activity'] = time();
        return true;
    }

    public static function require(): void
    {
        if (!self::check()) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    public static function user(): array
    {
        return [
            'id'    => $_SESSION['_user_id']    ?? null,
            'name'  => $_SESSION['_user_name']  ?? '',
            'email' => $_SESSION['_user_email'] ?? '',
            'role'  => $_SESSION['_user_role']  ?? 'manager',
        ];
    }

    public static function id(): ?int
    {
        return $_SESSION['_user_id'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return ($_SESSION['_user_role'] ?? '') === 'admin';
    }
}
