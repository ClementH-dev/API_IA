-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 07 fév. 2025 à 11:30
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `chat_ai`
--

-- --------------------------------------------------------

--
-- Structure de la table `conversation`
--

CREATE TABLE `conversation` (
  `id` int(11) NOT NULL,
  `id_personnage` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `conversation`
--

INSERT INTO `conversation` (`id`, `id_personnage`, `id_utilisateur`, `created_at`, `updated_at`) VALUES
(2, 3, 2, '2025-02-07 10:17:32', '2025-02-07 10:17:32');

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `envoye_par_ia` tinyint(1) NOT NULL DEFAULT 0,
  `id_conversation` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id`, `contenu`, `envoye_par_ia`, `id_conversation`, `created_at`, `updated_at`) VALUES
(1, 'Bonjour ! Comment ça va ?', 0, 2, '2025-02-07 10:17:45', '2025-02-07 10:17:45'),
(2, 'Bonjour ! Ça va bien, merci. La paix est toujours fragile dans la galaxie, mais nous continuons à œuvrer pour protéger les innocents et maintenir l\'équilibre de la Force. Les jours sont remplis de défis, mais avec la sagesse et la puissance de la lumière, nous affrontons les ténèbres avec courage et détermination. Que puis-je faire pour toi, jeune ?', 1, 2, '2025-02-07 10:17:45', '2025-02-07 10:17:45');

-- --------------------------------------------------------

--
-- Structure de la table `personnage`
--

CREATE TABLE `personnage` (
  `id` int(11) NOT NULL,
  `nom_personnage` varchar(255) NOT NULL,
  `description_personnage` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `id_univers` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `personnage`
--

INSERT INTO `personnage` (`id`, `nom_personnage`, `description_personnage`, `image`, `id_utilisateur`, `id_univers`, `created_at`, `updated_at`) VALUES
(3, 'Obi-Wan', 'Obi-Wan Kenobi est un personnage emblématique de l\'univers Star Wars, connu pour sa sagesse, sa maîtrise du sabre laser et son rôle de mentor pour les jeunes Jedi. Issu de l\'ordre des Jedi, il est désigné pour former Anakin Skywalker, un jeune esclave doué du côté lumineux de la Force. Avec son épée laser verte, Obi-Wan affronte les dangers de la galaxie, notamment les Sith et les armées de droids de combat, tout en essayant de maintenir la paix et la justice dans l\'univers. Sa relation avec Anakin est particulièrement importante, car il essaie de le guider sur le chemin du droit, malgré les tentations du côté obscur qui finiront par détruire son apprenti. À travers ses nombreuses aventures, Obi-Wan Kenobi incarne les valeurs des Jedi : courage, loyauté et dévotion à la cause de la lumière.', './uploads/star_wars/Obi-Wan_#2.png', 2, 2, '2025-02-07 09:37:19', '2025-02-07 09:37:19');

-- --------------------------------------------------------

--
-- Structure de la table `univers`
--

CREATE TABLE `univers` (
  `id` int(11) NOT NULL,
  `nom_univers` varchar(255) NOT NULL,
  `description_univers` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `univers`
--

INSERT INTO `univers` (`id`, `nom_univers`, `description_univers`, `image`, `id_utilisateur`, `created_at`, `updated_at`) VALUES
(2, 'Star wars', 'L\'univers de Star Wars est un vaste et complexe monde de science-fiction créé par George Lucas. Il se déroule dans une galaxie lointaine où diverses civilisations, espèces et technologies coexistent. Pendant la postlogie, la galaxie est plongée dans un contexte de reconstruction et de réorganisation après les événements dramatiques de la saga originale. Les Jedi et les Sith, deux ordres puissants de guerriers utilisant la Force, ont un rôle central dans l\'histoire. Les vaisseaux spatiaux, comme le Faucon Millenium et les X-wings, sont des éléments clés du vaisseau spatial, tandis que les planètes comme Coruscant, Tatooine et Endor offrent des paysages diversifiés et des environnements uniques. Les créatures alien, telles que les Wookiees, les Ewoks et les Stormtroopers, enrichissent la diversité de l\'univers. L\'ambiance de la postlogie est marquée par une quête de paix et de stabilité dans un contexte de menaces persistantes et de luttes pour le pouvoir.', './uploads/star_wars/pictureUnivers_#2.png', 2, '2025-02-07 09:29:13', '2025-02-07 09:29:13');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pseudo` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `telephone`, `mail`, `password`, `pseudo`, `created_at`, `updated_at`) VALUES
(2, 'Dupont', 'Jean', '0123456789', 'jean.dupont@example.com', '$2y$10$iS4T9V63U2Ym5Uc.93ia9.9fuDnUIJOVxXAsFB/sjdKTAxIfJlxaG', 'JeanBon', '2025-02-07 09:11:47', '2025-02-07 09:11:47');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `conversation`
--
ALTER TABLE `conversation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_personnage` (`id_personnage`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_conversation` (`id_conversation`);

--
-- Index pour la table `personnage`
--
ALTER TABLE `personnage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_univers` (`id_univers`);

--
-- Index pour la table `univers`
--
ALTER TABLE `univers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mail` (`mail`),
  ADD UNIQUE KEY `pseudo` (`pseudo`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `conversation`
--
ALTER TABLE `conversation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `personnage`
--
ALTER TABLE `personnage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `univers`
--
ALTER TABLE `univers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `conversation`
--
ALTER TABLE `conversation`
  ADD CONSTRAINT `conversation_ibfk_1` FOREIGN KEY (`id_personnage`) REFERENCES `personnage` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversation_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`id_conversation`) REFERENCES `conversation` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `personnage`
--
ALTER TABLE `personnage`
  ADD CONSTRAINT `personnage_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `personnage_ibfk_2` FOREIGN KEY (`id_univers`) REFERENCES `univers` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `univers`
--
ALTER TABLE `univers`
  ADD CONSTRAINT `univers_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
