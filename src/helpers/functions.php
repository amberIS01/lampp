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
    return ($_SESSION['user_role'] ?? '') === 'admin';
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function currentUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}
