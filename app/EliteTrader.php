<?php

class EliteTrader {
	const TABLE_GOODS     = 'goods';
	const TABLE_ROADS     = 'roads';
	const TABLE_PRICES    = 'prices';
	const TABLE_LOCATIONS = 'locations';
	const TABLE_TRADERS   = 'traders';
	const TABLE_CRAFT     = 'craft';
	const TABLE_X_CT      = 'craft_trader';
	const TABLE_X_CL      = 'craft_locations';

	const STATUS_NEW      = -1;
	const STATUS_OLD      = 1;
	const STATUS_VERY_OLD = 2;

	const SECONDS_NEW      = 900;
	const SECONDS_OLD      = 86400;
	const SECONDS_VERY_OLD = 259200;

	protected $pdo;
	protected $api;
	protected $tsNow;

	public $currentLocation;
	public $currentTrader;

	public $listGoods      = array();
	public $listLocations  = array();

	/**
	 * [__construct description]
	 * @param SuperPDO $pdo [description]
	 * @param [type]   $api [description]
	 */
	public function __construct(SuperPDO $pdo, HttpApi $api = NULL) {
		$this->pdo = $pdo;
		$this->pdo->useUtf8();
		$this->pdo->setAttribute(SuperPDO::ATTR_DEFAULT_FETCH_MODE, SuperPDO::FETCH_OBJ);
		$this->api = $api;
		$this->tsNow = time();
		$this->currentTrader = array();
	}

	/**
	 * [getCurrentTrader description]
	 * @param  integer $hops        [description]
	 * @param  float   $distance_max       [description]
	 * @return [type]               [description]
	 */
	public function getCurrentTrader ($hops = 2, $distance_max = 10) {
		$this->currentTrader = (object)array(
			'hops'             => (int)$hops,
			'distance_max'            => (float)$distance_max,
			'location_id'      => NULL,
			'craft_id'         => NULL,
			'is_editor'        => TRUE,
		);
		foreach ($_SESSION as $key => $value) {
			if (isset($value)) {
				$this->currentTrader->$key = $value;
			}
		}
	}

	/**
	 * [setTraderHops description]
	 * @param integer $hops [description]
	 */
	public function setTraderHops ($hops) {
		$hops = (int)$hops;
		if (!empty($hops) && $hops > 0  && $hops <= 15) {
			$this->currentTrader->hops = $hops;
		}
	}

	/**
	 * [setTraderdistance_max description]
	 * @param float $distance_max [description]
	 */
	public function setTraderRange ($distance_max) {
		$distance_max = (float)$distance_max;
		if (!empty($distance_max) && $distance_max > 0  && $distance_max <= 2000) {
			$this->currentTrader->distance_max = $distance_max;
		}
	}

	/**
	 * [persistCurrentTrader description]
	 */
	public function persistCurrentTrader () {
		if (!empty($this->currentTrader->id)) {
			$altered = FALSE;
			foreach ($_SESSION as $key => $value) {
				if (!empty($this->currentTrader->$key) && $this->currentTrader->$key != $value) {
					$altered = TRUE;
					break;
				}
			}
			if ($altered) {
				$this->saveCurrentTrader();
			}
		}
		foreach ($this->currentTrader as $key => $value) {
			$_SESSION[$key] = $value;
		}
	}

	/**
	 * [createTrader description]
	 * @param  [type] $email    [description]
	 * @param  [type] $password [description]
	 * @return [type]           [description]
	 */
	public function createTrader ($email, $password) {
		unset($this->currentTrader->id);
		$success = $this->pdo->insert(self::TABLE_TRADERS, array(
			'name'  => $email,
			'email' => $email,
			'pwd'   => $this->returnPassword($password),
		));
		$this->currentTrader->id = $this->pdo->lastInsertId();
		return $success;
	}

	/**
	 * [saveCurrentTrader description]
	 * @return boolean [description]
	 */
	public function saveCurrentTrader () {
		if (!empty($this->currentTrader->id)) {
			$data = array(
				'hops'  => $this->currentTrader->hops,
				'distance_max' => $this->currentTrader->distance_max,
				'location_id'   => $this->currentTrader->location_id,
				'craft_id'      => $this->currentTrader->craft_id,
				'is_editor'     => (int)$this->currentTrader->is_editor
			);
			if (empty($data['location_id'])) {
				unset($data['location_id']);
			}
			if (empty($data['craft_id'])) {
				unset($data['craft_id']);
			}
			return $this->pdo->update(
				self::TABLE_TRADERS,
				$data,
				'id='.$this->pdo->quote($this->currentTrader->id)
			);
		}
		return FALSE;
	}

