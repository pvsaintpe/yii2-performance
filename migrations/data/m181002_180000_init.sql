-- -----------------------------------------------------
-- Table `ff2`.`performance`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ff2`.`performance` ;

CREATE TABLE IF NOT EXISTS `ff2`.`performance` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `merchant_id` INT(11) UNSIGNED NOT NULL COMMENT 'Мерчант',
  `search_class` VARCHAR(255) NULL COMMENT 'Search-модель',
  `route` VARCHAR(255) NULL COMMENT 'URL грида',
  `name` VARCHAR(120) NOT NULL COMMENT 'Название',
  `order` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Порядок',
  `is_default` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'По умолчанию',
  `enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Вкл.',
  PRIMARY KEY (`id`),
  INDEX `performance-merchant_id_idx` (`merchant_id` ASC),
  CONSTRAINT `performance-merchant_id`
    FOREIGN KEY (`merchant_id`)
    REFERENCES `ff2`.`admin` (`id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Представления';


-- -----------------------------------------------------
-- Table `ff2`.`performance_column_settings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ff2`.`performance_column_settings` ;

CREATE TABLE IF NOT EXISTS `ff2`.`performance_column_settings` (
  `performance_id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Представление',
  `attribute` VARCHAR(120) NOT NULL COMMENT 'Аттрибут',
  `value` VARCHAR(255) NOT NULL COMMENT 'Значение по умолчанию',
  `order` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Порядок',
  `required` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Обязательное',
  `sort` TINYINT(3) UNSIGNED NULL DEFAULT NULL COMMENT 'Сортировка по умолчанию',
  PRIMARY KEY (`performance_id`, `attribute`),
  CONSTRAINT `performance_column_settings-performance_id`
    FOREIGN KEY (`performance_id`)
    REFERENCES `ff2`.`performance` (`id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Настройки полей представления';


-- -----------------------------------------------------
-- Table `ff2`.`performance_admin_settings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ff2`.`performance_admin_settings` ;

CREATE TABLE IF NOT EXISTS `ff2`.`performance_admin_settings` (
  `performance_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Представление',
  `merchant_id` INT(11) UNSIGNED NOT NULL COMMENT 'Мерчант',
  `expired_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Шаринг истекает в',
  `admin_enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Управление разрешено',
  `view_enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Просмотр разрешено',
  `edit_enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Изменение разрешено',
  `share_enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Шаринг разрешено',
  `delete_enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Удаление разрешено',
  `switch_enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Блокировка разрешено',
  PRIMARY KEY (`performance_id`, `merchant_id`),
  INDEX `performance_admin_settings-merchant_id_idx` (`merchant_id` ASC),
  CONSTRAINT `performance_admin_settings-performance_id`
    FOREIGN KEY (`performance_id`)
    REFERENCES `ff2`.`performance` (`id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION,
  CONSTRAINT `performance_admin_settings-merchant_id`
    FOREIGN KEY (`merchant_id`)
    REFERENCES `ff2`.`admin` (`id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Настройки представлений';

USE `ff2`$$
DROP TRIGGER IF EXISTS `ff2`.`performance_AFTER_INSERT` $$
USE `ff2`$$
CREATE DEFINER = CURRENT_USER TRIGGER `ff2`.`performance_AFTER_INSERT` AFTER INSERT ON `performance` FOR EACH ROW
BEGIN
	INSERT INTO `performance_admin_settings`
    SET
		`performance_id` = NEW.`id`,
        `merchant_id` = NEW.`merchant_id`,
        `admin_enabled` = 1,
        `view_enabled` = 1,
        `edit_enabled` = 1,
        `share_enabled` = 1,
        `switch_enabled` = 1,
        `delete_enabled` = 1;
END$$