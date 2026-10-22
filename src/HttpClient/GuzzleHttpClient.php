<?php

namespace PyGPaymentLib\HttpClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\TransferStats;
use GuzzleHttp\Exception\RequestException;

use PyGPaymentLib\Exceptions\ApiException;

use GuzzleHttp\Psr7\Request;

class GuzzleHttpClient
{

	protected $_guzzle_http;
	private static $_guzzle_instance;
	//
	protected $effective_uri;
	public $request;
	public $response;
	//
	// protected $timeout = 25;
	protected $timeout = 40; // 11/09/23 - Case MPAY timeout & Duitku timeout

	public function __construct($args)
	{
		if (!$this->_guzzle_http) {
			$this->_guzzle_http = new GuzzleClient(array_merge([
				'verify' => false,
				'timeout' => $this->timeout,
				// 'read_timeout' => 10,
			], $args));
		}
	}

	//

	public static function getInstance($args)
	{
		if (!SELF::$_guzzle_instance) {
			SELF::$_guzzle_instance = new SELF($args);
		}
		return SELF::$_guzzle_instance;
	}

	public function sendRequest(
		string $method,
		string $url,
		$data,
		array $headers = [],
		array $option = []
	)
	{
		try {
			switch (strtoupper(trim($method))) {
				case 'GET':
					$type = 'query';
					break;
				case 'POST':
					$type = 'form_params';
					break;
				case 'JSON':
					$type = 'json';
					break;
			}
			// Overide
			if (!empty($option['as_json']) && $option['as_json']) {
				$type = 'json';
			}
			// Request options
			$option = array_merge($option, [
				'headers' => $headers,
				$type => $data,
				'on_stats' => function(TransferStats $stats) {
						$this->effective_uri = $stats->getEffectiveUri();
					},
				'timeout' => $option['timeout'] ?? $this->timeout,
			]);

			// //
			// //
			// //
			// /*--------------------------------------  // Changed #1 from  -------------------------------------------------------*/
			// // $response = $this->_guzzle_http->request(
			// // 	$method,
			// // 	$url,
			// // 	$option
			// // );
			// /*--------------------------------------  // Changed into: Return of PSR objects  -------------------------------------------------------*/
			// switch ($type) {
			// 	case 'query':
			// 		$request = new Request($method, $url . '?' . http_build_query($data), $headers);
			// 		break;
			// 	case 'form_params':
			// 		$request = new Request($method, $url, $headers, http_build_query($data));
			// 		break;
			// 	case 'json':
			// 		$request = new Request($method, $url, $headers, json_encode($data));
			// 		break;
			// }
			// $response = $this->_guzzle_http->send($request, $option);
			// /*--------------------------------------  // Changed #1 ends  -------------------------------------------------------*/
			// //
			// //
			// //
			// /*--------------------------------------  // Changed #2 from  -------------------------------------------------------*/
			// // if (isset($option['to_json']) && !empty($option['to_json'])) {
			// // 	$result = [
			// // 			'content' => $response->getBody()->getContents(),
			// // 			'status_code' => (int) $response->getStatusCode(),
			// // 			'headers' => $response->getHeaders(),
			// // 		];
			// // 	$response->getBody()->rewind();
			// // } elseif (isset($option['to_uri']) && !empty($option['to_uri'])) {
			// // 	$result = (string) strval($this->effective_uri);
			// // } else {
			// // 	// Default is return as PSR7 response
			// // 	$result = $response;
			// // }
			// // return $result ?? [];
			// /*--------------------------------------  // Changed into: Return of PSR objects  -------------------------------------------------------*/
			// $this->request = $request;
			// $this->response = $response;
			// // $this->result = $result;
			// // return $result ?? [];
			// return $response;
			// /*--------------------------------------  // Changed #2 ends  -------------------------------------------------------*/
			// //
			// //
			// //

			$response = $this->_guzzle_http->request(
				$method,
				$url,
				$option
			);
			if (isset($option['to_json']) && !empty($option['to_json'])) {
				$result = [
						'content' => $response->getBody()->getContents(),
						'status_code' => (int) $response->getStatusCode(),
						'headers' => $response->getHeaders(),
					];
				$response->getBody()->rewind();
			} elseif (isset($option['to_uri']) && !empty($option['to_uri'])) {
				$result = (string) strval($this->effective_uri);
			} else {
				// Default is return as PSR7 response
				$result = $response;
			}
			return $result ?? [];
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
				SELF::_HandleAPIError($e);
				// $result = SELF::_HandleAPIError($e);
			}
		}
	}

	//

	private static function _HandleAPIError($e)
	{
		// // $message = $e->getMessage();
		// // $message = $e->getResponse()->getBody()->getContents();
		// //
		// $request = $e->getRequest();
		// $response = $e->getResponse();
		// $level = (int) \floor($response->getStatusCode() / 100);
		// if ($level === 4) {
		// 	$label = 'Client error';
		// } elseif ($level === 5) {
		// 	$label = 'Server error';
		// } else {
		// 	$label = 'Unsuccessful request';
		// }
		// $uri = $request->getUri();
		// $userInfo = $uri->getUserInfo();
		// if (false !== ($pos = \strpos($userInfo, ':'))) {
		// 	$uri = $uri->withUserInfo(\substr($userInfo, 0, $pos), '***');
		// }
		// // $message = sprintf(
		// // 	'%s: `%s %s` resulted in a `%s %s` response: "%s" ',
		// // 	$label,
		// // 	$request->getMethod(),
		// // 	$uri,
		// // 	$response->getStatusCode(),
		// // 	$response->getReasonPhrase(),
		// // 	$response->getBody()->getContents()
		// // );
		// $message = sprintf(
		// 	'%s %s [%s:%s] "%s" ',
		// 	$response->getReasonPhrase(),
		// 	$response->getStatusCode(),
		// 	$request->getMethod(),
		// 	$uri,
		// 	$response->getBody()->getContents()
		// );
		// //
		// $status_code = (int) $e->getResponse()->getStatusCode();
		// $error_code = $e->getCode();
		// // throw new ApiException($message, $status_code, $error_code);
		// // // return [
		// // // 	'content' => [
		// // // 		'status' => 999,
		// // // 		'message' => $message,
		// // // 		'data' => null,
		// // // 	],
		// // // 	'status_code' => $status_code,
		// // // 	'error_code' => $error_code,
		// // // ];
		// if (!isset($_ENV['IB_APP_THROW_GUZZLE_ERROR']) || (isset($_ENV['IB_APP_THROW_GUZZLE_ERROR']) && $_ENV['IB_APP_THROW_GUZZLE_ERROR'] == 'false')) {
		// 	$response->getBody()->rewind();
		// 	$result = $response->getBody()->getContents();
		// 	return [
		// 		'status_code' => $status_code,
		// 		'content' => json_encode([
		// 			'message' => "$label > " . $response->getReasonPhrase() . " " . $response->getStatusCode() . " [" . $request->getMethod() . ":$uri]",
		// 			'data' => is_array($result) ? $result : (is_string($result) ? strip_tags(preg_replace('/\r\n|\r|\n/', '', $result)) : $result),
		// 		]),
		// 	];
		// } else {
		// 	// throw new \IbIntegrator\Exceptions\RequestorException($message, $status_code, $error_code);
		// 	throw new ApiException($message, $status_code, $error_code);
		// }

		$request = $e->getRequest();
		$response = $e->getResponse();
		$level = (int) \floor($response->getStatusCode() / 100);
		if ($level === 4) {
			$label = 'Client error';
		} elseif ($level === 5) {
			$label = 'Server error';
		} else {
			$label = 'Unsuccessful request';
		}
		$uri = $request->getUri();
		$userInfo = $uri->getUserInfo();
		if (false !== ($pos = \strpos($userInfo, ':'))) {
			$uri = $uri->withUserInfo(\substr($userInfo, 0, $pos), '***');
		}
		$message = sprintf(
			'%s %s [%s:%s] "%s" ',
			$response->getReasonPhrase(),
			$response->getStatusCode(),
			$request->getMethod(),
			$uri,
			$response->getBody()->getContents()
		);
		//
		$status_code = (int) $e->getResponse()->getStatusCode();
		$error_code = $e->getCode();
		throw new ApiException($message, $status_code, $error_code);
	}

}
