<?php
// src/DentistRepository.php

class DentistRepository extends AbstractRepository
{
    protected string $table = 'dentists';
    protected string $primaryKey = 'dentist_id';

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'dentists', 'dentist_id');
    }

    public function findAll(
        array $where = [],
        array $params = [],
        ?string $orderBy = null,
        ?int $limit = null
    ): array {
        $order = $orderBy ?? 'dentist_id ASC';
        $sql = "SELECT * FROM {$this->table} ORDER BY {$order}";
        if ($limit) $sql .= " LIMIT {$limit}";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO dentists
                (last_name, first_name, specialization, phone, cabinet_number)
            VALUES
                (:last_name, :first_name, :specialization, :phone, :cabinet_number)
        ");
        $stmt->execute($data);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE dentists SET
                last_name = :last_name,
                first_name = :first_name,
                specialization = :specialization,
                phone = :phone,
                cabinet_number = :cabinet_number
            WHERE dentist_id = :id
        ");
        $data['id'] = $id;
        $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?"
        );
        $result = $stmt->execute([$id]);
        if (!$result) {
            throw new RepositoryException("Не удалось удалить запись с ID {$id}");
        }
        return true;
    }
}
