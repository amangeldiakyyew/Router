<?php

/**
 * Router is basic routing system
 *
 * @copyright   2020 (c) Amangeldi Akyyew
 * @author      Amangeldi Akyyew <http://amangeldi.online>
 * @version     1.0.0
 */

class Router
{
    /* Namespace of controllers. Files will be called by its namespace in loadControllers() method*/
    protected $config = ['controller' => ['namespace' => ''], "display_errors" => true];
    /* Current RequestMethod .*/
    protected $requestMethod;
    /* Complete URL of current page.*/
    protected $requestUrl;
    /* The URI pattern the route responds to.*/
    protected $requestUri;
    /* Returns domain.*/
    public $domain;
    /* Keeps all current url data as an array.*/
    public static $current;
    /* All Routes as an array.*/
    private static $routes = [];
    /* Route group as an array*/
    private static $groups = [];
    /* Methods of route being matched. Afterwards it will get values of next route.*/
    private static $latestMethods = [];
    /* Class Instance */
    private static $instance;


    function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->domain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $this->requestUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $this->requestUri = $_SERVER['REQUEST_URI'];
    }

    public static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new self;
        return self::$instance;
    }

    /* Method for error messages */
    public static function displayError($message)
    {
        if ((new self)->config['display_errors'] == true)
            echo "<h5 style='color:red'>Error:</h5>" . "<p>$message</p>";
        return self::getInstance();
    }

    /* Creates routes as an array. Used by match,get,post,put,patch,delete methods.*/
    public static function matchRoutes($requestMethod, $route, $callback)
    {
        if (!is_array($requestMethod))
            self::$latestMethods = [$requestMethod];
        else
            self::$latestMethods = $requestMethod;

        self::$latestMethods = array_map('strtoupper', self::$latestMethods);

        $prefix = $name = null;
        if (count(self::$groups) > 0) {
            $prefix = isset(self::$groups['prefix']) ? self::$groups['prefix'] : null;
            $name = isset(self::$groups['name']) ? self::$groups['name'] : null;
            $route = self::$groups['prefix'] . $route;
        }

        foreach (self::$latestMethods as $method) {
            self::$routes[] = [
                "requestMethod" => strtoupper($method),
                "route" => '/' . trim($route, '/'),
                "function" => $callback,
                "prefix" => $prefix,
                "name" => $name
            ];
        }
        return self::getInstance();
    }

    public static function match($arr, $route, $callback)
    {
        return self::matchRoutes($arr, $route, $callback);
    }

    public static function get($route, $callback)
    {
        return self::matchRoutes("GET", $route, $callback);
    }

    public static function post($route, $callback)
    {
        return self::matchRoutes("POST", $route, $callback);
    }

    public static function put($route, $callback)
    {
        return self::matchRoutes("PUT", $route, $callback);
    }

    public static function patch($route, $callback)
    {
        return self::matchRoutes("PATCH", $route, $callback);
    }

    public static function delete($route, $callback)
    {
        return self::matchRoutes("DELETE", $route, $callback);
    }

    public static function group($arr, $callback)
    {
        self::$groups = $arr;
        $callback();
        self::$groups = [];
        return self::getInstance();
    }

    /* Gives name to the routes */
    public function name($name)
    {
        $count = count(self::$latestMethods);
        for ($i = 0; $i < $count; $i++) {
            $ifNameExists = self::$routes[count(self::$routes) - ($i + 1)]["name"];
            self::$routes[count(self::$routes) - ($i + 1)]["name"] = $ifNameExists . $name;
        }
        return $this;
    }

    /* Returns URL by the route name */
    public static function route($name, $params = [])
    {
        if (!is_array($params))
            $params = [$params];
        foreach (self::$routes as $route) {
            if ($route['name'] == $name) {
                $_route = $route['route'];
                $totalParams = preg_match_all('/[{].*[}]/U', $_route, $matches);
                if (count($params) == $totalParams) {
                    foreach ($params as $param) {
                        $_route = preg_replace('/[{].*[}]/U', $param, $_route, 1);
                    }
                    return (new self)->domain . $_route;
                } else {
                    self::displayError("Wrong Route Parameters. Route::route('" . $route['name'] . "') should have total $totalParams parameters.");
                }
            }
        }
        self::displayError("Undefined  Route::route('" . $name . "')");
        return self::getInstance();
    }

    private static function setCurrent($route, $parameters = null)
    {
        if (!is_callable($route)) {
            if (preg_match("/^([{?}a-zA-Z0-9]+)@([{?}a-zA-Z0-9]+)$/", $route['route'], $match)) {
                $controller = $match[1];
                $method = $match[2];
            } else {
                $controller = null;
                $method = null;
            }
        }

        self::$current = [
            "prefix" => $route['prefix'],
            "name" => $route['name'],
            "route" => $route['route'],
            "url" => (new self)->requestUrl,
            "uri" => (new self)->requestUri,
            "controller" => $controller,
            "method" => $method,
            "parameters" => $parameters
        ];
        return self::getInstance();
    }

    public static function getRoutes()
    {
        return self::$routes;
    }

    public static function getCurrent()
    {
        return self::$current;
    }

    public static function getCurrentRouteName()
    {
        return self::$current['name'];
    }

    public static function getCurrentRoute()
    {
        return self::$current['route'];
    }

    public static function getCurrentUrl()
    {
        return self::$current['url'];
    }

    public static function getCurrentUri()
    {
        return self::$current['uri'];
    }

    public static function getCurrentController()
    {
        return self::$current['controller'];
    }


    public static function getCurrentMethod()
    {
        return self::$current['method'];
    }

    public static function loadControllers($className)
    {
        spl_autoload_register(function ($className) {
            $className = str_replace("\\", "/", $className);
            $path = strtolower(substr($className, 0, strrpos($className, "/") + 1));
            $class = substr($className, strrpos($className, '/') + 1);
            $file = $path . $class . '.php';
            if (file_exists($file))
                require_once $file;
            else
                self::displayError("File $file not Found");
        });
    }

    public static function handleController($route, $parameters)
    {
        $func = $route['function'];
        if (is_callable($func)) {
            return call_user_func_array($func, array_values($parameters));
        } else if (preg_match("/^([{?}a-zA-Z0-9]+)@([{?}a-zA-Z0-9]+)$/", $func, $match)) {
            $controller = (new self)->config['controller']['namespace'] . $match[1];
            self::loadControllers($controller);
            $method = $match[2];
            if (class_exists($controller)) {
                if (method_exists($controller, $method)) {
                    return call_user_func_array([$controller, $method], array_values($parameters));
                } else {
                    self::displayError("Undefined method '$controller::$method'");
                }
            } else {
                self::displayError("Class '$controller' not found");
            }
        } else {
            self::displayError("Routing error on route '" . $route['route'] . "'");
        }
        return self::getInstance();
    }

    /* Initiates  Router Class*/
    public static function run()
    {
        foreach (self::$routes as $route) {
            $routeMatch = preg_replace('/[{].*[}]/U', '([^/]+)', $route['route']);
            if (preg_match('#^' . $routeMatch . '$#', (new self)->requestUri, $parameters) && (new self)->requestMethod == $route['requestMethod']) {
                array_shift($parameters);
                self::setCurrent($route, $parameters);
                self::handleController($route, $parameters);
                return self::getInstance();
            }
        }
        return http_response_code(404);
    }

}

?>