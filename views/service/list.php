<div class="container mt-4">
  <h2>Услуги</h2>
  <a href="?entity=service&action=create" class="btn btn-primary mb-3">
    + Добавить услугу
  </a>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>
          <a href="?entity=service&action=list&sort=service_id&dir=
            <?= $orderBy==='service_id' && $direction==='ASC' ? 'DESC':'ASC' ?>">
            ID
          </a>
        </th>
        <th>Название</th>
        <th>
          <a href="?entity=service&action=list&sort=price&dir=
            <?= $orderBy==='price' && $direction==='ASC' ? 'DESC':'ASC' ?>">
            Цена ₽
          </a>
        </th>
        <th>Длительность (мин)</th>
        <th>Действия</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($services as $s): ?>
      <tr>
        <td><?= $s['service_id'] ?></td>
        <td><?= htmlspecialchars($s['service_name']) ?></td>
        <td><?= number_format($s['price'], 2) ?></td>
        <td><?= $s['duration_minutes'] ?></td>
        <td>
          <a href="?entity=service&action=edit&id=<?= $s['service_id'] ?>"
             class="btn btn-sm btn-warning">Изменить</a>
          <a href="?entity=service&action=delete&id=<?= $s['service_id'] ?>"
             class="btn btn-sm btn-danger">Удалить</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
