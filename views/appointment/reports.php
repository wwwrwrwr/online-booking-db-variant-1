<div class="container mt-4">
  <h2>Отчёты</h2>

  <!-- Отчёт 1: записи и выручка по дням -->
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">
      Отчёт 1: Количество записей и выручка по дням
    </div>
    <div class="card-body">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Дата</th>
            <th>Количество записей</th>
            <th>Выручка (₽)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($report1 as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['day']) ?></td>
            <td><?= $row['cnt'] ?></td>
            <td><?= number_format($row['revenue'], 2) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Отчёт 2: рейтинг врачей -->
  <div class="card mb-4">
    <div class="card-header bg-success text-white">
      Отчёт 2: Рейтинг врачей по количеству записей
    </div>
    <div class="card-body">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Врач</th>
            <th>Специализация</th>
            <th>Количество записей</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($report2 as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['last_name'] . ' ' . $row['first_name']) ?></td>
            <td><?= htmlspecialchars($row['specialization']) ?></td>
            <td><?= $row['cnt'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Отчёт 3: отменённые записи -->
  <div class="card mb-4">
    <div class="card-header bg-danger text-white">
      Отчёт 3: Отменённые записи
    </div>
    <div class="card-body">
      <p>Всего отменённых записей: <strong><?= $report3 ?></strong></p>
    </div>
  </div>

  <a href="?entity=appointment&action=list" class="btn btn-secondary">Назад</a>
</div>
