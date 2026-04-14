-- =============================================================
--  4Learning — Script de creation de la base de donnees
--  Groupe 11 — L1 MISPI — Universite Savoie Mont Blanc
-- =============================================================

CREATE DATABASE IF NOT EXISTS `4learning` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `4learning`;

CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(50)      NOT NULL UNIQUE,
    `email`         VARCHAR(150)     NOT NULL UNIQUE,
    `password_hash` VARCHAR(255)     NOT NULL,
    `niveau`        TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `points`        INT UNSIGNED     NOT NULL DEFAULT 0,
    `fiabilite`     TINYINT UNSIGNED NOT NULL DEFAULT 50,
    `avatar`        VARCHAR(255)     DEFAULT NULL,
    `role`          ENUM('user','admin') NOT NULL DEFAULT 'user',
    `created_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tags` (
    `id`      TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nom`     VARCHAR(100)     NOT NULL UNIQUE,
    `couleur` CHAR(7)          NOT NULL DEFAULT '#5c6672',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `questions` (
    `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `user_id`       INT UNSIGNED     NOT NULL,
    `tag_id`        TINYINT UNSIGNED NOT NULL,
    `titre`         VARCHAR(255)     NOT NULL,
    `contenu`       TEXT             NOT NULL,
    `niveau_requis` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `statut`        ENUM('ouverte','resolue','fermee') NOT NULL DEFAULT 'ouverte',
    `created_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`tag_id`)  REFERENCES `tags`(`id`)  ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `answers` (
    `id`          INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    `question_id` INT UNSIGNED      NOT NULL,
    `user_id`     INT UNSIGNED      NOT NULL,
    `contenu`     TEXT              NOT NULL,
    `note_moy`    DECIMAL(3,2)      NOT NULL DEFAULT 0.00,
    `nb_votes`    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`  DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ratings` (
    `id`        INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `answer_id` INT UNSIGNED     NOT NULL,
    `user_id`   INT UNSIGNED     NOT NULL,
    `note`      TINYINT UNSIGNED NOT NULL CHECK (`note` BETWEEN 1 AND 5),
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_vote` (`answer_id`, `user_id`),
    FOREIGN KEY (`answer_id`) REFERENCES `answers`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`)   REFERENCES `users`(`id`)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `saisons` (
    `id`         TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nom`        VARCHAR(50)      NOT NULL,
    `date_debut` DATE             NOT NULL,
    `date_fin`   DATE             NOT NULL,
    `active`     TINYINT(1)       NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `badges` (
    `id`          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nom`         VARCHAR(100)     NOT NULL,
    `description` VARCHAR(255)     NOT NULL,
    `icone`       VARCHAR(10)      NOT NULL DEFAULT '🏅',
    `couleur`     CHAR(7)          NOT NULL DEFAULT '#53dd6c',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user_badges` (
    `id`        INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `user_id`   INT UNSIGNED     NOT NULL,
    `badge_id`  TINYINT UNSIGNED NOT NULL,
    `saison_id` TINYINT UNSIGNED DEFAULT NULL,
    `obtenu_le` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`)   REFERENCES `users`(`id`)   ON DELETE CASCADE,
    FOREIGN KEY (`badge_id`)  REFERENCES `badges`(`id`)  ON DELETE CASCADE,
    FOREIGN KEY (`saison_id`) REFERENCES `saisons`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Donnees initiales : tags
INSERT INTO `tags` (`nom`, `couleur`) VALUES
    ('Mathematiques',   '#134611'),
    ('Informatique',    '#53dd6c'),
    ('Physique',        '#5c6672'),
    ('Economie',        '#14080E'),
    ('Statistiques',    '#2a7d4f'),
    ('Algorithmique',   '#1a6b3c'),
    ('Bases de donnees','#3d8b5e'),
    ('Anglais',         '#6b7c87');

-- Donnees initiales : badges
INSERT INTO `badges` (`nom`, `description`, `icone`, `couleur`) VALUES
    ('Premiere reponse', 'A repondu pour la premiere fois',     '✏️',  '#53dd6c'),
    ('Expert',           'Note moyenne superieure a 4.5',       '⭐',  '#f0c040'),
    ('Top 3 Saison',     'Top 3 du classement saisonnier',      '🏆',  '#f0a800'),
    ('Fiable',           'Jauge de fiabilite a 100%',           '🛡️', '#134611'),
    ('Curieux',          '10 questions posees',                 '🔍',  '#5c6672');

-- Saison inaugurale
INSERT INTO `saisons` (`nom`, `date_debut`, `date_fin`, `active`) VALUES
    ('Saison 1 — Printemps 2026', '2026-02-01', '2026-06-30', 1);
