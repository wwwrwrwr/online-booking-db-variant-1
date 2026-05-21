<div class="container mt-4">
  <h2>Удаление услуги</h2>
  <div class="alert alert-danger">
    Вы уверены что хотите удалить услугу
    <strong><?= htmlspecialchars($service['service_name']) ?></strong>?
  </div>
  <form method="post">
    <input type="hidden" name="csrf_token"
           value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <button type="submit" class="btn btn-danger">Удалить</button>
    <a href="?entity=service&action=list" class="btn btn-secondary">Отмена</a>
  </form>
</div>
