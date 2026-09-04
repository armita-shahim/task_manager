<?php

namespace App\models;

use App\core\Database;

class Category
{

    public static function allCategories(): array
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = 'SELECT * FROM categories';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findCategory(int $id): ?array
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = 'SELECT * FROM categories WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        if ($result === false) {
            return null;
        } else {
            return $result;
        }
    }

    public static function addCategory(string $name): void
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = "INSERT INTO categories (name) VALUES (:name)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['name' => $name]);
    }

    public static function updateCategory(int $id, string $name): void
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = "UPDATE categories SET name = :name WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id, 'name' => $name]);
    }

    public static function deleteCategory(int $id): bool
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = 'SELECT * FROM tasks WHERE category_id = :id LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        if ($stmt->fetch() !== false) {
            return false;
        } else {

            $sql = 'DELETE FROM categories WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            return true;
        }
    }
}
