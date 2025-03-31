<?php

namespace App\Controllers;

use App\Core\Response;
use App\Models\Task;
use App\Services\TaskService;
use App\Utils\Validator;

class TaskController
{
    private TaskService $taskService;
    private Response $response;

    public function __construct()
    {
        $this->taskService = new TaskService(new \App\Repositories\TaskRepository());
        $this->response = new Response();
    }

    public function index(array $data): void
    {
        try {
            $userId = $data['user_id'] ?? null;
            $page = $_GET['page'] ?? 1;
            $perPage = $_GET['per_page'] ?? 10;

            $tasks = $this->taskService->getUserTasks($userId, $page, $perPage);
            
            $this->response->json([
                'tasks' => array_map(fn($task) => $task->toArray(), $tasks),
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage
                ]
            ]);
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600 
                ? $e->getCode() 
                : 500;
            $this->response->json(['error' => $e->getMessage()], $statusCode);
        }
    }

    public function create(array $data): void
    {
        try {
            $validator = new Validator();
            $validator->validate($data, [
                'title' => 'required|max:255',
                'description' => 'nullable',
                'status' => 'required|in:в работе,завершено,дедлайн',
                'deadline' => 'nullable|date'
            ]);

            $task = $this->taskService->createTask(
                $data['user_id'],
                $data['title'],
                $data['description'] ?? '',
                $data['status'],
                $data['deadline'] ?? null
            );
            
            $this->response->json([
                'message' => 'Task created successfully',
                'task' => $task->toArray()
            ], 201);
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600 
                ? $e->getCode() 
                : 500;
            $this->response->json(['error' => $e->getMessage()], $statusCode);
        }
    }

    public function show(array $data): void
    {
        try {
            $task = $this->taskService->getTask($data['id'], $data['user_id']);
            
            $this->response->json([
                'task' => $task->toArray()
            ]);
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600 
                ? $e->getCode() 
                : 500;
            $this->response->json(['error' => $e->getMessage()], $statusCode);
        }
    }

    public function update(array $data): void
    {
        try {
            $validator = new Validator();
            $validator->validate($data, [
                'title' => 'sometimes|max:255',
                'description' => 'nullable',
                'status' => 'sometimes|in:в работе,завершено,дедлайн',
                'deadline' => 'sometimes|date'
            ]);

            $task = $this->taskService->updateTask(
                $data['id'],
                $data['user_id'],
                $data
            );
            
            $this->response->json([
                'message' => 'Task updated successfully',
                'task' => $task->toArray()
            ]);
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600 
                ? $e->getCode() 
                : 500;
            $this->response->json(['error' => $e->getMessage()], $statusCode);
        }
    }

    public function delete(array $data): void
    {
        try {
            $this->taskService->deleteTask($data['id'], $data['user_id']);
            
            $this->response->json(['message' => 'Task deleted successfully']);
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600 
                ? $e->getCode() 
                : 500;
            $this->response->json(['error' => $e->getMessage()], $statusCode);
        }
    }
}