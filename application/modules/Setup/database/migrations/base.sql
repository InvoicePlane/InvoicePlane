# ************************************************************
# InvoicePlane 2
# Base SQL File
#
# This file is used to create the initial database structure
# needed to run InvoicePlane. It is used in the setup and must
# be updated if there are any database changes in later
# versions of the application.
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;


# Dump of table client_contacts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `client_contacts`;

CREATE TABLE `client_contacts` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id`  INT(11) UNSIGNED NOT NULL,
  `forename`   TEXT,
  `surname`    TEXT,
  `position`   TEXT,
  `address`    TEXT,
  `country`    TEXT,
  `phone`      TEXT,
  `mobile`     TEXT,
  `fax`        TEXT,
  `email`      TEXT,
  `gender`     VARCHAR(1)                DEFAULT NULL,
  `birthdate`  DATETIME                  DEFAULT NULL,
  `language`   TEXT,
  `is_active`  TINYINT(1)       NOT NULL DEFAULT '1',
  `created_at` DATETIME         NOT NULL,
  `updated_at` DATETIME         NOT NULL,
  `deleted_at` DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table clients
# ------------------------------------------------------------

DROP TABLE IF EXISTS `clients`;

CREATE TABLE `clients` (
  `id`                 INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`               TEXT             NOT NULL,
  `name_short`         TEXT,
  `address`            TEXT,
  `country`            TEXT,
  `phone`              TEXT,
  `fax`                TEXT,
  `web`                TEXT,
  `email`              TEXT,
  `vat_id`             TEXT,
  `tax_code`           TEXT,
  `custom_terms`       TEXT,
  `language`           VARCHAR(255)              DEFAULT NULL,
  `primary_contact_id` INT(11) UNSIGNED          DEFAULT NULL,
  `credit_amount`      DECIMAL(30, 10)           DEFAULT NULL,
  `currency_id`        INT(11) UNSIGNED          DEFAULT NULL,
  `is_active`          TINYINT(1)       NOT NULL DEFAULT '1',
  `is_archived`        TINYINT(1)       NOT NULL DEFAULT '0',
  `created_at`         DATETIME         NOT NULL,
  `updated_at`         DATETIME         NOT NULL,
  `deleted_at`         DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table currencies
# ------------------------------------------------------------

DROP TABLE IF EXISTS `currencies`;

CREATE TABLE `currencies` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        TEXT             NOT NULL,
  `symbol`      VARCHAR(20)      NOT NULL DEFAULT '',
  `placement`   TINYINT(1)       NOT NULL,
  `is_archived` TINYINT(1)                DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table custom_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `custom_fields`;

CREATE TABLE `custom_fields` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        TEXT             NOT NULL,
  `model`       VARCHAR(255)     NOT NULL DEFAULT '',
  `type`        VARCHAR(255)     NOT NULL DEFAULT '',
  `is_archived` TINYINT(1)       NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table custom_options
# ------------------------------------------------------------

DROP TABLE IF EXISTS `custom_options`;

