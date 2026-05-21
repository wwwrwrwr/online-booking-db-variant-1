<div class="container mt-4">
  <h2>Записи на приём</h2>
  <!-- Фильтры -->
  <form class="row g-2 mb-3" method="get">
    <input type="hidden" name="entity" value="appointment">
    <input type="hidden" name="action" value="list">
    <div class="col-md-3">
      <input type="date" name="date_from" class="form-control"
             placeholder="Дата от"
             value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
    </div>
    <div class="col-md-3">
      <input type="date" name="date_to" class="form-control"
             placeholder="Дата до"
             value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
    </div>
    <div class="col-md-3">
      <select name="status" class="form-control">
        <option value="">Все статусы</option>
        <option value="запланировано"
          <?= ($_GET['status'] ?? '') === 'запланировано' ? 'selected' : '' ?>>
          Запланировано
        </option>
        <option value="проведено"
          <?= ($_GET['status'] ?? '') === 'проведено' ? 'selected' : '' ?>>
          Проведено
        </option>
        <option value="отменено"
          <?= ($_GET['status'] ?? '') === 'отменено' ? 'selected' : '' ?>>
          Отменено
        </option>
      </select>
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-secondary">Фильтр</button>
      <a href="?entity=appointment&action=list" class="btn btn-outline-secondary">Сброс</a>
    </div>
  </form>
  <a href="?entity=appointment&action=create" class="btn btn-primary mb-3">
    + Создать запись
  </a>
  <?php if (empty($appointments)): ?>
    <div class="alert alert-info">Нет записей</div>
  <?php else: ?>
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Дата и время</th>
          <th>Клиент</th>
          <th>Врач</th>
          <th>Услуга</th>
          <th>Статус</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($appointments as $a): ?>
        <?php
          $rowClass = match($a['status']) {
            'запланировано' => 'table-success',
            'отменено'      => 'table-danger',
            'проведено'     => 'table-secondary',
            default         => ''
          };
        ?>
        <tr class="<?= $rowClass ?>">
          <td><?= $a['appointment_id'] ?></td>
          <td><?= htmlspecialchars($a['appointment_datetime']) ?></td>
          <td><?= htmlspecialchars($a['client_last_name'] . ' ' . $a['client_first_name']) ?></td>
          <td><?= htmlspecialchars($a['dentist_last_name']) ?></td>
          <td><?= htmlspecialchars($a['service_name']) ?></td>
          <td><?= htmlspecialchars($a['status']) ?></td>
          <td>
            <a href="?entity=appointment&action=view&id=<?= $a['appointment_id'] ?>"
               class="btn btn-sm btn-info">Просмотр</a>
            <?php if ($a['status'] === 'запланировано'): ?>
              <a href="?entity=appointment&action=complete&id=<?= $a['appointment_id'] ?>"
                 class="btn btn-sm btn-success">Провести</a>
              <a href="?entity=appointment&action=cancel&id=<?= $a['appointment_id'] ?>"
                 class="btn btn-sm btn-danger">Отмена</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
