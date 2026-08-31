<?php

namespace App\controllers;

use App\models\Task;
use App\enums\Priority;
use App\enums\Status;
use DateTime;

class TaskController
{

    public function store(): void
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        $priority = Priority::from($input['priority']);
        $dueDate = new DateTime($input['due_date']);
        $status = Status::from($input['status']);

        Task::addTask(
            $input['title'],
            $input['description'],
            $priority,
            $dueDate,
            $status,
            $input['category_id'] ?? null
        );

        header('Content-Type: application/json');
        echo json_encode(
            ['message' => 'Task created successfully'],
            JSON_PRETTY_PRINT
        );
    }

    public function index(): void
    {
        $tasks = Task::allTasks();
        $json = json_encode($tasks, JSON_PRETTY_PRINT);
        header('Content-Type: application/json');
        echo $json;
    }

    public function show(int $id): void
    {
        $task = Task::findTask($id);
        if ($task !== null) {
            $task['assigned_users'] = Task::getAssignedUsers($id);
            $json = json_encode($task, JSON_PRETTY_PRINT);
            header('Content-Type: application/json');
            echo $json;
        } else {
            header('Content-Type: application/json');
            echo json_encode(
                ['message' => 'task with this id not found'],
                JSON_PRETTY_PRINT
            );
        }
    }

    public function update(int $id): void
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        $priority = Priority::from($input['priority']);
        $dueDate = new DateTime($input['due_date']);
        $status = Status::from($input['status']);

        Task::updateTask(
            $id,
            $input['title'],
            $input['description'],
            $priority,
            $dueDate,
            $status,
            $input['category_id'] ?? null
        );

        header('Content-Type: application/json');
        echo json_encode(
            ['message' => 'Task updated successfully'],
            JSON_PRETTY_PRINT
        );
    }

    public function destroy(int $id): void
    {
        Task::deleteTask($id);
        header('Content-Type: application/json');
        echo json_encode(
            ['message' => 'task with this id deleted successfully'],
            JSON_PRETTY_PRINT
        );
    }

    public function assignUsers(int $id): void
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        Task::assignUsers($id, $input['user_ids']);
        header('Content-Type: application/json');
        echo json_encode(
            ['message' => 'user assigned to task successfully'],
            JSON_PRETTY_PRINT
        );
    }
}
