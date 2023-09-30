<?php

namespace System\Session;

class Session
{

    public function set($name, $value): void
    {
        $_SESSION[$name] = $value;
    }

    public function get($name)
    {
        return $_SESSION[$name] ?? null;
    }


    public function remove($name): void
    {
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
    }

    public static function __callStatic($name, $arguments)
    {
        $instance = new self();
        return call_user_func_array([$instance, $name], $arguments);
    }
}