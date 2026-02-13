<?php

class AuthMiddleware
{
    public static function requireAuth(): void
    {
        if (!isLoggedIn()) {
            flash('error', 'Please log in to continue.');
            redirect('/login');
        }
    }

    public static function requireAdmin(): void
    {
        self::requireAuth();
        if (!isAdmin()) {
            flash('error', 'Access denied. Admin only.');
            redirect('/dashboard');
        }
    }

    public static function requireGuest(): void
    {
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
    }
}
