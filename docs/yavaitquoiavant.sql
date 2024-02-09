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
(1,	'Île-de-France',	'Paris',	'Quartier latin',	'28 place du Panthéon',	'Paris',	75005,	48.84619800,	2.34610500,	'2024-02-07 13:48:00',	NULL),
(2,	'Île-de-France',	'Paris',	'Gros-Caillou',	'5 avenue Anatole-France',	'Paris',	75007,	48.85829600,	2.29447900,	'2024-02-07 14:48:05',	NULL),
(3,	'Île-de-France',	'Paris',	'Notre-Dame',	'6 Parvis Notre-Dame - Place Jean-Paul II',	'Paris',	75004,	48.51110000,	2.20590000,	'2024-02-08 09:45:03',	NULL);

DROP TABLE IF EXISTS `memory`;
CREATE TABLE `memory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
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

INSERT INTO `memory` (`id`, `location_id`, `user_id`, `place_id`, `title`, `content`, `picture_date`, `main_picture`, `published_at`, `created_at`, `updated_at`) VALUES
(1,	1,	1,	1,	'Le Panthéon en 1792',	'Le Panthéon en 1792, avec La Renommée en son sommet.n',	'1792-01-01 00:00:00',	'https://upload.wikimedia.org/wikipedia/commons/thumb/3/31/Pierre-Antoine_de_Machy_-_Le_Panth%C3%A9on.jpg/1280px-Pierre-Antoine_de_Machy_-_Le_Panth%C3%A9on.jpg',	NULL,	'2024-02-07 13:52:16',	NULL),
(2,	1,	1,	1,	'Le Panthéon de nos jours',	'Le Panthéon vu de la tour Montparnasse en 2016.',	'2016-01-01 00:00:00',	'https://upload.wikimedia.org/wikipedia/commons/thumb/b/bb/Panth%C3%A9on_vu_de_la_tour_Montparnasse_en_2016.jpg/1280px-Panth%C3%A9on_vu_de_la_tour_Montparnasse_en_2016.jpg',	NULL,	'2024-02-07 13:52:58',	NULL),
(3,	2,	1,	2,	'La naissance de la tour Eiffel',	'C’est à l’occasion de l’Exposition Universelle de 1889, date qui marquait le centenaire de la Révolution française qu\'il a été décidé de construire une tour de 300m.\r\n\r\nLes premiers coups de pelle sont donnés le 26 janvier 1887.\r\nLe 31 mars 1889, la Tour achevée en un temps record -2 ans, 2 mois et 5 jours- s’impose comme une véritable prouesse technique.',	'1887-01-26 00:00:00',	'https://www.toureiffel.paris/sites/default/files/styles/mobile_x1_560/public/2017-10/070601-014_1.JPEG?itok=3lGoqCZK',	NULL,	'2024-02-07 14:50:11',	NULL),
(4,	2,	1,	2,	'La Tour Eiffel de nos jours',	'La tour Eiffel [tuʁɛfɛl] est une tour de fer puddlé de 330 m de hauteur située à Paris, à l’extrémité nord-ouest du parc du Champ-de-Mars en bordure de la Seine dans le 7ᵉ arrondissement. Son adresse officielle est 5, avenue Anatole-France.',	'2009-06-01 00:00:00',	'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a8/Tour_Eiffel_Wikimedia_Commons.jpg/260px-Tour_Eiffel_Wikimedia_Commons.jpg',	NULL,	'2024-02-07 14:51:04',	NULL),
(5,	3,	1,	3,	'Notre-Dame',	'La cathédrale Notre-Dame de Paris, communément appelée Notre-Dame, est l\'un des monuments les plus emblématiques de Paris et de la France. Elle est située sur l\'île de la Cité et est un lieu de culte catholique, siège de l\'archidiocèse de Paris, dédié à la Vierge Marie.',	'2009-01-01 00:00:00',	'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3d/Notre-Dame_de_Paris_2009-04-28.jpg/1024px-Notre-Dame_de_Paris_2009-04-28.jpg',	NULL,	'2024-02-08 09:48:14',	NULL),
(6,	3,	1,	3,	'Incendie de Notre-Dame',	'L’incendie de Notre-Dame de Paris est un incendie majeur survenu à la cathédrale Notre-Dame de Paris, les 15 et 16 avril 2019, pendant près de 15 heures.',	'2019-04-15 00:00:00',	'https://upload.wikimedia.org/wikipedia/commons/thumb/3/39/Incendie_Notre_Dame_de_Paris.jpg/280px-Incendie_Notre_Dame_de_Paris.jpg',	NULL,	'2024-02-08 09:50:03',	NULL);

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
(2,	2,	'https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Dome_Panth%C3%A9on_Paris_16.jpg/800px-Dome_Panth%C3%A9on_Paris_16.jpg',	'2024-02-07 13:54:31',	NULL),
(3,	4,	'https://upload.wikimedia.org/wikipedia/commons/thumb/1/10/Dimensions_Eiffel_Tower.svg/440px-Dimensions_Eiffel_Tower.svg.png',	'2024-02-07 14:51:29',	NULL),
(4,	3,	'https://cloudfront-eu-central-1.images.arcpublishing.com/leparisien/VK5YKZHUCDVRZDNC3N3KTWFCWY.jpg',	'2024-02-07 14:51:44',	NULL),
(5,	6,	'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3c/Fl%C3%A8che_en_feu_-_Spire_on_Fire.png/170px-Fl%C3%A8che_en_feu_-_Spire_on_Fire.png',	'2024-02-08 09:50:20',	NULL),
(6,	5,	'https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Notre-Dame_de_Paris_2013-07-24.jpg/280px-Notre-Dame_de_Paris_2013-07-24.jpg',	'2024-02-08 09:50:36',	NULL);

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
(1,	1,	'Le Panthéon',	'Mausolée',	'2024-02-07 13:49:15',	NULL),
(2,	2,	'Tour Eiffel',	'Tour autoportante',	'2024-02-07 14:48:49',	NULL),
(3,	3,	'Notre-Dame de Paris',	'Cathédrale',	'2024-02-08 09:46:04',	NULL);

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
(1,	'Aurélien',	'ROUCHETTE-MARET',	'aurelien.rouchette@orange.fr',	'aurélien',	'[\"ROLE_ADMIN\"]',	'2024-02-07 13:44:52',	NULL),
(3,	'Christophe',	'LE LAZ',	'christophe.le-laz@oclock.school',	'christophe',	'[\"ROLE_ADMIN\"]',	'2024-02-08 14:19:24',	NULL);

-- 2024-02-09 14:57:50