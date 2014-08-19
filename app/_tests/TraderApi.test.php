<?php

require('../vendor/small-php-helpers/Tester.php');
require('../TraderApi.php');

class TraderApiTest extends Tester {
	public function testSimple () {
		#$this->outputLine($api);
	}

	/*
	public function testMemoization () {
		$baseUrl = 'http://3960.org/';

		require_once('../Memoization.php');

		$api = new HttpApi($baseUrl, HttpApi::REPLY_TYPE_HTML);
		$api->setMemoization(new Memoization());
		$this->assertTrue(is_object($api));
		$this->outputLine($api);

		$result = $api->get(array('a' => 'b'));
		$this->assertTrue(!empty($result));
	}
	*/
}

TraderApiTest::doTest();
