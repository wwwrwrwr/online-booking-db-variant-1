<div class="container mt-4">
  <h2>Карточка клиента</h2>

  <div class="card mb-4">
    <div class="card-header bg-primary text-white">Информация о клиенте</div>
    <div class="card-body">
      <h5><?= htmlspecialchars($client['last_name'] . ' ' . $client['first_name'] . ' ' . ($client['patronymic'] ?? '')) ?></h5>
      <p class="mb-1"><strong>Телефон:</strong> <?= htmlspecialchars($client['phone']) ?></p>
      <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($client['email']) ?></p>
      <p class="mb-0"><strong>Дата рождения:</strong> <?= htmlspecialchars($client['birth_date']) ?></p>
    </div>
  </div>

  <h4>Записи клиента на приём</h4>
  <?php if (empty($appointments)): ?>
    <div class="alert alert-info">У клиента нет записей</div>
  <?php else: ?>
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>Дата и время</th>
          <th>Врач</th>
          <th>Услуга</th>
          <th>Статус</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($appointments as $a): ?>
        <tr>
          <td><?= htmlspecialchars($a['appointment_datetime']) ?></td>
          <td><?= htmlspecialchars($a['dentist_last_name']) ?></td>
          <td><?= htmlspecialchars($a['service_name']) ?></td>
          <td><?= htmlspecialchars($a['status']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <a href="?entity=client&action=list" class="btn btn-secondary">Назад к списку</a>
</div>
