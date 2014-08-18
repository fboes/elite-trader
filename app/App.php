<?php
/**
 * @class App
 */
class App {
	public $path = NULL;
	public $id   = NULL;

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
		if (!empty($path)) {
			$pathParts = explode('/', $path);
			$this->path = (!empty($pathParts[0])) ? $pathParts[0] : NULL;
			$this->id   = (!empty($pathParts[1])) ? $pathParts[1] : NULL;
		}
	}

	/**
	 * [url description]
	 * @param  string  $path       [description]
	 * @param  string  $id         [description]
	 * @param  array   $parameters [description]
	 * @param  boolean $absolute   [description]
	 * @return string              [description]
	 */
	public function url ($path, $id = NULL, array $parameters = array(), $absolute = FALSE) {
		$url = !empty($path) ? $path.'/'.$id : NULL;

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
		return $this->url($this->path, $this->id, $parameters, $absolute);
	}

	/**
	 * [redirect description]
	 * @param  [type]  $path       [description]
	 * @param  [type]  $id         [description]
	 * @param  integer $statusCode [description]
	 * @return [type]              [description]
	 */
	public function redirect ($path, $id = NULL, $statusCode = 303) {
		header('Location: '.$this->url($path,$id,array(), TRUE), $statusCode);
	}
}