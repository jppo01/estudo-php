-- ============================================================
-- VSJBC - Schema do Banco de Dados
-- Executar no phpMyAdmin do cPanel HostGator
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)    NOT NULL,
  `email`      VARCHAR(150)    NOT NULL,
  `password`   VARCHAR(255)    NOT NULL,
  `role`       ENUM('admin','manager') NOT NULL DEFAULT 'manager',
  `active`     TINYINT(1)      NOT NULL DEFAULT 1,
  `last_login` DATETIME        NULL,
  `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `demands` (
  `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(200)    NOT NULL,
  `description` TEXT            NULL,
  `category`    VARCHAR(100)    NULL,
  `priority`    ENUM('baixa','media','alta','critica') NOT NULL DEFAULT 'media',
  `status`      ENUM('pendente','em_andamento','concluida','cancelada') NOT NULL DEFAULT 'pendente',
  `deadline`    DATE            NULL,
  `assignee`    VARCHAR(100)    NULL,
  `notes`       TEXT            NULL,
  `deleted_at`  DATETIME        NULL,
  `created_by`  INT UNSIGNED    NOT NULL,
  `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_status`     (`status`),
  INDEX `idx_priority`   (`priority`),
  INDEX `idx_deleted_at` (`deleted_at`),
  CONSTRAINT `fk_demands_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `demand_history` (
  `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `demand_id`     INT UNSIGNED    NOT NULL,
  `field_changed` VARCHAR(50)     NOT NULL,
  `old_value`     VARCHAR(500)    NULL,
  `new_value`     VARCHAR(500)    NULL,
  `comment`       TEXT            NULL,
  `changed_by`    INT UNSIGNED    NOT NULL,
  `changed_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_demand_id` (`demand_id`),
  CONSTRAINT `fk_history_demand` FOREIGN KEY (`demand_id`) REFERENCES `demands` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_history_user`   FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `demand_comments` (
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `demand_id`  INT UNSIGNED    NOT NULL,
  `user_id`    INT UNSIGNED    NOT NULL,
  `comment`    TEXT            NOT NULL,
  `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_demand_id` (`demand_id`),
  CONSTRAINT `fk_comment_demand` FOREIGN KEY (`demand_id`) REFERENCES `demands` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comment_user`   FOREIGN KEY (`user_id`)   REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_tickets` (
  `id`              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `glpi_id`         VARCHAR(50)     NOT NULL,
  `title`           VARCHAR(300)    NOT NULL,
  `description`     TEXT            NULL,
  `category`        VARCHAR(150)    NULL,
  `status`          VARCHAR(80)     NOT NULL DEFAULT 'aberto',
  `priority`        VARCHAR(50)     NULL,
  `requester`       VARCHAR(150)    NULL,
  `assignee`        VARCHAR(150)    NULL,
  `glpi_created_at` DATETIME        NULL,
  `glpi_updated_at` DATETIME        NULL,
  `solution`        TEXT            NULL,
  `imported_at`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `import_batch`    VARCHAR(100)    NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_glpi_id` (`glpi_id`),
  INDEX `idx_status`    (`status`),
  INDEX `idx_opened_at` (`glpi_created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `oracle_knowledge` (
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `category`   VARCHAR(100)    NULL,
  `question`   TEXT            NOT NULL,
  `answer`     TEXT            NOT NULL,
  `tags`       VARCHAR(300)    NULL,
  `active`     TINYINT(1)      NOT NULL DEFAULT 1,
  `created_by` INT UNSIGNED    NOT NULL,
  `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `ft_knowledge` (`question`, `answer`, `tags`),
  CONSTRAINT `fk_knowledge_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
