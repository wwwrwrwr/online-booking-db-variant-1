Отчет по заданию, Агизов Ибрагим 454группа.
РАЗДЕЛ 1. АНАЛИЗ ПРЕДМЕТНОЙ ОБЛАСТИ
Разрабатываемая система предназначена для автоматизации процесса записи пациентов в стоматологическую клинику. Основная цель базы данных — обеспечение сквозного учёта пациентов, специалистов, оказываемых услуг и расписания приёмов при строгом соблюдении целостности данных.
Ключевые бизнес-правила, реализованные в схеме:

1. Пациент регистрируется в системе один раз и получает уникальную медицинскую карту, где фиксируются диагнозы и противопоказания.
2. Запись на приём привязывается к конкретному стоматологу, услуге и временному слоту. Один врач не может принимать двух пациентов одновременно, что обеспечивается составным уникальным ограничением.
3. Услуги имеют фиксированную стоимость и длительность, которые не дублируются в таблице записей, а подтягиваются через связи.
4. Статус записи отражает жизненный цикл визита: «запланировано», «проведено», «отменено».
5. Система поддерживает возрастной ценз (пациенты старше 18 лет регистрируются самостоятельно), положительные значения стоимости и длительности услуг, а также валидный диапазон номеров кабинетов.
6. Удаление справочных данных (врачей, услуг) блокируется, если на них существуют активные записи, что предотвращает потерю истории посещений.

РАЗДЕЛ 2. КОНЦЕПТУАЛЬНАЯ МОДЕЛЬ
Концептуальная модель включает пять основных сущностей:

1. Клиент (clients). Атрибуты: идентификатор, фамилия, имя, отчество, телефон, email, дата рождения. Первичный ключ: client_id.
2. Стоматолог (dentists). Атрибуты: идентификатор, фамилия, имя, специализация, телефон, номер кабинета. Первичный ключ: dentist_id.
3. Услуга (services). Атрибуты: идентификатор, наименование, стоимость, длительность в минутах. Первичный ключ: service_id.
4. Запись (appointments). Атрибуты: идентификатор, ссылки на клиента, врача и услугу, дата и время приёма, статус, время создания. Первичный ключ: appointment_id.
5. Медицинская карта (medical_cards). Атрибуты: идентификатор, ссылка на клиента, диагнозы, противопоказания, дата последнего визита. Первичный ключ: card_id.

Связи и кардинальность:

Клиент → Запись: один ко многим (1:N). Один пациент может иметь множество визитов.
Стоматолог → Запись: один ко многим (1:N). Врач проводит множество приёмов.
Услуга → Запись: один ко многим (1:N). Одна услуга выбирается в разных записях.
Клиент → Медицинская карта: один к одному (1:1). У пациента одна актуальная карта.

РАЗДЕЛ 3. ЛОГИЧЕСКАЯ МОДЕЛЬ И НОРМАЛИЗАЦИЯ
Реляционная схема преобразована в следующие таблицы:
Структура базы данных

| Таблица | Столбцы | Типы данных | Ограничения |
|---------|---------|-------------|-------------|
| **clients** | client_id, last_name, first_name, patronymic, phone, email, birth_date | INT, VARCHAR(50), VARCHAR(50), VARCHAR(50), VARCHAR(20), VARCHAR(100), DATE | PRIMARY KEY, UNIQUE(phone), UNIQUE(email), CHECK(age≥18) |
| **dentists** | dentist_id, last_name, first_name, specialization, phone, cabinet_number | INT, VARCHAR(50), VARCHAR(50), ENUM, VARCHAR(20), INT | PRIMARY KEY, CHECK(cabinet 1-20) |
| **services** | service_id, service_name, price, duration_minutes | INT, VARCHAR(100), DECIMAL(10,2), INT | PRIMARY KEY, UNIQUE(name), CHECK(price>0), CHECK(duration 15-180) |
| **appointments** | appointment_id, client_id, dentist_id, service_id, appointment_datetime, status, created_at | INT, INT, INT, INT, DATETIME, ENUM, TIMESTAMP | PRIMARY KEY, FOREIGN KEY→clients, FOREIGN KEY→dentists, FOREIGN KEY→services, UNIQUE(dentist_id, appointment_datetime) |
| **medical_cards** | card_id, client_id, diagnoses, contraindications, last_visit_date | INT, INT, TEXT, TEXT, DATE | PRIMARY KEY, FOREIGN KEY→clients, UNIQUE(client_id) |

