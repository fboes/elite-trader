ALTER TABLE `craft_locations` ADD `trader_id` INT UNSIGNED NULL , ADD `ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;
ALTER TABLE `craft_traders` ADD `ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;

ALTER TABLE  `craft_locations` ADD INDEX (  `trader_id` );
ALTER TABLE  `craft_locations` ADD FOREIGN KEY (  `trader_id` ) REFERENCES  `elite_trader`.`traders` (`id`) ON DELETE SET NULL ON UPDATE CASCADE ;

#ALTER TABLE  `craft_locations` ADD  `trader_id` INT UNSIGNED NULL AFTER  `price_sell`
