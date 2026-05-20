<?php
// public/index.php
// Точка входа приложения. Маршрутизация запросов.

// Подключение конфигурации и классов
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/RepositoryException.php';
require_once __DIR__ . '/../src/AbstractRepository.php';
require_once __DIR__ . '/../src/ClientRepository.php';
require_once __DIR__ . '/../src/ServiceRepository.php';
require_once __DIR__ . '/../src/AppointmentRepository.php';
require_once __DIR__ . '/../src/DentistRepository.php';

// Получение параметров из URL
$entity = $_GET['entity'] ?? 'client';
$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Валидация entity — разрешённые справочники
$allowedEntities = ['client', 'service', 'dentist'];
if (!in_array($entity, $allowedEntities, true)) {
    http_response_code(400);
    echo "Недопустимый справочник";
    exit;
}

// Подключение к БД и создание репозиториев
$pdo = Database::getConnection();

// Выбор контроллера в зависимости от entity
$controllerFile  = __DIR__ . '/../controllers/' . ucfirst($entity) . 'Controller.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerClass = ucfirst($entity) . 'Controller';
    $controller      = new $controllerClass($pdo, $entity);
    $controller->handle($action, $id);
} else {
    http_response_code(404);
    echo "Контроллер для '{$entity}' не найден";
}
