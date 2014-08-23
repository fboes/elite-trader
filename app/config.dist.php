<?php

define ('CONFIG_DB_HOST','localhost');
define ('CONFIG_DB_DB','elite_trader');
define ('CONFIG_DB_DSN','mysql:host='.CONFIG_DB_HOST.';dbname='.CONFIG_DB_DB.';charset=utf8');
define ('CONFIG_DB_USR','elite_trader');
define ('CONFIG_DB_PWD','elite_trader');

define ('CONFIG_UNIT_CURRENCY','credits');
define ('CONFIG_UNIT_CURRENCY_SHORT','CR');
define ('CONFIG_UNIT_DISTANCE','ly');

define ('CONFIG_HOPS_DEFAULT',       3);
define ('CONFIG_HOPS_SEARCH',        CONFIG_HOPS_DEFAULT);
define ('CONFIG_HOPDISTANCE_DEFAULT',5.6);
define ('CONFIG_HOPDISTANCE_SEARCH', 10);

define ('CONFIG_API_BASEURL', 'http://localhost:3000');
define ('CONFIG_API_USR', '');
define ('CONFIG_API_PWD', '');

define ('CONFIG_SESSION_LENGTH_SEC', 48 * 3600);
define ('CONFIG_SESSION_NAME', 'elitetraderid');
