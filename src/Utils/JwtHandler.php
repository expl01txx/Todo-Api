<?php

namespace App\Utils;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHandler
{
    private string $secretKey;
    private string $algorithm;
    private int $expireTime;

    public function __construct()
    {
        $this->secretKey = $_ENV['JWT_SECRET'] ?? 'your-secret-key';
        $this->algorithm = 'HS256';
        $this->expireTime = 3600; // 1 час
    }

    public function generateToken(int $userId): string
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->expireTime;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'sub' => $userId
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function validateToken(string $token): int
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return $decoded->sub;
        } catch (\Firebase\JWT\ExpiredException $e) {
            throw new Exception('Token expired', 401);
        } catch (Exception $e) {
            throw new Exception('Invalid token', 401);
        }
    }
}