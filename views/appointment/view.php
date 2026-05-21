<div class="container mt-4">
  <h2>Запись №<?= $appointment['appointment_id'] ?></h2>

  <div class="card mb-3">
    <div class="card-header bg-primary text-white">Информация о записи</div>
    <div class="card-body">
      <p><strong>Дата и время:</strong> <?= htmlspecialchars($appointment['appointment_datetime']) ?></p>
      <p><strong>Статус:</strong> <?= htmlspecialchars($appointment['status']) ?></p>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header bg-success text-white">Клиент</div>
    <div class="card-body">
      <p><strong>ФИО:</strong> <?= htmlspecialchars($appointment['client_last_name'] . ' ' . $appointment['client_first_name']) ?></p>
      <p><strong>Телефон:</strong> <?= htmlspecialchars($appointment['client_phone']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($appointment['client_email']) ?></p>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header bg-info text-white">Врач</div>
    <div class="card-body">
      <p><strong>ФИО:</strong> <?= htmlspecialchars($appointment['dentist_last_name'] . ' ' . $appointment['dentist_first_name']) ?></p>
      <p><strong>Специализация:</strong> <?= htmlspecialchars($appointment['specialization']) ?></p>
      <p><strong>Кабинет:</strong> <?= htmlspecialchars($appointment['cabinet_number']) ?></p>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header bg-warning">Услуга</div>
    <div class="card-body">
      <p><strong>Название:</strong> <?= htmlspecialchars($appointment['service_name']) ?></p>
      <p><strong>Цена:</strong> <?= number_format($appointment['price'], 2) ?> ₽</p>
      <p><strong>Длительность:</strong> <?= $appointment['duration_minutes'] ?> мин</p>
    </div>
  </div>

  <a href="?entity=appointment&action=list" class="btn btn-secondary">Назад</a>
  <?php if ($appointment['status'] === 'запланировано'): ?>
    <a href="?entity=appointment&action=cancel&id=<?= $appointment['appointment_id'] ?>"
       class="btn btn-danger">Отменить запись</a>
  <?php endif; ?>
</div>
