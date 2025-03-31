<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=utf8',
                    $_ENV['DB_HOST'] ?? 'mysql',
                    $_ENV['DB_NAME'] ?? 'todo_db'
                );
                
                self::$instance = new PDO($dsn, $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASS'] ?? 'password', [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage(), (int) $e->getCode());
            }
        }

        return self::$instance;
    }
}