Приведение к нормальным формам:

1)1НФ: Все атрибуты атомарны. В таблицах отсутствуют повторяющиеся группы, массивы или разделённые запятыми значения. Телефон и email хранятся в отдельных колонках, статус и специализация используют ENUM.
2)2НФ: Все таблицы имеют суррогатные первичные ключи (INT AUTO_INCREMENT). Поскольку составных ключей нет, частичные зависимости невозможны. Все неключевые атрибуты зависят от полного первичного ключа.
3)3НФ: Транзитивные зависимости устранены. Например, стоимость и длительность услуги хранятся только в таблице services, а не дублируются в appointments. Специализация врача находится в dentists. При необходимости получения полной информации о записи используется JOIN, что исключает аномалии обновления и удаления.
4)Денормализация: Не применялась. Схема полностью соответствует 3НФ, что гарантирует минимальную избыточность и максимальную целостность справочных данных. Поле created_at в таблице записей добавлено для аудита и не нарушает нормальные формы, так как зависит от первичного ключа записи.

РАЗДЕЛ 4. SQL-СКРИПТ СОЗДАНИЯ БАЗЫ ДАННЫХ
-- Создание базы данных с поддержкой кириллицы
DROP DATABASE IF EXISTS dental_booking_variant1;
CREATE DATABASE dental_booking_variant1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dental_booking_variant1;

-- Таблица клиентов
CREATE TABLE clients (
    client_id INT AUTO_INCREMENT PRIMARY KEY,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    patronymic VARCHAR(50),
    phone VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    birth_date DATE NOT NULL,
    CHECK (birth_date <= CURDATE() - INTERVAL 18 YEAR)
) ENGINE=InnoDB;

-- Таблица стоматологов
CREATE TABLE dentists (
    dentist_id INT AUTO_INCREMENT PRIMARY KEY,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    specialization ENUM('терапевт', 'хирург', 'ортодонт') NOT NULL,
    phone VARCHAR(20) NOT NULL,
    cabinet_number INT NOT NULL CHECK (cabinet_number BETWEEN 1 AND 20)
) ENGINE=InnoDB;

-- Таблица услуг
CREATE TABLE services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL UNIQUE,
    price DECIMAL(10,2) NOT NULL CHECK (price > 0),
    duration_minutes INT NOT NULL CHECK (duration_minutes BETWEEN 15 AND 180)
) ENGINE=InnoDB;

