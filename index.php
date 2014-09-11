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
require('app/vendor/small-php-helpers/Messages.php');
require('app/TraderApi.php');
require('app/EliteTrader.php');
require('app/App.php');

// --------------------------
// SETUP
// --------------------------

$app = new App();
$messages = new Messages();
$messages->restoreFromSession();
$data = array(
	'template' => 'index',
	'title'    => NULL,
	'messages' => &$messages->messages,
);
$elite = new EliteTrader(
	new SuperPDO(CONFIG_DB_DSN,CONFIG_DB_USR,CONFIG_DB_PWD)
	#,TraderApi::init(CONFIG_API_BASEURL, TraderApi::REPLY_TYPE_JSON)->setHttpCredentials(CONFIG_API_USR,CONFIG_API_PWD)
);
$elite->getCurrentTrader(CONFIG_HOPS_DEFAULT,CONFIG_RANGE_DEFAULT);
$data['currentTrader'] = &$elite->currentTrader;

// --------------------------
// POST
// --------------------------

if (!empty($_POST['action'])) {
	$success = TRUE;
	switch ($_POST['action']) {
		case 'login':
			if (!empty($_POST['email']) && !empty($_POST['password'])) {
				$success = $elite->login($_POST['email'],$_POST['password']);
			}
			break;
		case 'logout':
			session_unset();
			$elite->currentTrader = NULL;
			$success = TRUE;
			break;
		case 'trader-create':
			if (!empty($_POST['email']) && !empty($_POST['password'])) {
				$success = $elite->createTrader($_POST['email'],$_POST['password']);
			}
			break;
		case 'trader':
			if (!empty($_POST['hops'])) {
				$success = $elite->setTraderHops($_POST['hops']);
			}
			if (!empty($_POST['distance_max'])) {
				$success = $elite->setTraderrange($_POST['distance_max']);
			}
			break;
		default:
			if ($elite->currentTrader->is_editor) {
				switch ($app->path[0]) {
					case 'good-update':
						$app->path[0] = 'goods';
						# create
						# update
						# connect
						if (!empty($_POST['name']) && empty($_POST['good_id'])) {
							$_POST['good_id'] = $elite->createGood($_POST['name'],$_POST['description']);
						}
						if (!empty($_POST['good_id']) && !empty($_POST['price_buy'])) {
							$success = $elite->setPriceForCurrentLocation($_POST['good_id'],$_POST['price_buy'],$_POST['price_sell']);
						}
						break;
					case 'location-update':
						$app->path[0] = 'locations';
						# create
						# update
						# connect
						if (!empty($_POST['name']) && empty($_POST['location_id'])) {
							$_POST['location_id'] = $elite->createLocation($_POST['name'],@$_POST['description']);
						}
						if (!empty($_POST['location_id'])) {
							$success = $elite->setRoadForCurrentLocation($_POST['location_id'],@$_POST['distance']);
						}
						break;
					case 'craft-update':
						$app->path[0] = 'craft';
						# create
						# update
						# connect
						$success = $elite->createCraft($_POST['name'],@$_POST['description'],@$_POST['cargo'],@$_POST['speed'],@$_POST['range_min'],@$_POST['range_max']);
						if (!empty($success)) {
							$app->path[1] = $success;
						}
						#if (!empty($success) && !empty($_POST['craft_id'])) {
						#	$success = $elite->setCraftPriceForCurrentLocation($_POST['craft_id'],$_POST['price_buy'],$_POST['price_sell']);
						#}

						break;
					case 'craft':
						if (!empty($app->path[1])) {
							$success = $elite->updateCraft($app->path[1], $_POST['name'],$_POST['description'],@$_POST['cargo'],@$_POST['speed'],@$_POST['range_min'],@$_POST['range_max']);
						}
						elseif (!empty($_POST['craft'])) {
							foreach ($_POST['craft'] as $idCraft => $craft) {
								$success = $elite->updateCraft($idCraft, NULL,$craft['description'],@$craft['cargo'],@$craft['speed'],@$craft['range_min'],@$craft['range_max']) && $success;
							}
						}
						break;
					case 'locations':
						switch ($_POST['action']) {
							case 'lanes_update':
								# update
								if (!empty($_POST['lanes'])) {
									foreach ($_POST['lanes'] as $idLocation => $distance) {
										$success = $elite->setRoadForCurrentLocation($idLocation, $distance) && $success;
									}
								}
								if (!empty($_POST['lanes_delete'])) {
									foreach ($_POST['lanes_delete'] as $idLocation => $value) {
										$success = $elite->deleteLaneForCurrentLocation($idLocation) && $success;
									}
								}
								break;
							case 'craft_xl_update':
								# update
								if (!empty($_POST['name']) && empty($_POST['craft_id'])) {
									$_POST['craft_id'] = $elite->createCraft($_POST['name'],@$_POST['description'],@$_POST['cargo'],@$_POST['speed'],@$_POST['range_min'],@$_POST['range_max']);
								}
								if (!empty($_POST['craft_id'])) {
									$success = $elite->setCraftPriceForCurrentLocation($_POST['craft_id'],$_POST['price_buy'],$_POST['price_sell']);
								}
								break;
							default:
								# update
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
						}
						break;
				}
			}
			break;
	}
	$messages->addMessageOnAssert($success, 'Successful', 'An error occured, please try again later');
	if (!empty($success)) {
		$messages->storeInSession();
		$app->redirect ($app->path[0], $app->path[1]);
	}
}

