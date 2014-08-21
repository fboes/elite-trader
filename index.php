<?php

require('app/config.php');

if (defined('CONFIG_SESSION_LENGTH_SEC')) {
	ini_set('session.gc_maxlifetime', CONFIG_SESSION_LENGTH_SEC);
	session_set_cookie_params(CONFIG_SESSION_LENGTH_SEC);
}
session_name(CONFIG_SESSION_NAME);
session_start();
require('app/vendor/small-php-helpers/toolshed.php');
require('app/vendor/small-php-helpers/SuperPDO.php');
require('app/TraderApi.php');
require('app/EliteTrader.php');
require('app/App.php');

// Logic

$app = new App();
$data = array(
	'template' => 'index',
	'title'    => NULL,
);
$elite = new EliteTrader(
	new SuperPDO(CONFIG_DB_DSN,CONFIG_DB_USR,CONFIG_DB_PWD)
	#,TraderApi::init(CONFIG_API_BASEURL, TraderApi::REPLY_TYPE_JSON)->setHttpCredentials(CONFIG_API_USR,CONFIG_API_PWD)
);
$elite->getCurrentTrader(session_id());
$data['currentTrader'] = &$elite->currentTrader;

if (!empty($_POST['action'])) {
	switch ($_POST['action']) {
		case 'trader':
			if (!empty($_POST['hops'])) {
				$elite->setTraderHops($_POST['hops']);
			}
			if (!empty($_POST['hopdistance'])) {
				$elite->setTraderHopDistance($_POST['hopdistance']);
			}
			break;
	}
}

switch ($app->path) {
	case 'locations':
		if (empty($app->id)) {
			if (!empty($_POST['name'])) {
				$success = $elite->createLocation($_POST['name'],@$_POST['description']);
				if ($success) {
					$app->redirect ($app->path, $app->id);
				}
			}
			$data['allLocations'] = $elite->getAllLocations();
			$data['template']     = 'locations';
			$data['title']        = 'Locations';
		}
		else {
			$elite->setCurrentLocation($app->id);
			if (empty($elite->currentLocation)) {
				$app->redirect ($app->path, NULL, 307);
			}
			if (!empty($_POST['action'])) {
				$success = TRUE;
				switch ($_POST['action']) {
					case 'update_lane':
						if (!empty($_POST['lanes'])) {
							foreach ($_POST['lanes'] as $idLocation => $distance) {
								$success = $elite->setLaneForCurrentLocation($idLocation, $distance) && $success;
							}
						}
						if (!empty($_POST['lanes_delete'])) {
							foreach ($_POST['lanes_delete'] as $idLocation => $value) {
								$success = $elite->deleteLaneForCurrentLocation($idLocation) && $success;
							}
						}
						break;
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
					case 'good_update':
						if (!empty($_POST['name']) && empty($_POST['good_id'])) {
							$_POST['good_id'] = $elite->createGood($_POST['name'],$_POST['description']);
						}
						if (!empty($_POST['good_id'])) {
							$success = $elite->setPriceForCurrentLocation($_POST['good_id'],$_POST['price_buy'],$_POST['price_sell']);
						}
						break;
					case 'location_update':
						if (!empty($_POST['name']) && empty($_POST['location_id'])) {
							$_POST['location_id'] = $elite->createLocation($_POST['name'],$_POST['description']);
						}
						if (!empty($_POST['location_id'])) {
							$success = $elite->setLaneForCurrentLocation($_POST['location_id'],$_POST['distance']);
						}
						break;
				}
				if ($success) {
					$app->redirect ($app->path, $app->id);
				}
			}

			if (!empty($app->subId)) {
				$data['prices']       = $elite->getPricesForCurrentAndSpecificLocation($app->subId);
				$data['title']        = 'Comparing prices for '.$elite->currentLocation['name'].' with location '.$app->id;
			}
			else {
				$data['prices']       = $elite->getPricesForCurrentAndNeighbouringLocations($elite->currentTrader['hops'],$elite->currentTrader['hopdistance']);
				$data['title']        = $elite->currentLocation['name'];
			}
			$data['template']     = 'location';
			$data['connections']  = $elite->getNextLocationsForCurrentLocation(1);
			$data['currentLocation'] = $elite->currentLocation;
		}
		break;
	case 'goods':
		if (empty($app->id)) {
			if (!empty($_POST['name'])) {
				$success = $elite->createGood($_POST['name'],@$_POST['description']);
				if ($success) {
					$app->redirect ($app->path, $app->id);
				}
			}
			$data['allGoods']     = $elite->getAllGoods();
			$data['template']     = 'goods';
			$data['title']        = 'Goods';
		}
		else {
			if (!empty($_POST['name'])) {
				$success = $elite->createGood($_POST['name'],@$_POST['description']);
				if ($success) {
					$app->redirect ($app->path, $app->id);
				}
			}
			$data['good'] = $elite->getCompleteGood($app->id);
			if (empty($data['good'])) {
				$app->redirect ($app->path, NULL, 307);
			}
			if (!empty($_POST['action'])) {
				$success = TRUE;
				switch ($_POST['action']) {
					case 'update_good':
						if (!empty($_POST['good_name'])) {
							$success = $elite->updateGood($app->id, $_POST['good_name'],$_POST['good_description']) && $success;
						}
						break;
				}
				if ($success) {
					$app->redirect ($app->path, $app->id);
				}
			}

			$data['template']     = 'good';
			$data['title']        = $data['good']['name'];
		}
		break;
	case 'trader':
		$data['template']     = $app->path;
		$data['title']        = 'Trader & craft settings';
		break;
	case 'good-update':
		$data['title']        = 'New good';
		if (!empty($app->id)) {
			$elite->setCurrentLocation($app->id);
			$data['currentLocation'] = $elite->currentLocation;
			$data['title']        = 'New price for '.$elite->currentLocation['name'];
		}
		$data['allGoods']        = $elite->getAllGoods();

		$data['template']     = $app->path;
		break;
	case 'location-update':
		$data['title']        = 'New location';
		if (!empty($app->id)) {
			$elite->setCurrentLocation($app->id);
			$data['currentLocation'] = $elite->currentLocation;
			$data['title']        = 'New connection for '.$elite->currentLocation['name'];
		}
		$data['allLocations']    = $elite->getAllLocations();
		$data['currentLocation'] = $elite->currentLocation;

		$data['template']     = $app->path;
		break;
	default:
		break;
}
$elite->persistCurrentTrader();

// View

if (isset($_GET['json'])) {
	header('Content-type: application/json');
	echo(json_encode((object)$data));
}
else {
	require('views/'.$data['template'].'.html');
}
