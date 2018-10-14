-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Oct 14, 2017 at 06:56 PM
-- Server version: 5.6.35
-- PHP Version: 7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eatt`
--
CREATE DATABASE IF NOT EXISTS `eatt` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `eatt`;

-- --------------------------------------------------------

--
-- Table structure for table `hosts`
--

DROP TABLE IF EXISTS `hosts`;
CREATE TABLE `hosts` (
  `user` int(10) NOT NULL,
  `location_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `hosts`
--

INSERT INTO `hosts` (`user`, `location_id`) VALUES
(108, '76910461cf6ac3541ddc42c921d4a2264eccb295'),
(109, '76910461cf6ac3541ddc42c921d4a2264eccb295'),
(107, '1a0f08fcbc047354782f00ab52e66fb56d1aadf7'),
(111, '1a0f08fcbc047354782f00ab52e66fb56d1aadf7'),
(110, '76910461cf6ac3541ddc42c921d4a2264eccb295');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE `locations` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`) VALUES
('088418ddc17fef2513462d92dbee1355929b35ed', 'Auckland, New Zealand'),
('1a0f08fcbc047354782f00ab52e66fb56d1aadf7', 'Moscow, Russia'),
('1b9ea3c094d3ac23c9a3afa8cd4d8a41f05de50a', 'San Francisco, CA, United States'),
('76910461cf6ac3541ddc42c921d4a2264eccb295', 'Tauranga, Bay Of Plenty, New Zealand'),
('c5153b48ff062dcbd5e6bbb77bcaa3afb7458147', 'Austin, TX, United States');

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

DROP TABLE IF EXISTS `meetings`;
CREATE TABLE `meetings` (
  `id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `host_id` int(11) NOT NULL,
  `meet_on` datetime NOT NULL,
  `proposal` text,
  `is_confirmed` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `meetings`
--

INSERT INTO `meetings` (`id`, `guest_id`, `host_id`, `meet_on`, `proposal`, `is_confirmed`) VALUES
(5, 108, 107, '2017-09-14 13:32:50', 'Каша-малаша', 1),
(6, 109, 108, '2017-11-25 13:33:07', 'Tom Yum', -1),
(7, 109, 108, '2017-10-25 13:36:13', 'Pumpkin soup. But Thai version with green curry. I\'ll need coconut milk and I have lemongrass, galangal and kafir with me!:))', 1),
(9, 110, 108, '2017-09-19 00:04:41', 'Tiramissu', 1),
(10, 108, 109, '2017-10-11 17:00:00', 'Pad thai tofu noodle', 0),
(12, 107, 108, '2017-10-27 00:59:00', 'vzxa', -1),
(13, 108, 108, '2017-10-27 10:00:00', 'jljflkasjflkasjdflkasjdlkfsdf', 0),
(14, 108, 110, '2017-10-20 00:00:00', 'I can cook everything, especially if it\'s Thai food. :))\r\n', 0),
(15, 127, 108, '2017-11-04 00:00:00', 'I can cook many salads, pad thai, kao phat, thai green curry, pumpkin soup, fried tofu and great coffee. Just let me in!!! :)', 0);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `chat` varchar(15) NOT NULL,
  `sender` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `text` text NOT NULL,
  `sent` datetime NOT NULL,
  `is_read` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `chat`, `sender`, `receiver`, `text`, `sent`, `is_read`) VALUES
