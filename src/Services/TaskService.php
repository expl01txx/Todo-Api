<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Exception;

class TaskService
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getUserTasks(int $userId, int $page, int $perPage): array
    {
        return $this->taskRepository->getUserTasks($userId, $page, $perPage);
    }

    public function createTask(
        int $userId, 
        string $title, 
        string $description, 
        string $status,
        ?string $deadline
    ): Task {
        return $this->taskRepository->createTask(
            $userId,
            $title,
            $description,
            $status,
            $deadline
        );
    }

    public function getTask(int $taskId, int $userId): Task
    {
        $task = $this->taskRepository->getTaskForUser($taskId, $userId);
        
        if (!$task) {
            throw new Exception('Task not found or access denied', 404);
        }

        return $task;
    }

    public function updateTask(int $taskId, int $userId, array $data): Task
    {
        // First verify task belongs to user
        $this->getTask($taskId, $userId);
        
        return $this->taskRepository->updateTask($taskId, $data);
    }

    public function deleteTask(int $taskId, int $userId): void
    {
        // Verify ownership before deletion
        $this->getTask($taskId, $userId);
        
        $this->taskRepository->deleteTask($taskId);
    }
}