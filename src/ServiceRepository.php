<?php
require_once 'AbstractRepository.php';

class ServiceRepository extends AbstractRepository
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'services', 'service_id');
    }

    public function findByName(string $name): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM services WHERE service_name = :name");
        $stmt->execute(['name' => $name]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->pdo->prepare("INSERT INTO services ($columns) VALUES ($placeholders)");
        if (!$stmt->execute($data)) {
            throw new RepositoryException("Ошибка создания услуги");
        }
        return (int)$this->pdo->lastInsertId();
    }
}
