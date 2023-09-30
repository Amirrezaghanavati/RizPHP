<?php

function dd($value, $die = true)
{
    echo "<pre>";
    var_dump($value);
    if ($die) {
        exit();
    }
}

function html($text): string
{
    return html_entity_decode($text);
}

function old($name)
{
    return $_SESSION['temporary_old'][$name] ?? null;
}

function view($dir, $vars): void
{
    $viewBuilder = new \System\View\ViewBuilder();
    $viewBuilder->run($dir);
    $viewVars = $viewBuilder->vars;
    $content = $viewBuilder->content;
    empty($viewVars) ?: extract($viewVars);
    empty($vars) ?: extract($vars);
    eval(" ?>" . html_entity_decode($content));
}

function flash($name, $message = null)
{
    // Getter , Read the message
    if (!$message) {
        if (isset($_SESSION['temporary_flash'][$name])) {
            $temporary = $_SESSION['temporary_flash'][$name];
            unset($_SESSION['temporary_flash'][$name]);
            return $temporary;
        }
        return false;
    }

    // Setter , Set the message
    $_SESSION['flash'][$name] = $message;
}

function flashExists($name): bool
{
    return isset($_SESSION['temporary_flash'][$name]) === true ? true : false;
}


function allFlashes()
{
    if (isset($_SESSION['temporary_flash'])) {
        $temporary = $_SESSION['temporary_flash'];
        unset($_SESSION['temporary_flash']);
        return $temporary;
    }
    return false;

}

function error($name, $message = null)
{
    // Getter , Read the message
    if (!$message) {
        if (isset($_SESSION['temporary_errorFlash'][$name])) {
            $temporary = $_SESSION['temporary_errorFlash'][$name];
            unset($_SESSION['temporary_errorFlash'][$name]);
            return $temporary;
        }
        return false;
    }

    // Setter , Set the message
    $_SESSION['errorFlash'][$name] = $message;
}

function errorExists($name): bool
{
    return isset($_SESSION['temporary_errorFlash'][$name]) === true;
}


function allErrors()
{
    if (isset($_SESSION['temporary_errorFlash'])) {
        $temporary = $_SESSION['temporary_errorFlash'];
        unset($_SESSION['temporary_errorFlash']);
        return $temporary;
    }
    return false;

}

function currentDomain(): string
{
    $httpProtocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    $currentUrl = $_SERVER['HTTP_HOST'];
    return $httpProtocol . $currentUrl;
}

function redirect($url)
{
    $url = trim($url, '/ ');
    $url = str_starts_with($url, currentDomain()) ? $url : currentDomain() . '/' . $url;
    header('Location: ' . $url);
    exit();
}

function back(): void
{
    $http_referer = $_SERVER['HTTP_REFERER'] ?? null;
    redirect($http_referer);
}

function asset($src): string
{
    return currentDomain() . '/' . trim($src, '/ ');
}

function url($src): string
{
    return currentDomain() . '/' . trim($src, '/ ');
}

function findRouteByName($name)
{
    global $routes;
    $allRoutes = array_merge($routes['get'], $routes['post'], $routes['put'], $routes['delete']);
    $route = null;
    foreach ($allRoutes as $element) {
        if ($element['name'] === $name && $element['name'] !== null) {
            $route = $element['url'];
            break;
        }
    }
    return $route;
}

function route(string $name, array $params = []): string
{
    $route = findRouteByName($name);
    if ($route === null) {
        throw new \RuntimeException('route not found');
    }
    $params = array_reverse($params);
    $routeParamsMatch = [];
    preg_match_all("/{[^}.]*}/", $route, $routeParamsMatch);
    if (count($routeParamsMatch[0]) > count($params)) {
        throw new \RuntimeException('route params not enough');
    }

    foreach ($routeParamsMatch[0] as $key => $routeMatch) {
        $route = str_replace($routeMatch, array_pop($params), $route);
    }

    return currentDomain() . '/' . trim($route, ' /');
}

function generateToken(): string
{
    return bin2hex(string: openssl_random_pseudo_bytes(32));
}

function methodField()
{
    $method_field = strtolower($_SESSION['REQUEST_METHOD']);
    if (($method_field === 'post') && isset($_POST['_method'])) {
        if ($_POST['_method'] === 'put') {
            $method_field = 'put';
        } elseif ($_POST['_method'] === 'delete') {
            $method_field = 'delete';
        }
    }
    return $method_field;
}

function  array_dot($array, $return_array = [], $return_key = '')
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $return_array = array_merge($return_array, array_dot($value, $return_array, $return_key . $key . '.'));
        } else {
            $return_array[$return_key . $key] = $value;
        }
    }

    return $return_array;
}

function currentUrl(): string
{
    return currentDomain() . $_SERVER['REQUEST_URI'];
}