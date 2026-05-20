<?php
// includes/config.php
// Настройки сессии, CSRF-защиты и flash-сообщений

// Старт сессии (если ещё не начата)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Настройки безопасности
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_EXPIRE', 3600); // токен действует 1 час

/**
 * Генерация токена для защиты форм от подделки запросов
 */
function generateCsrfToken(): string {
    if (empty($_SESSION[CSRF_TOKEN_NAME]) || 
        time() - ($_SESSION[CSRF_TOKEN_TIME] ?? 0) > CSRF_TOKEN_EXPIRE) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        $_SESSION[CSRF_TOKEN_TIME] = time();
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Проверка токена формы
 */
function verifyCsrfToken(string $token): bool {
    return hash_equals($_SESSION[CSRF_TOKEN_NAME] ?? '', $token);
}

/**
 * Получение и удаление flash-сообщения
 */
function getFlashMessage(string $key): ?string {
    $msg = $_SESSION["flash_$key"] ?? null;
    unset($_SESSION["flash_$key"]);
    return $msg;
}

/**
 * Установка flash-сообщения
 */
function setFlashMessage(string $key, string $message): void {
    $_SESSION["flash_$key"] = $message;
}
