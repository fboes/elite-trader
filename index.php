<?php

require('app/config.php');
require('app/vendor/small-php-helpers/toolshed.php');
require('app/vendor/small-php-helpers/SuperPDO.php');
require('app/vendor/small-php-helpers/Form.php');
require('app/EliteTrader.php');

// Logic

if (empty($_GET['hops']) || $_GET['hops'] < 0) {
	$_GET['hops'] = 2;
}

$data = NULL;
$elite = new EliteTrader(
	SuperPDO::openMysql(CONFIG_DB_HOST,CONFIG_DB_DB,CONFIG_DB_USR,CONFIG_DB_PWD)
);
$elite->setCurrentLocation(!empty($_REQUEST['location']) ? $_REQUEST['location'] : 1);
$elite->getAllGoods();
$elite->getAllLocations();

if (!empty($_POST['action'])) {
	$success = TRUE;
	switch ($_POST['action']) {
		case 'update_price':
			foreach ($_POST['price'] as $idGood => $price) {
				$success = $elite->setPriceForCurrentLocation ($idGood, $price['buy'], $price['sell']) && $success;
			}
			break;
		case 'create_price':
			$success = $elite->setPriceForCurrentLocation($_POST['good_id'],$_POST['price_buy'],$_POST['price_sell']);
			break;
		case 'create_connection':
			$success = $elite->setLaneForCurrentLocation($_POST['location_id'],$_POST['distance']);
			break;
		case 'create_good':
			$success = $elite->createGood($_POST['name'],$_POST['description']);
			break;
		case 'create_location':
			$success = $elite->createLocation($_POST['name'],$_POST['description']);
			if ($success) {
				$elite->setLaneForCurrentLocation($success,$_POST['distance']);
			}
			break;
	}
	if ($success) {
		header('Location: '.$_SERVER['SCRIPT_NAME'].'?location='.urlencode($_POST['location']), 303);
	}
}

$data = $elite->getPricesForCurrentAndNeighbouringLocations($_GET['hops']);

// View

require('views/index.html');