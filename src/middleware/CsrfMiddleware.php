<?php

class CsrfMiddleware
{
    public static function generateToken(): string
    {
        // Use existing token from session or cookie (cookie survives session resets)
        $token = $_SESSION['csrf_token'] ?? $_COOKIE['csrf_token'] ?? '';

        if (empty($token)) {
            $token = bin2hex(random_bytes(32));
        }

        // Store in both session and cookie for redundancy
        $_SESSION['csrf_token'] = $token;
        if (!isset($_COOKIE['csrf_token']) || $_COOKIE['csrf_token'] !== $token) {
            setcookie('csrf_token', $token, [
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        return $token;
    }

    public static function check(): void
    {
        if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $token = $_POST['csrf_token']
                ?? $_SERVER['HTTP_X_CSRF_TOKEN']
                ?? '';

            // Accept from session or cookie (fallback for shared hosting session issues)
            $stored = $_SESSION['csrf_token'] ?? $_COOKIE['csrf_token'] ?? '';

            if (empty($stored) || empty($token) || !hash_equals($stored, $token)) {
                // Regenerate token in both session and cookie
                $newToken = bin2hex(random_bytes(32));
                $_SESSION['csrf_token'] = $newToken;
                setcookie('csrf_token', $newToken, [
                    'path' => '/',
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);

                // For API requests, return JSON error
                $uri = $_SERVER['REQUEST_URI'] ?? '';
                if (str_starts_with($uri, '/api/')) {
                    http_response_code(403);
                    header('Content-Type: application/json');
                    die(json_encode(['error' => 'Invalid CSRF token']));
                }

                // For web requests, redirect back with flash message
                $_SESSION['flash_error'] = 'Session expired. Please try again.';
                $referer = $_SERVER['HTTP_REFERER'] ?? '/login';
                header("Location: $referer");
                exit;
            }
        }
    }
}
