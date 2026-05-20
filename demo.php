<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключение классов вручную (разрешено методичкой)
require_once 'src/Database.php';
require_once 'src/RepositoryException.php';
require_once 'src/AbstractRepository.php';
require_once 'src/ClientRepository.php';
require_once 'src/ServiceRepository.php';
require_once 'src/AppointmentRepository.php';

echo "<h2>Демонстрация уровня доступа к данным (DAL)</h2>";

try {
    // 1. Получение соединения PDO
    $pdo = Database::getConnection();
    echo "<p>✅ Подключение к базе данных установлено.</p>";

    // 2. Создание объектов репозиториев
    $clientRepo = new ClientRepository($pdo);
    $appointmentRepo = new AppointmentRepository($pdo);

    // 3. Выборка всех клиентов
    echo "<h3>1. Все клиенты:</h3><pre>";
    print_r($clientRepo->findAll());
    echo "</pre>";

    // 4. Выборка одного клиента по ID
    echo "<h3>2. Клиент по ID 1:</h3><pre>";
    print_r($clientRepo->findById(1));
    echo "</pre>";

    // 5. Создание новой записи на приём (использует транзакцию внутри метода)
    echo "<h3>3. Создание новой записи:</h3>";
    $newId = $appointmentRepo->createAppointment([
        'client_id' => 1,
        'dentist_id' => 1,
        'service_id' => 1,
        'appointment_datetime' => '2026-06-15 10:00:00',
        'status' => 'запланировано'
    ]);
    echo "Запись создана. ID: {$newId}<br><br>";

    // 6. Изменение статуса записи
    echo "<h3>4. Изменение статуса записи:</h3>";
    $appointmentRepo->updateStatus($newId, 'проведено');
    echo "Статус обновлён. Текущие данные:<br><pre>";
    print_r($appointmentRepo->findById($newId));
    echo "</pre>";

    // 7. Удаление записи
    echo "<h3>5. Удаление записи:</h3>";
    $appointmentRepo->delete($newId);
    echo "Запись удалена. Проверка поиска (должно вернуть null):<br><pre>";
    print_r($appointmentRepo->findById($newId));
    echo "</pre>";

} catch (RepositoryException $e) {
    echo "<p>❌ Ошибка репозитория: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (PDOException $e) {
    echo "<p>❌ Ошибка базы данных: " . htmlspecialchars($e->getMessage()) . "</p>";
}
