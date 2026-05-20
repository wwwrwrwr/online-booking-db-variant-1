<?php
require_once __DIR__ . '/RepositoryException.php';

abstract class AbstractRepository
{
    protected PDO $pdo;
    protected string $table;
    protected string $primaryKey;

    public function __construct(PDO $pdo, string $table, string $primaryKey = 'id')
    {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->primaryKey = $primaryKey;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findAll(
        array $where = [],
        array $params = [],
        ?string $orderBy = null,
        ?int $limit = null
    ): array {
        $sql = "SELECT * FROM {$this->table}";
        $bindings = $params;
        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $column => $value) {
                $placeholder = ":w_{$column}";
                $conditions[] = "{$column} = {$placeholder}";
                $bindings[$placeholder] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        $allowedOrders = ['ASC', 'DESC'];
        if ($orderBy !== null && in_array(strtoupper($orderBy), $allowedOrders, true)) {
            $sql .= " ORDER BY created_at " . strtoupper($orderBy);
        }
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            $bindings[':limit'] = (int)$limit;
        }
        $stmt = $this->pdo->prepare($sql);
        foreach ($bindings as $key => $val) {
            $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id"
        );
        $result = $stmt->execute(['id' => $id]);
        if (!$result) {
            throw new RepositoryException("Не удалось удалить запись с ID {$id}");
        }
        return true;
    }
}
