<div class="container mt-4">
  <h2>Отмена записи</h2>
  <div class="alert alert-danger">
    Вы уверены что хотите отменить запись клиента
    <strong><?= htmlspecialchars($appointment['last_name'] . ' ' . $appointment['first_name']) ?></strong>
    на <?= htmlspecialchars($appointment['appointment_datetime']) ?>?
  </div>
  <form method="post">
    <input type="hidden" name="csrf_token"
           value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <button type="submit" class="btn btn-danger">Да, отменить</button>
    <a href="?entity=appointment&action=list" class="btn btn-secondary">Назад</a>
  </form>
</div>
