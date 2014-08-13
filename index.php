<?php

session_start();

require('app/config.php');
require('app/vendor/small-php-helpers/toolshed.php');
require('app/vendor/small-php-helpers/SuperPDO.php');
require('app/vendor/small-php-helpers/Form.php');
require('app/EliteTrader.php');

// Session

if (!empty($_GET['location'])) {
	$_SESSION['location'] = (int)$_GET['location'];
}
if (empty($_SESSION['location']) || $_SESSION['location'] <= 0) {
	$_SESSION['location'] = 1;
}

if (!empty($_GET['hops'])) {
	$_SESSION['hops'] = (int)$_GET['hops'];
}
if (empty($_SESSION['hops']) || $_SESSION['hops'] < 0  || $_SESSION['hops'] > 15) {
	$_SESSION['hops'] = 2;
}

// Logic

$data = NULL;
$elite = new EliteTrader(
	SuperPDO::openMysql(CONFIG_DB_HOST,CONFIG_DB_DB,CONFIG_DB_USR,CONFIG_DB_PWD)
);
$elite->setCurrentLocation(!empty($_GET['location']) ? $_GET['location'] : $_SESSION['location']);
$elite->getAllGoods();
$elite->getAllLocations();

if (!empty($_POST['action'])) {
	$success = TRUE;
	switch ($_POST['action']) {
		case 'update_price':
			if (!empty($_POST['good'])) {
				foreach ($_POST['good'] as $idGood => $name) {
					$success = $elite->updateGood($idGood, $name) && $success;
				}
			}
			if (!empty($_POST['location_name'])) {
				$elite->updateCurrentLocation($_POST['location_name'],$_POST['location_description']);
			}
			if (!empty($_POST['price'])) {
				foreach ($_POST['price'] as $idGood => $price) {
					$success = $elite->setPriceForCurrentLocation($idGood, $price['buy'], $price['sell']) && $success;
				}
			}
			break;
		case 'create_price':
			if (!empty($_POST['name']) && empty($_POST['good_id'])) {
				$_POST['good_id'] = $elite->createGood($_POST['name'],$_POST['description']);
			}
			if (!empty($_POST['good_id'])) {
				$success = $elite->setPriceForCurrentLocation($_POST['good_id'],$_POST['price_buy'],$_POST['price_sell']);
			}
			break;
		case 'create_connection':
			if (!empty($_POST['name']) && empty($_POST['location_id'])) {
				$_POST['location_id'] = $elite->createLocation($_POST['name'],$_POST['description']);
			}
			if (!empty($_POST['location_id'])) {
				$success = $elite->setLaneForCurrentLocation($_POST['location_id'],$_POST['distance']);
			}
			break;
	}
	if ($success) {
		header('Location: '.$_SERVER['SCRIPT_NAME'].'?location='.urlencode($_POST['location']), 303);
	}
}

$data = $elite->getPricesForCurrentAndNeighbouringLocations($_SESSION['hops']);

// View

if (isset($_GET['json'])) {
	header('Content-type: application/json');
	echo(json_encode((object)array(
		'elite' => $elite,
		'data'  => $data,
		'nextLocations' => $elite->getNextLocationsForCurrentLocation(1),
	)));
}
else {
	require('views/index.html');
}
