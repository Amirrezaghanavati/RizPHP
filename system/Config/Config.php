<?php

namespace System\Config;

class Config
{
    private static Config $instance;
    private array $config_nested_array = [];
    private array $config_dot_array = [];


    private function __construct()
    {
        $this->initialConfigArrays();
    }

    private function initialConfigArrays(): void
    {
        $configPath = dirname(__DIR__, 2) . '/config/';
        foreach (glob($configPath . '*.php') as $filename) {
            $config = require $filename;
            $key = $filename;
            $key = str_replace([$configPath, '.php'], '', $key);
            $this->config_nested_array[$key] = $config;
        }

        $this->initialDefaultValues();
        $this->config_dot_array = $this->array_dot($this->config_nested_array);
    }

    private function initialDefaultValues(): void
    {
        $temporary = str_replace($this->config_nested_array['app']['BASE_URL'], '', explode('?', $_SERVER['REQUEST_URI'][0]));
        $temporary === '/' ? $temporary = '' : $temporary = substr($temporary, 1);
        $this->config_nested_array['app']['BASE_URL'] = $temporary;
    }

    private function array_dot($array, $return_array = [], $return_key = '')
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $return_array = array_merge($return_array, $this->array_dot($value, $return_array, $return_key . $key . '.'));
            } else {
                $return_array[$return_key . $key] = $value;
            }
        }

        return $return_array;
    }

    private static function getInstance(): Config
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function get($key)
    {
        $instance = self::getInstance();
        if (isset($instance->config_dot_array[$key])) {
            return $instance->config_dot_array[$key];
        }
        throw new \RuntimeException('"' . $key . '" Not exist in config array');
    }


}