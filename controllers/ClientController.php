<?php
// controllers/ClientController.php

class ClientController
{
    private PDO $pdo;
    private ClientRepository $repo;
    private string $entity = 'client';

    public function __construct(PDO $pdo, string $entity)
    {
        $this->pdo = $pdo;
        $this->repo = new ClientRepository($pdo);
        $this->entity = $entity;
    }

    public function handle(string $action, ?int $id = null): void
    {
        switch ($action) {
            case 'list':
                $this->listAction();
                break;
            case 'create':
                $this->createAction();
                break;
            case 'edit':
                $this->editAction($id);
                break;
            case 'delete':
                $this->deleteAction($id);
                break;
            default:
                http_response_code(404);
                echo "Действие не найдено";
        }
    }

    private function listAction(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $orderBy = $_GET['sort'] ?? 'client_id';
        $allowedSort = ['client_id', 'last_name', 'first_name', 'phone', 'email', 'birth_date'];
        if (!in_array($orderBy, $allowedSort, true)) {
            $orderBy = 'client_id';
        }
        $direction = strtoupper($_GET['dir'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

        $clients = $this->repo->findAll([], [], "$orderBy $direction", $limit);
        $total = $this->pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
        $pages = ceil($total / $limit);

        $title = 'Справочник: Клиенты';
        $content = $this->render('client/list', [
            'clients' => $clients,
            'page' => $page,
            'pages' => $pages,
            'orderBy' => $orderBy,
            'direction' => $direction,
            'entity' => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function createAction(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlashMessage('error', 'Ошибка безопасности');
                header('Location: ?entity=client&action=list');
                exit;
            }

            $errors = $this->validateClient($_POST);
            if (empty($errors)) {
                try {
                    $this->repo->create([
                        'last_name' => trim($_POST['last_name']),
                        'first_name' => trim($_POST['first_name']),
                        'patronymic' => trim($_POST['patronymic'] ?? ''),
                        'phone' => trim($_POST['phone']),
                        'email' => trim($_POST['email']),
                        'birth_date' => $_POST['birth_date']
                    ]);
                    setFlashMessage('success', 'Клиент успешно добавлен');
                    header('Location: ?entity=client&action=list');
                    exit;
                } catch (RepositoryException $e) {
                    setFlashMessage('error', 'Ошибка: ' . $e->getMessage());
                }
            } else {
                $title = 'Добавить клиента';
                $content = $this->render('client/form', [
                    'errors' => $errors,
                    'old' => $_POST,
                    'isEdit' => false,
                    'entity' => $this->entity
                ]);
                require __DIR__ . '/../views/layout.php';
                return;
            }
        }

        $title = 'Добавить клиента';
        $content = $this->render('client/form', [
            'errors' => [],
            'old' => [],
            'isEdit' => false,
            'entity' => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function editAction(?int $id): void
    {
        if ($id === null) {
            http_response_code(400);
            echo "Не указан ID";
            return;
        }

        $client = $this->repo->findById($id);
        if (!$client) {
            http_response_code(404);
            echo "Клиент не найден";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlashMessage('error', 'Ошибка безопасности');
                header('Location: ?entity=client&action=list');
                exit;
            }

            $errors = $this->validateClient($_POST);
            if (empty($errors)) {
                try {
                    $stmt = $this->pdo->prepare("
                        UPDATE clients SET
                            last_name = :last_name,
                            first_name = :first_name,
                            patronymic = :patronymic,
                            phone = :phone,
                            email = :email,
                            birth_date = :birth_date
                        WHERE client_id = :id
                    ");
                    $stmt->execute([
                        'last_name' => trim($_POST['last_name']),
                        'first_name' => trim($_POST['first_name']),
                        'patronymic' => trim($_POST['patronymic'] ?? ''),
                        'phone' => trim($_POST['phone']),
                        'email' => trim($_POST['email']),
                        'birth_date' => $_POST['birth_date'],
                        'id' => $id
                    ]);
                    setFlashMessage('success', 'Данные обновлены');
                    header('Location: ?entity=client&action=list');
                    exit;
                } catch (RepositoryException $e) {
                    setFlashMessage('error', 'Ошибка: ' . $e->getMessage());
                }
            } else {
                $title = 'Редактировать клиента';
                $content = $this->render('client/form', [
                    'errors' => $errors,
                    'old' => $_POST,
                    'isEdit' => true,
                    'client' => $client,
                    'entity' => $this->entity
                ]);
                require __DIR__ . '/../views/layout.php';
                return;
            }
        }

        $title = 'Редактировать клиента';
        $content = $this->render('client/form', [
            'errors' => [],
            'old' => $client,
            'isEdit' => true,
            'client' => $client,
            'entity' => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function deleteAction(?int $id): void
    {
        if ($id === null) {
            http_response_code(400);
            echo "Не указан ID";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlashMessage('error', 'Ошибка безопасности');
                header('Location: ?entity=client&action=list');
                exit;
            }

            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM appointments WHERE client_id = :id"
            );
            $stmt->execute(['id' => $id]);
            $count = (int)$stmt->fetchColumn();

            if ($count > 0) {
                setFlashMessage('error',
                    "Нельзя удалить: у клиента есть записи на приём ($count шт.)");
                header('Location: ?entity=client&action=list');
                exit;
            }

            try {
                $this->repo->delete($id);
                setFlashMessage('success', 'Клиент удалён');
            } catch (RepositoryException $e) {
                setFlashMessage('error', 'Ошибка: ' . $e->getMessage());
            }
            header('Location: ?entity=client&action=list');
            exit;
        }

        $client = $this->repo->findById($id);
        if (!$client) {
            http_response_code(404);
            echo "Клиент не найден";
            return;
        }

        $title = 'Подтвердите удаление';
        $content = $this->render('client/delete', [
            'client' => $client,
            'entity' => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function validateClient(array $data): array
    {
        $errors = [];

        if (empty(trim($data['last_name'] ?? ''))) {
            $errors['last_name'] = 'Фамилия обязательна';
        }
        if (empty(trim($data['first_name'] ?? ''))) {
            $errors['first_name'] = 'Имя обязательно';
        }
        if (!preg_match('/^[\+]?[0-9\s\-\(\)]{10,20}$/', $data['phone'] ?? '')) {
            $errors['phone'] = 'Неверный формат телефона';
        }
        if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Неверный формат email';
        }
        if (empty($data['birth_date']) || $data['birth_date'] > date('Y-m-d')) {
            $errors['birth_date'] = 'Дата рождения не может быть в будущем';
        }
        if (!empty($data['birth_date'])) {
            $age = date_diff(new DateTime($data['birth_date']), new DateTime())->y;
            if ($age < 18) {
                $errors['birth_date'] = 'Клиент должен быть старше 18 лет';
            }
        }

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
