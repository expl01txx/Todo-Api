<?php

namespace App\Models;

class Task
{
    private int $id;
    private int $userId;
    private string $title;
    private string $description;
    private string $status;
    private ?string $deadline;
    private string $createdAt;

    public function __construct(
        int $id,
        int $userId,
        string $title,
        string $description,
        string $status,
        ?string $deadline,
        string $createdAt
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
        $this->deadline = $deadline;
        $this->createdAt = $createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDeadline(): ?string
    {
        return $this->deadline;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'deadline' => $this->deadline,
            'created_at' => $this->createdAt
        ];
    }
}