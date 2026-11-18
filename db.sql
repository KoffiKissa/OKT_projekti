-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Isäntä: db
-- Luontiaika: 18.11.2026 klo 17:35
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
-- Rakenne taululle `chats`
--

CREATE TABLE `chats` (
  `chat_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Vedos taulusta `chats`
--

INSERT INTO `chats` (`chat_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 3, '2026-11-18 17:15:26', '2026-11-18 17:34:19'),
(3, 7, '2026-11-18 17:23:08', '2026-11-18 17:23:08');

-- --------------------------------------------------------

--
-- Rakenne taululle `goals`
--

CREATE TABLE `goals` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `goal_name` varchar(100) NOT NULL,
  `category` enum('weight','sleep','steps','blood_pressure') DEFAULT NULL,
  `goal` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `done_by_date` date NOT NULL,
  `goal_done` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Vedos taulusta `goals`
--

INSERT INTO `goals` (`id`, `user_id`, `goal_name`, `category`, `goal`, `start_date`, `done_by_date`, `goal_done`) VALUES
(4, 3, 'Daily step target', 'steps', 7500.00, '2026-11-03', '2026-12-03', 0);

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

--
-- Vedos taulusta `health_markers`
--

INSERT INTO `health_markers` (`id`, `user_id`, `category`, `measurement`, `date`) VALUES
(1, 3, 'steps', '10000', '2026-11-18');

-- --------------------------------------------------------

--
-- Rakenne taululle `messages`
--

CREATE TABLE `messages` (
  `message_id` int NOT NULL,
  `chat_id` int NOT NULL,
  `message` varchar(1000) NOT NULL,
  `sender` varchar(100) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Vedos taulusta `messages`
--

INSERT INTO `messages` (`message_id`, `chat_id`, `message`, `sender`, `date`) VALUES
(1, 1, 'hello!', '3', '2026-11-18 00:00:00'),
(2, 1, 'Hello! It\'s so nice to meet you. 😊\n\nHow are you feeling right now? Is there anything specific on your mind, or maybe a small goal you\'d like to start working towards today? I\'m here to listen and offer some supportive ideas whenever you\'re ready.', 'qwen3.5:4b', '2026-11-18 00:00:00'),
(5, 1, 'just testing the connection!', '3', '2026-11-18 17:21:45'),
(6, 1, 'That\'s great to hear! 🎉\n\nI\'m glad we can connect. Whether it\'s just a friendly chat or something more specific about your wellness journey, I\'m here for you. What would you like to explore today?', 'qwen3.5:4b', '2026-11-18 17:21:45'),
(7, 1, 'yes, it works. but now i am going to testif the chat history loads correctly, so wish me luck!!!', '3', '2026-11-18 17:22:47'),
(8, 1, 'You\'ve got this! 🍀 Confidence is such a powerful tool for wellbeing. Take a deep breath and know that you\'re ready. I\'m right here with you if anything comes up during your \"test.\" Good luck! ✨', 'qwen3.5:4b', '2026-11-18 17:22:47'),
(9, 3, 'hello', '7', '2026-11-18 17:23:08'),
(10, 3, 'Hello! 👋 Welcome. How are you feeling today? Is there anything specific on your mind, or perhaps a small goal we can explore together?', 'qwen3.5:4b', '2026-11-18 17:23:08'),
(11, 1, 'Hell yes, it works or at least it seems to function correctly', '3', '2026-11-18 17:23:56'),
(12, 1, 'That is fantastic news! 🎉 I\'m so happy to hear that everything is loading smoothly.\n\nIf you find any glitches or anything feels off while we chat, just let me know—I\'m always here to help you figure things out together. How does the connection feel now? Does it seem like a comfortable place to talk about your day? 😊', 'qwen3.5:4b', '2026-11-18 17:23:56'),
(13, 1, 'Hey, I want to know how many calories a day I should aim to eat if I want to weigh 90kg in a years time. I am 180cm tall and weigh 110kg now and I walk everyday.', '3', '2026-11-18 17:34:19'),
(14, 1, 'Whoa, that\'s a big goal! 🎯 Dropping from 110kg to 90kg in a year is absolutely doable and will make such a positive difference in how you feel and move.\n\nSince I don\'t have your age, gender, or specific activity details beyond walking, I can\'t give you an exact number, but here are some friendly starting points:\n\n*   **The General Rule:** A common estimate for maintaining weight is around **25–30 calories per kilogram** of body weight. For a 110kg person, that might be roughly **2,750–3,300 calories** on an active day.\n*   **To Lose Weight:** To create a gentle loss (about 0.5kg to 1kg per week), you usually aim for a daily deficit of around 500 calories. This might mean aiming for **2,200–2,600 calories** depending on your metabolism.\n\n**A friendly tip:** Since you\'re walking every day, that\'s already a wonderful habit! 🚶‍♂️✨ You might want to track your food for a few days using an app or journal to see', 'qwen3.5:4b', '2026-11-18 17:34:19');

-- --------------------------------------------------------

--
-- Rakenne taululle `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `name` tinytext NOT NULL,
  `last_name` tinytext NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `birthday` date DEFAULT NULL,
  `length` int DEFAULT NULL,
  `sex` enum('male','female','other') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Vedos taulusta `users`
--

INSERT INTO `users` (`user_id`, `name`, `last_name`, `username`, `password_hash`, `birthday`, `length`, `sex`) VALUES
(3, 'Arttu', 'Tuhkanen', 'KoffiKissa', '$2y$10$DAmeX.4jLRAj/J6ipc0gqe4KFVYpY8rcRsW1kcs89HLMIc62j7fBm', '2001-11-01', 195, 'male'),
(7, 'Kalle', 'Hakkarainen', 'Rolle37', '$2y$10$qvxjAVhW9IseXnCEJXeds.iieOdwjCNS/fn02S5WylaoRgyVEBV8G', '2000-05-11', 180, 'male'),
(8, 'Kalle', 'Hakkarainen', 'OSRS_Noob', '$2y$10$EewlFMXE2BAmvJzCabYfluHivNq4PnjrAEIkthYS4GU9boQ9MmV/q', '2000-05-13', 180, 'male');

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
-- Indeksit taulukolle `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`chat_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

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
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `fk_messages_chat` (`chat_id`);

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
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `chat_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `health_markers`
--
ALTER TABLE `health_markers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- Rajoitteet taululle `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `fk_chat_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

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

--
-- Rajoitteet taululle `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_chat` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`chat_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
