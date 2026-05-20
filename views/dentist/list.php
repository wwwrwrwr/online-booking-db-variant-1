<div class="container mt-4">
  <h2>Врачи</h2>
  <a href="?entity=dentist&action=create" class="btn btn-primary mb-3">
    + Добавить врача
  </a>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Фамилия</th>
        <th>Имя</th>
        <th>Специализация</th>
        <th>Телефон</th>
        <th>Кабинет</th>
        <th>Действия</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($dentists as $d): ?>
      <tr>
        <td><?= $d['dentist_id'] ?></td>
        <td><?= htmlspecialchars($d['last_name']) ?></td>
        <td><?= htmlspecialchars($d['first_name']) ?></td>
        <td><?= htmlspecialchars($d['specialization']) ?></td>
        <td><?= htmlspecialchars($d['phone']) ?></td>
        <td><?= $d['cabinet_number'] ?></td>
        <td>
          <a href="?entity=dentist&action=edit&id=<?= $d['dentist_id'] ?>"
             class="btn btn-sm btn-warning">Изменить</a>
          <a href="?entity=dentist&action=delete&id=<?= $d['dentist_id'] ?>"
             class="btn btn-sm btn-danger">Удалить</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
