<div class="container mt-4">
  <h2><?= $isEdit ? 'Редактировать врача' : 'Добавить врача' ?></h2>
  <form method="post">
    <input type="hidden" name="csrf_token"
           value="<?= htmlspecialchars(generateCsrfToken()) ?>">

    <div class="mb-3">
      <label>Фамилия *</label>
      <input type="text" name="last_name" class="form-control
        <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>"
        value="<?= htmlspecialchars($old['last_name'] ?? '') ?>">
      <?php if (isset($errors['last_name'])): ?>
        <div class="invalid-feedback"><?= $errors['last_name'] ?></div>
      <?php endif; ?>
    </div>

    <div class="mb-3">
      <label>Имя *</label>
      <input type="text" name="first_name" class="form-control
        <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
        value="<?= htmlspecialchars($old['first_name'] ?? '') ?>">
      <?php if (isset($errors['first_name'])): ?>
        <div class="invalid-feedback"><?= $errors['first_name'] ?></div>
      <?php endif; ?>
    </div>

    <div class="mb-3">
      <label>Специализация *</label>
      <select name="specialization" class="form-control
        <?= isset($errors['specialization']) ? 'is-invalid' : '' ?>">
        <option value="">-- Выберите --</option>
        <?php foreach (['терапевт','хирург','ортодонт'] as $spec): ?>
          <option value="<?= $spec ?>"
            <?= ($old['specialization'] ?? '') === $spec ? 'selected' : '' ?>>
            <?= $spec ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (isset($errors['specialization'])): ?>
        <div class="invalid-feedback"><?= $errors['specialization'] ?></div>
      <?php endif; ?>
    </div>

    <div class="mb-3">
      <label>Телефон *</label>
      <input type="tel" name="phone" class="form-control
        <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
        value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
      <?php if (isset($errors['phone'])): ?>
        <div class="invalid-feedback"><?= $errors['phone'] ?></div>
      <?php endif; ?>
    </div>

    <div class="mb-3">
      <label>Номер кабинета * (1-20)</label>
      <input type="number" name="cabinet_number" min="1" max="20"
        class="form-control
        <?= isset($errors['cabinet_number']) ? 'is-invalid' : '' ?>"
        value="<?= htmlspecialchars($old['cabinet_number'] ?? '') ?>">
      <?php if (isset($errors['cabinet_number'])): ?>
        <div class="invalid-feedback"><?= $errors['cabinet_number'] ?></div>
      <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-success">
      <?= $isEdit ? 'Сохранить изменения' : 'Создать' ?>
    </button>
    <a href="?entity=dentist&action=list" class="btn btn-secondary">Отмена</a>
  </form>
</div>
