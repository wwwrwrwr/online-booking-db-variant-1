<?php
// controllers/DentistController.php

class DentistController
{
    private PDO $pdo;
    private DentistRepository $repo;
    private string $entity = 'dentist';

    public function __construct(PDO $pdo, string $entity)
    {
        $this->pdo  = $pdo;
        $this->repo = new DentistRepository($pdo);
        $this->entity = $entity;
    }

    public function handle(string $action, ?int $id = null): void
    {
        switch ($action) {
            case 'list':   $this->listAction();         break;
            case 'create': $this->createAction();       break;
            case 'edit':   $this->editAction($id);      break;
            case 'delete': $this->deleteAction($id);    break;
            default:
                http_response_code(404);
                echo "Действие не найдено";
        }
    }

    private function listAction(): void
    {
        $page      = max(1, (int)($_GET['page'] ?? 1));
        $limit     = 10;
        $orderBy   = $_GET['sort'] ?? 'dentist_id';
        $allowed   = ['dentist_id','last_name','first_name',
                      'specialization','phone','cabinet_number'];
        if (!in_array($orderBy, $allowed, true)) $orderBy = 'dentist_id';
        $direction = strtoupper($_GET['dir'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

        $dentists = $this->repo->findAll([], [], "$orderBy $direction", $limit);
        $total    = $this->pdo->query("SELECT COUNT(*) FROM dentists")->fetchColumn();
        $pages    = ceil($total / $limit);

        $title   = 'Справочник: Врачи';
        $content = $this->render('dentist/list', [
            'dentists'  => $dentists,
            'page'      => $page,
            'pages'     => $pages,
            'orderBy'   => $orderBy,
            'direction' => $direction,
            'entity'    => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function createAction(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlashMessage('error', 'Ошибка безопасности');
                header('Location: ?entity=dentist&action=list');
                exit;
            }
            $errors = $this->validateDentist($_POST);
            if (empty($errors)) {
                try {
                    $this->repo->create([
                        'last_name'      => trim($_POST['last_name']),
                        'first_name'     => trim($_POST['first_name']),
                        'specialization' => $_POST['specialization'],
                        'phone'          => trim($_POST['phone']),
                        'cabinet_number' => (int)$_POST['cabinet_number']
                    ]);
                    setFlashMessage('success', 'Врач добавлен');
                    header('Location: ?entity=dentist&action=list');
                    exit;
                } catch (RepositoryException $e) {
                    setFlashMessage('error', 'Ошибка: ' . $e->getMessage());
                }
            }
            $title   = 'Добавить врача';
            $content = $this->render('dentist/form', [
                'errors' => $errors,
                'old'    => $_POST,
                'isEdit' => false,
                'entity' => $this->entity
            ]);
            require __DIR__ . '/../views/layout.php';
            return;
        }

        $title   = 'Добавить врача';
        $content = $this->render('dentist/form', [
            'errors' => [],
            'old'    => [],
            'isEdit' => false,
            'entity' => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function editAction(?int $id): void
    {
        if ($id === null) { http_response_code(400); echo "Не указан ID"; return; }
        $dentist = $this->repo->findById($id);
        if (!$dentist) { http_response_code(404); echo "Врач не найден"; return; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlashMessage('error', 'Ошибка безопасности');
                header('Location: ?entity=dentist&action=list');
                exit;
            }
            $errors = $this->validateDentist($_POST);
            if (empty($errors)) {
                try {
                    $this->repo->update($id, [
                        'last_name'      => trim($_POST['last_name']),
                        'first_name'     => trim($_POST['first_name']),
                        'specialization' => $_POST['specialization'],
                        'phone'          => trim($_POST['phone']),
                        'cabinet_number' => (int)$_POST['cabinet_number']
                    ]);
                    setFlashMessage('success', 'Данные обновлены');
                    header('Location: ?entity=dentist&action=list');
                    exit;
                } catch (RepositoryException $e) {
                    setFlashMessage('error', 'Ошибка: ' . $e->getMessage());
                }
            }
            $title   = 'Редактировать врача';
            $content = $this->render('dentist/form', [
                'errors'  => $errors,
                'old'     => $_POST,
                'isEdit'  => true,
                'dentist' => $dentist,
                'entity'  => $this->entity
            ]);
            require __DIR__ . '/../views/layout.php';
            return;
        }

        $title   = 'Редактировать врача';
        $content = $this->render('dentist/form', [
            'errors'  => [],
            'old'     => $dentist,
            'isEdit'  => true,
            'dentist' => $dentist,
            'entity'  => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function deleteAction(?int $id): void
    {
        if ($id === null) { http_response_code(400); echo "Не указан ID"; return; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlashMessage('error', 'Ошибка безопасности');
                header('Location: ?entity=dentist&action=list');
                exit;
            }
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM appointments WHERE dentist_id = :id"
            );
            $stmt->execute(['id' => $id]);
            $count = (int)$stmt->fetchColumn();
            if ($count > 0) {
                setFlashMessage('error',
                    "Нельзя удалить: у врача есть записи на приём ($count шт.)");
                header('Location: ?entity=dentist&action=list');
                exit;
            }
            try {
                $this->repo->delete($id);
                setFlashMessage('success', 'Врач удалён');
            } catch (RepositoryException $e) {
                setFlashMessage('error', 'Ошибка: ' . $e->getMessage());
            }
            header('Location: ?entity=dentist&action=list');
            exit;
        }

        $dentist = $this->repo->findById($id);
        if (!$dentist) { http_response_code(404); echo "Врач не найден"; return; }

        $title   = 'Подтвердите удаление';
        $content = $this->render('dentist/delete', [
            'dentist' => $dentist,
            'entity'  => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function validateDentist(array $data): array
    {
        $errors = [];
        if (empty(trim($data['last_name'] ?? '')))
            $errors['last_name'] = 'Фамилия обязательна';
        if (empty(trim($data['first_name'] ?? '')))
            $errors['first_name'] = 'Имя обязательно';
        if (!in_array($data['specialization'] ?? '',
            ['терапевт','хирург','ортодонт'], true))
            $errors['specialization'] = 'Выберите специализацию';
        if (!preg_match('/^[\+]?[0-9\s\-\(\)]{10,20}$/', $data['phone'] ?? ''))
            $errors['phone'] = 'Неверный формат телефона';
        $cab = (int)($data['cabinet_number'] ?? 0);
        if ($cab < 1 || $cab > 20)
            $errors['cabinet_number'] = 'Номер кабинета от 1 до 20';
        return $errors;
    }

    private function render(string $view, array $data = []): string
    {
        extract($data);
        ob_start();
        require __DIR__ . "/../views/{$view}.php";
        return ob_get_clean();
    }
}
