# --------------------------------------------------------
# 2014-09-27 13:00

ALTER TABLE `craft` ADD `description` TEXT NULL AFTER `name`;
ALTER TABLE `craft` ADD `data_json` TEXT NULL ;
ALTER TABLE `craft` CHANGE `cargo` `cargo` INT(11) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `craft` CHANGE `speed` `speed` INT(11) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `craft`     ADD `trader_id` INT UNSIGNED NULL , ADD `ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;
ALTER TABLE `craft`     ADD INDEX(`trader_id`);
ALTER TABLE `craft`     ADD FOREIGN KEY (`trader_id`) REFERENCES `traders`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

