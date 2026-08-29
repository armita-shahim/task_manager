<?php

namespace App\models;

use App\enums\Priority;
use App\enums\Status;
use App\core\Database;
use DateTime;


class Task
{
    public static function allTasks(): array
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = 'SELECT * FROM tasks';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return  $stmt->fetchAll();
    }

    public static function findTask(int $id): ?array
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = 'SELECT * FROM tasks WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        if ($result === false) {
            return null;
        } else {
            return $result;
        }
    }

    public static function addTask(string $title, ?string $description, Priority $priority, DateTime $dueDate, Status $status, int $categoryId): void
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = "INSERT INTO tasks (title, description, priority, due_date, status, category_id) VALUES(:title, :description, :priority, :dueDate, :status, :categoryId)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['title' => $title, 'description' => $description, 'priority' => $priority->value, 'dueDate' => $dueDate->format('Y-m-d'), 'status' => $status->value, 'categoryId' => $categoryId]);
    }

    public static function updateTask(int $id, string $title, ?string $description, Priority $priority, DateTime $dueDate, Status $status, int $categoryId): void
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = "UPDATE tasks SET title = :title, description = :description, priority = :priority, due_date = :dueDate, status = :status, category_id = :categoryId, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id, 'title' => $title, 'description' => $description, 'priority' => $priority->value, 'dueDate' => $dueDate->format('Y-m-d'), 'status' => $status->value, 'categoryId' => $categoryId]);
    }

    public static function deleteTask(int $id): void
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = 'DELETE FROM tasks WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
