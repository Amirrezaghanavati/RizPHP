<?php

namespace App\Providers;

class SessionProvider extends provider
{
    public function boot(): void
    {
        // TODO: Implement boot() method.
        session_start();

        // Start old helper
        if (isset($_SESSION['old'])) {
            unset($_SESSION['temporary_old']);
        }

        if (isset($_SESSION['old'])) {
            $_SESSION['temporary_old'] = $_SESSION['old'];
            unset($_SESSION['old']);
        }
        $params = [];
        $params = !isset($_GET) ? $params : array_merge($params, $_GET);
        $params = !isset($_GET) ? $params : array_merge($params, $_POST);
        $_SESSION['old'] = $params;
        unset($params);
        // End old helper

        // Start flash helper
        if (isset($_SESSION['temporary_flash'])) {
            unset($_SESSION['temporary_flash']);
        }

        if (isset($_SESSION['flash'])) {
            $_SESSION['temporary_flash'] = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }
        // End flash helper

        // Start error helper
        if (isset($_SESSION['temporary_errorFlash'])) {
            unset($_SESSION['temporary_errorFlash']);
        }

        if (isset($_SESSION['errorFlash'])) {
            $_SESSION['temporary_errorFlash'] = $_SESSION['errorFlash'];
            unset($_SESSION['errorFlash']);
        }
        // End error helper

    }
}