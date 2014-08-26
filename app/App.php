<?php
/**
 * @class App
 */
class App {
	public $path  = array();

	/**
	 * [__construct description]
	 */
	public function __construct() {
		if (!empty($_REQUEST['p'])) {
			$path = $_REQUEST['p'];
		}
		elseif (!empty($_SERVER['PATH_INFO'])) {
			$path = substr($_SERVER['PATH_INFO'],1);
		}
		if (empty($path[0])) {$path[0] = NULL;}
		if (empty($path[1])) {$path[1] = NULL;}
		if (empty($path[2])) {$path[2] = NULL;}
		$this->path    = (!empty($path)) ? explode('/', $path) : array();
	}

	/**
	 * [url description]
	 * @param  string  $path       [description]
	 * @param  string  $id         [description]
	 * @param  array   $parameters [description]
	 * @param  boolean $absolute   [description]
	 * @return string              [description]
	 */
	public function url ($path, $id = NULL, $subId = NULL, array $parameters = array(), $absolute = FALSE) {
		$url = !empty($path) ? $path.'/'.$id : NULL;
		if (!empty($subId)) {
			$url .= '/'.$subId;
		}

		$url = !CONFIG_USE_NICE_URLS
			? ($_SERVER['SCRIPT_NAME'].(!empty($url) ? '/'.$url : ''))
			: dirname($_SERVER['SCRIPT_NAME']). '/' . $url
		;

		if (!empty($parameters)) {
			$url .= '?'.http_build_query($parameters);
		}
		return $absolute ? returnCompleteUrl($url) : $url;
	}

	/**
	 * [currentUrl description]
	 * @param  array   $parameters [description]
	 * @param  boolean $absolute   [description]
	 * @return [type]              [description]
	 */
	public function currentUrl (array $parameters = array(), $absolute = FALSE) {
		return $this->url($this->path[0], $this->path[1], $this->path[2], $parameters, $absolute);
	}

	/**
	 * [redirect description]
	 * @param  [type]  $path       [description]
	 * @param  [type]  $id         [description]
	 * @param  integer $statusCode [description]
	 * @return [type]              [description]
	 */
	public function redirect ($path, $id = NULL, $statusCode = 303) {
		header('Location: '.$this->url($path,$id,NULL,array(), TRUE), $statusCode);
	}

	public function echoStatus ($status) {
		switch ($status) {
			case EliteTrader::STATUS_NEW:
				echo('fa-plus-square-o');
				break;
			case EliteTrader::STATUS_OLD:
				echo('fa-minus-square-o');
				break;
			case EliteTrader::STATUS_VERY_OLD:
				echo('fa-minus-square');
				break;
			default:
				echo('fa-square-o');
				break;
		}
	}

	public function echoNumber ($number, $unit = NULL) {
		if (is_int($number)) {
			$str = (number_format($number));
		} else {
			$str = (number_format($number,2));
		}
		echo ($str . (!empty($unit) ? '&nbsp;'.$unit : ''));
	}
}