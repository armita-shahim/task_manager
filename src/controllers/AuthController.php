<?php

namespace App\controllers;

use App\models\User;
use App\enums\Role;

class AuthController
{
    public function register(): void
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        $password = password_hash($input['password'], PASSWORD_DEFAULT);

        User::addUser(
            $input['username'],
            $password,
            $input['email'],
            Role::member
        );

        header('Content-Type: application/json');
        http_response_code(201);
        echo json_encode(
            ['message' => 'user registered successfully'],
            JSON_PRETTY_PRINT
        );
    }
    public function login(): void
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        $user = User::findByUsernameOrEmail($input['identifier']);
        if ($user === null) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(
                ['message' => 'invalid username/email or password'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        $passcheck = password_verify($input['password'], $user['password']);
        if ($passcheck === false) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(
                ['message' => 'invalid username/email or password'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(
            ['message' => 'login successful'],
            JSON_PRETTY_PRINT
        );
    }

    public function logout(): void
    {
        session_destroy();

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(
            ['message' => 'logout successful'],
            JSON_PRETTY_PRINT
        );
    }
}
