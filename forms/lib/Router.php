<?php

class Router {
    private $routes = [];

    public function add($path, $controllerAction) {
        $this->routes[$path] = $controllerAction;
    }

    public function dispatch($uri) {
        $path = parse_url($uri, PHP_URL_PATH);
        $controllerFile = "UNKNOWN"; 

        if (isset($this->routes[$path])) {
            list($controllerName, $action) = explode('@', $this->routes[$path]);
            $controllerFile = "controllers/{$controllerName}.php";
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controller = new $controllerName();
                if (method_exists($controller, $action)) {
                    $controller->$action();
                } else {
                    $this->error404($controllerFile);
                }
            } else {
                $this->error404($controllerFile);
            }
        } else {
            $this->error404($controllerFile);
        }
    }

    private function error404($controllerFile) {
        header("HTTP/1.0 404 Not Found");
        echo "404 Página no encontrada:".$controllerFile;
        exit;
    }
}
