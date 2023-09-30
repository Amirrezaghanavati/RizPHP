<?php

/*
|--------------------------------------------------------------------------
| System Routing
|--------------------------------------------------------------------------
*/

namespace System\Router;

use ReflectionMethod;
use System\Config\Config;

class Routing
{

    private array $current_route; // routes that user entered
    private mixed $method; // Http verbs
    private array $routes; // global routes
    private array $values = []; //params

    public function __construct()
    {
        $this->current_route = explode("/", Config::get('app.CURRENT_ROUTE'));
        $this->method = $this->methodField();
        global $routes;
        $this->routes = $routes;
    }

    // Runs the routing system

    public function run(): void
    {
        $match = $this->match();



        //if $match is empty
        if (!$match) {
            $this->error404();
        }

        // Get Controller path
        $controllerClassPath = str_replace('\\', '/', $match['class']);

        //check the controller exist or not
        $path = Config::get('app.BASE_DIR')."/app/Http/Controllers/"."$controllerClassPath.php";

        if (!file_exists($path)) {
            $this->error404();
        }

        //check the method exist or not
        $controllerClass = "\App\Http\Controllers\\".$match["class"];

        $object = new $controllerClass();
        if (!method_exists($object, $match["method"])) {
            $this->error404();
        }

        //ok and check parameters
        $reflectionMethod = new ReflectionMethod($controllerClass, $match["method"]);
        $parameterCount = $reflectionMethod->getNumberOfParameters();
        //ok
        if ($parameterCount <= count($this->values)) {
            //run method
            call_user_func_array([$object, $match["method"]], $this->values);
        }else{
            $this->error404();
        }

    }


    // Same route and current route or not
    public function match(): array
    {
        //check current route with reserved route that method filed like get
        $reservedRoutes = $this->routes[$this->method];

        foreach ($reservedRoutes as $reservedRoute) {
            if ($this->compare($reservedRoute['url']) === true) {
                return ['class' => $reservedRoute['class'], 'method' => $reservedRoute['method']];
            }
            $this->values = [];
        }
        return [];
    }

    //Comparing two variables (current route & route)
    private function compare($reservedRouteUrl): bool
    {
        // When domain with slash
        if (trim($reservedRouteUrl, '/') === "") {
            return trim($this->current_route[0], '/') === "";
        }

        $reservedRouteUrlArray = explode('/', $reservedRouteUrl);
        if (count($reservedRouteUrlArray) !== count($this->current_route)) {
            return false;
        }

        $reservedRouteUrlElement = null;
        foreach ($this->current_route as $key => $currentRouteElement) {
            $reservedRouteUrlElement = $reservedRouteUrlArray[$key];
            if (str_starts_with($reservedRouteUrlElement, "{") && str_ends_with($reservedRouteUrlElement, "}")) {
                $this->values[] = $currentRouteElement;
            } elseif ($reservedRouteUrlElement !== $currentRouteElement) {
                return false;
            }
        }
        return true;
    }

    public function error404()
    {
        http_response_code(404);
        include __DIR__ . DIRECTORY_SEPARATOR . "View" . DIRECTORY_SEPARATOR . "404.php";
        exit();
    }

    // http verb detection
    public function methodField()
    {
        // String to lower case (get or post)
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        // Convert to Put Or Delete
        if (($method === "post") && isset($_POST['_method'])) {
            if ($_POST['_method'] === "put") {
                $method = "put";
            } elseif ($_POST['_method'] === "delete") {
                $method = "delete";
            }
        }
        return $method;
    }
}