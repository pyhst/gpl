<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

use Growinc\Payment\Helper;

class PayOk extends Requestor implements VendorInterface
{
	use Helper;

	protected $form;

	public function Index()
	{
		// Inapplicable
	}

	public function getPrivateKey()
	{
		$private_key = $this->init->getParam('PAYOK_PRIVATE_KEY');
		if (!empty($private_key)) {
			return
				"-----BEGIN RSA PRIVATE KEY-----\n" .
				chunk_split($private_key, 64, "\n") .
				"-----END RSA PRIVATE KEY-----\n";
		}
		if (!$cert_store = file_get_contents($_ENV['PAYOK_PRIVATE_KEY_FILE'])) {
			return "Error: Unable to read the cert file\n";
		}
		return $cert_store;
	}

	public function GetToken($args)
	{
		// Inapplicable
	}

	public function CreateDummyForm($args)
	{
		// Inapplicable
	}

	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\
	|
	| Payment
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\ ///////////////////////*/

	public function RedirectPayment(\Growinc\Payment\Transaction $transaction)
	{
		// Not applicable
	}

	public function SecurePayment(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$time = time();
			$timestamp = date("Y-m-d", $time) . "T" . date("H:i:s.B", $time) . "+07:00";
			$payment_method = $transaction->getPaymentMethod();
			$payment_channel = $transaction->getPaymentChannel();
			switch ($payment_method) {
				case 'QRIS':
					$url = '/payment/v2.1/qris/create';
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						$url
					);
					$this->request['data'] = [
						'requestId' => $transaction->getRequestID() ?? date('YmdHis', $time),
						'merchantId' => $this->init->getMID(),
						// 'storeId' => 'STORE01',
						'paymentType' => 'QRIS',
						'amount' => (int) $transaction->getAmount() . '.00',
						'merchantTradeNo' => $transaction->getReferenceNumber(),
						'notifyUrl' => $this->init->getCallbackURL(),
						'productName' => $transaction->getDescription(),
						'expire' => (int) $transaction->getExpireIn(), // minutes
						'productInfo' => [[
							'id' => $transaction->getReferenceNumber(),
							'name' => $transaction->getDescription(),
							'price' => (int) $transaction->getAmount() . '.00',
							'type' => strtoupper($payment_method),
							// 'url' => '',
							'quantity' => 1,
						]],
					];
					break;
				case 'EWALLET':
					$url = '/payment/v2.1/ewallet/create';
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						$url
					);
					$this->request['data'] = [
						'requestId' => $transaction->getRequestID() ?? date('YmdHis', $time),
						'merchantId' => $this->init->getMID(),
						// 'storeId' => 'STORE01',
						'paymentType' => $payment_channel,
						'amount' => (int) $transaction->getAmount() . '.00',
						'merchantTradeNo' => $transaction->getReferenceNumber(),
						'notifyUrl' => $this->init->getCallbackURL(),
						'paymentParams' => [
							'redirectUrl' => $this->init->getReturnURL(),
							'phoneNumber' => $transaction->getCustomerPhone(),
						],
						'productName' => $transaction->getDescription(),
						'productInfo' => [[
							'id' => $transaction->getReferenceNumber(),
							'name' => $transaction->getDescription(),
							'price' => (int) $transaction->getAmount() . '.00',
							'type' => strtoupper($payment_method),
							// 'url' => '',
							'quantity' => 1,
						]],
					];
					break;
				case 'VA':
					$url = '/payment/v2.1/va/create';
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						$url
					);
					$this->request['data'] = [
						'requestId' => $transaction->getRequestID() ?? date('YmdHis', $time),
						'merchantId' => $this->init->getMID(),
						// 'storeId' => 'STORE01',
						'paymentType' => $payment_channel,
						'amount' => (int) $transaction->getAmount() . '.00',
						'merchantTradeNo' => $transaction->getReferenceNumber(),
						'notifyUrl' => $this->init->getCallbackURL(),
						'payer' => $transaction->getCustomerName(),
						'productName' => $transaction->getDescription(),
						'productInfo' => [[
							'id' => $transaction->getReferenceNumber(),
							'name' => $transaction->getDescription(),
							'price' => (int) $transaction->getAmount() . '.00',
							'type' => strtoupper($payment_method),
							// 'url' => '',
							'quantity' => 1,
						]],
					];
					break;
				case 'CSTORE':
					break;
				case 'CC':
					break;
				default:
					throw new \Exception('Undefined payment method', 800);
					break;
			}
			//
			$hash = strtolower(hash("sha256", json_encode($this->request['data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
			$string_to_sign = implode(':', [
				'POST',
				$url,
				$hash,
				$timestamp
			]);
			$signature = '';
			$private_key = $this->getPrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$this->request['headers'] = [
				'Content-Type' => 'application/json;charset=utf-8',
				'X-TIMESTAMP' => $timestamp,
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->init->getMID(),
				'X-REQUEST-ID' => $transaction->getRequestID() ?? date('YmdHis', $time),
			];
			//
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					isset($content->errCode)
					&& $content->errCode == '0'
				) {
					/* // Success QRIS
						{
							"merchantId": "010430",
							"requestId": "20240621154626",
							"errCode": "0",
							"paymentType": "QRIS",
							"amount": "15000.00",
							"merchantTradeNo": "1718959586",
							"createTime": "20240621154629",
							"platformTradeNo": "2024062143000000029",
							"expiredTime": "20240622154629",
							"status": "01",
							"productName": "BILLING1",
							"productInfo": [
								{
										"id": "1718959586",
										"name": "BILLING1",
										"price": 15000,
										"type": "QRIS",
										"quantity": 1
								}
							],
							"transFeeRate": "0.000000",
							"transFeeAmount": "10000.00",
							"totalTransFee": "10000.00",
							"qrCode": "MOCK-QRIS:2024062143000000029",
							"qrisUrl": "https://sit-payer.paylabs.co.id/payer-api/qr?340c5b202e6a48b0a5bed402a21d699aMOCK-QRIS%3A2024062143000000029"
						}
					*/
					/* // Success EWALLET
						{
							"merchantId": "010430",
							"requestId": "20240621155035",
							"errCode": "0",
							"paymentType": "DANABALANCE",
							"amount": "15000.00",
							"merchantTradeNo": "1718959835",
							"createTime": "20240621155038",
							"platformTradeNo": "2024062143000000033",
							"expiredTime": "20240621155102",
							"status": "01",
							"productName": "BILLING1",
							"productInfo": [
								{
										"id": "1718959835",
										"name": "BILLING1",
										"price": 15000,
										"type": "EWALLET",
										"quantity": 1
								}
							],
							"redirectUrl": "http://tester.com/secure/callback/demo",
							"transFeeRate": "0.001000",
							"transFeeAmount": "10000.00",
							"totalTransFee": "10015.00",
							"paymentActions": {
								"mobilePayUrl": "https://sit-payer.paylabs.co.id/payer-api/v1/callback/forward/MDA5LTAwOQ==/8f3edf2d126d97b0dc793331297246d06eecfedf13d8e24cd2f6165a13cf61ce"
							}
						}
					*/
					/* // Success VA
						{
							"merchantId": "010430",
							"requestId": "20240621155814",
							"errCode": "0",
							"paymentType": "MuamalatVA",
							"amount": "15000.00",
							"merchantTradeNo": "1718960294",
							"createTime": "20240621155817",
							"platformTradeNo": "2024062143000000034",
							"expiredTime": "20240622155817",
							"status": "01",
							"productName": "BILLING1",
							"productInfo": [
								{
										"id": "1718960294",
										"name": "BILLING1",
										"price": 15000,
										"type": "VA",
										"quantity": 1
								}
							],
							"transFeeRate": "0.000000",
							"transFeeAmount": "10000.00",
							"totalTransFee": "10000.00",
							"vaCode": "9999900000000001"
						}
					*/
					$res = [
						'status' => '000',
						'request' => $this->request,
						'data' => (array) $content,
					];
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function Callback(object $request)
	{
		/* // Example incoming data
		*/
		try {
			if ($request->status == 'SUCCESS') {
				$res = [
					'status' => '000',
					'data' => (array) $request,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $request ? json_encode($request) : 'Unknown status']), 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, '200');
	}

	public function CallbackAlt(object $request)
	{
		// Inapplicable
	}

	public function Inquiry(object $request)
	{
		try {
			$time = time();
			$timestamp = date("Y-m-d", $time) . "T" . date("H:i:s.B", $time) . "+07:00";
			switch ($request->payment_method) {
				case 'QRIS':
					$url = '/payment/v2.1/qris/query';
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						$url
					);
					$this->request['data'] = [
						'requestId' => $request->request_id ?? date('YmdHis', $time),
						'merchantId' => $this->init->getMID(),
						// 'storeId' => 'STORE01',
						'merchantTradeNo' => $request->reference_num,
						'paymentType' => $request->payment_method,
					];
					break;
				case 'EWALLET':
					$url = '/payment/v2.1/ewallet/query';
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						$url
					);
					$this->request['data'] = [
						'requestId' => $request->request_id ?? date('YmdHis', $time),
						'merchantId' => $this->init->getMID(),
						// 'storeId' => 'STORE01',
						'merchantTradeNo' => $request->reference_num,
						'paymentType' => $request->payment_channel,
					];
					break;
				case 'VA':
					$url = '/payment/v2.1/va/query';
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						$url
					);
					$this->request['data'] = [
						'requestId' => $request->request_id ?? date('YmdHis', $time),
						'merchantId' => $this->init->getMID(),
						// 'storeId' => 'STORE01',
						'merchantTradeNo' => $request->reference_num,
						'paymentType' => $request->payment_channel,
					];
					break;
				default:
					throw new \Exception('Undefined payment method', 800);
					break;
			}
			//
			$hash = strtolower(hash("sha256", json_encode($this->request['data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
			$string_to_sign = implode(':', [
				'POST',
				$url,
				$hash,
				$timestamp
			]);
			$signature = '';
			$private_key = $this->getPrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$this->request['headers'] = [
				'Content-Type' => 'application/json;charset=utf-8',
				'X-TIMESTAMP' => $timestamp,
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->init->getMID(),
				'X-REQUEST-ID' => $request->request_id ?? date('YmdHis', $time),
			];
			//
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					isset($content->errCode)
					&& $content->errCode == '0'
				) {
					/* // Success inquiry QRIS
						{
							"merchantId": "010430",
							"requestId": "20240621160531",
							"errCode": "0",
							"paymentType": "QRIS",
							"amount": "15000.00",
							"merchantTradeNo": "1718959724",
							"createTime": "20240621154847",
							"platformTradeNo": "2024062143000000032",
							"successTime": "20240621154948",
							"expiredTime": "20240622154847",
							"status": "02",
							"productName": "BILLING1",
							"productInfo": [
								{
										"id": "1718959724",
										"name": "BILLING1",
										"price": 15000,
										"type": "QRIS",
										"quantity": 1
								}
							],
							"transFeeRate": "0.000000",
							"transFeeAmount": "10000.00",
							"totalTransFee": "10000.00",
							"qrCode": "MOCK-QRIS:2024062143000000032",
							"qrisUrl": "https://sit-payer.paylabs.co.id/payer-api/qr?8736791888f5d6107abea94c42853b0aMOCK-QRIS%3A2024062143000000032"
						}
					*/
					/* // Success inquiry EWALLET
						{
							"merchantId": "010430",
							"requestId": "20240621160842",
							"errCode": "0",
							"paymentType": "DANABALANCE",
							"amount": "15000.00",
							"merchantTradeNo": "1718959835",
							"createTime": "20240621155038",
							"platformTradeNo": "2024062143000000033",
							"successTime": "20240621155140",
							"expiredTime": "20240621155102",
							"status": "02",
							"productName": "BILLING1",
							"productInfo": [
								{
										"id": "1718959835",
										"name": "BILLING1",
										"price": 15000,
										"type": "EWALLET",
										"quantity": 1
								}
							],
							"redirectUrl": "http://sb.tf2us.com/secure/callback/demo",
							"transFeeRate": "0.001000",
							"transFeeAmount": "10000.00",
							"totalTransFee": "10015.00",
							"paymentActions": {
								"mobilePayUrl": "https://sit-payer.paylabs.co.id/payer-api/v1/callback/forward/MDA5LTAwOQ==/8f3edf2d126d97b0dc793331297246d06eecfedf13d8e24cd2f6165a13cf61ce"
							}
						}
					*/
					/* // Success inquiry VA
						{
							"merchantId": "010430",
							"requestId": "20240621161023",
							"errCode": "0",
							"paymentType": "MuamalatVA",
							"amount": "15000.00",
							"merchantTradeNo": "1718960294",
							"createTime": "20240621155817",
							"platformTradeNo": "2024062143000000034",
							"successTime": "20240621155919",
							"expiredTime": "20240622155817",
							"status": "02",
							"productName": "BILLING1",
							"productInfo": [
								{
										"id": "1718960294",
										"name": "BILLING1",
										"price": 15000,
										"type": "VA",
										"quantity": 1
								}
							],
							"transFeeRate": "0.000000",
							"transFeeAmount": "10000.00",
							"totalTransFee": "10000.00",
							"vaCode": "9999900000000001"
						}
					*/
					$res = [
						'status' => '000',
						'request' => $this->request,
						'data' => (array) $content,
					];
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function Cancel(object $request)
	{
		// Inapplicable
	}

	public function Settle(object $request)
	{
		// Inapplicable
	}

	public function Refund(object $request)
	{
		// Inapplicable
	}

	public function RefundStatus(object $request)
	{
		// Inapplicable
	}



	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\
	|
	| Disbursement
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\ ///////////////////////*/

	public function BankList()
	{
		// Inapplicable
	}

	public function CheckBalance()
	{
		try {
			$now = new \DateTime('now', new \DateTimeZone('UTC'));
			$time = date_format($now, 'Y-m-d\TH:i:s.v\Z');
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api-pay/remit/v1.1/balanceQuery'
			);
			$this->request['data'] = [
				'partnerReferenceNo' => date_format($now, 'YmdHis'),
				'accountNo' => $this->init->getMID(),
				'balanceTypes' => 'CASH',
				// 'additionalInfo' => [],
			];
$payload = implode(':', [
	'POST',
	'/api-pay/remit/v1.1/balanceQuery',
	strtolower(hash('sha256', json_encode($this->request['data']))),
	$time
]);
$signature = '';
$private_key = $this->getPrivateKey();
if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
	throw new \Exception('Failed to getToken', 800);
}
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $time,
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->init->getMID(),
				'X-EXTERNAL-ID' => date_format($now, 'YmdHis'),
				'ORIGIN' => 'www.yourdomain.com',
				'CHANNEL-ID' => '-',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseCode)
					&& trim(strtoupper($content->responseCode)) == 'SUCCESS'
				) {
					/* // Success check balance
						{
							"referenceNo": "2024062043090000006",
							"accountNo": "2406040000010430",
							"name": "Test Merchant",
							"partnerReferenceNo": "20240620100506",
							"responseMessage": "Request has been processed successfully",
							"accountInfos": {
								"amount": {
										"value": 0,
										"currency": "Rp"
								},
								"holdAmount": {
										"value": 0,
										"currency": "Rp"
								},
								"floatAmount": {
										"value": 0,
										"currency": "Rp"
								},
								"ledgerBalance": {
										"value": 5000000,
										"currency": "Rp"
								},
								"availableBalance": {
										"value": 5000000,
										"currency": "Rp"
								},
								"currentMultilateralLimit": {
										"value": 0,
										"currency": "Rp"
								},
								"status": "Available",
								"balanceType": "Cash",
								"registrationStatusCode": "registered"
							},
							"responseCode": "SUCCESS"
						}
					*/
					$res = [
						'status' => '000',
						'request' => $this->request,
						'data' => $content,
					];
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function BankAccountInquiry(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$now = new \DateTime('now', new \DateTimeZone('UTC'));
			$time = date_format($now, 'Y-m-d\TH:i:s.v\Z');
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api-pay/remit/v1.1/remitInquiry'
			);
			$this->request['data'] = [
				'partnerReferenceNo' => date_format($now, 'YmdHis'),
				'beneficiaryAccountNo' => $transaction->getCustomerBankAccountNumber(),
				'beneficiaryBankCode' => $transaction->getCustomerBankCode(),
				'additionalInfo' => [
					'remitType' => 'BANK',
					'amount' => $transaction->getAmount(),
				],
			];
$payload = implode(':', [
	'POST',
	'/api-pay/remit/v1.1/remitInquiry',
	strtolower(hash('sha256', json_encode($this->request['data']))),
	$time
]);
$signature = '';
$private_key = $this->getPrivateKey();
if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
	throw new \Exception('Failed to getToken', 800);
}
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $time,
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->init->getMID(),
				'X-EXTERNAL-ID' => date_format($now, 'YmdHis'),
				'ORIGIN' => 'www.yourdomain.com',
				'CHANNEL-ID' => '-',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseCode)
					&& trim(strtoupper($content->responseCode)) == 'SUCCESS'
				) {
					/* // Success bank account inquiry
						{
							"beneficiaryAccountNo": "7700173383",
							"beneficiaryAccountName": "7700173383",
							"referenceNo": "2024062043090000014",
							"beneficiaryBankName": "BANK DANAMON",
							"partnerReferenceNo": "20240620101331",
							"currency": "Rp",
							"beneficiaryBankCode": "011",
							"responseMessage": "Request has been processed successfully",
							"responseCode": "SUCCESS"
						}
					*/
					$res = [
						'status' => '000',
						'request' => $this->request,
						'data' => $content,
					];
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function OnlineTransferExec(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$now = new \DateTime('now', new \DateTimeZone('UTC'));
			$time = date_format($now, 'Y-m-d\TH:i:s.v\Z');
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api-pay/remit/v1.1/remitCreate'
			);
			$this->request['data'] = [
				'partnerReferenceNo' => $transaction->getReferenceNumber(),
				'feeType' => 'OUR',
				'currency' => 'Rp',
				'amount' => [
					'value' => (float) $transaction->getAmount(),
					'currency' => 'Rp',
				],
				'customerReference' => $transaction->getTransactionID(),
				'beneficiaryAccountNo' => $transaction->getCustomerBankAccountNumber(),
				'beneficiaryEmail' => $transaction->getCustomerEmail(),
				'transactionDate' => $transaction->getReqDateTime(),
				'sourceAccountNo' => $transaction->getParam('SOURCE_ACCOUNT_NUMBER'),
				'remark' => $transaction->getDescription(),
				// 'additionalInfo' => [],
				// 'remitType' => [],
			];
$payload = implode(':', [
	'POST',
	'/api-pay/remit/v1.1/remitCreate',
	strtolower(hash('sha256', json_encode($this->request['data']))),
	$time
]);
$signature = '';
$private_key = $this->getPrivateKey();
if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
	throw new \Exception('Failed to getToken', 800);
}
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $time,
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->init->getMID(),
				'X-EXTERNAL-ID' => date_format($now, 'YmdHis'),
				'ORIGIN' => 'www.yourdomain.com',
				'CHANNEL-ID' => '-',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseCode)
					&& trim(strtoupper($content->responseCode)) == 'SUCCESS'
				) {
					/* // Success disb
						{
							"beneficiaryAccountNo": "7700173383",
							"amount": {
								"currency": "Rp",
								"value": 10000
							},
							"referenceNo": "2024062043090000020",
							"partnerReferenceNo": "20240620180123",
							"beneficiaryBankCode": "011",
							"responseMessage": "Request has been processed successfully",
							"sourceAccountNo": "010430",
							"responseCode": "SUCCESS"
						}
					*/
					$res = [
						'status' => '000',
						'request' => $this->request,
						'data' => $content,
					];
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function CheckTransfer(object $request)
	{
		try {
			$now = new \DateTime('now', new \DateTimeZone('UTC'));
			$time = date_format($now, 'Y-m-d\TH:i:s.v\Z');
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api-pay/remit/v1.1/remitQuery'
			);
			$this->request['data'] = [
				'originalPartnerReferenceNo' => $request->originalPartnerReferenceNo ?? null,
				'originalReferenceNo' => $request->originalReferenceNo ?? null,
				'originalExternalId' => $request->originalExternalId ?? null,
				'serviceCode' => 'remit.query',
				'transactionDate' => $request->transactionDate ?? null,
				'amount' => [
					'value' => (float) $request->amount ?? 0,
					'currency' => 'Rp',
				],
				// 'additionalInfo' => [],
			];
$payload = implode(':', [
	'POST',
	'/api-pay/remit/v1.1/remitQuery',
	strtolower(hash('sha256', json_encode($this->request['data']))),
	$time
]);
$signature = '';
$private_key = $this->getPrivateKey();
if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
	throw new \Exception('Failed to getToken', 800);
}
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $time,
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->init->getMID(),
				'X-EXTERNAL-ID' => date_format($now, 'YmdHis'),
				'ORIGIN' => 'www.yourdomain.com',
				'CHANNEL-ID' => '-',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseCode)
					&& trim(strtoupper($content->responseCode)) == 'SUCCESS'
				) {
					/* // Success check status
						{
							"beneficiaryAccountNo": "7700173383",
							"amount": {
								"currency": "Rp",
								"value": 10000
							},
							"originalReferenceNo": "2024062043090000025",
							"referenceNumber": "20240620110823",
							"originalExternalId": "20240620110823",
							"serviceCode": "18",
							"latestTransactionStatus": "SUCCESS",
							"transactionStatusDesc": "Successful",
							"originalPartnerReferenceNo": "20240620180823",
							"responseMessage": "Request has been processed successfully",
							"sourceAccountNo": "010430",
							"responseCode": "SUCCESS"
						}
					*/
					$res = [
						'status' => '000',
						'request' => $this->request,
						'data' => $content,
					];
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

}
