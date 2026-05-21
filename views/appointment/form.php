<div class="container mt-4">
  <h2>Создать запись на приём</h2>
  <form method="post">
    <input type="hidden" name="csrf_token"
           value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

    <div class="mb-3">
      <label>Клиент *</label>
      <select name="client_id" class="form-control
        <?= isset($errors['client_id']) ? 'is-invalid' : '' ?>">
        <option value="">-- Выберите клиента --</option>
        <?php foreach ($clients as $c): ?>
          <option value="<?= $c['client_id'] ?>"
            <?= ($old['client_id'] ?? '') == $c['client_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['last_name'] . ' ' . $c['first_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (isset($errors['client_id'])): ?>
        <div class="invalid-feedback"><?= $errors['client_id'] ?></div>
      <?php endif; ?>
    </div>

    <div class="mb-3">
      <label>Врач *</label>
      <select name="dentist_id" class="form-control
        <?= isset($errors['dentist_id']) ? 'is-invalid' : '' ?>">
        <option value="">-- Выберите врача --</option>
        <?php foreach ($dentists as $d): ?>
          <option value="<?= $d['dentist_id'] ?>"
            <?= ($old['dentist_id'] ?? '') == $d['dentist_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($d['last_name'] . ' ' . $d['first_name'] . ' (' . $d['specialization'] . ')') ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (isset($errors['dentist_id'])): ?>
        <div class="invalid-feedback"><?= $errors['dentist_id'] ?></div>
      <?php endif; ?>
    </div>

    <div class="mb-3">
      <label>Услуга *</label>
      <select name="service_id" class="form-control
        <?= isset($errors['service_id']) ? 'is-invalid' : '' ?>">
        <option value="">-- Выберите услугу --</option>
        <?php foreach ($services as $s): ?>
          <option value="<?= $s['service_id'] ?>"
            <?= ($old['service_id'] ?? '') == $s['service_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($s['service_name'] . ' — ' . $s['price'] . ' ₽') ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (isset($errors['service_id'])): ?>
        <div class="invalid-feedback"><?= $errors['service_id'] ?></div>
      <?php endif; ?>
    </div>

    <div class="mb-3">
      <label>Дата и время *</label>
      <input type="datetime-local" name="appointment_datetime"
        class="form-control <?= isset($errors['appointment_datetime']) ? 'is-invalid' : '' ?>"
        value="<?= htmlspecialchars($old['appointment_datetime'] ?? '') ?>"
        min="<?= date('Y-m-d\TH:i') ?>">
      <?php if (isset($errors['appointment_datetime'])): ?>
        <div class="invalid-feedback"><?= $errors['appointment_datetime'] ?></div>
      <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-success">Создать</button>
    <a href="?entity=appointment&action=list" class="btn btn-secondary">Отмена</a>
  </form>
</div>
