<?php

// General
define ('CONFIG_USE_NICE_URLS',         TRUE);
define ('CONFIG_SESSION_LENGTH_SEC',    48 * 3600);
define ('CONFIG_SESSION_NAME',          'elitetraderid');

// DB
define ('CONFIG_DB_HOST',               'localhost');
define ('CONFIG_DB_DB',                 'elite_trader');
define ('CONFIG_DB_DSN',                'mysql:host='.CONFIG_DB_HOST.';dbname='.CONFIG_DB_DB.';charset=utf8');
define ('CONFIG_DB_USR',                'elite_trader');
define ('CONFIG_DB_PWD',                'elite_trader');

// API
define ('CONFIG_API_BASEURL',           'http://localhost:3000');
define ('CONFIG_API_USR',               '');
define ('CONFIG_API_PWD',               '');

// Units
define ('CONFIG_UNIT_CURRENCY',         'credits');
define ('CONFIG_UNIT_CURRENCY_SHORT',   'CR');
define ('CONFIG_UNIT_CURRENCY_DECIMALS',0);

define ('CONFIG_UNIT_DISTANCE',         'light years');
define ('CONFIG_UNIT_DISTANCE_SHORT',   'ly');
define ('CONFIG_UNIT_DISTANCE_DECIMALS',2);

define ('CONFIG_UNIT_CARGO',            'tons');
define ('CONFIG_UNIT_CARGO_SHORT',      't');
define ('CONFIG_UNIT_CARGO_DECIMALS',   0);

define ('CONFIG_HOPS_DEFAULT',          3);
define ('CONFIG_HOPS_SEARCH',           CONFIG_HOPS_DEFAULT);
define ('CONFIG_RANGE_DEFAULT',         5.6);
define ('CONFIG_RANGE_SEARCH',          10);
