<?php

namespace App\Controllers;

use App\Core\Response;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Utils\JwtHandler;
use App\Utils\Validator;

class AuthController
{
    private AuthService $authService;
    private Response $response;

    public function __construct()
    {
        $userRepository = new UserRepository();
        $jwtHandler = new JwtHandler();
        $this->authService = new AuthService($userRepository, $jwtHandler);
        $this->response = new Response();
    }

    public function register(array $data): void
    {
        try {
            // Валидация входных данных
            $validator = new Validator();
            $validator->validate($data, [
                'email' => 'required|email:users',
                'password' => 'required|min:6'
            ]);

            $user = $this->authService->register($data['email'], $data['password']);
            $this->response->json([
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail()
                ]
            ], 201);
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600 
                ? $e->getCode() 
                : 500;
            $this->response->json(['error' => $e->getMessage()], $statusCode);
        }
    }

    public function login(array $data): void
    {
        try {
            $validator = new Validator();
            $validator->validate($data, [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $token = $this->authService->login($data['email'], $data['password']);
            
            $this->response->json([
                'message' => 'Login successful',
                'token' => $token
            ]);
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600 
                ? $e->getCode() 
                : 500;
            $this->response->json(['error' => $e->getMessage()], $statusCode);
        }
    }

    public function logout(array $data): void
    {
        try {
            $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            $this->authService->logout($token);
            
            $this->response->json(['message' => 'Logout successful']);
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600 
                ? $e->getCode() 
                : 500;
            $this->response->json(['error' => $e->getMessage()], $statusCode);
        }
    }
}