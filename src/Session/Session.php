<?php

namespace carlansell94\Liteblog\Session;

use carlansell94\Liteblog\Session\User;

class Session
{
    private const SESSION_TIMEOUT = 3000;

    final public static function start(): void
    {
        session_start();
    }

    final public static function login(): void
    {
        $_SESSION['auth'] = true;
        unset($_SESSION['login_fails']);
        self::updateTimestamp();
    }

    final public static function failedLogin(string $reason = null): void
    {
        if (isset($_SESSION['login_fails'])) {
            $_SESSION['login_fails']++;
        } else {
            $_SESSION['login_fails'] = 1;
        }
    }

    final public static function failedLogins(): int
    {
        if (isset($_SESSION['login_fails'])) {
            return $_SESSION['login_fails'];
        }

        return 0;
    }

    final public static function isLoggedIn(): bool
    {
        if (!isset($_SESSION['auth'])) {
            return false;
        }

        if (
            !isset($_SESSION['timestamp']) ||
                time() - $_SESSION['timestamp'] > self::SESSION_TIMEOUT
        ) {
            return false;
        }

        return true;
    }

    final public static function updateTimestamp(): void
    {
        $_SESSION['timestamp'] = time();
    }

    final public static function end(): void
    {
        session_destroy();
        $_SESSION = array();
    }
}
