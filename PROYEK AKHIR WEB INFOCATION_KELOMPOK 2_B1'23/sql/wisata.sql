-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 03, 2024 at 03:56 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wisata`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE comments (
  id int NOT NULL,
  post_id int NOT NULL,
  user_id int NOT NULL,
  comment text NOT NULL,
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `comment`, `created_at`) VALUES
(3, 1, 7, 'svasfdbadfb', '2024-10-30 03:38:06'),
(4, 1, 4, 'bagus banget lur\r\n', '2024-10-30 03:39:10');

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE `photos` (
  `id` int NOT NULL,
  `post_id` int NOT NULL,
  `photo_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `photos`
--

INSERT INTO `photos` (`id`, `post_id`, `photo_path`) VALUES
(1, 1, 'uploads/Screenshot 2024-09-07 084239.png'),
(2, 4, 'uploads/8fc99470d0a9ca7d91e0c3b7448c1c50.jpg'),
(3, 6, 'uploads/2d996882ac6620475b267c0724cef7aa.jpg'),
(4, 8, 'uploads/553dacde4578e343c5869e05e2074fe1.jpg'),
(5, 10, 'uploads/bugbear rogue.jpg'),
(6, 12, 'uploads/#仮面ライダーアマゾンズ アマゾンオメガ - harymachinegunのイラスト - pixiv.jpeg'),
(7, 13, 'uploads/671f2b321bea6.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `body`, `created_at`) VALUES
(1, 3, 'liburan', 'wfWGWRG', '2024-10-28 01:51:31'),
(2, 3, 'sdfsVS', 'WRAGAERG', '2024-10-28 01:54:49'),
(3, 3, 'rgeqgqer', 'eqrgqe', '2024-10-28 01:57:04'),
(4, 3, 'rgeqgqer', 'eqrgqe', '2024-10-28 01:57:04'),
(5, 3, 'wfvsav', 'aegerg', '2024-10-28 02:00:17'),
(6, 3, 'wfvsav', 'aegerg', '2024-10-28 02:00:17'),
(7, 3, 'agasge', 'aergaeg', '2024-10-28 02:01:26'),
(8, 3, 'agasge', 'aergaeg', '2024-10-28 02:01:26'),
(9, 3, 'wGFSGR', 'GAEGET', '2024-10-28 02:04:55'),
(10, 3, 'wGFSGR', 'GAEGET', '2024-10-28 02:04:55'),
(11, 1, 'Makan lek', 'iuaghaeiogeio', '2024-10-28 04:30:31'),
(12, 1, 'Makan lek', 'iuaghaeiogeio', '2024-10-28 04:30:31'),
(13, 1, 'egneoi', 'epoggde', '2024-10-28 06:12:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `profile_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `profile_photo`) VALUES
(1, 'budi', 'budi@gmail.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'user', NULL),
(2, 'ana', 'anna@gmail.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'user', NULL),
(3, 'riski', 'riski@gmail.com', '6460662e217c7a9f899208dd70a2c28abdea42f128666a9b78e6c0c064846493', 'user', NULL),
(4, 'mamat', 'mamat@gmail.com', '$2y$10$dQbznuL1HZ3L0JYXO0avTusFAxVSI1QEYa4L7L9J4XY3snFLjHXvG', 'user', 'uploads/cbc6ef571454c3f2a0fbb400bcbebb78.jpg'),
(5, 'ujang', 'ujang@gmail.com', '$2y$10$/wabRA7vrsl0meOyffH2mOs2R7H5YgbqSw92qKD.B.zCIgfz4xLOC', 'user', NULL),
(6, 'adit', 'adit@gmail.com', '$2y$10$HoxWCq1IH74ogonfknPpwuCLkxGgD0V/fn4YsnsR46CSpgx.ff4im', 'user', NULL),
(7, 'bebek', 'bebek@gmail.com', '$2y$10$kZwRXBGH7vjnnG1GrV8yhOmDMIjFC6MOicwhNt.tnUpwCDWg6dIgq', 'user', NULL),
(8, 'raihan', 'raihan@gmail.com', '$2y$10$0Pvb.KA7rHWCnisVXEqqLeuLCds02rQ8/fea3V.h/M2MWEwrXUXae', 'user', NULL),
(9, 'yanto', 'yanto@gmail.com', '$2y$10$YR9iQFD3WdOaPf5cFWSd9eTFdfP4CNVTpetGYThpBj6DdbKJXvfTi', 'user', NULL),
(10, 'rusdi', 'rusdi@gmail.com', '$2y$10$ir9VaKGXBr9/PfUtp2wrdeHyu1S.J9VjXaC3gbpVSDnJ1SuxV/PUO', 'user', NULL),
(11, 'fadhil', 'fadhilnur246@gmail.com', '$2y$10$4YfGHAsOXfdtohyu1bI4wOLsxzW3dj2lFTAjpwTyg2RkUs86XjEM2', 'user', NULL),
(12, 'admin_username', 'admin@example.com', '$2y$10$L9sIO2BlB7PbbNc7iRtn8O.8zz6sLz/BRVtf5FAwA9FChFbAbzDZW\r\n', 'admin', NULL),
(13, 'admin123', 'admin@gmail.com', '$2y$10$vqGC8mrCorByuzSYKYaPIelxaIWeozvT8sTB.vwVvyxv77vdX5V1C', 'admin', NULL),
(14, 'rizka', 'rizka@gmail.com', '$2y$10$vkoYymdhVYr0pL11O88msu8IompgMOK3Kw0cWVZi9t6fiFXp8e4IC', 'user', NULL),
(16, 'admin321', 'admin@admin.com', '$2y$10$vkoYymdhVYr0pL11O88msu8IompgMOK3Kw0cWVZi9t6fiFXp8e4IC', 'admin', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int NOT NULL,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `vote_type` enum('upvote','downvote') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `post_id`, `user_id`, `vote_type`) VALUES
(9, 13, 7, 'upvote');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_posts` (`user_id`,`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `post_id` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
