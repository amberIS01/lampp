<?php

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login(): void
    {
        // Already logged in? Go to dashboard
        if (isLoggedIn()) {
            redirect('/dashboard');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
            return;
        }

        // GET: show login form
        clearOldInput();
        require __DIR__ . '/../views/auth/login.php';
    }

    private function handleLogin(): void
    {
        CsrfMiddleware::check();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Basic validation
        if (empty($username) || empty($password)) {
            flash('error', 'Username and password are required.');
            redirect('/login');
            return;
        }

        // Find user
        $user = $this->userModel->findByUsername($username);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            flash('error', 'Invalid username or password.');
            redirect('/login');
            return;
        }

        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);

        // Set session data
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['_last_regeneration'] = time();

        flash('success', 'Welcome back, ' . sanitize($user['username']) . '!');
        redirect('/dashboard');
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        redirect('/login');
    }
}
