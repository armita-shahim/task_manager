<?php

namespace App\core;

class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin(): bool
    {
        if ($_SESSION['role'] === 'admin') {
            return true;
        } else {
            return false;
        }
    }
}