(1, '108-109', 108, 109, 'Test message', '2017-09-19 23:58:47', 0),
(2, '108-109', 109, 108, 'Reply that first message', '2017-09-21 20:17:12', 1),
(3, '108-109', 108, 109, 'New message', '2017-09-21 20:17:35', 0),
(4, '108-109', 109, 108, 'My last reply to him', '2017-09-21 20:33:56', 1),
(5, '108-109', 108, 109, 'His last reply', '2017-09-21 20:34:53', 0),
(6, '108-109', 109, 108, 'ops', '2017-09-21 20:38:02', 0),
(7, '107-108', 107, 108, 'I wrote to myself', '2017-09-21 21:10:32', 1),
(9, '110-108', 110, 108, 'hmm.. who is it?', '2017-09-21 23:53:47', 1),
(10, '110-109', 110, 109, 'asdf to tim', '2017-09-22 01:18:48', 0),
(11, '107-108', 108, 107, 'Reply to myself', '2017-09-22 16:02:08', 1),
(20, '110-108', 108, 110, 'test', '2017-09-23 16:34:45', 1),
(21, '110-108', 108, 110, 'one more test', '2017-09-23 16:35:04', 1),
(22, '107-108', 108, 107, 'Еще ответ', '2017-09-23 16:36:08', 1),
(27, '107-108', 107, 108, 'asdf?', '2017-09-23 16:44:25', 1),
(28, '107-109', 107, 109, 'Anything!', '2017-09-24 15:37:28', 0),
(29, '107-108', 108, 107, '1234', '2017-09-25 23:28:04', 1),
(30, '107-108', 108, 107, 'asdf', '2017-09-26 00:16:37', 1),
(31, '110-108', 108, 110, 'asdf', '2017-09-27 06:13:07', 1),
(32, '110-108', 108, 110, 'Let\'s discuss all ingredients you need. I\'ll buy it. Just tell me how many galangal you need. 1kg is enough?', '2017-09-27 06:14:33', 1),
(34, '107-108', 107, 108, 'Eat this, man!', '2017-10-01 02:43:38', 1),
(35, '107-108', 108, 107, 'No way!!\r\n', '2017-10-08 04:49:20', 1),
(36, '107-108', 108, 107, 'gnfjh\r\n', '2017-10-09 14:26:10', 1),
(37, '110-108', 108, 110, 'jkhkj\r\n', '2017-10-09 14:56:26', 1),
(38, '127-108', 127, 108, 'Are you there? :)', '2017-10-13 20:39:52', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `user` int(11) NOT NULL,
  `msg` tinyint(1) DEFAULT NULL,
  `mtg` tinyint(1) DEFAULT NULL,
  `ref` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`user`, `msg`, `mtg`, `ref`) VALUES
