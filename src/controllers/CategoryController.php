<?php

namespace App\controllers;

use App\models\Category;
use App\core\Auth;

class CategoryController
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

        Category::addCategory($input['name']);

        header('Content-Type: application/json');
        http_response_code(201);
        echo json_encode(
            ['message' => 'category created successfully'],
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

        $categories = Category::allCategories();
        $json = json_encode($categories, JSON_PRETTY_PRINT);
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

        $category = Category::findCategory($id);
        if ($category !== null) {
            $json = json_encode($category, JSON_PRETTY_PRINT);
            header('Content-Type: application/json');
            http_response_code(200);
            echo $json;
        } else {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(
                ['message' => 'category with this id not found'],
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

        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        Category::updateCategory($id, $input['name']);

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(
            ['message' => 'category updated successfully'],
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

        if (!Auth::isAdmin()) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(
                ['message' => 'only admin can access'],
                JSON_PRETTY_PRINT
            );
            return;
        }

        Category::deleteCategory($id);
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(
            ['message' => 'category with this id deleted successfully'],
            JSON_PRETTY_PRINT
        );
    }
}
