-- phpMyAdmin SQL Dump
-- version 5.2.0-rc1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 29, 2022 at 05:38 PM
-- Server version: 10.3.31-MariaDB-0+deb10u1
-- PHP Version: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `liteblog`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `category_id` tinyint(2) UNSIGNED NOT NULL,
  `category_slug` varchar(16) NOT NULL DEFAULT '',
  `category_name` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Triggers `blog_categories`
--
DELIMITER $$
CREATE TRIGGER `categories` BEFORE INSERT ON `blog_categories` FOR EACH ROW BEGIN 
    SET NEW.category_slug = lower(replace(NEW.category_name, " ", "-"));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `post_id` smallint(5) UNSIGNED NOT NULL,
  `post_slug` varchar(64) NOT NULL,
  `post_title` varchar(64) NOT NULL,
  `post_excerpt` varchar(512) NOT NULL,
  `post_content` text NOT NULL,
  `post_status_id` tinyint(1) NOT NULL,
  `post_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Triggers `blog_posts`
--
DELIMITER $$
CREATE TRIGGER `slug` BEFORE INSERT ON `blog_posts` FOR EACH ROW BEGIN 
	DECLARE i integer;
    DECLARE j integer;
    
    SET NEW.post_slug = REGEXP_REPLACE(LOWER(REPLACE(NEW.post_title, " ", "-")), "[^A-Za-z0-9-]", "");
    SELECT count(post_slug) INTO i FROM blog_posts WHERE post_slug = NEW.post_slug;
    SET j = 0;
    
    WHILE i > 0 DO
    	SET j = j + 1;
        SET NEW.post_slug = concat(lower(replace(NEW.post_title, " ", "-")), "-", j);
        SELECT count(post_slug) INTO i FROM blog_posts WHERE post_slug = NEW.post_slug;
    END while;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `blog_post_categories`
--

CREATE TABLE `blog_post_categories` (
  `post_category_id` smallint(5) UNSIGNED NOT NULL,
  `post_id` smallint(5) UNSIGNED NOT NULL,
  `category_id` tinyint(2) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `blog_post_tags`
--

CREATE TABLE `blog_post_tags` (
  `post_tag_id` smallint(5) UNSIGNED NOT NULL,
  `post_id` smallint(5) UNSIGNED NOT NULL,
  `tag_id` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `blog_post_tags`
--
DELIMITER $$
CREATE TRIGGER `tags` BEFORE INSERT ON `blog_post_tags` FOR EACH ROW SET NEW.post_id = NEW.post_id
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `blog_tags`
--

CREATE TABLE `blog_tags` (
  `tag_id` smallint(5) UNSIGNED NOT NULL,
  `tag_slug` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `tag_label` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `blog_tags`
--
DELIMITER $$
CREATE TRIGGER `tag-slug` BEFORE INSERT ON `blog_tags` FOR EACH ROW BEGIN 
	DECLARE i integer;
    DECLARE j integer;
    
    SET NEW.tag_slug = REGEXP_REPLACE(LOWER(REPLACE(NEW.tag_label, " ", "-")), "[^A-Za-z0-9-]", "");
    SELECT count(tag_slug) INTO i FROM blog_tags WHERE tag_slug = NEW.tag_slug;
    SET j = 0;
    
    WHILE i > 0 DO
    	SET j = j + 1;
        SET NEW.tag_slug = concat(lower(replace(NEW.tag_label, " ", "-")), "-", j);
        SELECT count(tag_slug) INTO i FROM blog_tags WHERE tag_slug = NEW.tag_slug;
    END while;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` tinyint(3) UNSIGNED NOT NULL,
  `user_name` varchar(32) NOT NULL,
  `user_first_name` varchar(32) NOT NULL,
  `user_last_name` varchar(32) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`),
  ADD UNIQUE KEY `category_slug` (`category_slug`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`post_id`),
  ADD UNIQUE KEY `post_slug` (`post_slug`),
  ADD KEY `post_status_id` (`post_status_id`);

--
-- Indexes for table `blog_post_categories`
--
ALTER TABLE `blog_post_categories`
  ADD PRIMARY KEY (`post_category_id`),
  ADD UNIQUE KEY `post_id` (`post_id`,`category_id`),
  ADD KEY `post_category_post_id` (`post_id`),
  ADD KEY `post_category_category_id` (`category_id`);

--
-- Indexes for table `blog_post_tags`
--
ALTER TABLE `blog_post_tags`
  ADD PRIMARY KEY (`post_tag_id`),
  ADD UNIQUE KEY `post_id` (`post_id`,`tag_id`),
  ADD KEY `blog_post_tags-tag_id` (`tag_id`);

--
-- Indexes for table `blog_tags`
--
ALTER TABLE `blog_tags`
  ADD PRIMARY KEY (`tag_id`),
  ADD UNIQUE KEY `tag_label` (`tag_label`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `category_id` tinyint(2) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `post_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_post_categories`
--
ALTER TABLE `blog_post_categories`
  MODIFY `post_category_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_post_tags`
--
ALTER TABLE `blog_post_tags`
  MODIFY `post_tag_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_tags`
--
ALTER TABLE `blog_tags`
  MODIFY `tag_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog_post_categories`
--
ALTER TABLE `blog_post_categories`
  ADD CONSTRAINT `post_category_category_id` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`category_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `post_category_post_id` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`post_id`) ON UPDATE CASCADE;

--
-- Constraints for table `blog_post_tags`
--
ALTER TABLE `blog_post_tags`
  ADD CONSTRAINT `blog_post_tags-post_id` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`post_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `blog_post_tags-tag_id` FOREIGN KEY (`tag_id`) REFERENCES `blog_tags` (`tag_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