(107, 1, 0, 0),
(108, 0, 0, 0),
(109, 1, 1, NULL),
(110, 1, 1, NULL),
(111, NULL, NULL, 0),
(119, 0, 0, 0),
(127, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

DROP TABLE IF EXISTS `photos`;
CREATE TABLE `photos` (
  `id` int(11) NOT NULL,
  `user` int(11) DEFAULT NULL,
  `ref` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `photos`
--

INSERT INTO `photos` (`id`, `user`, `ref`) VALUES
(2, 109, 'matureman1-512.png'),
(14, 107, '18013556_119660065255178_9196132323471392768_n.jpg'),
(16, 110, '20184785_1542194545837823_7508560744258994176_a.jpg'),
(18, 108, 'memeditate.jpg'),
(35, 108, 'Yo814FWI.jpg'),
(36, 108, '20181119_1833674413628266_491333641503244288_n.jpg'),
(38, 108, '18096473_406754843044059_5203578244627955712_n.jpg'),
(39, 127, 'hatsume_miku1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
CREATE TABLE `profile` (
  `user_id` int(11) NOT NULL,
  `photo_id` int(11) DEFAULT NULL,
  `info` text NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` tinyint(2) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `current_location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`user_id`, `photo_id`, `info`, `address`, `status`, `user_name`, `current_location`) VALUES
(108, 18, 'Can teach photo and photoshop skills, tao practices, scuba diving. Also I\'d love to practice painting or sculpture.. )) Any art events which may occur around - its great opportunity to feel and learn something!', '12a Seaspray dr.', 0, 'Alexander Tikhonov', NULL),
(109, 2, 'Biker, pilot, house owner. Real kiwi.', '12b Seaspray dr.', 0, 'Tim Brown', NULL),
(110, 16, 'Digital maverick', 'Homeless :-)', 0, 'Eddie Thomson', NULL),
(107, 14, 'Me as a guest', NULL, 0, 'Alex Tikonoff', NULL),
(111, NULL, 'Why Gerasim drown his Mumu?', 'Ferganskaya str., 9-1-24', 1, 'Gerasim', '76910461cf6ac3541ddc42c921d4a2264eccb295'),
(119, NULL, '', NULL, 0, 'tusha', NULL),
(120, NULL, '', NULL, 0, 'New Person', NULL),
(122, NULL, '', NULL, 0, 'New Person', NULL),
(123, NULL, '', NULL, 0, 'Tutanota', NULL),
(124, NULL, '', NULL, 0, 'test', NULL),
(125, NULL, '', NULL, 0, 'tikonoff', NULL),
(126, NULL, '', NULL, 0, 'eattogether', NULL),
(127, 39, '', 'no address', 0, 'Hatsune Miku', NULL),
(128, NULL, '', NULL, 0, 'Alex Tikonoff', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `refs`
--

DROP TABLE IF EXISTS `refs`;
CREATE TABLE `refs` (
  `user` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `ref` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `refs`
--

INSERT INTO `refs` (`user`, `author`, `ref`) VALUES
(109, 107, 'Tim is a really cool guy with a lot of stories and traveling experience.\r\nHe cooked best Tom Yum in my life, it was delicios!\r\nHe even brought galangal and kafir with him and I bought only veggies.\r\nThank you Alex!\r\nCome to see me again! ))'),
(108, 110, 'Alaverdy'),
(109, 108, 'I even lived in Tim\'s house for a while!'),
(108, 109, 'I hosted Alex for a year. He\'s good.'),
(108, 107, 'Yummie!!'),
(107, 108, 'Yummy porch!');

-- --------------------------------------------------------

--
-- Table structure for table `remembered_logins`
--

DROP TABLE IF EXISTS `remembered_logins`;
CREATE TABLE `remembered_logins` (
  `token_hash` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `remembered_logins`
--

INSERT INTO `remembered_logins` (`token_hash`, `user_id`, `expires_at`) VALUES
('00abe0059ee13cde77c3ad02b2ca8305f65449b953546f779e04f7eb7b1e3887', 108, '2017-11-11 20:47:05'),
('43a02828974edd44d89201d3b07506ecd74860913c4326a67cb9cff0e6b82e33', 108, '2017-11-01 14:43:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_hash` varchar(64) DEFAULT NULL,
  `password_reset_expires_at` datetime DEFAULT NULL,
  `activation_hash` varchar(64) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `password_reset_hash`, `password_reset_expires_at`, `activation_hash`, `is_active`) VALUES
(107, 'Alex Tikonoff', 'alex@tikonoff.com', '$2y$10$JQU7LppFXL7gXeSeIwnd0.WSBFiRg8EVSecRHsKjEOWUOAp8iXryW', NULL, NULL, '', 1),
(108, 'Alexander Tikhonov', 'tikonoff@gmail.com', '$2y$10$FZ2yHzN/CiUt6CGnNDZKF.Kkzx6ObaI/r3FTbA.X4JWt6LNKutBl6', NULL, NULL, NULL, 1),
(109, 'Tim Brown', 'tim@brown.com', '1234', NULL, NULL, NULL, 1),
(110, 'Eddie Thomson', 'asdf@aasdf.ru', '$2y$10$FZ2yHzN/CiUt6CGnNDZKF.Kkzx6ObaI/r3FTbA.X4JWt6LNKutBl6', NULL, NULL, NULL, 1),
(111, 'Gerasim', 'tu-sha@mail.su', '$2y$10$9DDWlV8H/KzFYHK2J3rQk.0V.L2C.iPFYmVc8bOwTAFlyyZJyvJh.', NULL, NULL, '129e5ee19090f2ae4d178249cfac795d44b22dbfc101a1b716948f10b3ab3bb8', 1),
(119, 'tusha', 'tu-sha@mail.rusa', '$2y$10$sgvWwnks9y1MJcYCYOmLre23uK8hb6ihAAvSuGIHzYTmIZunRaf/a', NULL, NULL, 'e747fc8086abda438ec95cb3d9792ef9ae9ac87da28d5a2458a68ceceadddffb', 1),
(122, 'New Person', 'tu-sha@mail.ru', '$2y$10$2sj5TIQ4oQ7mOEdPQHPgpuUQ8riPE/efqDkHWwzlY9Lv7Pwv2f5Ay', NULL, NULL, '900d75f309f0a06cb66615da56a53a9090448e3c12dccec1fd787a3d083325ad', NULL),
(123, 'Tutanota', 'zz6010704@tutanota.com', '$2y$10$b0uzHBIekzLbtDXGM04gjegHsoh0sUM6fg31EZhRxYo84Wu60fRmC', NULL, NULL, '31b3bb04619ab7bf578482d414b1de8b21e1d4bbccb8022acad6d503639f0657', NULL),
(124, 'test', 'test@gmail.com', '$2y$10$ZZoJ8Q13Kxw3LarryOVkZumHpEY5OdckGbpkZLQykSS3a3IXRSOBG', NULL, NULL, '7d1fb3a5e73862fba2f74b636076f203ea27645e59ddf70177dd6c6966e315b1', NULL),
(125, 'tikonoff', 'tikonoffnz@gmail.com', '$2y$10$cZOv9jyo.TLixWUiBHh6/ej9hHfac0U4xhfMKoO9v2nbhoqx6M8wS', NULL, NULL, 'a2a1b32b76b794e70130f01ab11f9c327962618090155d285a457a364a6f50cf', NULL),
(126, 'eattogether', 'eattogether@mail.ru', '$2y$10$iyQWHLWg5/YFu7Ueoy/u7e6H7PMU.nKULm4oREScoKQCQpqz7LAHe', NULL, NULL, '0578d6b50f475e47c34dc444b4f4200d1157f7cbad30b78c7752c94782b9fc5d', NULL),
(127, 'Hatsune Miku', 'tikonoff@yandex.ru', '$2y$10$ZeL0kIilHiMEoTdRYDRxGeGWHqSWXQLDp2h4VhfQesmXN4TXX1IfC', NULL, NULL, NULL, 1),
(128, 'Alex Tikonoff', 'tikonoff@gmail.com1', '$2y$10$s1zCSzYFD9Tmk9aWv/o8eevng3QHMYVLbbIJHRZxqmRVMvS75Sdzq', NULL, NULL, '3d7fef145f9b7ecac0ad0479bfd525a1d62495259c2f6777a4acca9521e2603e', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hosts`
--
ALTER TABLE `hosts`
  ADD KEY `user` (`user`),
  ADD KEY `location` (`location_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `meetings_id_uindex` (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `messages_id_uindex` (`id`),
  ADD KEY `message_from_users_id_fk` (`receiver`),
  ADD KEY `message_to_users_id_fk` (`sender`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`user`),
  ADD UNIQUE KEY `notifications_user_uindex` (`user`);

--
-- Indexes for table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `user` (`user`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD KEY `profile_locations_id_fk` (`current_location`);

--
-- Indexes for table `remembered_logins`
--
ALTER TABLE `remembered_logins`
  ADD PRIMARY KEY (`token_hash`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `password_reset_hash` (`password_reset_hash`),
  ADD UNIQUE KEY `activation_hash` (`activation_hash`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `hosts`
--
ALTER TABLE `hosts`
  ADD CONSTRAINT `hosts_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `hosts_locations_id_fk` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `message_from_users_id_fk` FOREIGN KEY (`receiver`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `message_to_users_id_fk` FOREIGN KEY (`sender`) REFERENCES `users` (`id`);

--
-- Constraints for table `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`);

--
-- Constraints for table `remembered_logins`
--
ALTER TABLE `remembered_logins`
  ADD CONSTRAINT `hnn` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
