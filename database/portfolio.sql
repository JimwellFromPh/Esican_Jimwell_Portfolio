-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2026 at 11:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portfolio`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `issuer` varchar(150) DEFAULT NULL,
  `date_received` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`id`, `title`, `issuer`, `date_received`, `description`) VALUES
(1, 'Continuous Learning and Skill Development', 'School Activities and Self-Learning', 'Present', 'Developing skills in web development, programming, graphic design, and digital media through school activities, practical projects, online learning, and hands-on practice.');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$caK98fvcIinG4n7HDVk0ce5BcVccFm8J5JX/ryVtGS/FNazAPd5sm', '2026-11-18 16:12:21');

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int(11) NOT NULL,
  `school` varchar(150) NOT NULL,
  `degree` varchar(150) DEFAULT NULL,
  `start_year` varchar(10) DEFAULT NULL,
  `end_year` varchar(10) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `school`, `degree`, `start_year`, `end_year`, `description`) VALUES
(1, 'Negros Oriental State University - Bayawan Sta. Catalina Campus', 'Bachelor of Science in Information Technology', '2024', 'Present', 'Currently studying as a Third Year Information Technology Student.');

-- --------------------------------------------------------

--
-- Table structure for table `experience`
--

CREATE TABLE `experience` (
  `id` int(11) NOT NULL,
  `position` varchar(150) NOT NULL,
  `company` varchar(150) DEFAULT NULL,
  `start_date` varchar(20) DEFAULT NULL,
  `end_date` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `experience`
--

INSERT INTO `experience` (`id`, `position`, `company`, `start_date`, `end_date`, `description`) VALUES
(1, 'Graphic Design & Digital Design', 'Academic Projects', '2024', 'Present', 'Worked on school design activities involving typography, logos, posters, layouts, colors, and visual composition. Used Adobe Photoshop and other digital design tools.'),
(2, 'Animation', 'Academic Activity', '2024', 'Present', 'Practiced animation techniques using Adobe Animate, including classic tween and motion tween. Learned the importance of timing, movement, and planning.'),
(3, 'Web Development', 'Personal and School Projects', '2024', 'Present', 'Created practice websites using HTML, CSS, and JavaScript while learning responsive web development and interactive features.'),
(4, 'School Projects', 'Academic Projects', '2024', 'Present', 'Created graphic design and digital media projects, including posters, typography designs, logos, and other creative outputs.');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `name`, `email`, `subject`, `message`, `is_read`, `created_at`) VALUES
(1, 'jimwell', 'jimwellesican415@gmail.com', 'asdasd', 'unta mugana', 0, '2026-11-18 16:08:35');

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `headline` varchar(150) DEFAULT NULL,
  `introduction` text DEFAULT NULL,
  `biography` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `interests` text DEFAULT NULL,
  `goals` text DEFAULT NULL,
  `journey` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`id`, `full_name`, `headline`, `introduction`, `biography`, `email`, `phone`, `location`, `profile_image`, `interests`, `goals`, `journey`) VALUES
(1, 'Jimwell Esican', 'NORSU Student', 'I create clean designs, simple websites, and digital projects while learning more about technology and creative design.', 'I am a student who is interested in technology, graphic design, and web development.\r\n\r\nI enjoy creating digital projects and learning new things about computers and technology.\r\n\r\nThrough my school projects, I have gained experience in graphic design, animation, and website development.', 'jimwellesican415@gmail.com', '09948358747', 'Bayawan City, Negros Oriental', 'uploads/206c405ec0718fa616df8ec9db22f96e.jpg', '🚴 Cycling\r\n🎮 Playing video games\r\n🎨 Graphic design\r\n💻 Web development\r\n🎬 Watching anime', 'My goal is to improve my skills in programming, web development, and graphic design. I also want to become a successful IT professional and create useful digital projects.', 'My journey in technology started through my interest in computers and digital creativity. As an Information Technology student, I continue to learn programming, animation, and web development through school activities and personal projects.');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `link`, `image`, `created_at`) VALUES
(1, 'Letter Monsters', 'Letters designed as monsters. Letter F as Ferb from Phineas and Ferb, and Letter E as a stripe and spots purple monster.', NULL, 'assets/Letter Monsters.png', '2026-11-18 15:49:46'),
(2, 'Polygonal Animal', 'A design of an animal called Wombat. This design is made of polygons.', NULL, 'assets/Polygonal animals.jpg', '2026-11-18 15:49:46'),
(3, 'Vision Vault Logo', 'A logo design representing creativity and digital design. I used curved shapes and text to create a calm and modern appearance.', NULL, 'assets/logo.jpg', '2026-11-18 15:49:46'),
(4, 'Crunchies Advertisement', 'A promotional design created for a food product called Crunchies. The project focuses on making the product attractive and interesting to students and customers.', NULL, 'assets/crunchies.png', '2026-11-18 15:49:46'),
(5, 'Personal Portfolio Website', 'A personal portfolio website created to showcase my projects, skills, education, and experience. I designed the website with separate pages and added interactive features using JavaScript.', NULL, 'assets/portfolio.png', '2026-11-18 15:49:46');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_title` varchar(150) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `theme` varchar(20) DEFAULT 'light'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_title`, `logo`, `theme`) VALUES
(1, 'Jimwell Esican | Portfolio', 'uploads/d9f2262d4203e58f9203245b040f25fd.png', 'dark');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `level` tinyint(3) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `name`, `category`, `level`) VALUES
(1, 'HTML', 'Web Development', 75),
(2, 'CSS', 'Web Development', 70),
(3, 'JavaScript', 'Programming', 50),
(4, 'PHP', 'Programming', 40),
(5, 'MySQL', 'Database', 50),
(6, 'C++', 'Programming', 60),
(7, 'Adobe Photoshop', 'Graphic Design', 75),
(8, 'Adobe Illustrator', 'Graphic Design', 65),
(9, 'Adobe Animate', 'Animation', 70),
(10, 'Canva', 'Graphics Design', 68);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `experience`
--
ALTER TABLE `experience`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `experience`
--
ALTER TABLE `experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
