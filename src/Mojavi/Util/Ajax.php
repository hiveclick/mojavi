<?php
namespace Mojavi\Util;

use Mojavi\Request\Request;
use Mojavi\Core\MojaviObject;

/**
 * Ajax is used to encapsulate ajax calls to an api server
 */
class Ajax extends MojaviObject {

	const DEBUG = true;
	const APCU_CACHE_TTL = 14400;

	protected $func;
	protected $http_cookie_jar;
	protected $timeout;

	/**
	 * Returns the timeout
	 * @return integer
	 */
	function getTimeout() {
		if (is_null($this->timeout)) {
			$this->timeout = 60;
		}
		return $this->timeout;
	}
	/**
	 * Sets the timeout
	 * @param integer
	 */
	function setTimeout($arg0) {
		$this->timeout = $arg0;
		return $this;
	}

	/**
	 * Returns the func
	 * @return string
	 */
	function getFunc() {
		if (is_null($this->func)) {
			$this->func = "";
		}
		return $this->func;
	}

	/**
	 * Sets the func
	 * @param $arg0 string
	 */
	function setFunc($arg0) {
		$this->func = $arg0;
		return $this;
	}

	/**
	 * Static method for making an ajax call
	 * @param string $func
	 * @param Request|array $request
	 * @param integer $method
	 * @param string $url
	 * @param array $headers
	 * @param boolean $remove_unsafe_params
	 * @return array
	 */
	static function sendXmlAsAjax($func, $request, $method = Request::GET, $url = null, $headers = array(), $remove_unsafe_params = true) {
		$ret_val = self::sendXml($func, $request, $method, $url, $headers, $remove_unsafe_params);
		return \Zend\Json\Json::encode(\Zend\Json\Json::decode($ret_val, \Zend\Json\Json::TYPE_ARRAY), true);
	}

	/**
	 * Static method for making an ajax call
	 * @param string $cache_filename
	 * @param string $func
	 * @param Request|array $request
	 * @param integer $method
	 * @param string $url
	 * @param array $headers
	 * @param boolean $remove_unsafe_params
	 * @return array
	 */
	static function sendXmlAndCache($cache_filename, $func, $request, $method = Request::GET, $url = null, $headers = array(), $remove_unsafe_params = true) {
		// use APCu instead of the file cache
		if (ini_get('apc.enabled')) {
			$cache_key = md5($cache_filename);
			if (apc_exists($cache_key)) {
				$cache_contents = apc_fetch($cache_key);
				if (trim($cache_contents) != '') {
					return simplexml_load_string($cache_contents);
				}
			}
		} else if (file_exists($cache_filename)) {
			$cache_contents = file_get_contents($cache_filename);
			if (trim($cache_contents) != '') {
				return simplexml_load_string($cache_contents);
			}
		}
		// Request is not cached, so make a request, cache the response, and return it
		$cache_contents = self::sendRaw($func, $request, $method, $url, $headers, $remove_unsafe_params);

		if (ini_get('apc.enabled')) {
			$cache_key = md5($cache_filename);
			apc_store($cache_key, $cache_contents, self::APCU_CACHE_TTL);
		} else {
			if (!file_exists(dirname($cache_filename))) {
				@mkdir(dirname($cache_filename), 0777, true);
			}
			@file_put_contents($cache_filename, $cache_contents);
			@chmod($cache_filename, 0777);
		}
		return simplexml_load_string($cache_contents);
	}

	/**
	 * Static method for making an ajax call
	 * @param string $func
	 * @param Request|array $request
	 * @param integer $method
	 * @param string $url
	 * @param array $headers
	 * @param boolean $remove_unsafe_params
	 * @return array
	 */
	static function sendXml($func, $request, $method = Request::GET, $url = null, $headers = array(), $remove_unsafe_params = true) {
		$ajax = new Ajax();
		$ajax->setFunc($func);
		$response = $ajax->send($request, $method, $url, $headers, $remove_unsafe_params);
		try {
			$ret_val = simplexml_load_string(utf8_encode($response->getBody()));
		} catch (\Exception $e) {
			throw $e;
		}
		return $ret_val;
	}

