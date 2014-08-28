# --------------------------------------------------------
# 2014-09-21 09:00

CREATE TABLE IF NOT EXISTS `traders` (
`id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `location_id` int(10) unsigned DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
ALTER TABLE `traders`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);
ALTER TABLE `traders`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `goods`     ADD `trader_id` INT UNSIGNED NULL , ADD `ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;
ALTER TABLE `locations` ADD `trader_id` INT UNSIGNED NULL , ADD `ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;
ALTER TABLE `roads`     ADD `trader_id` INT UNSIGNED NULL , ADD `ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;
ALTER TABLE `prices`    ADD `trader_id` INT UNSIGNED NULL AFTER `price_sell`;

ALTER TABLE `goods`     ADD INDEX(`trader_id`);
ALTER TABLE `locations` ADD INDEX(`trader_id`);
ALTER TABLE `roads`     ADD INDEX(`trader_id`);
ALTER TABLE `prices`    ADD INDEX(`trader_id`);

ALTER TABLE `goods`     ADD FOREIGN KEY (`trader_id`) REFERENCES `traders`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE;
ALTER TABLE `locations` ADD FOREIGN KEY (`trader_id`) REFERENCES `traders`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE;
ALTER TABLE `roads`     ADD FOREIGN KEY (`trader_id`) REFERENCES `traders`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE;
ALTER TABLE `prices`    ADD FOREIGN KEY (`trader_id`) REFERENCES `traders`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

ALTER TABLE `traders` ADD `settings_json` TEXT NOT NULL AFTER `name`;

ALTER TABLE `traders` ADD `email` VARCHAR(255) NOT NULL AFTER `name`, ADD `pwd` VARCHAR(255) NOT NULL AFTER `email`;
ALTER TABLE `traders` ADD INDEX( `email`, `pwd`);

CREATE TABLE IF NOT EXISTS `craft` (
`id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `cargo` int(11) DEFAULT NULL,
  `speed` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
ALTER TABLE `craft`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);
ALTER TABLE `craft`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `craft_locations` (
  `craft_id` int(10) unsigned NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `price_buy` int(10) unsigned NOT NULL,
  `price_sell` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `craft_locations`
 ADD UNIQUE `craft_id` (`craft_id`, `location_id`)COMMENT '';

 ALTER TABLE `craft_locations` ADD FOREIGN KEY (`craft_id`) REFERENCES `craft`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `craft_locations` ADD FOREIGN KEY (`location_id`) REFERENCES `locations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `craft_traders` (
  `craft_id` int(10) unsigned NOT NULL,
  `trader_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `craft_traders`
 ADD UNIQUE `craft_id` (`craft_id`, `trader_id`)COMMENT '';

 ALTER TABLE `craft_traders` ADD FOREIGN KEY (`craft_id`) REFERENCES `craft`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `craft_traders` ADD FOREIGN KEY (`trader_id`) REFERENCES `traders`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

# --------------------------------------------------------
# 2014-09-27 13:00

ALTER TABLE `craft` ADD `description` TEXT NULL AFTER `name`;
ALTER TABLE `craft` ADD `data_json` TEXT NULL ;
ALTER TABLE `craft` CHANGE `cargo` `cargo` INT(11) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `craft` CHANGE `speed` `speed` INT(11) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `craft`     ADD `trader_id` INT UNSIGNED NULL , ADD `ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;
ALTER TABLE `craft`     ADD INDEX(`trader_id`);
ALTER TABLE `craft`     ADD FOREIGN KEY (`trader_id`) REFERENCES `traders`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

# --------------------------------------------------------
# 2014-09-27 20:00

ALTER TABLE `traders` ADD `craft_id` INT UNSIGNED NULL AFTER `location_id`, ADD INDEX (`craft_id`) ;
ALTER TABLE `traders` ADD INDEX(`location_id`);
ALTER TABLE `traders` ADD FOREIGN KEY (`location_id`) REFERENCES `elite_trader`.`locations`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `traders` ADD FOREIGN KEY (`craft_id`) REFERENCES `elite_trader`.`craft`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `prices` CHANGE `price_buy` `price_buy` DECIMAL(10,2) UNSIGNED NULL DEFAULT NULL, CHANGE `price_sell` `price_sell` DECIMAL(10,2) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `craft_locations` CHANGE `price_buy` `price_buy` DECIMAL(10,2) UNSIGNED NULL DEFAULT NULL, CHANGE `price_sell` `price_sell` DECIMAL(10,2) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `craft` ADD `range_min` FLOAT NULL AFTER `speed`, ADD `range_max` FLOAT NULL AFTER `range_min`;
ALTER TABLE `craft` CHANGE `range_min` `range_min` DECIMAL(10,2) NULL DEFAULT NULL, CHANGE `range_max` `range_max` DECIMAL(10,2) NULL DEFAULT NULL;

ALTER TABLE `traders` DROP `settings_json`;
ALTER TABLE `traders` ADD `hops` INT UNSIGNED NULL AFTER `pwd`, ADD `range` DECIMAL(10,2) UNSIGNED NULL AFTER `hops`;
ALTER TABLE `craft` CHANGE `range_min` `range_min` DECIMAL(10,2) UNSIGNED NULL DEFAULT NULL, CHANGE `range_max` `range_max` DECIMAL(10,2) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `craft` CHANGE `speed` `speed` INT(11) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `traders` ADD `is_editor` BOOLEAN NULL DEFAULT TRUE AFTER `craft_id`;

ALTER TABLE `traders` ADD `hops` INT UNSIGNED NULL AFTER `pwd`, ADD `distance_max` DECIMAL(10,2) UNSIGNED NULL AFTER `hops`;

ALTER TABLE `traders` CHANGE `ts` `ts` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `roads` CHANGE `ts` `ts` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `prices` CHANGE `ts` `ts` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `locations` CHANGE `ts` `ts` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `goods` CHANGE `ts` `ts` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `craft` CHANGE `ts` `ts` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `goods` DROP FOREIGN KEY `goods_ibfk_1`;
ALTER TABLE `goods` ADD CONSTRAINT `goods_ibfk_1` FOREIGN KEY (`trader_id`) REFERENCES `elite_trader`.`traders`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `craft` DROP FOREIGN KEY `craft_ibfk_1`;
ALTER TABLE `craft` ADD CONSTRAINT `craft_ibfk_1` FOREIGN KEY (`trader_id`) REFERENCES `elite_trader`.`traders`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `locations` DROP FOREIGN KEY `locations_ibfk_1`;
ALTER TABLE `locations` ADD CONSTRAINT `locations_ibfk_1` FOREIGN KEY (`trader_id`) REFERENCES `elite_trader`.`traders`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `roads` DROP FOREIGN KEY `roads_ibfk_1`;
ALTER TABLE `roads` ADD CONSTRAINT `roads_ibfk_1` FOREIGN KEY (`trader_id`) REFERENCES `elite_trader`.`traders`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;