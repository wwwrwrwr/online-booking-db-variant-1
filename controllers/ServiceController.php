<?php
// controllers/ServiceController.php

class ServiceController
{
    private PDO $pdo;
    private ServiceRepository $repo;
    private string $entity = 'service';

    public function __construct(PDO $pdo, string $entity)
    {
        $this->pdo    = $pdo;
        $this->repo   = new ServiceRepository($pdo);
        $this->entity = $entity;
    }

    public function handle(string $action, ?int $id = null): void
    {
        switch ($action) {
            case 'list':   $this->listAction();      break;
            case 'create': $this->createAction();    break;
            case 'edit':   $this->editAction($id);   break;
            case 'delete': $this->deleteAction($id); break;
            default:
                http_response_code(404);
                echo "Действие не найдено";
        }
    }

    private function listAction(): void
    {
        $page      = max(1, (int)($_GET['page'] ?? 1));
        $limit     = 10;
        $orderBy   = $_GET['sort'] ?? 'service_id';
        $allowed   = ['service_id','service_name','price','duration_minutes'];
        if (!in_array($orderBy, $allowed, true)) $orderBy = 'service_id';
        $direction = strtoupper($_GET['dir'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

        $services = $this->repo->findAll([], [], "$orderBy $direction", $limit);
        $total    = $this->pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
        $pages    = ceil($total / $limit);

        $title   = 'Справочник: Услуги';
        $content = $this->render('service/list', [
            'services'  => $services,
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
                header('Location: ?entity=service&action=list');
                exit;
            }
            $errors = $this->validateService($_POST);
            if (empty($errors)) {
                try {
                    $this->repo->create([
                        'service_name'     => trim($_POST['service_name']),
                        'price'            => (float)$_POST['price'],
                        'duration_minutes' => (int)$_POST['duration_minutes']
                    ]);
                    setFlashMessage('success', 'Услуга добавлена');
                    header('Location: ?entity=service&action=list');
                    exit;
                } catch (RepositoryException $e) {
                    setFlashMessage('error', 'Ошибка: ' . $e->getMessage());
                }
            }
            $title   = 'Добавить услугу';
            $content = $this->render('service/form', [
                'errors' => $errors,
                'old'    => $_POST,
                'isEdit' => false,
                'entity' => $this->entity
            ]);
            require __DIR__ . '/../views/layout.php';
            return;
        }

        $title   = 'Добавить услугу';
        $content = $this->render('service/form', [
            'errors' => [],
            'old'    => [],
            'isEdit' => false,
            'entity' => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function editAction(?int $id): void
    {
        if ($id === null) {
            http_response_code(400); echo "Не указан ID"; return;
        }
        $service = $this->repo->findById($id);
        if (!$service) {
            http_response_code(404); echo "Услуга не найдена"; return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlashMessage('error', 'Ошибка безопасности');
                header('Location: ?entity=service&action=list');
                exit;
            }
            $errors = $this->validateService($_POST);
            if (empty($errors)) {
                try {
                    $stmt = $this->pdo->prepare("
                        UPDATE services SET
                            service_name     = :service_name,
                            price            = :price,
                            duration_minutes = :duration_minutes
                        WHERE service_id = :id
                    ");
                    $stmt->execute([
                        'service_name'     => trim($_POST['service_name']),
                        'price'            => (float)$_POST['price'],
                        'duration_minutes' => (int)$_POST['duration_minutes'],
                        'id'               => $id
                    ]);
                    setFlashMessage('success', 'Услуга обновлена');
                    header('Location: ?entity=service&action=list');
                    exit;
                } catch (RepositoryException $e) {
                    setFlashMessage('error', 'Ошибка: ' . $e->getMessage());
                }
            }
            $title   = 'Редактировать услугу';
            $content = $this->render('service/form', [
                'errors'  => $errors,
                'old'     => $_POST,
                'isEdit'  => true,
                'service' => $service,
                'entity'  => $this->entity
            ]);
            require __DIR__ . '/../views/layout.php';
            return;
        }

        $title   = 'Редактировать услугу';
        $content = $this->render('service/form', [
            'errors'  => [],
            'old'     => $service,
            'isEdit'  => true,
            'service' => $service,
            'entity'  => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function deleteAction(?int $id): void
    {
        if ($id === null) {
            http_response_code(400); echo "Не указан ID"; return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlashMessage('error', 'Ошибка безопасности');
                header('Location: ?entity=service&action=list');
                exit;
            }
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM appointments WHERE service_id = :id"
            );
            $stmt->execute(['id' => $id]);
            $count = (int)$stmt->fetchColumn();
            if ($count > 0) {
                setFlashMessage('error',
                    "Нельзя удалить: услуга используется в $count записях");
                header('Location: ?entity=service&action=list');
                exit;
            }
            try {
                $this->repo->delete($id);
                setFlashMessage('success', 'Услуга удалена');
            } catch (RepositoryException $e) {
                setFlashMessage('error', 'Ошибка: ' . $e->getMessage());
            }
            header('Location: ?entity=service&action=list');
            exit;
        }

        $service = $this->repo->findById($id);
        if (!$service) {
            http_response_code(404); echo "Услуга не найдена"; return;
        }

        $title   = 'Подтвердите удаление';
        $content = $this->render('service/delete', [
            'service' => $service,
            'entity'  => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function validateService(array $data): array
    {
        $errors = [];
        if (empty(trim($data['service_name'] ?? '')))
            $errors['service_name'] = 'Название обязательно';
        $price = (float)($data['price'] ?? 0);
        if ($price <= 0)
            $errors['price'] = 'Цена должна быть больше 0';
        $dur = (int)($data['duration_minutes'] ?? 0);
        if ($dur < 15 || $dur > 180)
            $errors['duration_minutes'] = 'Длительность от 15 до 180 минут';
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
