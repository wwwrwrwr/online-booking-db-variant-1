<?php
// views/client/form.php
// Форма создания/редактирования клиента
?>
<div class="card">
    <div class="card-header">
        <h4><?= $isEdit ? 'Редактировать' : 'Добавить' ?> клиента</h4>
    </div>
    <div class="card-body">
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token"
       value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Фамилия <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" required>
                    <?php if (isset($errors['last_name'])): ?>
                        <div class="form-error"><?= htmlspecialchars($errors['last_name']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Имя <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" required>
                    <?php if (isset($errors['first_name'])): ?>
                        <div class="form-error"><?= htmlspecialchars($errors['first_name']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Отчество</label>
                    <input type="text" name="patronymic" class="form-control" 
                           value="<?= htmlspecialchars($old['patronymic'] ?? '') ?>">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Телефон <span class="text-danger">*</span></label>
                    <input type="tel" name="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($old['phone'] ?? '') ?>" 
                           pattern="^[\+]?[0-9\s\-\(\)]{10,20}$" required>
                    <?php if (isset($errors['phone'])): ?>
                        <div class="form-error"><?= htmlspecialchars($errors['phone']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="form-error"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Дата рождения <span class="text-danger">*</span></label>
                    <input type="date" name="birth_date" class="form-control <?= isset($errors['birth_date']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($old['birth_date'] ?? '') ?>" max="<?= date('Y-m-d') ?>" required>
                    <?php if (isset($errors['birth_date'])): ?>
                        <div class="form-error"><?= htmlspecialchars($errors['birth_date']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-success"><?= $isEdit ? 'Сохранить' : 'Создать' ?></button>
                <a href="?entity=<?= $entity ?>&action=list" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</div>
