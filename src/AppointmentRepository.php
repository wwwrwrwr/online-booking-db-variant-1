<?php
require_once 'AbstractRepository.php';

class AppointmentRepository extends AbstractRepository
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'appointments', 'appointment_id');
    }

    public function getAppointmentsByDate(string $date): array
    {
        $stmt = $this->pdo->prepare("
            SELECT a.*, c.last_name, c.first_name, d.last_name AS dentist_last, s.service_name 
            FROM appointments a
            JOIN clients c ON a.client_id = c.client_id
            JOIN dentists d ON a.dentist_id = d.dentist_id
            JOIN services s ON a.service_id = s.service_id
            WHERE DATE(a.appointment_datetime) = :date
        ");
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll();
    }

    public function getFreeSlots(int $dentistId, string $date): array
    {
        $stmt = $this->pdo->prepare("
            SELECT appointment_datetime FROM appointments 
            WHERE dentist_id = :dentist_id AND DATE(appointment_datetime) = :date AND status != 'отменено'
        ");
        $stmt->execute(['dentist_id' => $dentistId, 'date' => $date]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function createAppointment(array $data): int
    {
        $this->pdo->beginTransaction();
        try {
            // Проверка занятости слота
            $check = $this->pdo->prepare("SELECT 1 FROM appointments WHERE dentist_id = :dentist_id AND appointment_datetime = :datetime AND status != 'отменено'");
            $check->execute(['dentist_id' => $data['dentist_id'], 'datetime' => $data['appointment_datetime']]);
            if ($check->fetch()) {
                throw new RepositoryException("Врач уже занят в указанное время");
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO appointments (client_id, dentist_id, service_id, appointment_datetime, status) 
                VALUES (:client_id, :dentist_id, :service_id, :datetime, :status)
            ");
            $stmt->execute([
                'client_id' => $data['client_id'],
                'dentist_id' => $data['dentist_id'],
                'service_id' => $data['service_id'],
                'datetime' => $data['appointment_datetime'],
                'status' => $data['status'] ?? 'запланировано'
            ]);

            $this->pdo->commit();
            return (int)$this->pdo->lastInsertId();
        } catch (PDOException | RepositoryException $e) {
            $this->pdo->rollBack();
            throw new RepositoryException("Ошибка создания записи: " . $e->getMessage());
        }
    }

    public function updateStatus(int $id, string $status): bool
    {
        $allowed = ['запланировано', 'проведено', 'отменено'];
        if (!in_array($status, $allowed, true)) {
            throw new RepositoryException("Недопустимый статус");
        }
        $stmt = $this->pdo->prepare("UPDATE appointments SET status = :status WHERE appointment_id = :id");
        if (!$stmt->execute(['status' => $status, 'id' => $id])) {
            throw new RepositoryException("Не удалось обновить статус");
        }
        return true;
    }
}
