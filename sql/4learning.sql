-- =============================================================
--  4Learning — Script de creation de la base de donnees
--  Groupe 11 — L1 MISPI — Universite Savoie Mont Blanc
-- =============================================================

CREATE DATABASE IF NOT EXISTS `db_grp11` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_grp11`;

CREATE TABLE IF NOT EXISTS `users` (
    `id`              INT            NOT NULL AUTO_INCREMENT,
    `username`        VARCHAR(50)    NOT NULL UNIQUE,
    `email`           VARCHAR(150)   NOT NULL UNIQUE,
    `password`        VARCHAR(255)   NOT NULL,
    `niveau_etude`    TINYINT        NOT NULL DEFAULT 1,
    `points_total`    INT            NOT NULL DEFAULT 0,
    `score_fiabilite` TINYINT        NOT NULL DEFAULT 0,
    `avatar`          VARCHAR(255)   NOT NULL DEFAULT 'avatar_defaut.png',
    `created_at`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tags_matiere` (
    `id`    INT          NOT NULL AUTO_INCREMENT,
    `name`  VARCHAR(100) NOT NULL UNIQUE,
    `color` CHAR(7)      NOT NULL DEFAULT '#5c6672',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `questions` (
    `id`           INT          NOT NULL AUTO_INCREMENT,
    `titre`        VARCHAR(255) NOT NULL,
    `content`      TEXT         NOT NULL,
    `tag_matiere`  INT          DEFAULT NULL,
    `id_auteur`    INT          NOT NULL,
    `niveau_requis` TINYINT     NOT NULL DEFAULT 1,
    `statut`       ENUM('ouverte','resolue','fermee') NOT NULL DEFAULT 'ouverte',
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`id_auteur`)   REFERENCES `users`(`id`)        ON DELETE CASCADE,
    FOREIGN KEY (`tag_matiere`) REFERENCES `tags_matiere`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `reponses` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `id_question` INT          NOT NULL,
    `id_auteur`   INT          NOT NULL,
    `contenu`     TEXT         NOT NULL,
    `note_moy`    DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    `nb_votes`    SMALLINT     NOT NULL DEFAULT 0,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`id_question`) REFERENCES `questions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`id_auteur`)   REFERENCES `users`(`id`)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `votes_reponses` (
    `id`          INT     NOT NULL AUTO_INCREMENT,
    `id_reponse`  INT     NOT NULL,
    `id_voteur`   INT     NOT NULL,
    `note`        TINYINT NOT NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_vote` (`id_reponse`, `id_voteur`),
    FOREIGN KEY (`id_reponse`) REFERENCES `reponses`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`id_voteur`)  REFERENCES `users`(`id`)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `badges` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `nom`         VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `icone`       VARCHAR(10)  NOT NULL DEFAULT '🏅',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user_badges` (
    `id`              INT      NOT NULL AUTO_INCREMENT,
    `id_user`         INT      NOT NULL,
    `id_badge`        INT      NOT NULL,
    `date_obtention`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`id_user`)  REFERENCES `users`(`id`)  ON DELETE CASCADE,
    FOREIGN KEY (`id_badge`) REFERENCES `badges`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Données initiales : tags
INSERT INTO `tags_matiere` (`name`, `color`) VALUES
    ('Mathematiques',    '#134611'),
    ('Informatique',     '#53dd6c'),
    ('Physique',         '#5c6672'),
    ('Economie',         '#14080E'),
    ('Statistiques',     '#2a7d4f'),
    ('Algorithmique',    '#1a6b3c'),
    ('Bases de donnees', '#3d8b5e'),
    ('Anglais',          '#6b7c87');

-- Données initiales : badges
INSERT INTO `badges` (`nom`, `description`, `icone`) VALUES
    ('Premiere reponse', 'A repondu pour la premiere fois',   '✏️'),
    ('Expert',           'Note moyenne superieure a 4.5',     '⭐'),
    ('Top 3',            'Top 3 du classement general',       '🏆'),
    ('Fiable',           'Score de fiabilite a 100',          '🛡️'),
    ('Curieux',          '10 questions posees',               '🔍');