	/**
	 * Static method for making an ajax call
	 * @param string $cache_filename
	 * @param string $func
	 * @param Request|array $request
	 * @param integer $method
	 * @param string $url
	 * @param array $headers
	 * @param boolean $remove_unsafe_params
	 * @return array
	 */
	static function sendAjaxAndCache($cache_filename, $func, $request, $method = Request::GET, $url = null, $headers = array(), $remove_unsafe_params = true) {
		// use APCu instead of the file cache
		if (ini_get('apc.enabled')) {
			$cache_key = basename($cache_filename);
			if (apc_exists($cache_key)) {
				$cache_contents = apc_fetch($cache_key);
				if (trim($cache_contents) != '') {
					return \Zend\Json\Json::decode($cache_contents, \Zend\Json\Json::TYPE_ARRAY);
				}
			}
		} else if (file_exists($cache_filename)) {
			$cache_contents = file_get_contents($cache_filename);
			if (trim($cache_contents) != '') {
				return \Zend\Json\Json::decode($cache_contents, \Zend\Json\Json::TYPE_ARRAY);
			}
		}
		// Request is not cached, so make a request, cache the response, and return it
		$cache_contents = self::sendRaw($func, $request, $method, $url, $headers, $remove_unsafe_params);

		if (ini_get('apc.enabled')) {
			$cache_key = basename($cache_filename);
			apc_store($cache_key, $cache_contents, self::APCU_CACHE_TTL);
		} else {
			if (!file_exists(dirname($cache_filename))) {
				@mkdir(dirname($cache_filename), 0777, true);
			}
			@file_put_contents($cache_filename, $cache_contents);
			@chmod($cache_filename, 0777);
		}
		return \Zend\Json\Json::decode($cache_contents, \Zend\Json\Json::TYPE_ARRAY);
	}

	/**
	 * Static method for making an ajax call
	 * @param string $func
	 * @param Request|array $request
	 * @param integer $method
	 * @param string $url
	 * @param array $headers
	 * @param boolean $remove_unsafe_params
	 * @return array
	 */
	static function sendAjax($func, $request, $method = Request::GET, $url = null, $headers = array(), $remove_unsafe_params = true) {
		$ajax = new Ajax();
		$ajax->setFunc($func);
		$response = $ajax->send($request, $method, $url, $headers, $remove_unsafe_params);
		try {
			$ret_val = \Zend\Json\Json::decode($response->getBody(), \Zend\Json\Json::TYPE_ARRAY);
		} catch (\Exception $e) {
			throw $e;
		}
		return $ret_val;
	}

	/**
	 * Static method for making an ajax call
	 * @param string $cache_filename
	 * @param string $func
	 * @param Request|array $request
	 * @param integer $method
	 * @param string $url
	 * @param array $headers
	 * @param boolean $remove_unsafe_params
	 * @return array
	 */
	static function sendRawAndCache($cache_filename, $func, $request, $method = Request::GET, $url = null, $headers = array(), $remove_unsafe_params = true) {
		// use APCu instead of the file cache
		if (ini_get('apc.enabled')) {
			$cache_key = md5($cache_filename);
			if (apc_exists($cache_key)) {
				$cache_contents = apc_fetch($cache_key);
				if (trim($cache_contents) != '') {
					return $cache_contents;
				}
			}
		} else if (file_exists($cache_filename)) {
			$cache_contents = file_get_contents($cache_filename);
			if (trim($cache_contents) != '') {
				return $cache_contents;
			}
		}
		// Request is not cached, so make a request, cache the response, and return it
		$cache_contents = self::sendRaw($func, $request, $method, $url, $headers, $remove_unsafe_params);

		if (ini_get('apc.enabled')) {
			$cache_key = md5($cache_filename);
			apc_store($cache_key, $cache_contents, self::APCU_CACHE_TTL);
		} else {
			if (!file_exists(dirname($cache_filename))) {
				@mkdir(dirname($cache_filename), 0777, true);
			}
			@file_put_contents($cache_filename, $cache_contents);
			@chmod($cache_filename, 0777);
		}
		return $cache_contents;
	}