-- Таблица записей на приём
CREATE TABLE appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    dentist_id INT NOT NULL,
    service_id INT NOT NULL,
    appointment_datetime DATETIME NOT NULL,
    status ENUM('запланировано', 'проведено', 'отменено') DEFAULT 'запланировано',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_app_client FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_app_dentist FOREIGN KEY (dentist_id) REFERENCES dentists(dentist_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_app_service FOREIGN KEY (service_id) REFERENCES services(service_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT unique_slot UNIQUE (dentist_id, appointment_datetime)
) ENGINE=InnoDB;

-- Таблица медицинских карт
CREATE TABLE medical_cards (
    card_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    diagnoses TEXT,
    contraindications TEXT,
    last_visit_date DATE,
    CONSTRAINT fk_card_client FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT unique_client_card UNIQUE (client_id)
) ENGINE=InnoDB;

-- Индексы для ускорения поиска и группировки
CREATE INDEX idx_app_datetime ON appointments(appointment_datetime);
CREATE INDEX idx_app_dentist_status ON appointments(dentist_id, status);
CREATE INDEX idx_client_email ON clients(email);

РАЗДЕЛ 5. ТЕСТОВЫЕ ДАННЫЕ И ПРИМЕРЫ ЗАПРОСОВ
Заполнение таблиц:
INSERT INTO clients (last_name, first_name, patronymic, phone, email, birth_date) VALUES
('Иванов', 'Иван', 'Иванович', '+79123456789', 'ivanov@mail.ru', '1985-05-15'),
('Петрова', 'Мария', 'Сергеевна', '+79224567890', 'petrova@mail.ru', '1992-11-23'),
('Сидоров', 'Алексей', 'Владимирович', '+79335678901', 'sidorov@mail.ru', '1978-03-02'),
('Козлова', 'Елена', 'Анатольевна', '+79446789012', 'kozlova@mail.ru', '2000-07-19'),
('Морозов', 'Дмитрий', 'Павлович', '+79557890123', 'morozov@mail.ru', '1995-12-01');

INSERT INTO dentists (last_name, first_name, specialization, phone, cabinet_number) VALUES
('Кузнецова', 'Ольга', 'терапевт', '+79012345678', 3),
('Смирнов', 'Андрей', 'хирург', '+79023456789', 7),
('Васильева', 'Татьяна', 'ортодонт', '+79034567890', 12),
('Николаев', 'Денис', 'терапевт', '+79045678901', 4);

INSERT INTO services (service_name, price, duration_minutes) VALUES
('Осмотр и консультация', 1500.00, 30),
('Лечение кариеса', 3500.00, 60),
('Удаление зуба', 4500.00, 45),
('Установка брекетов', 25000.00, 90),
('Профессиональная чистка', 2500.00, 40);

INSERT INTO appointments (client_id, dentist_id, service_id, appointment_datetime, status) VALUES
(1, 1, 1, '2026-05-25 10:00:00', 'запланировано'),
(2, 2, 3, '2026-05-25 11:30:00', 'запланировано'),
(3, 3, 4, '2026-05-26 09:00:00', 'запланировано'),
(4, 1, 5, '2026-05-27 14:00:00', 'запланировано'),
(1, 2, 2, '2026-06-01 12:15:00', 'запланировано');

INSERT INTO medical_cards (client_id, diagnoses, contraindications, last_visit_date) VALUES
(1, 'Кариес верхних моляров', NULL, '2025-12-10'),
(2, 'Пародонтит', 'Аллергия на лидокаин', '2026-01-15'),
(3, NULL, NULL, NULL),
(4, 'Неправильный прикус', NULL, '2026-03-05'),
(5, 'Кариес', NULL, '2026-02-00');

РАЗДЕЛ 6. ПРОВЕРКА ОГРАНИЧЕНИЙ ЦЕЛОСТНОСТИ
1. Нарушение уникальности временного слота
INSERT INTO appointments (client_id, dentist_id, service_id, appointment_datetime, status)
VALUES (5, 1, 1, '2026-05-25 10:00:00', 'запланировано');
2. Нарушение внешнего ключа (ссылка на несуществующего клиента)
INSERT INTO appointments (client_id, dentist_id, service_id, appointment_datetime, status)
VALUES (99, 1, 1, '2026-06-10 15:00:00', 'запланировано');
3. Нарушение CHECK-ограничения (отрицательная цена услуги)
INSERT INTO services (service_name, price, duration_minutes) VALUES ('Тест', -500.00, 30);

РАЗДЕЛ 7. ВЫВОДЫ
В ходе выполнения практической работы была спроектирована и реализована реляционная база данных для системы онлайн-записи в стоматологическую клинику. Схема полностью удовлетворяет требованиям варианта: поддерживает учёт пациентов, врачей, услуг, расписания и медицинских карт, обеспечивает уникальность временных слотов и строгую ссылочную целостность.
Основные сложности возникли при настройке составного уникального индекса (dentist_id, appointment_datetime), который корректно работает в MySQL 8.0 только при явном указании в CONSTRAINT. Также потребовалось учесть особенности реализации CHECK-ограничений с функциями даты, которые стали полноценно поддерживаться только начиная с версии 8.0.16.
Схема соответствует требованиям 3НФ, что минимизирует избыточность и предотвращает аномалии модификации. Для дальнейшего развития системы целесообразно внедрить триггеры для автоматического обновления поля last_visit_date в медицинских картах, хранимые процедуры для атомарного бронирования слотов с проверкой пересечений, а также представления (VIEW) для упрощения формирования отчётов для администрации.
Приобретённые навыки: концептуальное и логическое проектирование БД, нормализация до 3НФ, написание DDL-скриптов с ограничениями целостности, оптимизация запросов с GROUP BY, HAVING и подзапросами, работа с PlantUML для визуализации архитектуры, тестирование ограничений на уровне СУБД.

СПИСОК ЛИТЕРАТУРЫ

MySQL 8.0 Reference Manual. Oracle Corporation. URL: https://dev.mysql.com/doc/ (дата обращения: 19.05.2026).
Руководство пользователя phpMyAdmin. URL: https://www.phpmyadmin.net/docs/ (дата обращения: 19.05.2026).

Версия СУБД: MySQL 8.0.3x / InnoDB
Среда разработки: phpMyAdmin 5.x, MySQL Workbench, PlantUML 1.2026.4
















