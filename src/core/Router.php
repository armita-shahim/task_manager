<?php

namespace App\core;

use App\controllers\TaskController;
use App\controllers\CategoryController;
use App\controllers\UserController;
use App\controllers\AuthController;

class Router
{
    private array $routes = [
        //tasks
        [
            'method' => 'POST',
            'uri' => '/tasks',
            'action' => [TaskController::class, 'store']
        ],

        [
            'method' => 'GET',
            'uri' => '/tasks',
            'action' => [TaskController::class, 'index']
        ],

        [
            'method' => 'PUT',
            'uri' => '/tasks/{id}',
            'action' => [TaskController::class, 'update']
        ],

        [
            'method' => 'DELETE',
            'uri' => '/tasks/{id}',
            'action' => [TaskController::class, 'destroy']
        ],

        [
            'method' => 'GET',
            'uri' => '/tasks/{id}',
            'action' => [TaskController::class, 'show']
        ],

        [
            'method' => 'POST',
            'uri' => '/tasks/{id}/assign',
            'action' => [TaskController::class, 'assignUsers']
        ],
        //categories
        [
            'method' => 'POST',
            'uri' => '/categories',
            'action' => [CategoryController::class, 'store']
        ],

        [
            'method' => 'GET',
            'uri' => '/categories',
            'action' => [CategoryController::class, 'index']
        ],

        [
            'method' => 'PUT',
            'uri' => '/categories/{id}',
            'action' => [CategoryController::class, 'update']
        ],

        [
            'method' => 'DELETE',
            'uri' => '/categories/{id}',
            'action' => [CategoryController::class, 'destroy']
        ],

        [
            'method' => 'GET',
            'uri' => '/categories/{id}',
            'action' => [CategoryController::class, 'show']
        ],
        //users
        [
            'method' => 'POST',
            'uri' => '/users',
            'action' => [UserController::class, 'store']
        ],

        [
            'method' => 'GET',
            'uri' => '/users',
            'action' => [UserController::class, 'index']
        ],

        [
            'method' => 'PUT',
            'uri' => '/users/{id}',
            'action' => [UserController::class, 'update']
        ],

        [
            'method' => 'DELETE',
            'uri' => '/users/{id}',
            'action' => [UserController::class, 'destroy']
        ],

        [
            'method' => 'GET',
            'uri' => '/users/{id}',
            'action' => [UserController::class, 'show']
        ],
        //authentication
        [
            'method' => 'POST',
            'uri' => '/register',
            'action' => [AuthController::class, 'register']
        ],

        [
            'method' => 'POST',
            'uri' => '/login',
            'action' => [AuthController::class, 'login']
        ],

        [
            'method' => 'DELETE',
            'uri' => '/logout',
            'action' => [AuthController::class, 'logout']
        ],
    ];


    public function resolve(): void
    {
        $result = parse_url($_SERVER['REQUEST_URI']);
        $uri = $result['path'];

        $method = $_SERVER['REQUEST_METHOD'];

        for ($i = 0; $i < count($this->routes); $i++) {
            $route = $this->routes[$i];

            if ($route['method'] !== $method) {
                continue;
            }

            $routeParts = explode('/', $route['uri']);
            $uriParts = explode('/', $uri);

            if (count($routeParts) !== count($uriParts)) {
                continue;
            }

            $id = null;
            $match = true;
            for ($j = 0; $j < count($routeParts); $j++) {

                if ($routeParts[$j] === '{id}') {
                    $id = $uriParts[$j];
                } elseif ($routeParts[$j] !== $uriParts[$j]) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                $controllerClass = $route['action'][0];
                $actionMethod = $route['action'][1];

                $controller = new $controllerClass;
                if ($id !== null) {
                    $controller->$actionMethod((int) $id);
                } else {
                    $controller->$actionMethod();
                }

                return;
            }
        }
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(
            ['message' => 'route not found'],
            JSON_PRETTY_PRINT
        );
    }
}
