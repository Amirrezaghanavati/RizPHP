<?php

namespace System\Router\Web;

class Route{

    // Http Verbs Category
    public static function get($url, $executeMethod, $name = null): void
    {
        // Explode Execute Method To Class Name And Method
        [$class, $method] = explode('@', $executeMethod);
        global $routes;

        // Remove First Slash(/) from Url
        $url = trim($url, '/');

        //Fill Get Http Verb
        $routes['get'][] = compact('url', 'class', 'method', 'name');
    }

    public static function post($url, $executeMethod, $name = null): void
    {
        [$class, $method] = explode('@', $executeMethod);
        global $routes;
        $url = trim($url, '/');
        $routes['post'][] = compact('url', 'class', 'method', 'name');
    }

    public static function put($url, $executeMethod, $name = null): void
    {
        [$class, $method] = explode('@', $executeMethod);
        global $routes;
        $url = trim($url, '/');
        $routes['put'][] = compact('url', 'class', 'method', 'name');
    }

    public static function delete($url, $executeMethod, $name = null): void
    {
        [$class, $method] = explode('@', $executeMethod);
        global $routes;
        $url = trim($url, '/');
        $routes['delete'][] = compact('url', 'class', 'method', 'name');
    }

}