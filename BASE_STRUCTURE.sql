-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 10 sep. 2020 à 14:48
-- Version du serveur :  8.0.21-0ubuntu0.20.04.4
-- Version de PHP : 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `timeticket`
--

-- --------------------------------------------------------

--
-- Structure de la table `app`
--

CREATE TABLE `app` (
  `name` varchar(32) NOT NULL,
  `station_id` int NOT NULL,
  `button` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `avatar`
--

CREATE TABLE `avatar` (
  `user_screen_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `class`
--

CREATE TABLE `class` (
  `id` int NOT NULL,
  `name` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `station_id` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `collection`
--

CREATE TABLE `collection` (
  `cid` int NOT NULL,
  `sid` int NOT NULL,
  `unit` varchar(8) NOT NULL,
  `name` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `concept`
--

CREATE TABLE `concept` (
  `id` int NOT NULL,
  `name` varchar(40) NOT NULL,
  `station_id` int NOT NULL DEFAULT '1',
  `code` varchar(5) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `vacation` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `demande`
--

CREATE TABLE `demande` (
  `datetime` datetime NOT NULL,
  `titre` text NOT NULL,
  `delivery` datetime DEFAULT NULL,
  `created_by` varchar(64) NOT NULL,
  `modified_by` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `format`
--

CREATE TABLE `format` (
  `id` int NOT NULL,
  `name` varchar(32) NOT NULL,
  `station_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `group`
--

CREATE TABLE `group` (
  `id` int NOT NULL,
  `name` varchar(40) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `livraison`
--

CREATE TABLE `livraison` (
  `datetime` datetime NOT NULL,
  `titre` text NOT NULL,
  `image` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `log_mos`
--

CREATE TABLE `log_mos` (
  `datetime` datetime NOT NULL,
  `outtime` datetime NOT NULL,
  `id` int NOT NULL,
  `concept` varchar(32) NOT NULL,
  `template_id` int NOT NULL,
  `titre` varchar(64) NOT NULL,
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `memory`
--

CREATE TABLE `memory` (
  `last_time` date NOT NULL,
  `pile_num_row` int NOT NULL,
  `ticket_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `phototheque`
--

CREATE TABLE `phototheque` (
  `id` int NOT NULL,
  `path` varchar(512) NOT NULL,
  `description` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `pile_mos`
--

CREATE TABLE `pile_mos` (
  `datetime` datetime NOT NULL,
  `id` int NOT NULL,
  `template_id` int NOT NULL,
  `concept` varchar(32) NOT NULL,
  `titre` varchar(64) NOT NULL,
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `sample`
--

CREATE TABLE `sample` (
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cid` int NOT NULL,
  `uid` int NOT NULL,
  `value` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `slug`
--

CREATE TABLE `slug` (
  `thread` int NOT NULL,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `group_id` int NOT NULL DEFAULT '1',
  `station_id` int NOT NULL DEFAULT '1',
  `concept_id` int NOT NULL DEFAULT '0',
  `class_id` int NOT NULL DEFAULT '0',
  `system_id` int NOT NULL DEFAULT '0',
  `format_id` int NOT NULL DEFAULT '0',
  `deadline` datetime DEFAULT NULL,
  `client` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `path` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `snapshot`
--

CREATE TABLE `snapshot` (
  `id` int NOT NULL,
  `template_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `size` varchar(25) NOT NULL,
  `type` varchar(25) NOT NULL,
  `description` varchar(100) NOT NULL,
  `payload` mediumblob NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `station`
--

CREATE TABLE `station` (
  `id` int NOT NULL,
  `name` varchar(40) NOT NULL,
  `group_id` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `system`
--

CREATE TABLE `system` (
  `id` int NOT NULL,
  `name` varchar(32) NOT NULL,
  `station_id` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `systemmap`
--

CREATE TABLE `systemmap` (
  `mapKey` varchar(64) NOT NULL,
  `map` text NOT NULL,
  `station_id` int NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `tag`
--

CREATE TABLE `tag` (
  `id` int NOT NULL,
  `name` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `template`
--

CREATE TABLE `template` (
  `concept_id` int NOT NULL,
  `class_id` int NOT NULL,
  `id` int NOT NULL,
  `variant` varchar(40) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `version` int NOT NULL,
  `system_id` int NOT NULL,
  `tag_id` int NOT NULL,
  `file` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `description` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `author` varchar(80) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rating` double NOT NULL,
  `user_id` int NOT NULL,
  `format` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `size` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `category` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `popularity` int NOT NULL,
  `comment` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `background` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `layer` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `state` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `control` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `ticket`
--

CREATE TABLE `ticket` (
  `id` int NOT NULL,
  `thread` int NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(15) NOT NULL,
  `level` int NOT NULL,
  `initials` varchar(3) NOT NULL,
  `body` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  `type` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `snapshot` mediumblob,
  `uid` int NOT NULL,
  `station_id` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Ticketing';

-- --------------------------------------------------------

--
-- Structure de la table `time`
--

CREATE TABLE `time` (
  `station_id` int NOT NULL,
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `concept_id` int NOT NULL,
  `thread` int NOT NULL,
  `start` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `stop` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `username` varchar(32) NOT NULL,
  `password` varchar(64) NOT NULL,
  `id` int NOT NULL,
  `name` varchar(32) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `station_id` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vizcommunicationmap`
--

CREATE TABLE `vizcommunicationmap` (
  `mapKey` varchar(64) NOT NULL,
  `map` text NOT NULL,
  `station_id` int NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `watchdog`
--

CREATE TABLE `watchdog` (
  `template_id` int NOT NULL,
  `field_name` varchar(32) NOT NULL,
  `bark_value` varchar(64) NOT NULL,
  `template_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `app`
--
ALTER TABLE `app`
  ADD UNIQUE KEY `station_id` (`station_id`,`button`);

--
-- Index pour la table `avatar`
--
ALTER TABLE `avatar`
  ADD PRIMARY KEY (`user_screen_name`);

--
-- Index pour la table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `collection`
--
ALTER TABLE `collection`
  ADD UNIQUE KEY `cid` (`cid`,`sid`);

--
-- Index pour la table `concept`
--
ALTER TABLE `concept`
  ADD PRIMARY KEY (`id`,`station_id`);

--
-- Index pour la table `demande`
--
ALTER TABLE `demande`
  ADD PRIMARY KEY (`datetime`),
  ADD KEY `datetime` (`datetime`);

--
-- Index pour la table `format`
--
ALTER TABLE `format`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `livraison`
--
ALTER TABLE `livraison`
  ADD PRIMARY KEY (`datetime`),
  ADD KEY `datetime` (`datetime`);

--
-- Index pour la table `log_mos`
--
ALTER TABLE `log_mos`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `phototheque`
--
ALTER TABLE `phototheque`
  ADD PRIMARY KEY (`path`),
  ADD KEY `index` (`id`);

--
-- Index pour la table `pile_mos`
--
ALTER TABLE `pile_mos`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `slug`
--
ALTER TABLE `slug`
  ADD UNIQUE KEY `thread` (`thread`);

--
-- Index pour la table `snapshot`
--
ALTER TABLE `snapshot`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `station`
--
ALTER TABLE `station`
  ADD PRIMARY KEY (`id`,`group_id`);

--
-- Index pour la table `system`
--
ALTER TABLE `system`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tag`
--
ALTER TABLE `tag`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `template`
--
ALTER TABLE `template`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `time`
--
ALTER TABLE `time`
  ADD PRIMARY KEY (`id`,`station_id`) USING BTREE;

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `vizcommunicationmap`
--
ALTER TABLE `vizcommunicationmap`
  ADD UNIQUE KEY `mapKey` (`mapKey`,`station_id`) USING BTREE;

--
-- Index pour la table `watchdog`
--
ALTER TABLE `watchdog`
  ADD UNIQUE KEY `template_id` (`template_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `class`
--
ALTER TABLE `class`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `collection`
--
ALTER TABLE `collection`
  MODIFY `cid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `concept`
--
ALTER TABLE `concept`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `format`
--
ALTER TABLE `format`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `group`
--
ALTER TABLE `group`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `phototheque`
--
ALTER TABLE `phototheque`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `snapshot`
--
ALTER TABLE `snapshot`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `station`
--
ALTER TABLE `station`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `system`
--
ALTER TABLE `system`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `template`
--
ALTER TABLE `template`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `time`
--
ALTER TABLE `time`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
