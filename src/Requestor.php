<?php

namespace PyGPaymentLib;

class Requestor
{

	use Helper;

	protected $init;
	protected $transaction;
	//
	private static $_client;
	//
	protected $request;
	protected $response;
	//
	protected $dotenv;
	protected $log;

	public function __construct(\PyGPaymentLib\Init $init)
	{
		$this->init = $init;
		// Dotenv
		$env = __DIR__ .'/../.env';
		if (!file_exists($env) && file_exists($env.'.example')) {
			copy($env.'.example', $env);
		}
		if (file_exists($env)) {
			$this->dotenv = new \Symfony\Component\Dotenv\Dotenv();
			$this->dotenv->load($env);
		}
		// Monolog
		$this->log = new \Monolog\Logger('log');
		$stream = new \Monolog\Handler\StreamHandler(__DIR__ . '/../log-' . date('Y-m-d') . '.log');
		$stream->setFormatter(new \Monolog\Formatter\LineFormatter("[%datetime%] %channel%.%level_name%: %message% %context% \n", "Y-m-d H:i:s")); // $stream->setFormatter(new \Monolog\Formatter\LineFormatter("[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n", "Y-m-d H:i:s"));
		$this->log->pushHandler($stream, \Monolog\Logger::DEBUG);
	}

	//

	private static function _GetClient($args)
	{
		if (!SELF::$_client) {
			SELF::$_client = \PyGPaymentLib\HttpClient\GuzzleHttpClient::getInstance($args);
		}
		return SELF::$_client;
	}

	//

	public function getRequest(): ?object
	{
		return (object) $this->request;
	}

	public function getResponse(): ?object // PSR7 stream object
	{
		return (object) $this->response;
	}

	//

	public function DoRequest(string $method, $request)
	{
		if (strcasecmp($_SERVER['LOG_REQUESTOR'], 'true') === 0) { // Pre-log request
			$this->log->info('Requestor Prepare', $request);
		}
		try {
			$response = SELF::_GetClient([
				'base_uri' => $this->init->getBaseURI(),
			])->sendRequest(
				$method,
				$request['url'] ?? '',
				$request['data'] ?? [],
				$request['headers'] ?? [],
				$request['option'] ?? []
			);
			if (is_array($response)) {
				$result = [
					'request' => $request,
					'response' => $response,
				];
			} elseif (is_object($response)) {
				$result = [
					'request' => $request,
					'response' => [
						'content' => $response->getBody()->getContents(),
						'status_code' => (int) $response->getStatusCode(),
						'headers' => $response->getHeaders(),
					],
				];
				$response->getBody()->rewind();
			} elseif (is_string($response)) {
				$result = $response;
			}
		} catch (\Throwable $e) {
			$error = SELF::StringError($e);
			if (strcasecmp($_SERVER['LOG_REQUESTOR'], 'true') === 0) { // Log exception
				$this->log->info('Requestor Failed', array_merge($result ?? [], [
					'error' => $error
				]));
			}
			throw new \Exception($error, $e->getCode());
		}
		if (strcasecmp($_SERVER['LOG_REQUESTOR'], 'true') === 0) { // Log success
			// $this->log->info('Requestor Success', $response ?? $result ?? null);
			$this->log->info('Requestor Success', $result);
		}
		$this->request = $request;
		$this->response = $response;
		return $result ?? [];
	}

	//

	// public function ReturnResult($request, $result): ?object
	// {
	// 	$request = (object) $request;
	// 	return (object) [
	// 		'request' => $request->request,
	// 		'response' => [
	// 			'content' => (object) $result,
	// 			'status_code' => $request->status_code ?? 200,
	// 		],
	// 	];
	// }

	public function ThrowErrorException(\Throwable $e, $context = null, $return_err = false)
	{
		$error = SELF::StringError($e);
		if (strcasecmp($_SERVER['LOG_EXCEPTION'], 'true') === 0) {
			$this->log->error('Error Exception', array_merge($context ?? [], [
				'error' => $error
			]));
		}
		if ($return_err == true) {
			return [
				'status' => str_pad($e->getCode(), 3, '0', STR_PAD_LEFT),
				'message' => $error,
			];
		}
		throw new \Exception($error, $e->getCode());
	}

}