CREATE TABLE `custom_options` (
  `id`       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field_id` INT(11) UNSIGNED          DEFAULT NULL,
  `value`    TEXT,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table custom_values
# ------------------------------------------------------------

DROP TABLE IF EXISTS `custom_values`;

CREATE TABLE `custom_values` (
  `id`       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field_id` INT(11) UNSIGNED NOT NULL,
  `value`    TEXT,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table email_templates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `email_templates`;

CREATE TABLE `email_templates` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(255)     NOT NULL,
  `type`       VARCHAR(255)     NOT NULL,
  `subject`    TEXT             NOT NULL,
  `from_email` VARCHAR(255)     NOT NULL,
  `from_name`  VARCHAR(255)              DEFAULT NULL,
  `cc`         TEXT,
  `bcc`        TEXT,
  `body`       TEXT,
  `template`   VARCHAR(255)              DEFAULT NULL,
  `language`   VARCHAR(255)              DEFAULT NULL,
  `created_at` DATETIME         NOT NULL,
  `updated_at` DATETIME         NOT NULL,
  `deleted_at` DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table group_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `group_permissions`;

CREATE TABLE `group_permissions` (
  `perm_id`  INT(11) UNSIGNED NOT NULL,
  `group_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`perm_id`, `group_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)              DEFAULT NULL,
  `definition` TEXT,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `groups` VALUES ('1', 'admin', 'Super Admin Group');
INSERT INTO `groups` VALUES ('2', 'public', 'Public Access Group');
INSERT INTO `groups` VALUES ('3', 'default', 'Default Access Group');

# Dump of table login_attempts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `login_attempts`;

CREATE TABLE `login_attempts` (
  `id`             INT(11) NOT NULL AUTO_INCREMENT,
  `ip_address`     VARCHAR(39)      DEFAULT '0',
  `timestamp`      DATETIME         DEFAULT NULL,
  `login_attempts` TINYINT(2)       DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

# Dump of table notes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notes`;

CREATE TABLE `notes` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `model_name` VARCHAR(255)     NOT NULL DEFAULT '',
  `model_id`   INT(11)          NOT NULL,
  `author_id`  INT(11)          NOT NULL,
  `content`    TEXT             NOT NULL,
  `created_at` DATETIME         NOT NULL,
  `updated_at` DATETIME         NOT NULL,
  `deleted_at` DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table payment_methods
# ------------------------------------------------------------

DROP TABLE IF EXISTS `payment_methods`;

CREATE TABLE `payment_methods` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        TEXT             NOT NULL,
  `is_archived` TINYINT(1)       NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table payments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `payments`;

CREATE TABLE `payments` (
  `id`           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id`   INT(11) UNSIGNED          DEFAULT NULL,
  `client_id`    INT(11) UNSIGNED          DEFAULT NULL,
  `method_id`    INT(11) UNSIGNED NOT NULL,
  `payment_date` DATETIME         NOT NULL,
  `amount`       DECIMAL(30, 10)  NOT NULL,
  `note`         TEXT,
  `created_at`   DATETIME         NOT NULL,
  `updated_at`   DATETIME         NOT NULL,
  `deleted_at`   DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)              DEFAULT NULL,
  `definition` TEXT,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table product_families
# ------------------------------------------------------------

DROP TABLE IF EXISTS `product_families`;

CREATE TABLE `product_families` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        TEXT             NOT NULL,
  `is_archived` TINYINT(1)       NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table product_units
# ------------------------------------------------------------

DROP TABLE IF EXISTS `product_units`;

CREATE TABLE `product_units` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        TEXT             NOT NULL,
  `name_plural` TEXT,
  `is_archived` TINYINT(1)       NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `family_id`      INT(11) UNSIGNED          DEFAULT NULL,
  `sku`            TEXT,
  `name`           TEXT,
  `description`    TEXT,
  `price`          DECIMAL(30, 10)  NOT NULL,
  `purchase_price` DECIMAL(30, 10)           DEFAULT NULL,
  `provider`       TEXT,
  `tax_rate_id`    INT(11) UNSIGNED          DEFAULT NULL,
  `unit_id`        INT(11) UNSIGNED          DEFAULT NULL,
  `created_at`     DATETIME         NOT NULL,
  `updated_at`     DATETIME         NOT NULL,
  `deleted_at`     DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table projects
# ------------------------------------------------------------

DROP TABLE IF EXISTS `projects`;

CREATE TABLE `projects` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        TEXT             NOT NULL,
  `description` TEXT,
  `client_id`   INT(11) UNSIGNED          DEFAULT NULL,
  `status_id`   INT(11) UNSIGNED          DEFAULT NULL,
  `date_due`    DATETIME                  DEFAULT NULL,
  `is_archived` TINYINT(1)       NOT NULL DEFAULT '0',
  `created_at`  DATETIME         NOT NULL,
  `updated_at`  DATETIME         NOT NULL,
  `deleted_at`  DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED          DEFAULT NULL,
  `key`     VARCHAR(255)     NOT NULL,
  `value`   TEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_UNIQUE` (`key`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table subgroup_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `subgroup_groups`;

CREATE TABLE `subgroup_groups` (
  `group_id`    INT(11) UNSIGNED NOT NULL,
  `subgroup_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`group_id`, `subgroup_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table tasks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tasks`;

CREATE TABLE `tasks` (
  `id`          INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `project_id`  INT(11)             NOT NULL,
  `invoice_id`  INT(11)                      DEFAULT NULL,
  `title`       TEXT                NOT NULL,
  `description` TEXT,
  `price`       DECIMAL(30, 10)     NOT NULL,
  `tax_rate_id` INT(11) UNSIGNED             DEFAULT NULL,
  `date_due`    DATETIME                     DEFAULT NULL,
  `status_id`   TINYINT(1) UNSIGNED NOT NULL,
  `created_at`  INT(11)             NOT NULL,
  `updated_at`  INT(11)             NOT NULL,
  `deleted_at`  INT(11)                      DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table tax_rates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tax_rates`;

CREATE TABLE `tax_rates` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(255)     NOT NULL DEFAULT '',
  `percentage`  DECIMAL(10, 10)  NOT NULL,
  `is_archived` TINYINT(1)       NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table uploads
# ------------------------------------------------------------

DROP TABLE IF EXISTS `uploads`;

CREATE TABLE `uploads` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id`      INT(11) UNSIGNED          DEFAULT NULL,
  `key`            VARCHAR(255)     NOT NULL,
  `file_name_orig` TEXT             NOT NULL,
  `file_name_new`  TEXT             NOT NULL,
  `mime_type`      VARCHAR(255)              DEFAULT NULL,
  `uploader_id`    INT(11) UNSIGNED NOT NULL,
  `created_at`     DATETIME         NOT NULL,
  `updated_at`     DATETIME         NOT NULL,
  `deleted_at`     DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table user_clients
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_clients`;

CREATE TABLE `user_clients` (
  `user_id`   INT(11) UNSIGNED NOT NULL,
  `client_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`client_id`, `user_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table user_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_groups`;

CREATE TABLE `user_groups` (
  `user_id`  INT(11) UNSIGNED NOT NULL,
  `group_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`, `group_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table user_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_permissions`;

CREATE TABLE `user_permissions` (
  `perm_id` INT(11) UNSIGNED NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`perm_id`, `user_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table user_variables
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_variables`;

CREATE TABLE `user_variables` (
  `id`       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`  INT(11) UNSIGNED NOT NULL,
  `data_key` VARCHAR(100)     NOT NULL,
  `value`    TEXT,
  PRIMARY KEY (`id`),
  KEY `user_id_index` (`user_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id`                INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`             VARCHAR(100)     NOT NULL,
  `pass`              VARCHAR(64)      NOT NULL,
  `username`          VARCHAR(100)              DEFAULT NULL,
  `banned`            TINYINT(1)                DEFAULT '0',
  `last_login`        DATETIME                  DEFAULT NULL,
  `last_activity`     DATETIME                  DEFAULT NULL,
  `date_created`      DATETIME                  DEFAULT NULL,
  `forgot_exp`        TEXT,
  `remember_time`     DATETIME                  DEFAULT NULL,
  `remember_exp`      TEXT,
  `verification_code` TEXT,
  `totp_secret`       VARCHAR(16)               DEFAULT NULL,
  `ip_address`        TEXT,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# Dump of table voucher_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `voucher_groups`;

CREATE TABLE `voucher_groups` (
  `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`            TEXT             NOT NULL,
  `formatter`       TEXT             NOT NULL,
  `next_id`         INT(11) UNSIGNED          DEFAULT NULL,
  `left_pad`        INT(11) UNSIGNED          DEFAULT NULL,
  `increment_steps` INT(11) UNSIGNED          DEFAULT '1',
  `is_archived`     TINYINT(1)       NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
