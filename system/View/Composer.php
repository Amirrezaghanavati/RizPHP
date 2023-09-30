<?php

namespace System\View;

class Composer
{
    private static ?Composer $instance;
    private array $vars = [];
    private array $viewArray = [];

    private function __construct()
    {
    }

    private function registerView($name, $callback): void
    {
        if (in_array(str_replace('.', '/', $name), $this->viewArray, true) || $name === '*') {
            $viewVars = $callback();
            foreach ($viewVars as $key => $value) {
                $this->vars[$key] = $value;
            }
            if (isset($this->viewArray[$name])) {
                unset($this->viewArray[$name]);
            }
        }
    }

    private function setViewArray(array $viewArray): void
    {
        $this->viewArray = $viewArray;
    }

    private function getViewVars(): array
    {
        return $this->vars;
    }

    public static function __callStatic($name, $arguments)
    {
        $instance = self::getInstance();
        return match ($name) {
            'view' => call_user_func_array([$instance, 'registerView'], $arguments),
            'setViews' => call_user_func_array([$instance, 'setViewArray'], $arguments),
            'getVars' => call_user_func_array([$instance, 'getViewVars'], $arguments)
        };
    }

    private static function getInstance(): Composer
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}