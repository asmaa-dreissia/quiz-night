-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : jeu. 04 juil. 2024 à 09:00
-- Version du serveur : 5.7.39
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `quiznight`
--

-- --------------------------------------------------------

--
-- Structure de la table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `answer_text`, `is_correct`) VALUES
(38, 12, 'Samsung', 1),
(37, 12, 'Iphone', 0),
(36, 11, 'porcelaine', 0),
(35, 11, 'plastique', 0),
(34, 11, 'Bois', 1),
(33, 11, 'Fer', 0),
(29, 10, 'Rouge', 0),
(30, 10, 'Bleu', 1),
(13, 6, 'allemande', 1),
(14, 6, 'francaise', 0),
(15, 6, 'us', 0),
(16, 6, 'japoanaise', 0),
(31, 10, 'Blanc', 0),
(32, 10, 'Beige', 0),
(39, 12, 'Huawei', 0),
(40, 12, 'Nokia', 0),
(89, 25, 'Europe', 0),
(86, 24, 'Khéops', 1),
(85, 24, 'Khéphren', 0),
(84, 23, 'E =mc2', 0),
(76, 21, 'Mektoub', 1),
(83, 23, 'E = c² ', 0),
(82, 23, 'E = m6', 0),
(81, 23, 'E = mc² ', 1),
(88, 24, 'Djéser', 0),
(87, 24, 'Mykérinos', 0),
(75, 21, 'Peut etre', 0),
(74, 21, 'Non', 0),
(73, 21, 'Oui', 0),
(90, 25, 'Affrique', 1),
(91, 25, 'Amérique', 0),
(92, 25, 'Océanie', 0),
(93, 26, 'Mosasaures', 0),
(94, 26, 'Ichthyosaure', 0),
(95, 26, 'Mégalodon', 1),
(96, 26, 'Dunkleosteus', 0),
(97, 27, 'Paul Billy Jedusor', 0),
(98, 27, 'Ralph Robbie Jedusor', 0),
(99, 27, 'Tom Elvis Jedusor', 1),
(100, 27, 'Franck James Jedusor', 0),
(101, 28, 'William Kies', 0),
(102, 28, 'Steven Seagal', 0),
(103, 28, 'Chuck Norris', 1),
(104, 28, 'Tommy Lee Jones', 0),
(105, 29, 'Seattle', 0),
(106, 29, 'Chicago', 0),
(107, 29, 'La Nouvelle-Orléans', 1),
(108, 29, 'New York', 0),
(109, 30, 'Windsor', 0),
(110, 30, 'Hamilton', 0),
(111, 30, 'Oshawa', 0),
(112, 30, 'Toronto', 1),
(113, 31, 'La Grosse Guerre', 0),
(114, 31, 'La Petite Guerre', 0),
(115, 31, 'La Grande Guerre', 1),
(116, 31, 'La Guerre Interminable', 0);

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `quiz_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `questions`
--

INSERT INTO `questions` (`id`, `user_id`, `question_text`, `created_at`, `quiz_id`) VALUES
(6, 9, 'Voiture', '0000-00-00 00:00:00', 7),
(12, 9, 'Quel est votre telephone ?', '0000-00-00 00:00:00', 17),
(21, 7, 'Descendons nous du singe ?', '2024-06-13 12:57:15', 26),
(23, 11, 'Quelle est l’intitulé exact de la célèbre formule d’Albert Einstein ?', '2024-06-14 07:26:33', 27),
(24, 11, 'Quelle pyramide est la plus haute d’Égypte ?', '2024-06-14 08:11:22', 28),
(25, 11, 'Sur quel continent l’homme apparaît-il pour la première fois ?', '2024-06-14 12:56:32', 29),
(26, 11, 'Quelle espèce de requins aujourd’hui éteinte est considérée comme le plus grand requin prédateur ayant vécu sur Terre ?', '2024-06-14 08:22:59', 30),
(27, 11, 'Quel est le véritable nom de Voldemort ?', '2024-06-14 08:36:56', 31),
(28, 7, 'Quel acteur a interprété Cordell Walker dans la série Walker Texas ranger ?', '2024-06-14 09:44:08', 32),
(29, 7, 'Quelle ville américaine possède un fort héritage français ?', '2024-06-14 09:51:28', 33),
(30, 9, 'Quel port canadien situé sur le Lac Ontario était jadis connu sous le nom de « Fort York » ?', '2024-06-14 09:54:44', 34),
(31, 9, 'Quel est nom donné à la Première Guerre mondiale ?', '2024-06-14 10:00:53', 35);

-- --------------------------------------------------------

--
-- Structure de la table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cover_image` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `quizzes`
--

