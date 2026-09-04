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

        $sql = 'SELECT tasks.id, tasks.title, tasks.description, tasks.priority, tasks.due_date, tasks.status, tasks.created_at, tasks.updated_at, tasks.category_id, categories.name AS category_name FROM tasks
        LEFT JOIN categories ON tasks.category_id = categories.id ORDER BY tasks.id ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return  $stmt->fetchAll();
    }

    public static function findTask(int $id): ?array
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = 'SELECT tasks.id, tasks.title, tasks.description, tasks.priority, tasks.due_date, tasks.status, tasks.created_at, tasks.updated_at, tasks.category_id, categories.name AS category_name FROM tasks
        LEFT JOIN categories ON tasks.category_id = categories.id WHERE tasks.id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        if ($result === false) {
            return null;
        } else {
            return $result;
        }
    }

    public static function addTask(string $title, string $description, Priority $priority, DateTime $dueDate, Status $status, ?int $categoryId = null): int
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = 'INSERT INTO tasks (title, description, priority, due_date, status, category_id) VALUES(:title, :description, :priority, :dueDate, :status, :categoryId)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['title' => $title, 'description' => $description, 'priority' => $priority->value, 'dueDate' => $dueDate->format('Y-m-d'), 'status' => $status->value, 'categoryId' => $categoryId]);

        $taskId = $pdo->lastInsertId();
        return (int) $taskId;
    }

    public static function updateTask(int $id, string $title, string $description, Priority $priority, DateTime $dueDate, Status $status, ?int $categoryId = null): void
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = 'UPDATE tasks SET title = :title, description = :description, priority = :priority, due_date = :dueDate, status = :status, category_id = :categoryId, updated_at = CURRENT_TIMESTAMP WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id, 'title' => $title, 'description' => $description, 'priority' => $priority->value, 'dueDate' => $dueDate->format('Y-m-d'), 'status' => $status->value, 'categoryId' => $categoryId]);
    }

    public static function deleteTask(int $id): void
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = 'DELETE FROM user_task WHERE task_id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $sql = 'DELETE FROM tasks WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    public static function assignUsers(int $taskId, array $userIds): void
    {
        $database = new Database();
        $pdo = $database->connect();

        for ($i = 0; $i < count($userIds); $i++) {
            $userId = $userIds[$i];

            $sql = 'INSERT INTo user_task (task_id, user_id) VALUES (:taskId, :userId)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['taskId' => $taskId, 'userId' => $userId]);
        }
    }

    public static function getAssignedUsers(int $taskId): array
    {
        $database = new Database();
        $pdo = $database->connect();

        $sql = 'SELECT users.id, users.username, users.email, users.role FROM users
        JOIN user_task ON users.id = user_task.user_id WHERE user_task.task_id = :taskId ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['taskId' => $taskId]);
        return $stmt->fetchAll();
    }

    public static function isAssignedToUser(int $taskId, int $userId): bool
    {

        $database = new Database();
        $pdo = $database->connect();

        $sql = 'SELECT * FROM user_task WHERE task_id = :taskId AND user_id = :userId LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['taskId' => $taskId, 'userId' => $userId]);
        if ($stmt->fetch() !== false) {
            return true;
        } else {
            return false;
        }
    }
}
