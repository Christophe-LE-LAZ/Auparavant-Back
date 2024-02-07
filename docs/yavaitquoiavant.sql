-- Adminer 4.8.1 MySQL 10.11.3-MariaDB-1:10.11.3+maria~ubu2004 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20240207134244',	'2024-02-07 13:42:52',	173);

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area` varchar(50) NOT NULL,
  `department` varchar(30) NOT NULL,
  `district` varchar(20) DEFAULT NULL,
  `street` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `zipcode` int(11) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `location` (`id`, `area`, `department`, `district`, `street`, `city`, `zipcode`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(1,	'Île-de-France',	'Paris',	'Quartier latin',	'28 place du Panthéon',	'Paris',	75005,	48.84619800,	2.34610500,	'2024-02-07 13:48:00',	NULL);

DROP TABLE IF EXISTS `memory`;
CREATE TABLE `memory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(30) NOT NULL,
  `content` longtext NOT NULL,
  `picture_date` datetime NOT NULL,
  `main_picture` varchar(2000) NOT NULL,
  `published_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_EA6D343564D218E` (`location_id`),
  KEY `IDX_EA6D3435A76ED395` (`user_id`),
  CONSTRAINT `FK_EA6D343564D218E` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`),
  CONSTRAINT `FK_EA6D3435A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `memory` (`id`, `location_id`, `user_id`, `title`, `content`, `picture_date`, `main_picture`, `published_at`, `created_at`, `updated_at`) VALUES
(1,	1,	1,	'Le Panthéon en 1792',	'Le Panthéon en 1792, avec La Renommée en son sommet.n',	'1792-01-01 00:00:00',	'https://upload.wikimedia.org/wikipedia/commons/thumb/3/31/Pierre-Antoine_de_Machy_-_Le_Panth%C3%A9on.jpg/1280px-Pierre-Antoine_de_Machy_-_Le_Panth%C3%A9on.jpg',	NULL,	'2024-02-07 13:52:16',	NULL),
(2,	1,	1,	'Le Panthéon de nos jours',	'Le Panthéon vu de la tour Montparnasse en 2016.',	'2016-01-01 00:00:00',	'https://upload.wikimedia.org/wikipedia/commons/thumb/b/bb/Panth%C3%A9on_vu_de_la_tour_Montparnasse_en_2016.jpg/1280px-Panth%C3%A9on_vu_de_la_tour_Montparnasse_en_2016.jpg',	NULL,	'2024-02-07 13:52:58',	NULL);

DROP TABLE IF EXISTS `picture`;
CREATE TABLE `picture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `memory_id` int(11) NOT NULL,
  `picture` varchar(2000) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_16DB4F89CCC80CB3` (`memory_id`),
  CONSTRAINT `FK_16DB4F89CCC80CB3` FOREIGN KEY (`memory_id`) REFERENCES `memory` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `picture` (`id`, `memory_id`, `picture`, `created_at`, `updated_at`) VALUES
(1,	2,	'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c9/Dome_Panth%C3%A9on_Paris_10.jpg/1280px-Dome_Panth%C3%A9on_Paris_10.jpg',	'2024-02-07 13:54:09',	NULL),
(2,	2,	'https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Dome_Panth%C3%A9on_Paris_16.jpg/800px-Dome_Panth%C3%A9on_Paris_16.jpg',	'2024-02-07 13:54:31',	NULL);

DROP TABLE IF EXISTS `place`;
CREATE TABLE `place` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `type` varchar(30) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_741D53CD64D218E` (`location_id`),
  CONSTRAINT `FK_741D53CD64D218E` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `place` (`id`, `location_id`, `name`, `type`, `created_at`, `updated_at`) VALUES
(1,	1,	'Le Panthéon',	'Mausolée',	'2024-02-07 13:49:15',	NULL);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (`id`, `firstname`, `lastname`, `email`, `password`, `roles`, `created_at`, `updated_at`) VALUES
(1,	'Aurélien',	'ROUCHETTE-MARET',	'aurelien.rouchette@orange.fr',	'aurélien',	'[\"ROLE_ADMIN\"]',	'2024-02-07 13:44:52',	NULL);

-- 2024-02-07 13:54:40