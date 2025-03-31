<?php

require_once __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\TaskController;
use App\Middleware\AuthMiddleware;

$router = new Router();

// Public routes
$router->add('POST', '/auth/register', [AuthController::class, 'register']);
$router->add('POST', '/auth/login', [AuthController::class, 'login']);

// Protected routes
$router->add('POST', '/auth/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);
$router->add('GET', '/tasks', [TaskController::class, 'index'], [AuthMiddleware::class]);
$router->add('POST', '/tasks', [TaskController::class, 'create'], [AuthMiddleware::class]);
$router->add('GET', '/tasks/{id}', [TaskController::class, 'show'], [AuthMiddleware::class]);
$router->add('PUT', '/tasks/{id}', [TaskController::class, 'update'], [AuthMiddleware::class]);
$router->add('DELETE', '/tasks/{id}', [TaskController::class, 'delete'], [AuthMiddleware::class]);

$router->run();

?>