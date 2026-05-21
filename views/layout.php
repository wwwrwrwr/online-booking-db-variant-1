<?php
// views/layout.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Справочники') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 0; background: #f8f9fa; }
        .container { padding-top: 10px; }
        .flash-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px 15px; border-radius: 4px; margin-bottom: 15px; }
        .flash-error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px 15px; border-radius: 4px; margin-bottom: 15px; }
        .form-error { color: #dc3545; font-size: 0.875rem; margin-top: 5px; }
        .is-invalid { border-color: #dc3545; }
        .table-hover tbody tr:hover { background: #f1f1f1; }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
      <div class="container">
        <a class="navbar-brand" href="?entity=client&action=list">Стоматология</a>
        <div class="navbar-nav">
          <a class="nav-link" href="?entity=client&action=list">Клиенты</a>
          <a class="nav-link" href="?entity=dentist&action=list">Врачи</a>
          <a class="nav-link" href="?entity=service&action=list">Услуги</a>
          <a class="nav-link" href="?entity=appointment&action=list">Записи</a>
          <a class="nav-link" href="?entity=appointment&action=reports">Отчёты</a>
        </div>
      </div>
    </nav>

    <div class="container">
        <?php
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);
        ?>
        <?php if ($success): ?>
            <div class="flash-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="flash-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
