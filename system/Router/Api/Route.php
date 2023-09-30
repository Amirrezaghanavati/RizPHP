<?php

namespace System\Router\Api;

class Route{

    // Http Verbs Category
    public static function get($url, $executeMethod, $name = null): void
    {
        // Explode Execute Method To Class Name And Method
        [$class, $method] = explode('@', $executeMethod);
        global $routes;

        // Remove First Slash(/) from Url
        $url = "api/" . trim($url, '/');

        //Fill Get Http Verb
        $routes['get'][] = compact('url', 'class', 'method', 'name');
    }

    public static function post($url, $executeMethod, $name = null): void
    {
        [$class, $method] = explode('@', $executeMethod);
        global $routes;
        $url = "api/" . trim($url, '/');
        $routes['post'][] = compact('url', 'class', 'method', 'name');
    }


}