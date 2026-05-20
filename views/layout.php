<?php
// views/layout.php
// Единый HTML-шаблон для всех страниц (Bootstrap 5)
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Справочники') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f8f9fa; }
        .flash-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px 15px; border-radius: 4px; margin-bottom: 15px; }
        .flash-error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px 15px; border-radius: 4px; margin-bottom: 15px; }
        .form-error { color: #dc3545; font-size: 0.875rem; margin-top: 5px; }
        .is-invalid { border-color: #dc3545; }
        .table-hover tbody tr:hover { background: #f1f1f1; }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Вывод flash-сообщений -->
        <?php if ($success = getFlashMessage('success')): ?>
            <div class="flash-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error = getFlashMessage('error')): ?>
            <div class="flash-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <!-- Основной контент страницы -->
        <?= $content ?? '' ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
