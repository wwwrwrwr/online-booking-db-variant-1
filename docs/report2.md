# Отчёт: Уровень доступа к данным (Data Access Layer)

## 1. Архитектура решения
Уровень доступа к данным реализован на основе паттерна Repository. Абстрактный класс `AbstractRepository` инкапсулирует общую логику работы с PDO (подключение, базовые SELECT/DELETE, безопасная сборка WHERE и ORDER BY). Для каждой предметной таблицы создан наследник (`ClientRepository`, `AppointmentRepository` и др.), добавляющий бизнес-специфичные методы. Класс `Database` управляет соединением через Singleton, гарантируя единственное активное PDO-подключение на время выполнения скрипта.

## 2. Диаграмма классов (UML)
```plantuml
@startuml
class Database {
  - static ?PDO $instance
  + static getConnection(): PDO
}
class AbstractRepository {
  # PDO $pdo
  # string $table
  # string $primaryKey
  + findById(int): ?array
  + findAll(array, ?string, ?int): array
  + delete(int): bool
}
class RepositoryException extends Exception
class ClientRepository extends AbstractRepository {
  + findByPhone(string): ?array
  + findByEmail(string): ?array
  + create(array): int
}
class AppointmentRepository extends AbstractRepository {
  + getAppointmentsByDate(string): array
  + getFreeSlots(int, string): array
  + createAppointment(array): int
  + updateStatus(int, string): bool
}
Database --> AbstractRepository : использует
AbstractRepository <|-- ClientRepository
AbstractRepository <|-- AppointmentRepository
@enduml
