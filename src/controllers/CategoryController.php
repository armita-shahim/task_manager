<?php

namespace App\controllers;

use App\models\Category;

class CategoryController
{

    public function store(): void
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        Category::addCategory($input['name']);

        header('Content-Type: application/json');
        echo json_encode(
            ['message' => 'category created successfully'],
            JSON_PRETTY_PRINT
        );
    }

    public function index(): void
    {
        $categories = Category::allCategories();
        $json = json_encode($categories, JSON_PRETTY_PRINT);
        header('Content-Type: application/json');
        echo $json;
    }

    public function show(int $id): void
    {
        $category = Category::findCategory($id);
        if ($category !== null) {
            $json = json_encode($category, JSON_PRETTY_PRINT);
            header('Content-Type: application/json');
            echo $json;
        } else {
            header('Content-Type: application/json');
            echo json_encode(
                ['message' => 'category with this id not found'],
                JSON_PRETTY_PRINT
            );
        }
    }

    public function update(int $id): void
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        Category::updateCategory($id, $input['name']);

        header('Content-Type: application/json');
        echo json_encode(
            ['message' => 'category updated successfully'],
            JSON_PRETTY_PRINT
        );
    }

    public function destroy(int $id): void
    {
        Category::deleteCategory($id);
        header('Content-Type: application/json');
        echo json_encode(
            ['message' => 'category with this id deleted successfully'],
            JSON_PRETTY_PRINT
        );
    }
}
