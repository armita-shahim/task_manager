<?php

namespace App\controllers;

use App\enums\Role;
use App\models\User;
use App\core\Auth;

class UserController
{
    public function store(): void
    {
        if (!Auth::check()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(
                ['message' => 'authentication required'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        if (!Auth::isAdmin()) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(
                ['message' => 'only admin can access'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        if (
            !isset($input['username']) ||
            !isset($input['password']) ||
            !isset($input['email']) ||
            !isset($input['role'])
        ) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(
                ['message' => 'required fields are missing'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        $role = Role::from($input['role']);
        $password = $input['password'];
        $password = password_hash($password, PASSWORD_DEFAULT);


        User::addUser(
            $input['username'],
            $password,
            $input['email'],
            $role
        );

        header('Content-Type: application/json');
        http_response_code(201);
        echo json_encode(
            ['message' => 'user created successfully'],
            JSON_PRETTY_PRINT
        );
    }

    public function index(): void
    {
        if (!Auth::check()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(
                ['message' => 'authentication required'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        if (!Auth::isAdmin()) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(
                ['message' => 'only admin can access'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        $users = User::allUsers();
        $json = json_encode($users, JSON_PRETTY_PRINT);
        header('Content-Type: application/json');
        http_response_code(200);
        echo $json;
    }

    public function show(int $id): void
    {
        if (!Auth::check()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(
                ['message' => 'authentication required'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        if (!Auth::isAdmin() && $_SESSION['user_id'] !== $id) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(
                ['message' => 'you can only access your own account'],
                JSON_PRETTY_PRINT
            );
            return;
        }


        $user = User::findUser($id);
        if ($user !== null) {
            $user['assigned_tasks'] = User::getAssignedTasks($id);
            $json = json_encode($user, JSON_PRETTY_PRINT);
            header('Content-Type: application/json');
            http_response_code(200);
            echo $json;
        } else {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(
                ['message' => 'user not found'],
                JSON_PRETTY_PRINT
            );
        }
    }

    public function update(int $id): void
    {
        if (!Auth::check()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(
                ['message' => 'authentication required'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        if (!Auth::isAdmin() && $_SESSION['user_id'] !== $id) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(
                ['message' => 'you can only update your own account'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        $user = User::findUser($id);
        if ($user === null) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(
                ['message' => 'user not found'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        if (
            !isset($input['username']) ||
            !isset($input['password']) ||
            !isset($input['email']) ||
            !isset($input['role'])
        ) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(
                ['message' => 'required fields are missing'],
                JSON_PRETTY_PRINT
            );
            return;
        }


        if (Auth::isAdmin()) {
            $role = Role::from($input['role']);
        } else {
            $role = Role::from($user['role']);
        }

        $password = $input['password'];
        $password = password_hash($password, PASSWORD_DEFAULT);

        User::updateUser(
            $id,
            $input['username'],
            $password,
            $input['email'],
            $role
        );

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(
            ['message' => 'user updated successfully'],
            JSON_PRETTY_PRINT
        );
    }

    public function destroy(int $id): void
    {
        if (!Auth::check()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(
                ['message' => 'authentication required'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        if (!Auth::isAdmin() && $_SESSION['user_id'] !== $id) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(
                ['message' => 'you can only delete your own account'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        $user = User::findUser($id);
        if ($user === null) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(
                ['message' => 'user not found'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        User::deleteUser($id);

        if ($_SESSION['user_id'] === $id) {
            session_destroy();
        }
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(
            ['message' => 'user deleted successfully'],
            JSON_PRETTY_PRINT
        );
    }
}
