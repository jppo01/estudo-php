<?php
class AuthController
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            Response::redirect('dashboard');
        }
        $error = flash_get();
        require __DIR__ . '/../views/auth/login.php';
    }

    public function processLogin(): void
    {
        // CSRF não aplicado no login pois sessão pode não persistir em
        // hospedagem compartilhada antes da autenticação.

        $email    = Sanitizer::str('email', $_POST);
        $password = $_POST['password'] ?? '';

        $attempts = $_SESSION['_login_attempts'] ?? 0;
        $lockout  = $_SESSION['_login_lockout_until'] ?? 0;

        if ($lockout > time()) {
            $wait = ceil(($lockout - time()) / 60);
            flash('danger', "Muitas tentativas. Aguarde {$wait} minuto(s).");
            Response::redirect('login');
        }

        $user = $this->users->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['_login_attempts']      = 0;
            $_SESSION['_login_lockout_until'] = 0;
            Auth::login($user['id'], $user['name'], $user['email'], $user['role']);
            $this->users->updateLastLogin($user['id']);
            Response::redirect('dashboard');
        }

        $attempts++;
        $_SESSION['_login_attempts'] = $attempts;
        if ($attempts >= 5) {
            $_SESSION['_login_lockout_until'] = time() + 900;
        }
        flash('danger', 'E-mail ou senha inválidos.');
        Response::redirect('login');
    }

    public function logout(): void
    {
        Auth::logout();
        Response::redirect('login');
    }
}
