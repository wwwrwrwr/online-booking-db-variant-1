<div class="container mt-4">
  <h2>Удаление врача</h2>
  <div class="alert alert-danger">
    Вы уверены что хотите удалить врача
    <strong><?= htmlspecialchars($dentist['last_name'] . ' ' . $dentist['first_name']) ?></strong>?
  </div>
  <form method="post">
    <input type="hidden" name="csrf_token"
           value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <button type="submit" class="btn btn-danger">Удалить</button>
    <a href="?entity=dentist&action=list" class="btn btn-secondary">Отмена</a>
  </form>
</div>
