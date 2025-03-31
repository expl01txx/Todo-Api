<?php

namespace App\Repositories;

use App\Models\Task;
use App\Core\Database;
use PDO;
use PDOException;

class TaskRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getUserTasks(int $userId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare("
            SELECT * FROM tasks 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $tasks = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tasks[] = $this->mapToTask($data);
        }

        return $tasks;
    }

    public function createTask(
        int $userId, 
        string $title, 
        string $description, 
        string $status,
        ?string $deadline
    ): Task {
        $stmt = $this->pdo->prepare("
            INSERT INTO tasks 
            (user_id, title, description, status, deadline, created_at)
            VALUES (:user_id, :title, :description, :status, :deadline, NOW())
        ");
        $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'deadline' => $deadline
        ]);

        return $this->getTaskById($this->pdo->lastInsertId());
    }

    public function getTaskById(int $taskId): Task
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $taskId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new \Exception("Task not found", 404);
        }

        return $this->mapToTask($data);
    }

    public function updateTask(int $taskId, array $data): Task
    {
        $fields = [];
        $params = ['id' => $taskId];
        
        foreach (['title', 'description', 'status', 'deadline'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            throw new \Exception("No fields to update", 400);
        }

        $query = "UPDATE tasks SET " . implode(', ', $fields) . " WHERE id = :id";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

        return $this->getTaskById($taskId);
    }

    public function deleteTask(int $taskId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $taskId]);

        if ($stmt->rowCount() === 0) {
            throw new \Exception("Task not found", 404);
        }
    }

    public function getTaskForUser(int $taskId, int $userId): ?Task
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tasks 
            WHERE id = :id AND user_id = :user_id
        ");
        $stmt->execute([
            'id' => $taskId,
            'user_id' => $userId
        ]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToTask($data) : null;
    }

    private function mapToTask(array $data): Task
    {
        return new Task(
            $data['id'],
            $data['user_id'],
            $data['title'],
            $data['description'],
            $data['status'],
            $data['deadline'],
            $data['created_at']
        );
    }
}