<?php

namespace App\models;

use App\core\Database;
use App\enums\Role;

class User
{
    public static function allUsers(): array
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = 'SELECT * FROM users';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return  $stmt->fetchAll();
    }

    public static function findUser(int $id): ?array
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = 'SELECT * FROM users WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        if ($result === false) {
            return null;
        } else {
            return $result;
        }
    }

    public static function addUser(string $username, string $password, string $email, Role $role): void
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = "INSERT INTO users (username, password, email, role) VALUES(:username, :password, :email, :role)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username, 'password' => $password, 'email' => $email, 'role' => $role->value]);
    }

    public static function updateUser(int $id, string $username, string $password, string $email, Role $role): void
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = "UPDATE users SET username = :username, password = :password, email = :email, role = :role WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id, 'username' => $username, 'password' => $password, 'email' => $email, 'role' => $role->value]);
    }

    public static function deleteUser(int $id): void
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = 'DELETE FROM users WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
