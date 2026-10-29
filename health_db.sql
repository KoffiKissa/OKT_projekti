-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Isäntä: db
-- Luontiaika: 29.10.2026 klo 16:47
-- Palvelimen versio: 8.0.46
-- PHP-versio 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Tietokanta: `health_db`
--
CREATE DATABASE IF NOT EXISTS `health_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `health_db`;

-- --------------------------------------------------------

--
-- Rakenne taululle `achievements`
--

CREATE TABLE `achievements` (
  `achievement_id` int NOT NULL,
  `achievement_criteria` varchar(255) NOT NULL,
  `achievement_name` varchar(50) NOT NULL,
  `achievement_category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `achievements_user`
--

CREATE TABLE `achievements_user` (
  `user_id` int DEFAULT NULL,
  `achievement_id` int DEFAULT NULL,
  `achievement_completed` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `goals`
--

CREATE TABLE `goals` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `category` enum('weight','sleep','steps','blood_pressure') DEFAULT NULL,
  `goal` decimal(10,2) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Vedos taulusta `goals`
--

INSERT INTO `goals` (`id`, `user_id`, `category`, `goal`, `date`) VALUES
(4, 3, 'steps', 7500.00, '2026-11-03');

-- --------------------------------------------------------

--
-- Rakenne taululle `health_markers`
--

CREATE TABLE `health_markers` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `category` enum('weight','sleep','steps','blood_pressure') DEFAULT NULL,
  `measurement` varchar(255) DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `messages`
--

CREATE TABLE `messages` (
  `chat_id` int NOT NULL,
  `message` varchar(255) NOT NULL,
  `sender` enum('user','bot') NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `name` tinytext NOT NULL,
  `last_name` tinytext NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Vedos taulusta `users`
--

INSERT INTO `users` (`user_id`, `name`, `last_name`, `username`, `password_hash`) VALUES
(3, 'Arttu', 'Tuhkanen', 'KoffiKissa', '$2y$10$DAmeX.4jLRAj/J6ipc0gqe4KFVYpY8rcRsW1kcs89HLMIc62j7fBm');

--
-- Indexes for dumped tables
--

--
-- Indeksit taulukolle `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`achievement_id`),
  ADD UNIQUE KEY `achievement_name` (`achievement_name`);

--
-- Indeksit taulukolle `achievements_user`
--
ALTER TABLE `achievements_user`
  ADD KEY `fk_user3` (`user_id`),
  ADD KEY `fk_achievement` (`achievement_id`);

--
-- Indeksit taulukolle `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user1` (`user_id`);

--
-- Indeksit taulukolle `health_markers`
--
ALTER TABLE `health_markers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user2` (`user_id`);

--
-- Indeksit taulukolle `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`chat_id`);

--
-- Indeksit taulukolle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `achievement_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `health_markers`
--
ALTER TABLE `health_markers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `chat_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Rajoitteet vedostauluille
--

--
-- Rajoitteet taululle `achievements_user`
--
ALTER TABLE `achievements_user`
  ADD CONSTRAINT `fk_achievement` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`achievement_id`),
  ADD CONSTRAINT `fk_user3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Rajoitteet taululle `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `fk_user1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Rajoitteet taululle `health_markers`
--
ALTER TABLE `health_markers`
  ADD CONSTRAINT `fk_user2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