	/**
	 * Static method for making an ajax call
	 * @param string $func
	 * @param Request|array $request
	 * @param integer $method
	 * @param string $url
	 * @param array $headers
	 * @param boolean $remove_unsafe_params
	 * @return array
	 */
	static function sendRaw($func, $request, $method = Request::GET, $url = null, $headers = array(), $remove_unsafe_params = true) {
		$ajax = new Ajax();
		$ajax->setFunc($func);
		$response = $ajax->send($request, $method, $url, $headers, $remove_unsafe_params);
		try {
			$ret_val = $response->getBody();
		} catch (\Exception $e) {
			throw $e;
		}
		return $ret_val;
	}

	/**
	 * Sends the data to the api server and returns the result
	 * @param Request $request
	 * @param integer $method
	 * @return Zend_Http_Response
	 */
	function send($request, $method = Request::GET, $url = null, $headers = array(), $remove_unsafe_params = true) {
		if ($this->getFunc() === '') {
			throw new \Exception('Param \'func\' is required.');
		}

		if ($request instanceof Request) {
			$params = $request->getParameters();
		} else {
			$params = $request;
		}
		try {
			/* @var $response Zend\Http\Response */
			$response = $this->curl($url, $params, $method, $headers, $remove_unsafe_params);
		} catch (\Zend\Http\Client\Exception\RuntimeException $e) {
			if (self::DEBUG) { \Mojavi\Logging\LoggerManager::error('Error connecting to ' . $url . $this->getFunc()); }
			if (self::DEBUG) { \Mojavi\Logging\LoggerManager::error($e->getMessage()); }
			throw $e;
		} catch (\Exception $e) {
			throw $e;
		}

		if (is_object($response)) {
			if (!$response->isOk()) {
				$code = $response->getStatusCode();
				switch($code) {
					case 401:
						throw new \Exception($code . ' ' . $response->getReasonPhrase());
					case 404:
						throw new \Exception($code . ' ' . $response->getReasonPhrase() . ' (' . $url . ')');
					case 403:
					case 530:
						throw new \Exception('Access denied');
					default:
						throw new \Exception($code . ' ' . $response->getReasonPhrase());
				}
			}
		}
		return $response;
	}

