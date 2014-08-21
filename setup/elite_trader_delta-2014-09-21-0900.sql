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

ALTER TABLE `goods`     ADD FOREIGN KEY (`trader_id`) REFERENCES `elite_trader`.`traders`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE;
ALTER TABLE `locations` ADD FOREIGN KEY (`trader_id`) REFERENCES `elite_trader`.`traders`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE;
ALTER TABLE `roads`     ADD FOREIGN KEY (`trader_id`) REFERENCES `elite_trader`.`traders`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE;
ALTER TABLE `prices`    ADD FOREIGN KEY (`trader_id`) REFERENCES `elite_trader`.`traders`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

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

 ALTER TABLE `craft_locations` ADD FOREIGN KEY (`craft_id`) REFERENCES `elite_trader`.`craft`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `craft_locations` ADD FOREIGN KEY (`location_id`) REFERENCES `elite_trader`.`locations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `craft_traders` (
  `craft_id` int(10) unsigned NOT NULL,
  `trader_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `craft_traders`
 ADD UNIQUE `craft_id` (`craft_id`, `trader_id`)COMMENT '';

 ALTER TABLE `craft_traders` ADD FOREIGN KEY (`craft_id`) REFERENCES `elite_trader`.`craft`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `craft_traders` ADD FOREIGN KEY (`trader_id`) REFERENCES `elite_trader`.`traders`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;