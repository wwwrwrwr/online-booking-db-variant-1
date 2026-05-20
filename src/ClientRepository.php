<?php
require_once 'AbstractRepository.php';

class ClientRepository extends AbstractRepository
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'clients', 'client_id');
    }

    public function findByPhone(string $phone): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM clients WHERE phone = :phone");
        $stmt->execute(['phone' => $phone]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM clients WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->pdo->prepare("INSERT INTO clients ($columns) VALUES ($placeholders)");
        if (!$stmt->execute($data)) {
            throw new RepositoryException("Ошибка создания клиента");
        }
        return (int)$this->pdo->lastInsertId();
    }
}
