<?php

namespace App\Middleware;

use App\Services\AuthService;
use App\Core\Response;

class AuthMiddleware
{
    private AuthService $authService;
    private Response $response;

    public function __construct()
    {
        $this->authService = new AuthService(
            new \App\Repositories\UserRepository(),
            new \App\Utils\JwtHandler()
        );
        $this->response = new Response();
    }

    public function handle(): int
    {
        $token = $this->getBearerToken();
        
        try {
            return $this->authService->validateToken($token);
        } catch (\Exception $e) {
            $this->response->json(['error' => $e->getMessage()], $e->getCode() ?: 401);
            exit;
        }
    }

    private function getBearerToken(): string
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (empty($authHeader)) {
            $this->response->json(['error' => 'Authorization header missing'], 401);
            exit;
        }

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }

        $this->response->json(['error' => 'Invalid authorization header format'], 401);
        exit;
    }
}