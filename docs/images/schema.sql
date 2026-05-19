-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Май 19 2026 г., 05:39
-- Версия сервера: 8.0.34-26-beget-1-1
-- Версия PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `w92350sl_1`
--

-- --------------------------------------------------------

--
-- Структура таблицы `clients`
--
-- Создание: Май 19 2026 г., 01:52
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `client_id` int NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `patronymic` varchar(50) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `birth_date` date NOT NULL,
  `birth_day` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `clients`
--

INSERT INTO `clients` (`client_id`, `last_name`, `first_name`, `patronymic`, `phone`, `email`, `birth_date`, `birth_day`) VALUES
(1, 'Иванов', 'Иван', 'Иванович', '+79123456789', 'ivanov@example.com', '1985-05-15', '0000-00-00'),
(2, 'Петрова', 'Мария', 'Сергеевна', '+79224567890', 'petrova@example.com', '1992-11-23', '0000-00-00'),
(3, 'Сидоров', 'Алексей', 'Владимирович', '+79335678901', 'sidorov@example.com', '1978-03-02', '0000-00-00'),
(4, 'Козлова', 'Елена', 'Анатольевна', '+79446789012', 'kozlovae@example.com', '2000-07-19', '0000-00-00'),
(5, 'Морозов', 'Дмитрий', 'Павлович', '+79557890123', 'morozov@example.com', '1995-12-01', '0000-00-00');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `clients`
--
ALTER TABLE `clients`
  MODIFY `client_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
