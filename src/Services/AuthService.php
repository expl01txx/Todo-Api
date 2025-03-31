<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Utils\JwtHandler;
use Exception;

class AuthService
{
    private UserRepository $userRepository;
    private JwtHandler $jwtHandler;

    public function __construct(UserRepository $userRepository, JwtHandler $jwtHandler)
    {
        $this->userRepository = $userRepository;
        $this->jwtHandler = $jwtHandler;
    }

    public function register(string $email, string $password): User
    {
        if ($this->userRepository->findByEmail($email)) {
            throw new Exception('Email already exists', 400);
        }

        return $this->userRepository->create($email, $password);
    }

    public function login(string $email, string $password): string
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user || !password_verify($password, $user->getPassword())) {
            throw new Exception('Invalid credentials', 401);
        }

        return $this->jwtHandler->generateToken($user->getId());
    }

    public function logout(string $token): void
    {
        // В реальном приложении здесь можно добавить токен в чёрный список
        // или использовать Redis для хранения недействительных токенов
        // В этой реализации просто проверяем валидность токена
        $this->jwtHandler->validateToken($token);
    }

    public function validateToken(string $token): int
    {
        return $this->jwtHandler->validateToken($token);
    }
}