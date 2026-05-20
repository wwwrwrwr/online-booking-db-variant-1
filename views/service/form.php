<div class="container mt-4">
  <h2><?= $isEdit ? 'Редактировать услугу' : 'Добавить услугу' ?></h2>
  <form method="post">
    <input type="hidden" name="csrf_token"
           value="<?= htmlspecialchars(generateCsrfToken()) ?>">

    <div class="mb-3">
      <label>Название *</label>
      <input type="text" name="service_name" class="form-control
        <?= isset($errors['service_name']) ? 'is-invalid' : '' ?>"
        value="<?= htmlspecialchars($old['service_name'] ?? '') ?>">
      <?php if (isset($errors['service_name'])): ?>
        <div class="invalid-feedback"><?= $errors['service_name'] ?></div>
      <?php endif; ?>
    </div>

    <div class="mb-3">
      <label>Цена (₽) *</label>
      <input type="number" name="price" min="1" step="0.01"
        class="form-control
        <?= isset($errors['price']) ? 'is-invalid' : '' ?>"
        value="<?= htmlspecialchars($old['price'] ?? '') ?>">
      <?php if (isset($errors['price'])): ?>
        <div class="invalid-feedback"><?= $errors['price'] ?></div>
      <?php endif; ?>
    </div>

    <div class="mb-3">
      <label>Длительность (минут) * — от 15 до 180</label>
      <input type="number" name="duration_minutes" min="15" max="180"
        class="form-control
        <?= isset($errors['duration_minutes']) ? 'is-invalid' : '' ?>"
        value="<?= htmlspecialchars($old['duration_minutes'] ?? '') ?>">
      <?php if (isset($errors['duration_minutes'])): ?>
        <div class="invalid-feedback"><?= $errors['duration_minutes'] ?></div>
      <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-success">
      <?= $isEdit ? 'Сохранить изменения' : 'Создать' ?>
    </button>
    <a href="?entity=service&action=list" class="btn btn-secondary">Отмена</a>
  </form>
</div>
