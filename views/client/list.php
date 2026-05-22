<?php

// views/client/list.php

// Страница списка клиентов

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Клиенты</h2>
    <a href="?entity=<?= $entity ?>&action=create" class="btn btn-primary">+ Добавить</a>

</div>



<!-- Поиск и фильтры -->

<form class="row g-2 mb-3" method="get">
    <input type="hidden" name="entity" value="<?= $entity ?>">
    <input type="hidden" name="action" value="list">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Поиск по фамилии..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-secondary">Найти</button>
    </div>

</form>



<!-- Таблица -->

<?php if (empty($clients)): ?>
    <div class="alert alert-info">Нет записей</div>

<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th><a href="?entity=<?= KaTeX parse error: Expected 'EOF', got '&' at position 9: entity ?&̲gt;&amp;action=…orderBy === 'client_id' && $direction === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
                    <th><a href="?entity=<?= KaTeX parse error: Expected 'EOF', got '&' at position 9: entity ?&̲gt;&amp;action=…orderBy === 'last_name' && $direction === 'ASC' ? 'DESC' : 'ASC' ?>">Фамилия</a></th>
                    <th><a href="?entity=<?= KaTeX parse error: Expected 'EOF', got '&' at position 9: entity ?&̲gt;&amp;action=…orderBy === 'first_name' && $direction === 'ASC' ? 'DESC' : 'ASC' ?>">Имя</a></th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Дата рождения</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (clientsasc): ?>
                <tr>
                    <td><?= htmlspecialchars($c['client_id']) ?></td>
                    <td><?= htmlspecialchars($c['last_name']) ?></td>
                    <td><?= htmlspecialchars($c['first_name']) ?></td>
                    <td><?= htmlspecialchars($c['phone']) ?></td>
                    <td><?= htmlspecialchars($c['email']) ?></td>
                    <td><?= htmlspecialchars($c['birth_date']) ?></td>
                    <td>
                        <a href="?entity=<?= KaTeX parse error: Expected 'EOF', got '&' at position 9: entity ?&̲gt;&amp;action=…c['client_id'] ?>" class="btn btn-sm btn-info">Просмотр</a>
                        <a href="?entity=<?= KaTeX parse error: Expected 'EOF', got '&' at position 9: entity ?&̲gt;&amp;action=…c['client_id'] ?>" class="btn btn-sm btn-warning">Изменить</a>
                        <a href="?entity=<?= KaTeX parse error: Expected 'EOF', got '&' at position 9: entity ?&̲gt;&amp;action=…c['client_id'] ?>" class="btn btn-sm btn-danger">Удалить</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>



<!-- Пагинация -->
    <?php if ($pages > 1): ?>
    <nav>
        <ul class="pagination">
            <?php for (p=1;p <= pages;p++): ?>
            <li class="page-item <?= p===page ? 'active' : '' ?>">
                <a class="page-link" href="?entity=<?= KaTeX parse error: Expected 'EOF', got '&' at position 9: entity ?&̲gt;&amp;action=…p ?>&sort=<?= KaTeX parse error: Expected 'EOF', got '&' at position 10: orderBy ?&̲gt;&amp;dir=&lt…direction ?>"><?= $p ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>

<?php endif; ?>