// --------------------------
// GET
// --------------------------

switch ($app->path[0]) {
	case 'locations':
		if (!empty($app->path[1]) && $app->path[1] === 'new') {
			$data['template']     = 'location-edit';
		}
		elseif (empty($app->path[1])) {
			$data['allLocations'] = $elite->getAllLocations();
			$data['template']     = 'locations';
			$data['title']        = 'Locations';
		}
		else {
			$elite->setCurrentLocation($app->path[1]);
			if (empty($elite->currentLocation)) {
				$app->redirect ($app->path[0], NULL, 307);
			}
			if (!empty($app->path[2])) {
				$data['prices']       = $elite->getPricesForCurrentAndSpecificLocation($app->path[2]);
				$data['title']        = 'Comparing prices for '.$elite->currentLocation->name.' with location '.$app->path[1];
			}
			else {
				$data['prices']       = $elite->getPricesForCurrentAndNeighbouringLocations($elite->currentTrader->hops,$elite->currentTrader->distance_max);
				$data['title']        = $elite->currentLocation->name;
			}
			$data['craft']        = $elite->getCraftForCurrentLocation();
			$data['template']     = 'location';
			$data['connections']  = $elite->getNextLocationsForCurrentLocation(1);
			$data['currentLocation'] = $elite->currentLocation;
		}
		break;
	case 'goods':
		if (!empty($app->path[1]) && $app->path[1] === 'new') {
			$data['template']     = 'good-edit';
		}
		elseif (empty($app->path[1])) {
			$data['allGoods']     = $elite->getAllGoods();
			$data['template']     = 'goods';
			$data['title']        = 'Goods';
		}
		else {
			$data['good'] = $elite->getCompleteGood($app->path[1]);
			if (empty($data['good'])) {
				$app->redirect ($app->path[0], NULL, 307);
			}
			$data['template']     = 'good';
			$data['title']        = $data['good']->name;
		}
		break;
	case 'craft':
		if (!empty($app->path[1]) && $app->path[1] === 'new') {
			$data['template']     = 'craft-edit';
		}
		elseif (empty($app->path[1])) {
			$data['allCraft']     = $elite->getAllCraft();
			$data['template']     = 'crafts';
			$data['title']        = 'Craft';
		}
		else {
			$data['craft'] = $elite->getCompleteCraft($app->path[1]);
			if (empty($data['craft'])) {
				$app->redirect ($app->path[0], NULL, 307);
			}
			$data['template']     = 'craft';
			$data['title']        = $data['craft']->name;
		}
		break;
	case 'trader':
		$data['template']     = $app->path[0];
		$data['title']        = 'Trader & craft settings';
		break;
	case 'login':
		$data['template']     = $app->path[0];
		$data['title']        = 'Login';
		break;
	case 'logout':
		$data['template']     = 'login';
		$data['title']        = 'Logout';
		break;
	case 'good-link':
		if (!empty($app->path[1])) {
			$elite->setCurrentLocation($app->path[1]);
			$data['currentLocation'] = $elite->currentLocation;
			$data['title']           = 'New price for '.$elite->currentLocation->name;
			$data['locationGoods']   = $elite->getGoodsForCurrentLocationPlus();
		}
		else {
			$app->redirect ($app->path[0], NULL, 307);
		}
		$data['template']     = 'good-edit';
		break;
	case 'craft-link':
		if (!empty($app->path[1])) {
			$elite->setCurrentLocation($app->path[1]);
			$data['currentLocation'] = $elite->currentLocation;
			$data['title']           = 'New craft price for '.$elite->currentLocation->name;
			$data['locationCraft']   = $elite->getCraftForCurrentLocationPlus();
		}
		else {
			$app->redirect ($app->path[0], NULL, 307);
		}
		$data['template']     = 'craft-edit';
		break;
	case 'location-link':
		if (!empty($app->path[1])) {
			$elite->setCurrentLocation($app->path[1]);
			$data['currentLocation'] = $elite->currentLocation;
			$data['title']        = 'New connection for '.$elite->currentLocation->name;
		}
		else {
			$app->redirect ($app->path[0], NULL, 307);
		}
		$data['locations']       = !empty($elite->currentLocation)
			? $elite->getNextLocationsForCurrentLocation(CONFIG_HOPS_SEARCH,CONFIG_RANGE_SEARCH)
			: $elite->getAllLocations()
		;
		if (empty($data['locations'])) {
			$data['locations']= $elite->getAllLocations();
		}
		$data['currentLocation'] = $elite->currentLocation;

		$data['template']     = 'location-edit';
		break;
	default:
		break;
}
$elite->persistCurrentTrader();
$data = (object)$data;

// --------------------------
// View
// --------------------------

if (isset($_GET['json'])) {
	header('Content-type: application/json');
	echo(json_encode((object)$data));
}
else {
	require('views/'.$data->template.'.html');
}