	/**
	 * [login description]
	 * @param  [type] $email    [description]
	 * @param  [type] $password [description]
	 * @return [type]           [description]
	 */
	public function login ($email, $password) {
		$this->pdo->lastCmd =
			'SELECT t.*'
			.' FROM '.self::TABLE_TRADERS.' AS t'
			.' WHERE t.email = '.$this->pdo->quote($email)
			.' AND t.pwd = '.$this->pdo->quote($this->returnPassword($password))
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();
		$result = $sth->fetchAll();
		$this->currentTrader = (!empty($result[0]) ? $result[0] : array());
		if (!empty($this->currentTrader)) {
			foreach ($this->currentTrader as $key => $value) {
				$_SESSION[$key] = $value;
			}
		}
		$this->currentTrader = (object)$this->currentTrader;
		return !empty($this->currentTrader);
	}

	/**
	 * Set current location and get all information
	 * @param   integer $id [description]
	 * @return  boolean [description]
	 */
	public function setCurrentLocation ($id) {
		$id = (int)$id;
		$this->currentLocation = $this->getLocation($id);
		$this->currentTrader->location_id = $id;
		if (!empty($this->currentLocation)) {
			$this->currentLocation->id = (int)$this->currentLocation->id;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * [getCurrentTraderId description]
	 * @return integer [description]
	 */
	public function getCurrentTraderId () {
		return !empty($this->currentTrader->id) ? $this->currentTrader->id : NULL;
	}

	/**
	 * [getLocation description]
	 * @param  integer $id [description]
	 * @return [type]      [description]
	 */
	public function getLocation ($id) {
		$id = (int)$id;
		$this->pdo->lastCmd =
			'SELECT l.*'
			.' FROM locations AS l'
			.' WHERE l.id = '.$this->pdo->quote((int)$id)
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();
		$result = $sth->fetchAll();
		return (!empty($result[0]) ? $result[0] : array());
	}


	// -------------------------------------------
	// CREATE
	// -------------------------------------------

	/**
	 * [createLocation description]
	 * @param  string $name        [description]
	 * @param  string $description [description]
	 * @return integer             [description]
	 */
	public function createLocation ($name, $description = NULL) {
		$id = NULL;
		if ($this->pdo->replace(
			self::TABLE_LOCATIONS,
			$this->modifyRow(array(
				'name'        => $name,
				'description' => $description,
			))
		)) {
			$id = $this->pdo->lastInsertId();
			$this->listLocations[$id] = $name;
		}
		return $id;
	}

	/**
	 * [createGood description]
	 * @param  string $name        [description]
	 * @param  string $description [description]
	 * @return integer             [description]
	 */
	public function createGood ($name, $description = NULL) {
		$id = NULL;
		if ($this->pdo->replace(
			self::TABLE_GOODS,
			$this->modifyRow(array(
				'name'        => $name,
				'description' => $description,
			))
		)) {
			$id = $this->pdo->lastInsertId();
			$this->listGoods[$id] = $name;
		}
		return $id;
	}

	/**
	 * [createCraft description]
	 * @param  string  $name        [description]
	 * @param  string  $description [description]
	 * @param  integer $cargo       [description]
	 * @param  integer $speed       [description]
	 * @param  float   $minRange    [description]
	 * @param  float   $maxRange    [description]
	 * @return integer              [description]
	 */
	public function createCraft ($name, $description = NULL, $cargo = NULL, $speed = 0, $minRange = 0, $maxRange = 0) {
		$id = NULL;
		if ($this->pdo->replace(
			self::TABLE_CRAFT,
			$this->modifyRow(array(
				'name'        => $name,
				'description' => $description,
				'cargo'       => (int)$cargo,
				'speed'       => (int)$speed,
				'range_min'   => (float)$minRange,
				'range_max'   => (float)$maxRange,
			))
		)) {
			$id = $this->pdo->lastInsertId();
		}
		return $id;
	}

	// -------------------------------------------
	// READ
	// -------------------------------------------

	/**
	 * [getAllGoods description]
	 */
	public function getAllGoods () {
		$this->pdo->lastCmd =
			'SELECT *'
			.' FROM '.self::TABLE_GOODS
			.' ORDER BY name'
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();

		$goods = array();
		while (($row = $sth->fetch()) !== false) {
			$this->listGoods[$row->id] = $row->name;
			$goods[] = $row;
		}
		return $goods;
	}

	/**
	 * [getAllLocations description]
	 */
	public function getAllLocations () {
		$this->pdo->lastCmd =
			'SELECT *'
			.' FROM '.self::TABLE_LOCATIONS
			.' ORDER BY name'
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();

		$locations = array();
		while (($row = $sth->fetch()) !== false) {
			$this->listLocations[$row->id] = $row->name;
			$locations[] = $row;
		}
		return $locations;
	}

	/**
	 * [getAllGoods description]
	 */
	public function getAllCraft () {
		$this->pdo->lastCmd =
			'SELECT *'
			.' FROM '.self::TABLE_CRAFT
			.' ORDER BY name'
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();

		$goods = array();
		while (($row = $sth->fetch()) !== false) {
			$this->listGoods[$row->id] = $row->name;
			$goods[] = $row;
		}
		return $goods;
	}

	// -------------------------------------------
	// Prices
	// -------------------------------------------

	/**
	 * [getCompleteGood description]
	 * @param  integer $idGood [description]
	 * @return [type]          [description]
	 */
	public function getCompleteGood ($idGood) {
		$idGood = (int)$idGood;
		$this->pdo->lastCmd =
			'SELECT *'
			.' FROM '.self::TABLE_GOODS
			.' WHERE id = '.$this->pdo->quote((int)$idGood)
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();
		$result = $sth->fetchAll();
		$good = !empty($result[0]) ? $result[0] : array();

		if ($good) {
			$good->prices = $this->getPricesForGood($idGood);
		}
		$good->delta = (object)array(
			'highestBuy'  => NULL,
			'highestSell' => NULL,
			'lowestBuy'   => NULL,
			'lowestSell'  => NULL,
		);
		foreach ($good->prices as $p) {
			if (!empty($p->price_buy)) {
				if (empty($good->delta->highestBuy) || $good->delta->highestBuy->price < $p->price_buy) {
					$good->delta->highestBuy = $p;
					$good->delta->highestBuy->price = $p->price_buy;
				}
				if (empty($good->delta->lowestBuy) || $good->delta->lowestBuy->price > $p->price_buy) {
					$good->delta->lowestBuy = $p;
					$good->delta->lowestBuy->price = $p->price_buy;
				}
			}
			if (!empty($p->price_sell)) {
				if (empty($good->delta->highestSell) || $good->delta->highestSell->price < $p->price_sell) {
					$good->delta->highestSell = $p;
					$good->delta->highestSell->price = $p->price_sell;
				}
				if (empty($good->delta->lowestSell) || $good->delta->lowestSell->price > $p->price_sell) {
					$good->delta->lowestSell = $p;
					$good->delta->lowestSell->price = $p->price_sell;
				}
			}
		}
		return $good;
	}

	/**
	 * [getCompleteCraft description]
	 * @param  integer $idCraft [description]
	 * @return [type]           [description]
	 */
	public function getCompleteCraft ($idCraft) {
		$idCraft = (int)$idCraft;
		$this->pdo->lastCmd =
			'SELECT *'
			.' FROM '.self::TABLE_CRAFT
			.' WHERE id = '.$this->pdo->quote((int)$idCraft)
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();
		$result = $sth->fetchAll();
		$craft = !empty($result[0]) ? $result[0] : array();

		if ($craft) {
			$craft->prices = $this->getPricesForCraft($idCraft);
		}
		$craft->delta = (object)array(
			'highestBuy'  => NULL,
			'highestSell' => NULL,
			'lowestBuy'   => NULL,
			'lowestSell'  => NULL,
		);
		foreach ($craft->prices as $p) {
			if (!empty($p->price_buy)) {
				if (empty($craft->delta->highestBuy) || $craft->delta->highestBuy->price < $p->price_buy) {
					$craft->delta->highestBuy = $p;
					$craft->delta->highestBuy->price = $p->price_buy;
				}
				if (empty($craft->delta->lowestBuy) || $craft->delta->lowestBuy->price > $p->price_buy) {
					$craft->delta->lowestBuy = $p;
					$craft->delta->lowestBuy->price = $p->price_buy;
				}
			}
			if (!empty($p->price_sell)) {
				if (empty($craft->delta->highestSell) || $craft->delta->highestSell->price < $p->price_sell) {
					$craft->delta->highestSell = $p;
					$craft->delta->highestSell->price = $p->price_sell;
				}
				if (empty($craft->delta->lowestSell) || $craft->delta->lowestSell->price > $p->price_sell) {
					$craft->delta->lowestSell = $p;
					$craft->delta->lowestSell->price = $p->price_sell;
				}
			}
		}
		return $craft;
	}

	/**
	 * [getPricesForGood description]
	 * @param  integer $idGood [description]
	 * @return [type]          [description]
	 */
	public function getPricesForGood ($idGood) {
		$this->pdo->lastCmd =
			'SELECT p.price_buy, p.price_sell, l.id AS location_id, l.name AS location_name'
			.' FROM '.self::TABLE_PRICES.' AS p'
			.' JOIN '.self::TABLE_LOCATIONS.' AS l ON l.id = p.location_id'
			.' WHERE p.good_id = '.$this->pdo->quote((int) $idGood)
			.' AND ( p.price_buy > 0 OR p.price_sell > 0)'
			.' ORDER BY l.name'
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();
		return $sth->fetchAll();
	}

	/**
	 * [getPricesForCraft description]
	 * @param  integer $idCraft [description]
	 * @return [type]           [description]
	 */
	public function getPricesForCraft ($idCraft) {
		$this->pdo->lastCmd =
			'SELECT p.price_buy, p.price_sell, l.id AS location_id, l.name AS location_name'
			.' FROM '.self::TABLE_X_CL.' AS p'
			.' JOIN '.self::TABLE_LOCATIONS.' AS l ON l.id = p.location_id'
			.' WHERE p.craft_id = '.$this->pdo->quote((int) $idCraft)
			.' AND ( p.price_buy > 0 OR p.price_sell > 0)'
			.' ORDER BY l.name'
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();
		return $sth->fetchAll();
	}

	/**
	 * [getPricesForCurrentAndSpecificLocation description]
	 * @param  integer $locationId [description]
	 * @return [type]              [description]
	 */
	public function getPricesForCurrentAndSpecificLocation ($locationId) {
		return $this->getPricesSpecificLocation($this->currentLocation, $locationId);
	}

	/**
	 * [getPricesSpecificLocation description]
	 * @param  stdClass  $location   [description]
	 * @param  [type]    $locationId [description]
	 * @return [type]                [description]
	 */
	public function getPricesSpecificLocation (stdClass $location, $locationId) {
		$pricesForThisLocation = $this->getPricesForLocations(array($location->id), TRUE);

		$pricesOfOtherLocations = $this->getPricesForLocations(array($locationId));
		foreach ($pricesForThisLocation as $goodIndex => &$price) {
			if (!empty($pricesOfOtherLocations[$goodIndex])) {
				$goods   = &$pricesOfOtherLocations[$goodIndex];
				$profits = $this->getProfitSpan($goods);

				if (!empty($price->price_sell) && !empty($profits->highestId)) {
					if ($profits->highestPrice > $price->price_sell && $profits->highestPrice > $price->price_buy) {
						$price->buyer = (object)array(
							'id'    => $profits->highestId,
							'price' => (float)$profits->highestPrice,
							'delta' => (float)$profits->highestPrice - $price->price_sell,
							'name'  => $goods[$profits->highestId]->location_name,
						);
					}
				}
				if (!empty($price->price_buy) && !empty($profits->lowestId)) {
					if ($profits->lowestPrice < $price->price_buy && $profits->lowestPrice < $price->price_sell || $price->price_sell == 0) {
						$price->seller = (object)array(
							'id'    => $profits->lowestId,
							'price' => (float)$profits->lowestPrice,
							'delta' => (float)$price->price_buy - $profits->lowestPrice,
							'name'  => $goods[$profits->lowestId]->location_name,
						);
					}
				}
			}
		}

		return $pricesForThisLocation;

	}

	/**
	 * [getPricesForCurrentAndNeighbouringLocations description]
	 * @param  integer $hops  [description]
	 * @param  float   $distance_max Maximum distance for a hop
	 * @return [type]         [description]
	 */
	public function getPricesForCurrentAndNeighbouringLocations ($hops = 1, $distance_max = 999) {
		if (empty($this->currentLocation)) {
			throw new \Exception('No location set');
		}
		return $this->getPricesForThisAndNeighbouringLocations($this->currentLocation, $hops, $distance_max);
	}

	/**
	 * [getPricesForThisAndNeighbouringLocations description]
	 * @param  stdClass $location [description]
	 * @param  integer  $hops     [description]
	 * @param  float    $distance_max    Maximum distance for a hop
	 * @return array              [description]
	 */
	public function getPricesForThisAndNeighbouringLocations (stdClass $location, $hops = 1, $distance_max = 999) {
		$pricesForThisLocation = $this->getPricesForLocations(array($location->id), TRUE);

		$locations = $this->getNextLocations(array($location),$hops,$distance_max);
		if (!empty($locations)) {
			$locationIds = array();
			foreach ($locations as $s) {
				$locationIds[] = $s->id;
			}
			$pricesOfOtherLocations = $this->getPricesForLocations($locationIds);
			foreach ($pricesForThisLocation as $goodIndex => &$price) {
				if (!empty($pricesOfOtherLocations[$goodIndex])) {
					$goods   = &$pricesOfOtherLocations[$goodIndex];
					$profits = $this->getProfitSpan($goods);

					if (!empty($price->price_sell) && !empty($profits->highestId)) {
						if ($profits->highestPrice > $price->price_sell && $profits->highestPrice > $price->price_buy) {
							$price->buyer = (object)array(
								'id'    => $profits->highestId,
								'price' => (float)$profits->highestPrice,
								'delta' => (float)$profits->highestPrice - $price->price_sell,
								'name'  => $goods[$profits->highestId]->location_name,
							);
						}
					}
					if (!empty($price->price_buy) && !empty($profits->lowestId)) {
						if ($profits->lowestPrice < $price->price_buy && $profits->lowestPrice < $price->price_sell || $price->price_sell == 0) {
							$price->seller = (object)array(
								'id'    => $profits->lowestId,
								'price' => (float)$profits->lowestPrice,
								'delta' => (float)$price->price_buy - $profits->lowestPrice,
								'name'  => $goods[$profits->lowestId]->location_name,
							);
						}
					}
				}
			}
		}

		return $pricesForThisLocation;
	}

	/**
	 * Return id of highest buyer
	 * @param  array  $traders [description]
	 * @return [type]          [description]
	 */
	protected function getProfitSpan (array $traders) {
		$result = (object)array(
			'highestId'    => NULL,
			'highestPrice' => NULL,
			'lowestId'     => NULL,
			'lowestPrice'  => NULL,
		);
		foreach ($traders as $tryId => $trader) {
			if ((float)$trader->price_buy > 0 && (empty($result->highestId) || (float)$trader->price_buy > $result->highestPrice)) {
				$result->highestPrice   = (float)$trader->price_buy;
				$result->highestId      = $tryId;
			}
			if ((float)$trader->price_sell > 0 && (empty($result->lowestId) || (float)$trader->price_sell < $result->lowestPrice)) {
				$result->lowestPrice    = (float)$trader->price_sell;
				$result->lowestId       = $tryId;
			}
		}
		if (empty($result->highestId) && empty($result->lowestId)) {
			return array();
		}
		return $result;
	}

	/**
	 * [getPricesForCurrentLocation description]
	 * @return [type] [description]
	 */
	public function getPricesForCurrentLocation () {
		if (empty($this->currentLocation->id)) {
			throw new \Exception('No location set');
		}
		return $this->getPricesForLocation($this->currentLocation->id);
	}

	/**
	 * [getPricesForLocation description]
	 * @param  integer $id [description]
	 * @return [type]      [description]
	 */
	public function getPricesForLocation ($id) {
		return $this->getPricesForLocations(array($id), TRUE);
	}

	/**
	 * Returns a list of goods and prices at the given locations
	 * @param  array  $ids [description]
	 * @return [type]      [description]
	 */
	public function getPricesForLocations (array $ids, $singleMode = FALSE) {
		$this->pdo->lastCmd =
			'SELECT g.id, g.name, p.price_buy, p.price_sell, p.ts, l.id AS location_id, l.name AS location_name'
			.' FROM '.self::TABLE_LOCATIONS.' AS l'
			.' JOIN '.self::TABLE_PRICES.' AS p ON p.location_id = l.id'
			.' JOIN '.self::TABLE_GOODS.' AS g ON g.id = p.good_id'
			.' WHERE l.id IN ('.implode(',',$ids).')'
			.' AND NOT (p.price_sell = 0 AND p.price_buy = 0)'
			.' ORDER BY g.name, l.name'
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();

		$result = array();
		while (($row = $sth->fetch()) !== false) {
			$row->id          = (int)$row->id;
			$row->location_id = (int)$row->location_id;
			$row->price_buy   = (float)$row->price_buy;
			$row->price_sell  = (float)$row->price_sell;
			$row = $this->addTsStatus($row);
			if (empty($result[$row->id])) {
				$result[$row->id] = array();
			}
			if (empty($this->listGoods[$row->id])) {
				$this->listGoods[$row->id] = $row->name;
			}
			if (empty($this->listLocations[$row->location_id])) {
				$this->listLocations[$row->location_id] = $row->location_name;
			}
			if ($singleMode) {
				$result[$row->id] = $row;
			}
			else {
				$result[$row->id][$row->location_id] = $row;
			}
		}
		return $result;
	}

	// -------------------------------------------
	// Travelling
	// -------------------------------------------

	/**
	 * [getNextLocationsForCurrentLocation description]
	 * @param  integer $hops        [description]
	 * @param  float   $maxDistance Maximum distance for a hop
	 * @return [type]               [description]
	 */
	public function getNextLocationsForCurrentLocation ($hops = 1, $maxDistance = 999) {
		return $this->getNextLocations(
			array($this->currentLocation),
			$hops,
			$maxDistance
		);
	}

	/**
	 * [getNextLocations description]
	 * @param  array   $locations           [description]
	 * @param  integer $hops                [description]
	 * @param  float   $maxDistance         Maximum distance for a hop
	 * @param  array   $excludedLocationIds [description]
	 * @return [type]                       [description]
	 */
	public function getNextLocations (array $locations, $hops, $maxDistance = 999, array $excludedLocationIds = array()) {
		if (empty($locations)) {
			return array();
		}
		$locationIds = array();
		foreach ($locations as $s) {
			$locationIds[] = $s->id;
		}
		$excludedLocationIds = array_merge($locationIds, $excludedLocationIds);

		$this->pdo->lastCmd =
			'SELECT l.*, r.distance'
			.' FROM roads AS r'
			.' JOIN locations AS l ON l.id = r.location_id_to'
			.' WHERE l.id NOT IN ('.implode(',', $excludedLocationIds).')'
			.' AND r.location_id_from IN('.implode(',', $locationIds).')'
			.' AND r.distance < '.$this->pdo->quote((float)$maxDistance)
			.' ORDER BY l.name'
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();
		$results = $sth->fetchAll();

		$hops --;
		if ($hops > 0) {
			$results = array_merge($results, $this->getNextLocations($results, $hops, $maxDistance, $excludedLocationIds));
		}

		$foundIds = array();
		foreach ($results as $key => $r) {
			if (!empty($foundIds[$r->id])) {
				unset($results[$key]);
			}
			$foundIds[$r->id] = $r->id;
		}
		$results = $this->sortByKey($results,'name');
		return $results;
	}

	/**
	 * Invoke setRoadForCurrentLocation for current location
	 * @param integer $idLocation [description]
	 * @param float   $distance   [description]
	 * @return boolean
	 */
	public function setRoadForCurrentLocation ($idLocation, $distance) {
		if (empty($this->currentLocation->id)) {
			throw new \Exception('No location set');
		}
		return $this->setRoad($this->currentLocation->id, $idLocation, $distance);
	}

	/**
	 * Generate lane beweteen two locations
	 * @param integer $idLocation1 [description]
	 * @param integer $idLocation2 [description]
	 * @param float   $distance    [description]
	 * @return boolean
	 */
	public function setRoad ($idLocation1, $idLocation2, $distance) {
		$this->pdo->replace(self::TABLE_ROADS, $this->modifyRow(array(
			'location_id_from' => (int)$idLocation1,
			'location_id_to'   => (int)$idLocation2,
			'distance'         => (float)$distance,
		)));
		$this->pdo->replace(self::TABLE_ROADS, $this->modifyRow(array(
			'location_id_from' => (int)$idLocation2,
			'location_id_to'   => (int)$idLocation1,
			'distance'         => (float)$distance,
		)));
		return TRUE;
	}

	/**
	 * Get all goods, but divided into old and new ones
	 * @see getGoodsForLocationPlus
	 */
	public function getGoodsForCurrentLocationPlus() {
		if (empty($this->currentLocation->id)) {
			throw new \Exception('No location set');
		}
		return $this->getGoodsForLocationPlus($this->currentLocation->id);
	}

	/**
	 * Get all goods, but divided into old and new ones
	 * @param  integer $locationId [description]
	 * @return object  with new & old
	 */
	public function getGoodsForLocationPlus ($locationId) {
		$oldGoods = $this->getGoodsForLocation($locationId);
		$ids = array();
		foreach ($oldGoods as $g) {
			$ids[] = $g->id;
		}
		$newGoods = $this->getGoodsNotInArray($ids);
		$return = (object)array(
			'old' => $oldGoods,
			'new' => $newGoods,
		);
		return $return;
	}

	public function getGoodsForLocation ($locationId) {
		$this->pdo->lastCmd =
			'SELECT g.*'
			.' FROM '.self::TABLE_GOODS.' AS g'
			.' JOIN '.self::TABLE_PRICES.' AS p ON g.id = p.good_id'
			.' WHERE p.location_id = '.$this->pdo->quote((int) $locationId)
			.' AND NOT (p.price_sell = 0 AND p.price_buy = 0)'
			.' ORDER BY g.name'
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();
		return $sth->fetchAll();
	}

	public function getGoodsNotInArray (array $ids) {
		$this->pdo->lastCmd =
			'SELECT g.*'
			.' FROM '.self::TABLE_GOODS.' AS g'
			.(!empty($ids) ? ' WHERE g.id NOT IN ('.implode(',',$ids).')' : '')
			.' ORDER BY g.name'
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();
		return $sth->fetchAll();
	}

	/**
	 * Get all crafts, but divided into old and new ones
	 * @see getGoodsForLocationPlus
	 */
	public function getCraftForCurrentLocationPlus() {
		if (empty($this->currentLocation->id)) {
			throw new \Exception('No location set');
		}
		return $this->getCraftForLocationPlus($this->currentLocation->id);
	}

	/**
	 * Get all crafts, but divided into old and new ones
	 * @param  integer $locationId [description]
	 * @return object  with new & old
	 */
	public function getCraftForLocationPlus ($locationId) {
		$oldCraft = $this->getCraftForLocation($locationId);
		$ids = array();
		foreach ($oldCraft as $g) {
			$ids[] = $g->id;
		}
		$newCraft = $this->getCraftNotInArray($ids);
		$return = (object)array(
			'old' => $oldCraft,
			'new' => $newCraft,
		);
		return $return;
	}

	public function getCraftForCurrentLocation() {
		if (empty($this->currentLocation->id)) {
			throw new \Exception('No location set');
		}
		return $this->getCraftForLocation($this->currentLocation->id);
	}

	public function getCraftForLocation ($locationId) {
		$this->pdo->lastCmd =
			'SELECT g.*, p.price_sell, p.price_buy'
			.' FROM '.self::TABLE_CRAFT.' AS g'
			.' JOIN '.self::TABLE_X_CL.' AS p ON g.id = p.craft_id'
			.' WHERE p.location_id = '.$this->pdo->quote((int) $locationId)
			.' AND NOT (p.price_sell = 0 AND p.price_buy = 0)'
			.' ORDER BY g.name'
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();
		return $sth->fetchAll();
	}

	public function getCraftNotInArray (array $ids) {
		$this->pdo->lastCmd =
			'SELECT g.*'
			.' FROM '.self::TABLE_CRAFT.' AS g'
			.(!empty($ids) ? ' WHERE g.id NOT IN ('.implode(',',$ids).')' : '')
			.' ORDER BY g.name'
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();
		return $sth->fetchAll();
	}

	// -------------------------------------------
	// UPDATE
	// -------------------------------------------

	/**
	 * Will invoke setPriceForLocation for current location
	 * @param integer $idGood    [description]
	 * @param float   $priceBuy  [description]
	 * @param float   $priceSell [description]
	 * @return boolean           [description]
	 */
	public function setPriceForCurrentLocation ($idGood, $priceBuy, $priceSell = 0) {
		if (empty($this->currentLocation->id)) {
			throw new \Exception('No location set');
		}
		return $this->setPriceForLocation($this->currentLocation->id, $idGood, $priceBuy, $priceSell);
	}

	/**
	 * Set new prices for good at given location
	 * @param integer  $idLocation [description]
	 * @param integer  $idGood     [description]
	 * @param float    $priceBuy   [description]
	 * @param float    $priceSell  [description]
	 * @return boolean             [description]
	 */
	public function setPriceForLocation ($idLocation, $idGood, $priceBuy, $priceSell = 0) {
		return ($this->pdo->replace(self::TABLE_PRICES, $this->modifyRow(array(
			'good_id'      => (int)$idGood,
			'location_id'  => (int)$idLocation,
			'price_buy'    => (float)$priceBuy,
			'price_sell'   => (float)$priceSell,
		))) >= 0);
	}

	/**
	 * Will invoke setPriceForLocation for current location
	 * @param integer $idCraft   [description]
	 * @param float   $priceBuy  [description]
	 * @param float   $priceSell [description]
	 * @return boolean           [description]
	 */
	public function setCraftPriceForCurrentLocation ($idCraft, $priceBuy, $priceSell = 0) {
		if (empty($this->currentLocation->id)) {
			throw new \Exception('No location set');
		}
		return $this->setCraftPriceForLocation($this->currentLocation->id, $idCraft, $priceBuy, $priceSell);
	}

	/**
	 * Set new prices for good at given location
	 * @param integer  $idLocation [description]
	 * @param integer  $idCraft    [description]
	 * @param float  $priceBuy   [description]
	 * @param float  $priceSell  [description]
	 * @return boolean             [description]
	 */
	public function setCraftPriceForLocation ($idLocation, $idCraft, $priceBuy, $priceSell = 0) {
		return ($this->pdo->replace(self::TABLE_X_CL, $this->modifyRow(array(
			'craft_id'     => (int)$idCraft,
			'location_id'  => (int)$idLocation,
			'price_buy'    => (float)$priceBuy,
			'price_sell'   => (float)$priceSell,
		))) >= 0);
	}

	/**
	 * Will invoke updateLocation for current location
	 * @param [type]  $idGood    [description]
	 * @param [type]  $priceBuy  [description]
	 * @param integer $priceSell [description]
	 * @return boolean           [description]
	 */
	public function updateCurrentLocation ($name, $description = NULL) {
		if (empty($this->currentLocation->id)) {
			throw new \Exception('No location set');
		}
		return $this->updateLocation ($this->currentLocation->id, $name, $description);
	}

	/**
	 * Set new name and description for location
	 * @param  integer $id          [description]
	 * @param  string  $name        [description]
	 * @param  string  $description [description]
	 * @return boolean              [description]
	 */
	public function updateLocation ($id, $name, $description = NULL) {
		$data = array(
			'name'        => $name,
			'description' => $description,
		);
		if (empty($description)) {
			unset($data['description']);
		}
		return ($this->pdo->update(
			self::TABLE_LOCATIONS,
			$this->modifyRow($data),
			'id='.$this->pdo->quote($id)
		));
	}

	/**
	 * Set new name and description for good
	 * @param  integer $id          [description]
	 * @param  string  $name        [description]
	 * @param  string  $description [description]
	 * @return boolean              [description]
	 */
	public function updateGood ($id, $name, $description = NULL) {
		$data = array(
			'name'        => $name,
			'description' => $description,
		);
		if (empty($description)) {
			unset($data['description']);
		}
		return ($this->pdo->update(
			self::TABLE_GOODS,
			$this->modifyRow($data),
			'id='.$this->pdo->quote($id)
		));
	}

	/**
	 * Set new name and description for craft
	 * @param  integer $id          [description]
	 * @param  string  $name        [description]
	 * @param  string  $description [description]
	 * @param  float   $minRange    [description]
	 * @param  float   $maxRange    [description]
	 * @return boolean              [description]
	 */
	public function updateCraft ($id, $name, $description = NULL, $cargo = NULL, $speed = NULL, $minRange = 0, $maxRange = 0) {
		if (!$minRange) {
			$minRange = floor($maxRange / 2);
		}
		$data = array(
			'name'        => $name,
			'description' => $description,
			'cargo'       => (int)$cargo,
			'speed'       => (int)$speed,
			'range_min'   => (float)$minRange,
			'range_max'   => (float)$maxRange,
		);
		if (empty($name)) {
			unset($data['name']);
		}
		if (empty($description)) {
			unset($data['description']);
		}
		if (empty($cargo)) {
			unset($data['cargo']);
		}
		if (empty($speed)) {
			unset($data['speed']);
		}
		if (empty($minRange)) {
			unset($data['range_min']);
		}
		if (empty($maxRange)) {
			unset($data['range_max']);
		}
		return ($this->pdo->update(
			self::TABLE_CRAFT,
			$this->modifyRow($data),
			'id='.$this->pdo->quote($id)
		));
	}

	// -------------------------------------------
	// DELETE
	// -------------------------------------------

	/**
	 * [deleteLaneForCurrentLocation description]
	 * @param  integer $idLocation [description]
	 * @return [type]              [description]
	 */
	public function deleteLaneForCurrentLocation ($idLocation) {
		if (empty($this->currentLocation->id)) {
			throw new \Exception('No location set');
		}
		return $this->deleteLane($this->currentLocation->id, $idLocation);
	}

	/**
	 * Delete lane bewteen two locations
	 * @param  integer $idLocation1 [description]
	 * @param  integer $idLocation2 [description]
	 * @return boolean              [description]
	 */
	public function deleteLane ($idLocation1, $idLocation2) {
		return $this->pdo->delete(
			self::TABLE_ROADS,
			'(location_id_from='.$this->pdo->quote((int)$idLocation1).' AND location_id_to='.$this->pdo->quote((int)$idLocation2).')'
			.'OR (location_id_from='.$this->pdo->quote((int)$idLocation2).' AND location_id_to='.$this->pdo->quote((int)$idLocation1).')'
		);
	}

	// -------------------------------------------
	// HELPERS
	// -------------------------------------------

	/**
	 * [addTsStatus description]
	 * @param stdClass $row [description]
	 */
	protected function addTsStatus(stdClass $row) {
		if (!empty($row->ts)) {
			$row->tsStatus = 0;
			$ts = strtotime($row->ts);
			if ($ts + self::SECONDS_VERY_OLD < $this->tsNow) {
				$row->tsStatus = self::STATUS_VERY_OLD;
			}
			elseif ($ts + self::SECONDS_OLD < $this->tsNow) {
				$row->tsStatus = self::STATUS_OLD;
			}
			elseif ($ts + self::SECONDS_NEW > $this->tsNow) {
				$row->tsStatus = self::STATUS_NEW;
			}
		}
		return $row;
	}

	/**
	 * [modifyRow description]
	 * @param  array  $row [description]
	 * @return [type]      [description]
	 */
	protected function modifyRow (array $row) {
		$currentTraderId = $this->getCurrentTraderId();
		if (!empty($currentTraderId)) {
			$row['trader_id'] = $currentTraderId;
		}
		return $row;
	}

	protected function sortByKey (array $results, $keyName) {
		$order = array();
		foreach ($results as $key => $row) {
			$order[$key] = $row->$keyName;
		}
		array_multisort($order, SORT_ASC, $results);
		return $results;
	}

	/**
	 * [returnPassword description]
	 * @param  string $password [description]
	 * @return string           [description]
	 */
	protected function returnPassword($password) {
		return md5($password);
	}
}
