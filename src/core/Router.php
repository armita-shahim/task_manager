<?php

namespace App\core;

use App\controllers\TaskController;

class Router
{
    private array $routes = [

        [
            'method' => 'POST',
            'uri' => '/tasks',
            'action' => [TaskController::class, 'create']
        ],

        [
            'method' => 'GET',
            'uri' => '/tasks',
            'action' => [TaskController::class, 'list']
        ],

        [
            'method' => 'PUT',
            'uri' => '/tasks',
            'action' => [TaskController::class, 'update']
        ],

        [
            'method' => 'DELETE',
            'uri' => '/tasks',
            'action' => [TaskController::class, 'delete']
        ]
    ];


    public function resolve(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $method = $_SERVER['REQUEST_METHOD'];

        for ($i = 0; $i < count($this->routes); $i++) {
            $route = $this->routes[$i];
            if ($route['method'] === $method && $route['uri'] === $uri) {
                $controllerClass = $route['action'][0];
                $actionMethod = $route['action'][1];

                $controller = new $controllerClass;
                $controller->$actionMethod();
                return;
            }
        }
        echo '404: not found';
    }
}
