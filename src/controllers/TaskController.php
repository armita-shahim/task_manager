<?php

namespace App\controllers;

use App\models\Task;
use App\enums\Priority;
use App\enums\Status;
use DateTime;
use App\core\Auth;
use App\models\User;

class TaskController
{

    public function store(): void
    {
        if (!Auth::check()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(
                ['message' => 'unauthorized'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        $priority = Priority::from($input['priority']);
        $dueDate = new DateTime($input['due_date']);
        $status = Status::from($input['status']);

        $taskId = Task::addTask(
            $input['title'],
            $input['description'],
            $priority,
            $dueDate,
            $status,
            $input['category_id'] ?? null
        );

        if (!Auth::isAdmin()) {
            $userId = $_SESSION['user_id'];
            Task::assignUsers($taskId, [$userId]);
        }

        header('Content-Type: application/json');
        http_response_code(201);
        echo json_encode(
            ['message' => 'Task created successfully'],
            JSON_PRETTY_PRINT
        );
    }

    public function index(): void
    {
        if (!Auth::check()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(
                ['message' => 'unauthorized'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        if (!Auth::isAdmin()) {
            $userId = $_SESSION['user_id'];
            $tasks = User::getAssignedTasks($userId);
        } else {
            $tasks = Task::allTasks();
        }

        $json = json_encode($tasks, JSON_PRETTY_PRINT);
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
                ['message' => 'unauthorized'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        $task = Task::findTask($id);
        if ($task === null) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(
                ['message' => 'task with this id not found'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        if (!Auth::isAdmin()) {
            $userId = $_SESSION['user_id'];

            if (!Task::isAssignedToUser($id, $userId)) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(
                    ['message' => 'you do not have access to this task'],
                    JSON_PRETTY_PRINT
                );
                return;
            }
        }

        $task['assigned_users'] = Task::getAssignedUsers($id);
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(
            $task,
            JSON_PRETTY_PRINT
        );
    }

    public function update(int $id): void
    {
        if (!Auth::check()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(
                ['message' => 'unauthorized'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        $task = Task::findTask($id);
        if ($task === null) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(
                ['message' => 'task with this id not found'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        if (!Auth::isAdmin()) {
            $userId = $_SESSION['user_id'];

            if (!Task::isAssignedToUser($id, $userId)) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(
                    ['message' => 'you do not have access to this task'],
                    JSON_PRETTY_PRINT
                );
                return;
            }
        }

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
        http_response_code(200);
        echo json_encode(
            ['message' => 'Task updated successfully'],
            JSON_PRETTY_PRINT
        );
    }

    public function destroy(int $id): void
    {
        if (!Auth::check()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(
                ['message' => 'unauthorized'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        $task = Task::findTask($id);
        if ($task === null) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(
                ['message' => 'task with this id not found'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        if (!Auth::isAdmin()) {
            $userId = $_SESSION['user_id'];

            if (!Task::isAssignedToUser($id, $userId)) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(
                    ['message' => 'you do not have access to this task'],
                    JSON_PRETTY_PRINT
                );
                return;
            }
        }

        Task::deleteTask($id);
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(
            ['message' => 'task with this id deleted successfully'],
            JSON_PRETTY_PRINT
        );
    }

    public function assignUsers(int $id): void
    {

        if (!Auth::check()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(
                ['message' => 'unauthorized'],
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

        $task = Task::findTask($id);
        if ($task === null) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(
                ['message' => 'task with this id not found'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        for ($i = 0; $i < count($input['user_ids']); $i++) {
            $userId = $input['user_ids'][$i];

            $user = User::findUser($userId);
            if ($user === null) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode(
                    ['message' => 'user with this id not found'],
                    JSON_PRETTY_PRINT
                );
                return;
            }
        }

        Task::assignUsers($id, $input['user_ids']);
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(
            ['message' => 'users assigned to task successfully'],
            JSON_PRETTY_PRINT
        );
    }
}
