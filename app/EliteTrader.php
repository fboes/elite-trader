<?php

class EliteTrader {
	const TABLE_GOODS     = 'goods';
	const TABLE_ROADS     = 'roads';
	const TABLE_PRICES    = 'prices';
	const TABLE_LOCATIONS = 'locations';

	protected $pdo;

	public $currentLocation;

	public $listGoods      = array();
	public $listLocations  = array();

	public function __construct(SuperPDO $pdo) {
		$this->pdo = $pdo;
	}

	/**
	 * Set current location and get all information
	 * @param   integer $id [description]
	 * @return  boolean [description]
	 */
	public function setCurrentLocation ($id) {
		$this->pdo->lastCmd =
			'SELECT l.*'
			.' FROM locations AS l'
			.' WHERE l.id = '.$this->pdo->quote((int)$id)
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();
		$result = $sth->fetchAll(SuperPDO::FETCH_ASSOC);
		$this->currentLocation = !empty($result[0]) ? $result[0] : array();
		if (!empty($this->currentLocation)) {
			$this->currentLocation['id'] = (int)$this->currentLocation['id'];
			return TRUE;
		}
		return FALSE;
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
			array(
				'name'        => $name,
				'description' => $description,
			)
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
			array(
				'name'        => $name,
				'description' => $description,
			)
		)) {
			$id = $this->pdo->lastInsertId();
			$this->listGoods[$id] = $name;
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
		while (($row = $sth->fetch(SuperPDO::FETCH_ASSOC)) !== false) {
			$this->listGoods[$row['id']] = $row['name'];
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
		while (($row = $sth->fetch(SuperPDO::FETCH_ASSOC)) !== false) {
			$this->listLocations[$row['id']] = $row['name'];
			$locations[] = $row;
		}
		return $locations;
	}

	// -------------------------------------------
	// Prices
	// -------------------------------------------

	public function getCompleteGood ($idGood) {
		$this->pdo->lastCmd =
			'SELECT *'
			.' FROM '.self::TABLE_GOODS
			.' WHERE id = '.$this->pdo->quote((int)$idGood)
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();
		$result = $sth->fetchAll(SuperPDO::FETCH_ASSOC);
		$good = !empty($result[0]) ? $result[0] : array();

		if ($good) {
			$good['prices'] = $this->getPricesForGood($idGood);
		}
		$good['delta'] = array(
			'highestBuy'  => NULL,
			'highestSell' => NULL,
			'lowestBuy'   => NULL,
			'lowestSell'  => NULL,
		);
		foreach ($good['prices'] as $p) {
			if (!empty($p['price_buy'])) {
				if (empty($good['delta']['highestBuy']) || $good['delta']['highestBuy']['price'] < $p['price_buy']) {
					$good['delta']['highestBuy'] = $p;
					$good['delta']['highestBuy']['price'] = $p['price_buy'];
				}
				if (empty($good['delta']['lowestBuy']) || $good['delta']['lowestBuy']['price'] > $p['price_buy']) {
					$good['delta']['lowestBuy'] = $p;
					$good['delta']['lowestBuy']['price'] = $p['price_buy'];
				}
			}
			if (!empty($p['price_sell'])) {
				if (empty($good['delta']['highestSell']) || $good['delta']['highestSell']['price'] < $p['price_sell']) {
					$good['delta']['highestSell'] = $p;
					$good['delta']['highestSell']['price'] = $p['price_sell'];
				}
				if (empty($good['delta']['lowestSell']) || $good['delta']['lowestSell']['price'] > $p['price_sell']) {
					$good['delta']['lowestSell'] = $p;
					$good['delta']['lowestSell']['price'] = $p['price_sell'];
				}
			}
		}
		return $good;
	}

	public function getPricesForGood ($idGood) {
		$this->pdo->lastCmd =
			'SELECT p.price_buy, p.price_sell, l.id AS location_id, l.name AS location_name'
			.' FROM '.self::TABLE_PRICES.' AS p'
			.' JOIN '.self::TABLE_LOCATIONS.' AS l ON l.id = p.location_id'
			.' WHERE p.good_id = '.$this->pdo->quote((int) $idGood)
			.' ORDER BY l.name'
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();
		return $sth->fetchAll(SuperPDO::FETCH_ASSOC);
	}

	/**
	 * [getPricesForCurrentAndNeighbouringLocations description]
	 * @param  integer $hops [description]
	 * @param  float   $hopdistance Maximum distance for a hop
	 * @return [type]        [description]
	 */
	public function getPricesForCurrentAndNeighbouringLocations ($hops = 1, $hopdistance = 999) {
		if (empty($this->currentLocation)) {
			throw new \Exception('No location set');
		}
		return $this->getPricesForThisAndNeighbouringLocations($this->currentLocation, $hops, $hopdistance);
	}

	/**
	 * [getPricesForThisAndNeighbouringLocations description]
	 * @param  array   $location [description]
	 * @param  integer $hops     [description]
	 * @param  float   $hopdistance Maximum distance for a hop
	 * @return array             [description]
	 */
	public function getPricesForThisAndNeighbouringLocations ($location, $hops = 1, $hopdistance = 999) {
		$pricesForThisLocation = $this->getPricesForLocations(array($location['id']), TRUE);

		$locations = $this->getNextLocations(array($location),$hops,$hopdistance);
		if (!empty($locations)) {
			$locationIds = array();
			foreach ($locations as $s) {
				$locationIds[] = $s['id'];
			}
			$pricesOfOtherLocations = $this->getPricesForLocations($locationIds);
			foreach ($pricesForThisLocation as $goodIndex => &$price) {
				if (!empty($pricesOfOtherLocations[$goodIndex])) {
					$goods   = &$pricesOfOtherLocations[$goodIndex];
					$profits = $this->getProfitSpan($goods);
					$price['buyer']  = array();
					$price['seller'] = array();

					if (!empty($price['price_sell']) && !empty($profits['highestId'])) {
						if ($profits['highestPrice'] > $price['price_sell'] && $profits['highestPrice'] > $price['price_buy']) {
							$price['buyer'] = array(
								'id'    => $profits['highestId'],
								'price' => (int)$profits['highestPrice'],
								'delta' => (int)$profits['highestPrice'] - $price['price_sell'],
								'name'  => $goods[$profits['highestId']]['location_name'],
							);
						}
					}
					if (!empty($price['price_buy']) && !empty($profits['lowestId'])) {
						if ($profits['lowestPrice'] < $price['price_buy'] && $profits['lowestPrice'] < $price['price_sell'] || $price['price_sell'] == 0) {
							$price['seller'] = array(
								'id'    => $profits['lowestId'],
								'price' => (int)$profits['lowestPrice'],
								'delta' => (int)$price['price_buy'] - $profits['lowestPrice'],
								'name'  => $goods[$profits['lowestId']]['location_name'],
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
	 * @param  [type] $priceSell [description]
	 * @return [type]            [description]
	 */
	protected function getProfitSpan (array $traders) {
		$result = array();
		$result['highestId'] = NULL;
		$result['highestPrice'] = NULL;
		$result['lowestId']  = NULL;
		$result['lowestPrice']  = NULL;
		foreach ($traders as $tryId => $trader) {
			if ((int)$trader['price_buy'] > 0 && (empty($result['highestId']) || (int)$trader['price_buy'] > $result['highestPrice'])) {
				$result['highestPrice']   = (int)$trader['price_buy'];
				$result['highestId']      = $tryId;
			}
			if ((int)$trader['price_sell'] > 0 && (empty($result['lowestId']) || (int)$trader['price_sell'] < $result['lowestPrice'])) {
				$result['lowestPrice']    = (int)$trader['price_sell'];
				$result['lowestId']       = $tryId;
			}
		}
		if (empty($result['highestId']) && empty($result['lowestId'])) {
			return array();
		}
		return $result;
	}

	/**
	 * [getPricesForCurrentLocation description]
	 * @return [type] [description]
	 */
	public function getPricesForCurrentLocation () {
		if (empty($this->currentLocation['id'])) {
			throw new \Exception('No location set');
		}
		return $this->getPricesForLocation($this->currentLocation['id']);
	}

	/**
	 * [getPricesForLocation description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
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
			'SELECT g.id, g.name, p.price_buy, p.price_sell, l.id AS location_id, l.name AS location_name'
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
		while (($row = $sth->fetch(SuperPDO::FETCH_ASSOC)) !== false) {
			$row['id']          = (int)$row['id'];
			$row['location_id'] = (int)$row['location_id'];
			$row['price_buy']   = (int)$row['price_buy'];
			$row['price_sell']  = (int)$row['price_sell'];
			if (empty($result[$row['id']])) {
				$result[$row['id']] = array();
			}
			if (empty($this->listGoods[$row['id']])) {
				$this->listGoods[$row['id']] = $row['name'];
			}
			if (empty($this->listLocations[$row['location_id']])) {
				$this->listLocations[$row['location_id']] = $row['location_name'];
			}
			if ($singleMode) {
				$result[$row['id']] = $row;
			}
			else {
				$result[$row['id']][$row['location_id']] = $row;
			}
		}
		return $result;
	}

	// -------------------------------------------
	// Travelling
	// -------------------------------------------

	/**
	 * [getNextLocationsForCurrentLocation description]
	 * @param  integer $hops [description]
	 * @param  float   $hopdistance Maximum distance for a hop
	 * @return [type]        [description]
	 */
	public function getNextLocationsForCurrentLocation ($hops = 1, $hopdistance = 999) {
		return $this->getNextLocations(
			array($this->currentLocation),
			$hops,
			$hopdistance
		);
	}

	/**
	 * [getNextLocations description]
	 * @param  array  $locations           [description]
	 * @param  [type] $hops                [description]
	 * @param  float   $hopdistance Maximum distance for a hop
	 * @param  array  $excludedLocationIds [description]
	 * @return [type]                      [description]
	 */
	public function getNextLocations (array $locations, $hops, $hopdistance = 999, array $excludedLocationIds = array()) {
		if (empty($locations)) {
			return array();
		}
		$locationIds = array();
		foreach ($locations as $s) {
			$locationIds[] = $s['id'];
		}
		$excludedLocationIds = array_merge($locationIds, $excludedLocationIds);

		$this->pdo->lastCmd =
			'SELECT l.*, r.distance'
			.' FROM roads AS r'
			.' JOIN locations AS l ON l.id = r.location_id_to'
			.' WHERE l.id NOT IN ('.implode(',', $excludedLocationIds).')'
			.' AND r.location_id_from IN('.implode(',', $locationIds).')'
			.' AND r.distance < '.$this->pdo->quote((float)$hopdistance)
			.' ORDER BY l.name'
		;
		$this->pdo->lastData = NULL;
		$sth = $this->pdo->prepare($this->pdo->lastCmd);
		$sth->execute();
		$results = $sth->fetchAll(SuperPDO::FETCH_ASSOC);

		$hops --;
		if ($hops > 0) {
			$results = array_merge($results, $this->getNextLocations($results, $hops, $hopdistance, $excludedLocationIds));
		}
		return $results;
	}

	/**
	 * Invoke setLaneForCurrentLocation for current location
	 * @param integer $idLocation [description]
	 * @param float   $distance   [description]
	 * @return boolean
	 */
	public function setLaneForCurrentLocation ($idLocation, $distance) {
		if (empty($this->currentLocation['id'])) {
			throw new \Exception('No location set');
		}
		return $this->setLane($this->currentLocation['id'], $idLocation, $distance);
	}

	/**
	 * Generate lane beweteen two locations
	 * @param integer $idLocation1 [description]
	 * @param integer $idLocation2 [description]
	 * @param float   $distance    [description]
	 * @return boolean
	 */
	public function setLane ($idLocation1, $idLocation2, $distance) {
		$this->pdo->replace(self::TABLE_ROADS, array(
			'location_id_from' => (int)$idLocation1,
			'location_id_to'   => (int)$idLocation2,
			'distance'         => (float)$distance,
		));
		$this->pdo->replace(self::TABLE_ROADS, array(
			'location_id_from' => (int)$idLocation2,
			'location_id_to'   => (int)$idLocation1,
			'distance'         => (float)$distance,
		));
		return TRUE;
	}


	// -------------------------------------------
	// UPDATE
	// -------------------------------------------

	/**
	 * Will invoke setPriceForLocation for current location
	 * @param integer $idGood    [description]
	 * @param integer $priceBuy  [description]
	 * @param integer $priceSell [description]
	 * @return boolean           [description]
	 */
	public function setPriceForCurrentLocation ($idGood, $priceBuy, $priceSell = 0) {
		if (empty($this->currentLocation['id'])) {
			throw new \Exception('No location set');
		}
		return $this->setPriceForLocation($this->currentLocation['id'], $idGood, $priceBuy, $priceSell);
	}

	/**
	 * Set new prices for good at given location
	 * @param integer  $idLocation [description]
	 * @param integer  $idGood     [description]
	 * @param integer  $priceBuy   [description]
	 * @param integer  $priceSell  [description]
	 * @return boolean             [description]
	 */
	public function setPriceForLocation ($idLocation, $idGood, $priceBuy, $priceSell = 0) {
		return ($this->pdo->replace(self::TABLE_PRICES, array(
			'good_id'      => (int)$idGood,
			'location_id'  => (int)$idLocation,
			'price_buy'    => (int)$priceBuy,
			'price_sell'   => (int)$priceSell,
		)) >= 0);
	}

	/**
	 * Will invoke updateLocation for current location
	 * @param [type]  $idGood    [description]
	 * @param [type]  $priceBuy  [description]
	 * @param integer $priceSell [description]
	 * @return boolean           [description]
	 */
	public function updateCurrentLocation ($name, $description = NULL) {
		if (empty($this->currentLocation['id'])) {
			throw new \Exception('No location set');
		}
		return $this->updateLocation ($this->currentLocation['id'], $name, $description);
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
			$data,
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
			$data,
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
		if (empty($this->currentLocation['id'])) {
			throw new \Exception('No location set');
		}
		return $this->deleteLane($this->currentLocation['id'], $idLocation);
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

}
