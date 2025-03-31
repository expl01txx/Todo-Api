# API To-Do List

## RESTful API для управления задачами с аутентификацией JWT, построенный на PHP 8.1+ и MySQL/PostgreSQL.

### Функции

- Аутентификация JWT

- CRUD-операции для задач

- Отслеживание статуса задачи («в работе», «завершено», «дедлайн»)

- Управление сроками

- Пагинация

- Проверка ввода

- Безопасное хеширование паролей


## Примеры

### Регистрация
- Запрос
```bash
curl -X POST http://localhost/auth/register \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password123"}'
```

- Ответ
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "email": "user@example.com"
  }
}
```

### Логин
- Запрос
```bash
curl -X POST http://localhost/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password123"}'
```

- Ответ
```json
{
  "message": "Login successful",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

### Создание задачи
- Запрос
```bash
curl -X POST http://localhost/tasks \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..." \
  -H "Content-Type: application/json" \
  -d '{"title":"Buy groceries","status":"в работе","deadline":"2023-12-31 18:00:00"}'
```

- Ответ
```json
{
  "message": "Task created successfully",
  "task": {
    "id": 1,
    "user_id": 1,
    "title": "Buy groceries",
    "description": null,
    "status": "в работе",
    "deadline": "2023-12-31T18:00:00+00:00",
    "created_at": "2023-11-20T14:30:00+00:00"
  }
}
```

### Получение задач
- Запрос
```bash
curl -X GET "http://localhost/tasks?page=1&per_page=5" \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
```

- Ответ
```json
{
  "tasks": [
    {
      "id": 1,
      "user_id": 1,
      "title": "Buy groceries",
      "description": null,
      "status": "в работе",
      "deadline": "2023-12-31T18:00:00+00:00",
      "created_at": "2023-11-20T14:30:00+00:00"
    }
  ],
  "pagination": {
    "page": 1,
    "per_page": 5
  }
}
```

### Получение задачи
- Запрос
```bash
curl -X GET http://localhost/tasks/1 \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
```

- Ответ
```json
{
  "task": {
    "id": 1,
    "user_id": 1,
    "title": "Купить продукты",
    "description": "Молоко, хлеб, яйца",
    "status": "в работе",
    "deadline": "2023-12-31T18:00:00+00:00",
    "created_at": "2023-11-20T14:30:00+00:00"
  }
}
```

### Обновление задачи
- Запрос
```bash
curl -X PUT http://localhost/tasks/1 \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..." \
  -H "Content-Type: application/json" \
  -d '{"title":"Обновленный список покупок","status":"завершено"}'
```

- Ответ
```json
{
  "message": "Задача успешно обновлена",
  "task": {
    "id": 1,
    "user_id": 1,
    "title": "Обновленный список покупок",
    "description": "Молоко, хлеб, яйца",
    "status": "завершено",
    "deadline": "2023-12-31T18:00:00+00:00",
    "created_at": "2023-11-20T14:30:00+00:00"
  }
}
```

### Удаление задачи
- Запрос
```bash
curl -X DELETE http://localhost/tasks/1 \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
```

- Ответ
```json
{
  "message": "Задача успешно удалена"
}
```