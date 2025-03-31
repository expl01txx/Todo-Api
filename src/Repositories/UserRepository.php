<?php

namespace App\Repositories;

use App\Models\User;
use App\Core\Database;
use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new User(
            $data['id'],
            $data['email'],
            $data['password_hash']
        ) : null;
    }

    public function create(string $email, string $password): User
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (email, password_hash) VALUES (:email, :password)");
        $stmt->execute([
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ]);

        return new User(
            $this->pdo->lastInsertId(),
            $email,
            $password
        );
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new User(
            $data['id'],
            $data['email'],
            $data['password_hash']
        ) : null;
    }
}