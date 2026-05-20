<?php
require_once 'src/Database.php';
try {
    $pdo = Database::getConnection();
    echo "✅ Подключение успешно! Версия MySQL: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
} catch (PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage();
}
