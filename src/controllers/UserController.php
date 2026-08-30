<?php

namespace App\controllers;

use App\enums\Role;
use App\models\User;

class UserController
{
    public function store(): void
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

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
        echo json_encode(
            ['message' => 'user created successfully'],
            JSON_PRETTY_PRINT
        );
    }

    public function index(): void
    {
        $users = User::allUsers();
        $json = json_encode($users, JSON_PRETTY_PRINT);
        header('Content-Type: application/json');
        echo $json;
    }

    public function show(int $id): void
    {
        $user = User::findUser($id);
        if ($user !== null) {
            $json = json_encode($user, JSON_PRETTY_PRINT);
            header('Content-Type: application/json');
            echo $json;
        } else {
            header('Content-Type: application/json');
            echo json_encode(
                ['message' => 'user with this id not found'],
                JSON_PRETTY_PRINT
            );
        }
    }

    public function update(int $id): void
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        $role = Role::from($input['role']);
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
        echo json_encode(
            ['message' => 'user updated successfully'],
            JSON_PRETTY_PRINT
        );
    }

    public function destroy(int $id): void
    {
        User::deleteUser($id);
        header('Content-Type: application/json');
        echo json_encode(
            ['message' => 'user with this id deleted successfully'],
            JSON_PRETTY_PRINT
        );
    }
}
