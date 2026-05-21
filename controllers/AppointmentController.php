<?php
// controllers/AppointmentController.php

class AppointmentController
{
    private PDO $pdo;
    private AppointmentRepository $repo;
    private string $entity = 'appointment';

    public function __construct(PDO $pdo, string $entity)
    {
        $this->pdo  = $pdo;
        $this->repo = new AppointmentRepository($pdo);
        $this->entity = $entity;
    }

    public function handle(string $action, ?int $id = null): void
    {
        switch ($action) {
            case 'list':    $this->listAction();      break;
            case 'create':  $this->createAction();    break;
            case 'view':    $this->viewAction($id);   break;
            case 'cancel':  $this->cancelAction($id); break;
            case 'reports': $this->reportsAction();   break;
            default:
                http_response_code(404);
                echo "Действие не найдено";
        }
    }

    private function listAction(): void
    {
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo   = $_GET['date_to']   ?? '';
        $status   = $_GET['status']    ?? '';

        $sql = "
            SELECT
                a.appointment_id,
                a.appointment_datetime,
                a.status,
                c.last_name  AS client_last_name,
                c.first_name AS client_first_name,
                d.last_name  AS dentist_last_name,
                s.service_name
            FROM appointments a
            JOIN clients  c ON a.client_id  = c.client_id
            JOIN dentists d ON a.dentist_id = d.dentist_id
            JOIN services s ON a.service_id = s.service_id
            WHERE 1=1
        ";
        $params = [];

        if ($dateFrom !== '') {
            $sql .= " AND DATE(a.appointment_datetime) >= :date_from";
            $params['date_from'] = $dateFrom;
        }
        if ($dateTo !== '') {
            $sql .= " AND DATE(a.appointment_datetime) <= :date_to";
            $params['date_to'] = $dateTo;
        }
        if ($status !== '') {
            $sql .= " AND a.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY a.appointment_datetime DESC LIMIT 20";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $title   = 'Записи на приём';
        $content = $this->render('appointment/list', [
            'appointments' => $appointments,
            'entity'       => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function viewAction(?int $id): void
    {
        if ($id === null) {
            http_response_code(400); echo "Не указан ID"; return;
        }

        $stmt = $this->pdo->prepare("
            SELECT
                a.*,
                c.last_name  AS client_last_name,
                c.first_name AS client_first_name,
                c.phone      AS client_phone,
                c.email      AS client_email,
                d.last_name  AS dentist_last_name,
                d.first_name AS dentist_first_name,
                d.specialization,
                d.cabinet_number,
                s.service_name,
                s.price,
                s.duration_minutes
            FROM appointments a
            JOIN clients  c ON a.client_id  = c.client_id
            JOIN dentists d ON a.dentist_id = d.dentist_id
            JOIN services s ON a.service_id = s.service_id
            WHERE a.appointment_id = :id
        ");
        $stmt->execute(['id' => $id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$appointment) {
            http_response_code(404); echo "Запись не найдена"; return;
        }

        $title   = 'Просмотр записи';
        $content = $this->render('appointment/view', [
            'appointment' => $appointment,
            'entity'      => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function createAction(): void
    {
        $clients  = $this->pdo->query("SELECT client_id, last_name, first_name FROM clients ORDER BY last_name")->fetchAll(PDO::FETCH_ASSOC);
        $dentists = $this->pdo->query("SELECT dentist_id, last_name, first_name, specialization FROM dentists ORDER BY last_name")->fetchAll(PDO::FETCH_ASSOC);
        $services = $this->pdo->query("SELECT service_id, service_name, price, duration_minutes FROM services ORDER BY service_name")->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
                $_SESSION['flash_error'] = 'Ошибка безопасности';
                header('Location: ?entity=appointment&action=list');
                exit;
            }

            $errors = $this->validateAppointment($_POST);
            if (empty($errors)) {
                $stmt = $this->pdo->prepare("
                    SELECT COUNT(*) FROM appointments
                    WHERE dentist_id = :dentist_id
                    AND appointment_datetime = :datetime
                ");
                $stmt->execute([
                    'dentist_id' => (int)$_POST['dentist_id'],
                    'datetime'   => $_POST['appointment_datetime']
                ]);
                if ((int)$stmt->fetchColumn() > 0) {
                    $errors['appointment_datetime'] = 'Это время уже занято у данного врача';
                }
            }

            if (empty($errors)) {
                try {
                    $stmt = $this->pdo->prepare("
                        INSERT INTO appointments
                            (client_id, dentist_id, service_id, appointment_datetime, status)
                        VALUES
                            (:client_id, :dentist_id, :service_id, :appointment_datetime, 'запланировано')
                    ");
                    $stmt->execute([
                        'client_id'            => (int)$_POST['client_id'],
                        'dentist_id'           => (int)$_POST['dentist_id'],
                        'service_id'           => (int)$_POST['service_id'],
                        'appointment_datetime' => $_POST['appointment_datetime']
                    ]);
                    $_SESSION['flash_success'] = 'Запись создана';
                    header('Location: ?entity=appointment&action=list');
                    exit;
                } catch (Exception $e) {
                    $_SESSION['flash_error'] = 'Ошибка: ' . $e->getMessage();
                }
            }

            $title   = 'Создать запись';
            $content = $this->render('appointment/form', [
                'errors'   => $errors,
                'old'      => $_POST,
                'clients'  => $clients,
                'dentists' => $dentists,
                'services' => $services,
                'entity'   => $this->entity
            ]);
            require __DIR__ . '/../views/layout.php';
            return;
        }

        $title   = 'Создать запись';
        $content = $this->render('appointment/form', [
            'errors'   => [],
            'old'      => [],
            'clients'  => $clients,
            'dentists' => $dentists,
            'services' => $services,
            'entity'   => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function cancelAction(?int $id): void
    {
        if ($id === null) {
            http_response_code(400); echo "Не указан ID"; return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
                $_SESSION['flash_error'] = 'Ошибка безопасности';
                header('Location: ?entity=appointment&action=list');
                exit;
            }

            $stmt = $this->pdo->prepare("
                UPDATE appointments SET status = 'отменено'
                WHERE appointment_id = :id
                AND status = 'запланировано'
            ");
            $stmt->execute(['id' => $id]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['flash_success'] = 'Запись отменена';
            } else {
                $_SESSION['flash_error'] = 'Нельзя отменить эту запись';
            }
            header('Location: ?entity=appointment&action=list');
            exit;
        }

        $stmt = $this->pdo->prepare("
            SELECT a.*, c.last_name, c.first_name
            FROM appointments a
            JOIN clients c ON a.client_id = c.client_id
            WHERE a.appointment_id = :id
        ");
        $stmt->execute(['id' => $id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$appointment) {
            http_response_code(404); echo "Запись не найдена"; return;
        }

        $title   = 'Отмена записи';
        $content = $this->render('appointment/cancel', [
            'appointment' => $appointment,
            'entity'      => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function reportsAction(): void
    {
        $report1 = $this->pdo->query("
            SELECT
                DATE(a.appointment_datetime) AS day,
                COUNT(*) AS cnt,
                SUM(s.price) AS revenue
            FROM appointments a
            JOIN services s ON a.service_id = s.service_id
            WHERE a.status != 'отменено'
            GROUP BY DATE(a.appointment_datetime)
            ORDER BY day
        ")->fetchAll(PDO::FETCH_ASSOC);

        $report2 = $this->pdo->query("
            SELECT
                d.last_name, d.first_name, d.specialization,
                COUNT(a.appointment_id) AS cnt
            FROM dentists d
            LEFT JOIN appointments a ON d.dentist_id = a.dentist_id
            GROUP BY d.dentist_id
            ORDER BY cnt DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $report3 = $this->pdo->query("
            SELECT COUNT(*) FROM appointments WHERE status = 'отменено'
        ")->fetchColumn();

        $title   = 'Отчёты';
        $content = $this->render('appointment/reports', [
            'report1' => $report1,
            'report2' => $report2,
            'report3' => $report3,
            'entity'  => $this->entity
        ]);
        require __DIR__ . '/../views/layout.php';
    }

    private function validateAppointment(array $data): array
    {
        $errors = [];
        if (empty($data['client_id']))
            $errors['client_id'] = 'Выберите клиента';
        if (empty($data['dentist_id']))
            $errors['dentist_id'] = 'Выберите врача';
        if (empty($data['service_id']))
            $errors['service_id'] = 'Выберите услугу';
        if (empty($data['appointment_datetime']))
            $errors['appointment_datetime'] = 'Укажите дату и время';
        elseif ($data['appointment_datetime'] < date('Y-m-d H:i:s'))
            $errors['appointment_datetime'] = 'Дата не может быть в прошлом';
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
