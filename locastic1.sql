-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 03, 2022 at 10:02 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `locastic`
--

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20211228182139', '2021-12-28 19:22:19', 1773),
('DoctrineMigrations\\Version20211229080118', '2021-12-29 09:01:31', 8499),
('DoctrineMigrations\\Version20220101020239', '2022-01-01 03:03:53', 694);

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT '(DC2Type:datetime_immutable)',
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `user_id`, `title`, `slug`, `content`, `created_at`, `modified_at`) VALUES
(2, 1, 'CircleCI PhpUnit run test', 'circleci-phpunit-run-test-published-120221', '#!/bin/bash -eo pipefail\r\nphp ./bin/phpunit\r\nPHPUnit 9.5.11 by Sebastian Bergmann and contributors.\r\n\r\nTesting \r\n.................................................                 49 / 49 (100%)\r\n\r\nTime: 00:00.017, Memory: 10.00 MB\r\n\r\nOK (49 tests, 71 assertions)\r\n\r\nRemaining indirect deprecation notices (1)\r\n\r\n  1x: The \"DAMA\\DoctrineTestBundle\\Doctrine\\DBAL\\AbstractStaticDriver\" class implements \"Doctrine\\DBAL\\VersionAwarePlatformDriver\" that is deprecated All drivers will have to be aware of the server version in the next major release.\r\n    1x in PHPUnitExtension::executeBeforeFirstTest from DAMA\\DoctrineTestBundle\\PHPUnit\r\n\r\nCircleCI received exit code 0', '2021-12-31 14:02:05', '2022-01-03 08:19:16'),
(3, 1, 'api array call for reading now patching after putting', 'api-array-call-for-reading-published-1220215', 'Reading\r\nNow we will make a function that will retrieve one of our Customer records from the database when you provide an ID number in URL:', '2021-12-31 14:36:23', '2022-01-01 03:16:49'),
(7, 1, 'Hello Joel Okoromi! ✅', 'hello-joel-okoromi-published-120216', 'Hello Joel Okoromi! ✅\r\nYou\'re welcome! ✅\r\n\r\nYou are logged in as okmarq@gmail.com, Logout\r\nThis friendly message is coming from Locastic Blog API:\r\n\r\nGo to your dashboard page to see our blog posts\r\nyou can create read, update and delete your own posts', '2022-01-01 12:04:00', '2022-01-01 12:04:00'),
(8, 1, 'Configuring Heroku', 'configuring-heroku-published-120221', 'Oops! An Error Occurred\r\nThe server returned a \"500 Internal Server Error\".\r\nSomething is broken. Please let us know what you were doing when this error occurred. We will fix it as soon as possible. Sorry for any inconvenience caused.', '2022-01-03 08:10:06', '2022-01-03 08:10:06');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `role_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `role_name`) VALUES
(1, 'ROLE_ADMIN'),
(3, 'ROLE_BLOGGER'),
(2, 'ROLE_USER');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `name`) VALUES
(1, 'Verification requested'),
(2, 'Approved'),
(3, 'Denied');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `firstname`, `lastname`) VALUES
(1, 'okmarq@gmail.com', '[]', '$2y$13$Hq6lFfEUPX9r8gN492B4EOg7M2/RSkUeWDxkW0Pege0Z8woVfMd9y', 'Joel', 'Okoromi'),
(2, 'marsdevs@gmail.com', '[]', '$2y$13$/1z00NAam8jkR4fkd84/.ukmG48UhT7TUTxKSWjIWHedgdq9EoVPm', 'Marquis', 'Okoromi'),
(3, 'marvisj@gmail.com', '[]', '$2y$13$1/Fg/j78bYNgLYjXdgQwy.4fRyWudN9NiKfwwzND.yn6t8az4Xj0y', 'Marvelous', 'Okoromi'),
(5, 'admin@admin.com', '[]', '$2y$13$yqBfJDDo2VulI2.OzEKkq.oc1sxmIaXFLv7xgRP.7WV38iaS7utu.', 'AdminF', 'AdminL'),
(6, 'user@user.com', '[]', '$2y$13$kOF4s62dGPUH3YDLKWghXeRbUkMbfYxD1uxfAqXKcVABdhuzEPLM.', 'UserF', 'UserL'),
(7, 'blogger@blogger.com', '[]', '$2y$13$KQo11VyeUTZeA5pxsQkkauJ7/6nuNXJ.M4Yoh0EH0Z1vGwkNetLy2', 'BloggerF', 'BloggerL');

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`user_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 2),
(3, 2),
(5, 1),
(5, 2),
(5, 3),
(6, 2),
(7, 2),
(7, 3);

-- --------------------------------------------------------

--
-- Table structure for table `vrequest`
--

CREATE TABLE `vrequest` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `id_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT '(DC2Type:datetime_immutable)',
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vrequest`
--

INSERT INTO `vrequest` (`id`, `user_id`, `status_id`, `id_image`, `message`, `reason`, `created_at`, `modified_at`) VALUES
(1, 1, 2, 'assignment-61cf365c28742.png', 'I\'d like to request the role of blogger edited', 'All checks were verified and you passed.', '2021-12-29 13:58:01', '2021-12-31 17:57:00'),
(9, 3, 3, 'assignment-61cf3e4548762.png', 'I\'d like to request the role of blogger', 'we apologize, requirement check not met', '2021-12-31 18:30:45', '2022-01-03 09:59:59'),
(10, 2, 1, 'w-61cf3e93626b5.png', 'I\'d like to request the role of blogger', 'Request was reversed by the admin', '2021-12-31 18:32:03', '2022-01-03 09:58:09'),
(11, 7, 2, 'Screenshot-2021-11-15-142348-61d2b06d4159e.png', 'I\'d like to request the role of blogger', 'All checks were verified and you passed.', '2022-01-03 09:14:37', '2022-01-03 09:36:57'),
(12, 5, 2, 'warv-61d2b75660ec0.png', 'I\'d like to request the role of blogger', 'All checks were verified and you passed.', '2022-01-03 09:44:06', '2022-01-03 09:48:11'),
(13, 6, 3, 'pw-61d2b7dc05ee9.png', 'I\'d like to request the role of blogger', 'All checks were verified and you passed.', '2022-01-03 09:46:20', '2022-01-03 09:55:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_9B62` (`slug`),
  ADD KEY `IDX_D395` (`user_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_0C92` (`role_name`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_7C74` (`email`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `IDX_D395` (`user_id`),
  ADD KEY `IDX_22AC` (`role_id`);

--
-- Indexes for table `vrequest`
--
ALTER TABLE `vrequest`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_D395` (`user_id`),
  ADD KEY `IDX_00BD` (`status_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vrequest`
--
ALTER TABLE `vrequest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `FK_5A8A6C8DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `FK_2DE8C6A3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_2DE8C6A3D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vrequest`
--
ALTER TABLE `vrequest`
  ADD CONSTRAINT `FK_A9E6179F6BF700BD` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`),
  ADD CONSTRAINT `FK_A9E6179FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
