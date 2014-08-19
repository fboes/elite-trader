<?php

require_once('vendor/small-php-helpers/HttpApi.php');

/**
 * @class TraderApi
 * Connect to Trader-API via HTTP
 */
class TraderApi extends HttpApi {

	/**
	 * Invoke HttpApi object
	 */
	public function __construct ($baseUrl = NULL, $standardReplyMimeType = self::REPLY_TYPE_PLAIN) {
		$this->baseUrl               = 'http://localhost:3000';
		$this->standardReplyMimeType = self::REPLY_TYPE_JSON;
		$this->clearLastrequest();
	}
}