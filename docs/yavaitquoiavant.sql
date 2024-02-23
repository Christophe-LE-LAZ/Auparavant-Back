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
(3,	'Île-de-France',	'Paris',	'Notre-Dame',	'6 Parvis Notre-Dame - Place Jean-Paul II',	'Paris',	75004,	48.51110000,	2.20590000,	'2024-02-08 09:45:03',	NULL),
(5,	'Île-de-France',	'Essonne',	'Clause',	'Impasse du Blutin',	'Brétigny',	91220,	48.60870000,	2.30685000,	'2024-02-12 10:18:15',	NULL),
(6,	'Île-de-France',	'Paris',	'Les Halles',	'Rues de Rivoli et Étienne-Marcel, bd de Sébastopol',	'Paris',	75001,	48.51400000,	2.20500000,	'2024-02-23 13:53:12',	NULL);

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
(10,	1,	1,	1,	'Le Panthéon en 1792',	'Le Panthéon en 1792, avec La Renommée en son sommet.',	'1792-01-01 00:00:00',	'65d89b0a4c5e9.jpg',	NULL,	'2024-02-23 13:18:02',	NULL),
(11,	1,	1,	1,	'Le Panthéon de nos jours',	'Le Panthéon vu de la tour Montparnasse en 2016.',	'2016-01-01 00:00:00',	'65d89b3c82862.jpg',	NULL,	'2024-02-23 13:18:52',	NULL),
(12,	2,	1,	2,	'La naissance de la tour Eiffel',	'C’est à l’occasion de l’Exposition Universelle de 1889, date qui marquait le centenaire de la Révolution française qu\\\'il a été décidé de construire une tour de 300m. Les premiers coups de pelle sont donnés le 26 janvier 1887. Le 31 mars 1889, la Tour achevée en un temps record -2 ans, 2 mois et 5 jours- s’impose comme une véritable prouesse technique.',	'1887-01-26 00:00:00',	'65d89c12ed791.jpeg',	NULL,	'2024-02-23 13:22:26',	NULL),
(13,	2,	1,	2,	'La Tour Eiffel de nos jours',	'La tour Eiffel [tuʁɛfɛl] est une tour de fer puddlé de 330 m de hauteur située à Paris, à l’extrémité nord-ouest du parc du Champ-de-Mars en bordure de la Seine dans le 7ᵉ arrondissement. Son adresse officielle est 5, avenue Anatole-France.',	'2009-06-01 00:00:00',	'65d89c7601ee9.jpg',	NULL,	'2024-02-23 13:24:05',	NULL),
(14,	3,	1,	3,	'Notre-Dame',	'La cathédrale Notre-Dame de Paris, communément appelée Notre-Dame, est l\'un des monuments les plus emblématiques de Paris et de la France. Elle est située sur l\'île de la Cité et est un lieu de culte catholique, siège de l\'archidiocèse de Paris, dédié à la Vierge Marie.',	'2009-01-01 00:00:00',	'65d89cd754521.jpg',	NULL,	'2024-02-23 13:25:43',	NULL),
(15,	3,	1,	3,	'Incendie de Notre-Dame',	'L’incendie de Notre-Dame de Paris est un incendie majeur survenu à la cathédrale Notre-Dame de Paris, les 15 et 16 avril 2019, pendant près de 15 heures.',	'2019-04-15 00:00:00',	'65d89d54d31a9.jpg',	NULL,	'2024-02-23 13:27:48',	NULL),
(16,	5,	1,	5,	'Propriété de M. Clause',	'Propriété de M. Clause, édifiée en 1912',	'1912-01-01 00:00:00',	'65d89f89244b1.jpg',	NULL,	'2024-02-23 13:37:12',	NULL),
(17,	5,	1,	6,	'Quartier Clause',	'Projet d\'aménagement urbain en 2023',	'2024-01-01 00:00:00',	'65d89fae71353.jpg',	NULL,	'2024-02-23 13:37:50',	NULL),
(18,	6,	1,	7,	'Les Halles de Paris en 1862',	'Les Halles de Paris était le nom donné aux halles centrales, marché de vente en gros de produits alimentaires frais, situé au cœur de Paris, dans le premier arrondissement, et qui donna son nom au quartier environnant. Au plus fort de son activité et par manque de place, les étals des marchands s\'installaient même dans les rues adjacentes.',	'1862-01-01 00:00:00',	'65d8a43d3232a.jpg',	NULL,	'2024-02-23 13:57:16',	NULL),
(19,	6,	1,	8,	'Les Halles de nos jours',	'À l\'emplacement de ce vaste marché, qui se tenait jusqu\'au début des années 1970, se trouvent aujourd\'hui un espace vert (le jardin Nelson-Mandela, précédemment jardin des Halles), un centre commercial souterrain (le Forum des Halles) et de nombreux espaces consacrés aux loisirs (piscine, cinéma) et aux activités culturelles (conservatoire, bibliothèque, centre culturel). La gare RER Châtelet - Les Halles, située sous le complexe, est la plus grande gare souterraine du monde et permet un accès depuis toute la région parisienne.',	'2021-05-01 00:00:00',	'65d8a5a915f62.jpg',	NULL,	'2024-02-23 14:03:20',	NULL);

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
(11,	11,	'65d89b3c82964.jpg',	'2024-02-23 13:18:52',	NULL),
(12,	11,	'65d89b3c830a8.jpg',	'2024-02-23 13:18:52',	NULL),
(14,	12,	'65d89c12ed9bd.jpg',	'2024-02-23 13:22:26',	NULL),
(15,	12,	'65d89c8245710.png',	'2024-02-23 13:24:18',	NULL),
(16,	15,	'65d89d54d3412.jpg',	'2024-02-23 13:27:48',	NULL),
(17,	15,	'65d89d54d36c5.jpg',	'2024-02-23 13:27:48',	NULL),
(18,	12,	'65d89db3470fb.jpg',	'2024-02-23 13:29:23',	NULL),
(19,	10,	'65d89df3491d9.jpg',	'2024-02-23 13:30:27',	NULL),
(20,	11,	'65d89e0c7ac7a.jpg',	'2024-02-23 13:30:52',	NULL),
(21,	13,	'65d89e46bc19d.jpg',	'2024-02-23 13:31:50',	NULL),
(22,	14,	'65d89e777cbaf.avif',	'2024-02-23 13:32:39',	NULL),
(23,	14,	'65d89e940439e.jpg',	'2024-02-23 13:33:08',	NULL),
(24,	18,	'65d8a43d32450.jpg',	'2024-02-23 13:57:17',	NULL),
(25,	18,	'65d8a43d326ab.webp',	'2024-02-23 13:57:17',	NULL),
(26,	19,	'65d8a5a915ff5.jpg',	'2024-02-23 14:03:21',	NULL),
(27,	19,	'65d8a5e7a5c6b.jpg',	'2024-02-23 14:04:23',	NULL);

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
(3,	3,	'Notre-Dame de Paris',	'Cathédrale',	'2024-02-08 09:46:04',	NULL),
(5,	5,	'Propriété Clause',	'Propriété',	'2024-02-12 10:18:15',	NULL),
(6,	5,	'Résidence Clause',	'Résidence d\'immeubles',	'2024-02-12 10:24:58',	NULL),
(7,	6,	'Les Halles de Paris',	'Marché de vente en gros',	'2024-02-23 13:54:51',	NULL),
(8,	6,	'Les Halles',	'Forum et espace vert',	'2024-02-23 14:01:13',	NULL);

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
(1,	'Aurélien',	'ROUCHETTE-MARET',	'aurelien.rouchette@orange.fr',	'$2y$13$QQF8BE1NlrIp4QxrM062ietwpHynqiVZtoMJ1l2GZkMoc.OA.99Ke',	'[\"ROLE_USER\",\"ROLE_ADMIN\"]',	'2024-02-12 13:00:41',	NULL),
(2,	'Christophe',	'LE LAZ',	'christophe.le-laz@oclock.school',	'$2y$13$DsiSBUOOlmQqqlVNWyINF.DRXSQGZWcr6EVZhHcSjmGszgQL7feby',	'[\"ROLE_USER\",\"ROLE_ADMIN\"]',	'2024-02-12 13:53:25',	'2024-02-14 13:44:17'),
(3,	'Lisa',	'Lapierre',	'lisa.lapierre@sfr.fr',	'$2y$13$25Xr0Xx30EbnFqaGyXIVLeuIXpBHAgxRyhGqTvCvaqcypdLbGgk.y',	'[\"ROLE_USER\"]',	'2024-02-15 13:03:08',	NULL),
(4,	'Steven',	'Nguyen',	'steven.nguyen@oclock.school',	'$2y$13$NAKXQfNZT3mK1zMPKVEkfeLWuC.7Op0wC1D6FQbKzRUefuKuWeKGG',	'[\"ROLE_USER\"]',	'2024-02-15 13:05:22',	NULL),
(5,	'Dylan',	'Frossard',	'dylan.frossard@oclock.school',	'$2y$13$6DnxY7N6tor2VOsS.7pxdev4wT9UCKsYZ8ueXRbLTbHPLNx4djyoe',	'[\"ROLE_USER\",\"ROLE_ADMIN\"]',	'2024-02-23 14:07:15',	NULL);

-- 2024-02-23 14:08:16