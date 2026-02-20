<?php

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function sanitize(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function old(string $field, string $default = ''): string
{
    $value = $_SESSION['old'][$field] ?? $default;
    unset($_SESSION['old'][$field]);
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function errors(string $field): string
{
    $errors = $_SESSION['errors'][$field] ?? [];
    if (empty($errors)) {
        return '';
    }
    $html = '<div class="invalid-feedback d-block">';
    foreach ($errors as $error) {
        $html .= sanitize($error) . '<br>';
    }
    $html .= '</div>';
    return $html;
}

function clearOldInput(): void
{
    unset($_SESSION['errors'], $_SESSION['old']);
}

function csrf_field(): string
{
    require_once __DIR__ . '/../middleware/CsrfMiddleware.php';
    return '<input type="hidden" name="csrf_token" value="' . CsrfMiddleware::generateToken() . '">';
}

function isAdmin(): bool
{
    restoreFromAuthCookie();
    return ($_SESSION['user_role'] ?? '') === 'admin';
}

function isLoggedIn(): bool
{
    if (isset($_SESSION['user_id'])) {
        return true;
    }
    // Fallback: restore session from signed auth cookie (InfinityFree session fix)
    return restoreFromAuthCookie();
}

function currentUserId(): ?int
{
    restoreFromAuthCookie();
    return $_SESSION['user_id'] ?? null;
}

function setAuthCookie(int $userId, string $username, string $role): void
{
    $payload = $userId . ':' . $username . ':' . $role . ':' . time();
    $secret  = $_ENV['DB_PASS'] ?? 'fallback_secret_key';
    $sig     = hash_hmac('sha256', $payload, $secret);
    $token   = base64_encode($payload) . '.' . $sig;

    setcookie('auth_token', $token, [
        'path'     => '/',
        'httponly'  => true,
        'samesite' => 'Lax',
        'expires'  => time() + 86400, // 24 hours
    ]);
}

function clearAuthCookie(): void
{
    setcookie('auth_token', '', [
        'path'     => '/',
        'httponly'  => true,
        'samesite' => 'Lax',
        'expires'  => time() - 3600,
    ]);
}

function restoreFromAuthCookie(): bool
{
    if (isset($_SESSION['user_id'])) {
        return true;
    }

    $token = $_COOKIE['auth_token'] ?? '';
    if (empty($token) || !str_contains($token, '.')) {
        return false;
    }

    [$encodedPayload, $sig] = explode('.', $token, 2);
    $payload = base64_decode($encodedPayload);
    if ($payload === false) {
        return false;
    }

    $secret  = $_ENV['DB_PASS'] ?? 'fallback_secret_key';
    $expected = hash_hmac('sha256', $payload, $secret);
    if (!hash_equals($expected, $sig)) {
        return false;
    }

    $parts = explode(':', $payload, 4);
    if (count($parts) !== 4) {
        return false;
    }

    [$userId, $username, $role, $timestamp] = $parts;

    // Reject tokens older than 24 hours
    if (time() - (int)$timestamp > 86400) {
        return false;
    }

    $_SESSION['user_id']   = (int)$userId;
    $_SESSION['username']  = $username;
    $_SESSION['user_role'] = $role;
    return true;
}
