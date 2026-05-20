<?php
// views/client/delete.php
// Страница подтверждения удаления
?>
<div class="card border-danger">
    <div class="card-header bg-danger text-white">
        <h4>⚠️ Подтвердите удаление</h4>
    </div>
    <div class="card-body">
        <p>Вы действительно хотите удалить клиента:</p>
        <p class="fw-bold">
            <?= htmlspecialchars($client['last_name']) ?> 
            <?= htmlspecialchars($client['first_name']) ?> 
            <?= htmlspecialchars($client['patronymic'] ?? '') ?>
        </p>
        <p class="text-muted small">
            Телефон: <?= htmlspecialchars($client['phone']) ?><br>
            Email: <?= htmlspecialchars($client['email']) ?>
        </p>
        
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <button type="submit" class="btn btn-danger">🗑️ Да, удалить</button>
            <a href="?entity=<?= $entity ?>&action=list" class="btn btn-secondary">↩️ Отмена</a>
        </form>
    </div>
</div>
