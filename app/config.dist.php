<?php

define ('CONFIG_DB_HOST','localhost');
define ('CONFIG_DB_DB','elite_trader');
define ('CONFIG_DB_DSN','mysql:host='.CONFIG_DB_HOST.';dbname='.CONFIG_DB_DB);
define ('CONFIG_DB_USR','elite_trader');
define ('CONFIG_DB_PWD','elite_trader');

define ('CONFIG_UNIT_CURRENCY','credits');
define ('CONFIG_UNIT_CURRENCY_SHORT','CR');
define ('CONFIG_UNIT_DISTANCE','ly');

define ('CONFIG_USE_NICE_URLS',FALSE);

define ('CONFIG_API_BASEURL', 'http://localhost:3000');
define ('CONFIG_API_USR', '');
define ('CONFIG_API_PWD', '');

define ('CONFIG_SESSION_LENGTH_SEC', 48 * 3600);
define ('CONFIG_SESSION_NAME', 'elitetraderid');
