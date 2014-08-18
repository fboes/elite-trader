<?php

session_start();

require('app/config.php');
require('app/vendor/small-php-helpers/toolshed.php');
require('app/vendor/small-php-helpers/SuperPDO.php');
require('app/vendor/small-php-helpers/Form.php');
require('app/EliteTrader.php');
require('app/App.php');


// Logic

$app = new App();
$data = array(
	'template' => 'index',
	'title'    => NULL,
);
$elite = new EliteTrader(
	SuperPDO::openMysql(CONFIG_DB_HOST,CONFIG_DB_DB,CONFIG_DB_USR,CONFIG_DB_PWD)
);

if (!empty($_GET['hops'])) {
	$_SESSION['hops'] = (int)$_GET['hops'];
}
if (empty($_SESSION['hops']) || $_SESSION['hops'] < 0  || $_SESSION['hops'] > 15) {
	$_SESSION['hops'] = 2;
}
if (!empty($_GET['hopdistance'])) {
	$_SESSION['hopdistance'] = (int)$_GET['hopdistance'];
}
if (empty($_SESSION['hopdistance']) || $_SESSION['hopdistance'] < 0  || $_SESSION['hopdistance'] > 2000) {
	$_SESSION['hopdistance'] = 8;
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
			$data['allGoods']     = $elite->getAllGoods();
			$data['allLocations'] = $elite->getAllLocations();

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
					$app->redirect ($app->path, $app->id);
				}
			}

			$data['prices']       = $elite->getPricesForCurrentAndNeighbouringLocations($_SESSION['hops'],$_SESSION['hopdistance']);
			$data['template']     = 'location';
			$data['title']        = $elite->currentLocation['name'];
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
	default:
		break;
}

// View

if (isset($_GET['json'])) {
	header('Content-type: application/json');
	echo(json_encode((object)$data));
}
else {
	require('views/'.$data['template'].'.html');
}
