<?php

namespace App\Core;

use App\Core\Response;
use App\Exceptions\HttpException;

class Router
{
    private array $routes = [];
    private Response $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    public function add(string $method, string $path, array $handler, array $middlewares = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public function run(): void
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];
    
        $jsonData = [];
        if ($requestMethod === 'POST' || $requestMethod === 'PUT' || $requestMethod === 'PATCH') {
            $input = file_get_contents('php://input');
            $jsonData = json_decode($input, true) ?? [];
        }
    
        foreach ($this->routes as $route) {
            $pattern = $this->buildPattern($route['path']);
            
            if ($route['method'] === $requestMethod && preg_match($pattern, $requestUri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                foreach ($route['middlewares'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $userId = $middleware->handle();
                    $_REQUEST['user_id'] = $userId;
                }
                
                [$controllerClass, $method] = $route['handler'];
                
                if (!class_exists($controllerClass)) {
                    throw new HttpException("Controller $controllerClass not found", 500);
                }
                
                $controller = new $controllerClass();
                
                if (!method_exists($controller, $method)) {
                    throw new HttpException("Method $method not found in controller $controllerClass", 500);
                }
                
                $data = array_merge(
                    $_REQUEST,       // GET/POST параметры
                    $params,         // Параметры из URL
                    $jsonData        // JSON-данные из тела запроса
                );
                
                $controller->$method($data);
                
                return;
            }
        }
    
        $this->response->json(['error' => 'Not Found'], 404);
    }

    private function buildPattern(string $path): string
    {
        $pattern = preg_replace('/\{([a-z]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
}