	/**
	 * Sends the data via cURL
	 * @param array $params
	 * @param integer $method
	 * @param string $raw_body
	 * @return Zend_Http_Response
	 */
	private function curl($orig_url, $params = array(), $method = Request::GET, $headers = array(), $remove_unsafe_params = true, $retry_count = 0) {
		try {
			if (is_null($orig_url) && defined('MO_API_URL')) {
				$orig_url = MO_API_URL;
			} else if (is_null($orig_url)) {
				throw new \Exception('No url was provided and MO_API_URL is not defined');
			}

			$url = $orig_url . $this->getFunc();

			if ($method == Request::DELETE || $method == Request::PUT) {
				if(isset($params['_id'])) {
					$url .= '/' . ( isset($params['_id']) ? (int) $params['_id'] : 0 );
				}
			}
			
			// remove any routing to prevent overwritting the url
			if ($remove_unsafe_params) {
				if (isset($params['module'])) {	unset($params['module']); }
				if (isset($params['controller'])) {	unset($params['controller']); }
				if (isset($params['action'])) {	unset($params['action']); }
				if (isset($params['func'])) {	unset($params['func']); }
			}

			// gots to do this so that transfer-encoding: chunked comes through properly
			$curl_adapter = new \Zend\Http\Client\Adapter\Curl();
			// Enforce a 30 second timeout (default is unlimited)
			if ($this->getTimeout() > 0) {
				$curl_adapter->setCurlOption(CURLOPT_TIMEOUT, $this->getTimeout());
				$curl_adapter->setCurlOption(CURLOPT_CONNECTTIMEOUT, $this->getTimeout());
			}

			//$curl_adapter->setCurlOption(CURLOPT_ENCODING , "gzip");
			$curl_adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
			$curl_adapter->setCurlOption(CURLOPT_USERAGENT, 'Mozilla/5.0(iPad; U; CPU iPhone OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B314 Safari/531.21.10');
			$http_client = new \Zend\Http\Client();
			$http_client->setAdapter($curl_adapter);
			$http_client->setUri($url);
			$http_client->setArgSeparator('&');

			$request = new \Zend\Http\Request();
			$request->setUri($url);

			//$http_client->setCookieJar($this->getHttpCookieJar());

			// Set the token authentication
			$request->getHeaders()->addHeaderLine('Accept-Encoding', 'gzip,deflate');
			$request->getHeaders()->addHeaderLine('Content-Type', \Zend\Http\Client::ENC_URLENCODED);
			if (is_array($headers)) {
				foreach ($headers as $key => $header) {
					$request->getHeaders()->addHeaderLine($key, $header);
				}
			}

			if ($request->getUri() === null) {
				throw new \Exception('No URI given. Param \'func\' is required.');
			}

			/* @var $response Zend_Http_Response */
			$response = false;
			if ($method == \Mojavi\Request\Request::GET) {
				if (is_array($params)) { $request->getQuery()->fromArray($params); }
				$request->setMethod(\Zend\Http\Request::METHOD_GET);
				$response = $http_client->send($request);
			} else if ($method == \Mojavi\Request\Request::POST) {
				// If we uploaded files, we have to send them differently
				if (count($_FILES) > 0) {
					$request->getFiles()->fromArray($_FILES);
					$http_client->setEncType(\Zend\Http\Client::ENC_FORMDATA);
				} else {
					$http_client->setEncType(\Zend\Http\Client::ENC_URLENCODED);
				}
				if (is_array($params)) { $request->getPost()->fromArray($params); }
				$request->setMethod(\Zend\Http\Request::METHOD_POST);
				
				$response = $http_client->send($request);
			} else if ($method == \Mojavi\Request\Request::DELETE) {
				$request->setMethod(\Zend\Http\Request::METHOD_DELETE);
				$response = $http_client->send($request);
			} else if ($method == \Mojavi\Request\Request::PUT) {
				if (count($_FILES) > 0) {
					$request->getFiles()->fromArray($_FILES);
					$http_client->setEncType(\Zend\Http\Client::ENC_FORMDATA);
				} else {
					$http_client->setEncType(\Zend\Http\Client::ENC_FORMDATA);
				}
				if (is_array($params)) { $request->getQuery()->fromArray($params); }
				$request->setMethod(\Zend\Http\Request::METHOD_PUT);
				$response = $http_client->send($request);
			}
			return $response;
		} catch (\Exception $e) {
			if (strpos($e->getMessage(), 'connect() timed out!') !== false && $retry_count < 3) {
				return $this->curl($orig_url, $params, $method, $headers, $remove_unsafe_params, ++$retry_count);
			} else if (strpos($e->getMessage(), 'couldn\'t connect to host') !== false && $retry_count < 3) {
				return $this->curl($orig_url, $params, $method, $headers, $remove_unsafe_params, ++$retry_count);
			} else if (strpos($e->getMessage(), 'Operation timed out') !== false && $retry_count < 3) {
				return $this->curl($orig_url, $params, $method, $headers, $remove_unsafe_params, ++$retry_count);
			}
			throw $e;
		}
	}

}