INSERT INTO `quizzes` (`id`, `user_id`, `title`, `created_at`, `cover_image`) VALUES
(35, 9, 'Première Guerre Mondial', '2024-06-14 09:57:03', 'https://today.duke.edu/sites/default/files/legacy-files/styles/story_hero/public/Royal_Irish_Rifles_ration_party_Somme_July_1916.jpg?itok=v6lqtW-B'),
(32, 7, 'Walker Texas Ranger', '2024-06-14 09:42:30', 'https://images.bfmtv.com/OIja7SGuZPFpj1LtekOwy-OM7x4=/0x0:1200x675/1200x0/images/Chuck-Norris-et-Clarence-Gilyard-dans-leurs-roles-de-Walker-Texas-Ranger-1530810.jpg'),
(30, 11, 'Requins', '2024-06-14 08:20:13', 'https://qph.cf2.quoracdn.net/main-qimg-9dab4e090f22eef3f3d880d4716ac563-lq'),
(29, 11, 'Préhistoire', '2024-06-14 08:14:37', 'https://www.francebleu.fr/s3/cruiser-production/2020/09/628d5c15-3737-4af0-b3d3-aee41d1591c2/1200x680_gettyimages-165960104.jpg'),
(27, 11, 'Formule Einstein', '2024-06-14 07:24:55', 'https://cherry.img.pmdstatic.net/fit/https.3A.2F.2Fimg.2Emaxisciences.2Ecom.2Fs3.2Ffrgsd.2F1280.2Falbert-einstein.2Fdefault_2022-12-17_a0d0cda7-21ad-47ab-88a8-1dd6a0f4d03c.2Ejpeg/1200x675/quality/80/quel-etait-le-qi-d-einstein.jpg'),
(28, 11, 'Plus haute pyramide', '2024-06-14 08:09:20', 'https://static.nationalgeographic.fr/files/styles/image_3200/public/Pyramids-at-Giza.ngsversion.1458139144541.png?w=1600'),
(31, 11, 'Harry Potter', '2024-06-14 08:35:50', 'https://www.gazette-du-sorcier.com/wp-content/uploads/2021/03/voldemort-avec-baguette.jpg'),
(33, 7, 'Géographie', '2024-06-14 09:50:27', 'https://www.voyageursdumonde.fr/voyage-sur-mesure/magazine-voyage/ShowPhoto/1293/0'),
(34, 9, 'Géographie Canadienne', '2024-06-14 09:53:29', 'https://cdn.evopresse.ca/content/user_files/2023/03/17093507/0317_Francopresse_Lac_Ontario_Illustration_Cr.Berkay_Gumustekin_Unsplash.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(8, 'Asmaa', '$2y$10$bjdsIpwIQW/WSCoye9TNy./n.skd8mcd.17yqh.KZw/s4DtjN3d1q', '2024-06-10 09:15:14'),
(7, 'Alex', '$2y$10$AnF4iJ4sJomWIy4HnQw6PuQhKc.kv3byxSodqbCdQgdmBtgLb4PgO', '2024-06-10 09:13:07'),
(9, 'Adel', '$2y$10$y3nU2ZnJkadZH4OROdLeC.SJ7Gy.rGIqqQ4aLIGuJ0AZbK05k8MmW', '2024-06-10 09:34:19'),
(10, 'momo', '$2y$10$f9JEC/RfK4sH23tw4ABI7Oo5CEi4IOX/2BoZcGBG3ZKx/fNekrwcq', '2024-06-10 13:09:01'),
(11, 'Tommy', '$2y$10$1peJ9equlksnsUKi//PhQ.3BG3BQWw.mt0QTuyUZ5y50qTzDpEDza', '2024-06-11 16:54:02'),
(13, 'Bob', '$2y$10$0PH2190v0b8zNeS789/IT.ac/EbHatOAulQccNR4xO.eDsBA8Mriq', '2024-06-14 13:03:11'),
(15, 'Jacky', '$2y$10$aVNZZ/z0Dv0RSEmhkNbVmuoNns5WyZsLZohUrpD3h24m//5T0.8YG', '2024-06-14 13:09:34'),
(17, 'Pnl', '$2y$10$sYmXtKp7DANDVMFQE6lijOT5oAYzbXjrXy008Ee5FHehRwk9Laf5u', '2024-06-14 13:19:58'),
(18, 'Poulpe', '$2y$10$U6V0bHVlSwvimcncicSPUeX3x3tGBtB0E8Uzg.QFbDs5DKUWDUttO', '2024-06-14 14:05:22'),
(19, 'William', '$2y$10$Z8WhC9Zguc3TGvNA4AkuGe.Bn43VuDyZ7VGhkCraOswxGBY1s5VaS', '2024-06-19 12:43:50');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `username_2` (`username`),
  ADD UNIQUE KEY `username_3` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT pour la table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
