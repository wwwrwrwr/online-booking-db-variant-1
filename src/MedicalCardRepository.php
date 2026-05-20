<?php
require_once 'AbstractRepository.php';

class MedicalCardRepository extends AbstractRepository
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'medical_cards', 'card_id');
    }

    public function findByClientId(int $clientId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM medical_cards WHERE client_id = :client_id");
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetch() ?: null;
    }

    public function updateContraindications(int $clientId, string $text): bool
    {
        $stmt = $this->pdo->prepare("UPDATE medical_cards SET contraindications = :text, last_visit_date = CURDATE() WHERE client_id = :client_id");
        return $stmt->execute(['text' => $text, 'client_id' => $clientId]);
    }
}
