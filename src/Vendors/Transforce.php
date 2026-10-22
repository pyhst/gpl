<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

use Growinc\Payment\Helper;

class Transforce extends Requestor implements VendorInterface
{
	use Helper;

	protected $form;
	protected $access_token;

	//

	public function RetrievePrivateKey()
	{
		$private_key = $this->init->getPrivateKey();
		if (!empty($private_key)) {
			return
				"-----BEGIN PRIVATE KEY-----\n" .
				chunk_split($private_key, 64, "\n") .
				"-----END PRIVATE KEY-----\n";
		}
		if (!$cert_store = file_get_contents($_ENV['TRANSFORCE_MERCHANT_PRIVATE_KEY'])) {
			return "Error: Unable to read the cert file\n";
		}
		return $cert_store;
	}

	public function VerifySignature($string_to_sign, $signature, $public_key = null)
	{
		if (!$public_key) {
			$public_key = $this->init->getPublicKey();
			if (!empty($public_key)) {
				$public_key =
					"-----BEGIN PUBLIC KEY-----\n" .
					chunk_split($public_key, 64, "\n") .
					"-----END PUBLIC KEY-----\n";
			}
		}
		$verified = openssl_verify($string_to_sign, base64_decode($signature), $public_key, OPENSSL_ALGO_SHA256);
		return $verified;
	}

	//

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args = null)
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
			$time = $transaction->getTime() ?? time();
			$timestamp = (strlen($time) < 13) ? ($time * 1000) : $time;
			$request_id = $transaction->getRequestID() ?? ($time . mt_rand(10000, 99999));
			$payment_method = trim(strtolower($transaction->getPaymentMethod()));
			$payment_channel = trim(strtolower($transaction->getPaymentChannel()));
			switch ($payment_method) {
				case 'va':
					$url = '/v1/pay/va/dynamic';
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						$url
					);
					$this->request['data'] = [
						'applyAmount' => (int) $transaction->getAmount(),
						'bankCode' => $payment_channel,
						'callbackUrl' => $this->init->getCallbackURL(),
						'details' => $transaction->getDescription(),
						'displayName' => $transaction->getCustomerName(),
						'expiry' => $transaction->getExpireIn(), // In minutes
						'merchantReferenceNo' => $transaction->getReferenceNumber(),
					];
					break;
				case 'qris':
					$url = '/v1/pay/qr/generate';
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						$url
					);
					$this->request['data'] = [
						'applyAmount' => (int) $transaction->getAmount(),
						'callbackUrl' => $this->init->getCallbackURL(),
						'expiry' => $transaction->getExpireIn(), // In minutes
						'merchantReferenceNo' => $transaction->getReferenceNumber(),
					];
					break;
				case 'ewallet':
					$url = '/v1/pay/ewallet/link/create';
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						$url
					);
					$this->request['data'] = [
						'applyAmount' => (int) $transaction->getAmount(),
						'callbackUrl' => $this->init->getCallbackURL(),
						'expiry' => $transaction->getExpireIn(), // In minutes
						'merchantReferenceNo' => $transaction->getReferenceNumber(),
						'redirectUrl' => $this->init->getReturnURL(),
						'walletCode' => $payment_channel,
					];
					break;
				case 'cstore':
					break;
				case 'cc':
					break;
				default:
					throw new \Exception('Undefined payment method', 800);
					break;
			}
			//
			$payload = json_encode($this->request['data']);
			$string_to_sign = implode('|', [
				$this->init->getMID(),
				$payload,
				$timestamp,
				$request_id,
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$this->request['headers'] = [
				'X-Request-ID' => $request_id,
				'X-Timestamp' => $timestamp,
				'X-MerchantId' => $this->init->getMID(),
				'X-Signature' => base64_encode($signature),
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
					(isset($content->code) && $content->code == '200')
					&& !empty($content->data->merchantReferenceNo)
				) {
					/* // Success QRIS
						{
							"code": "200",
							"message": "success",
							"data": {
								"merchantReferenceNo": "1727758342",
								"applyAmount": "15000",
								"expiry": 120,
								"callbackUrl": "http:\/\/tester.com\/secure\/callback\/demo",
								"referenceNo": "QRIS100025241001521122990vwG7c",
								"qrCode": "00020101021226520012COM.DOKU.WWW011793600899000008705020487050303URE51440014ID.CO.QRIS.WWW0215ID20240116387050303URE520115303360540815000.005802ID5904Tony6007jakarta61051175062410703A015030QRIS100025241001521122990vwG7c6304C087"
							}
						}
					*/
					/* // Success VA
						{
							"code": "200",
							"message": "success",
							"data": {
								"merchantReferenceNo": "1727688470",
								"applyAmount": "15000",
								"expiry": 120,
								"bankCode": "PERMATA",
								"displayName": "TESTER",
								"details": "BILLING1",
								"callbackUrl": "http:\/\/tester.com\/secure\/callback\/demo",
								"referenceNo": "InVA100025240930270452301mbHY4",
								"virtualAccount": "8856511700010109"
							}
						}
					*/
					/* // Success EWALLET
						{
						}
					*/
					$res = [
						'status' => '000',
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
		/* // Example incoming data // VA
		*/
		/* // Success QRIS
		*/
		try {
			if ($request->success == 'true') {
				$res = [
					'status' => '000',
					'data' => (array) $request->data_payment,
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
			$timestamp = (strlen($time) < 13) ? ($time * 1000) : $time;
			$request_id = $time . mt_rand(10000, 99999);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v1/pay/payin/query'
			);
			$this->request['data'] = [
				'merchantReferenceNo' => $request->merchantReferenceNo,
			];
			//
			$payload = json_encode($this->request['data']);
			$string_to_sign = implode('|', [
				$this->init->getMID(),
				$payload,
				$timestamp,
				$request_id,
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$this->request['headers'] = [
				'X-Request-ID' => $request_id,
				'X-Timestamp' => $timestamp,
				'X-MerchantId' => $this->init->getMID(),
				'X-Signature' => base64_encode($signature),
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
					(isset($content->code) && $content->code == '200')
					&& isset($content->data->status)
				) {
					/* // Success inquiry QRIS
						{
							"code": "200",
							"message": "success",
							"data": {
								"referenceNo": null,
								"merchantReferenceNo": null,
								"channel": "QRIS",
								"expireDate": "2024-10-01 13:52:22",
								"status": "Pending",
								"message": null,
								"merchantFee": "5000",
								"merchantRate": "0.01",
								"merchantChargeFee": "5150",
								"creditAmount": "9850",
								"applyAmount": "15000",
								"currency": "IDR",
								"settleStatus": false,
								"settleTime": null,
								"createdAt": "2024-10-01 11:52:22",
								"finishedAt": null
							}
						}
					*/
					/* // Success inquiry VA
						{
							"code": "200",
							"message": "success",
							"data": {
								"referenceNo": null,
								"merchantReferenceNo": null,
								"channel": "DynamicVA",
								"expireDate": "2024-09-30 18:29:42",
								"status": "Pending",
								"message": null,
								"merchantFee": "5000",
								"merchantRate": "0.01",
								"merchantChargeFee": "5150",
								"creditAmount": "9850",
								"applyAmount": "15000",
								"currency": "IDR",
								"settleStatus": false,
								"settleTime": "2024-09-30 16:29:42",
								"createdAt": "2024-09-30 16:29:42",
								"finishedAt": null
							}
						}
					*/
					/* // Success inquiry EWALLET
						{
						}
					*/
					$res = [
						'status' => '000',
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
			$time = time();
			$timestamp = (strlen($time) < 13) ? ($time * 1000) : $time;
			$request_id = $time . mt_rand(10000, 99999);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v1/merchant/balance'
			);
			//
			$payload = null;
			$string_to_sign = implode('|', [
				$this->init->getMID(),
				$payload,
				$timestamp,
				$request_id,
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			//
			$this->request['headers'] = [
				'X-Request-ID' => $request_id,
				'X-Timestamp' => $timestamp,
				'X-MerchantId' => $this->init->getMID(),
				'X-Signature' => base64_encode($signature),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					(isset($content->code) && $content->code == '200')
					&& isset($content->data->availableBalance)
				) {
					/* // Success check balance
						{
							"code": "200",
							"message": "Success",
							"data": {
								"frozenBalance": "0",
								"unsettleBalance": "0",
								"availableBalance": "287000"
							}
						}
					*/
					$res = [
						'status' => '000',
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

	public function BankAccountInquiry(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$time = time();
			$timestamp = (strlen($time) < 13) ? ($time * 1000) : $time;
			$request_id = $time . mt_rand(10000, 99999);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v1/pay/disburse/account/inquiry'
			);
			$this->request['data'] = [
				'bankCode' => $transaction->getCustomerBankCode(),
				'beneficiaryAccountName' => 'CUSTOMER',
				'beneficiaryAccountNo' => $transaction->getCustomerBankAccountNumber(),
				'merchantReferenceNo' => $transaction->getReferenceNumber(),
			];
			//

$payload = json_encode($this->request['data']);
// $payload = null;

			$string_to_sign = implode('|', [
				$this->init->getMID(),
				$payload,
				$timestamp,
				$request_id,
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			//
			$this->request['headers'] = [
				'X-Request-ID' => $request_id,
				'X-Timestamp' => $timestamp,
				'X-MerchantId' => $this->init->getMID(),
				'X-Signature' => base64_encode($signature),
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
					(isset($content->code) && $content->code == '200')
					&& isset($content->data->beneficiaryAccountName)
				) {
					/* // Success bank account inquiry
						{
							"code": "200",
							"message": "success",
							"data": {
								"beneficiaryAccountNo": "7700173383",
								"status": "Success",
								"bankCode": "BNINIDJA",
								"beneficiaryAccountName": "ABIL AHMAD ABDULLAH"
							}
						}
					*/
					$res = [
						'status' => '000',
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

	public function OnlineTransferExec(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$time = $transaction->getTime() ?? time();
			$timestamp = (strlen($time) < 13) ? ($time * 1000) : $time;
			$request_id = $transaction->getRequestID() ?? ($time . mt_rand(10000, 99999));
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v1/pay/disburse'
			);
			$this->request['data'] = [
				'applyAmount' => (float) $transaction->getAmount(),
				'bankCode' => $transaction->getCustomerBankCode(),
				'beneficiaryAccountName' => $transaction->getCustomerBankAccountName(),
				'beneficiaryAccountNo' => $transaction->getCustomerBankAccountNumber(),
				'callbackUrl' => $this->init->getCallbackURL(),
				'details' => $transaction->getRemark(),
				'merchantReferenceNo' => $transaction->getReferenceNumber(),
			];
			//
			$payload = json_encode($this->request['data']);
			$string_to_sign = implode('|', [
				$this->init->getMID(),
				$payload,
				$timestamp,
				$request_id,
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$this->request['headers'] = [
				'X-Request-ID' => $request_id,
				'X-Timestamp' => $timestamp,
				'X-MerchantId' => $this->init->getMID(),
				'X-Signature' => base64_encode($signature),
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
					(isset($content->code) && $content->code == '200')
					&& isset($content->data->beneficiaryAccountName)
				) {
					/* // Success disb
						{
							"code": "200",
							"message": "success",
							"data": {
								"referenceNo": "TRBK10002520241009550141560ODFa1",
								"merchantReferenceNo": "20241009135541",
								"bankCode": "BNINIDJA",
								"bankName": "BANK BNI 46",
								"beneficiaryAccountNo": "7700173383",
								"beneficiaryAccountName": "ABIL AHMAD ABDULLAH",
								"status": "Pending",
								"merchantFee": 6000,
								"merchantRate": 0.01,
								"merchantChargeFee": 6100,
								"merchantDeductAmount": 16100,
								"applyAmount": 10000
							}
						}
					*/
					$res = [
						'status' => '000',
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

	public function OnlineTransferCheckTransfer(object $request)
	{
		try {
			$time = time();
			$timestamp = (strlen($time) < 13) ? ($time * 1000) : $time;
			$request_id = $time . mt_rand(10000, 99999);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v1/pay/disburse/query'
			);
			$this->request['data'] = [
				'merchantReferenceNo' => $request->merchantReferenceNo,
			];
			//
			$payload = json_encode($this->request['data']);
			$string_to_sign = implode('|', [
				$this->init->getMID(),
				$payload,
				$timestamp,
				$request_id,
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			//
			$this->request['headers'] = [
				'X-Request-ID' => $request_id,
				'X-Timestamp' => $timestamp,
				'X-MerchantId' => $this->init->getMID(),
				'X-Signature' => base64_encode($signature),
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
					(isset($content->code) && $content->code == '200')
					&& isset($content->data->status)
				) {
					/* // Success check status
						{
							"code": "200",
							"message": "success",
							"data": {
								"referenceNo": "TRBK10002520241009550141560ODFa1",
								"merchantReferenceNo": "20241009135541",
								"bankCode": "BNINIDJA",
								"bankName": "BANK BNI 46",
								"beneficiaryAccountNo": "7700173383",
								"beneficiaryAccountName": "ABIL AHMAD ABDULLAH",
								"status": "Failed",
								"remarks": "AccountNo not found or Closed",
								"merchantFee": "6000",
								"merchantRate": "0",
								"merchantChargeFee": "6100",
								"merchantDeductAmount": "16100",
								"applyAmount": "10000",
								"createdAt": "2024-10-09 13:55:41"
							}
						}
					*/
					$res = [
						'status' => '000',
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

	/*--------------------------------------  New API Withdrawal  -------------------------------------------------------*/

	public function RequestWithdrawal(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$time = $transaction->getTime() ?? time();
			$timestamp = (strlen($time) < 13) ? ($time * 1000) : $time;
			$request_id = $transaction->getRequestID() ?? ($time . mt_rand(10000, 99999));
			switch (strtolower($transaction->getTransferMethod())) {
				case 'idr':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						'/v2/api/merchant/withdraw/idr'
					);
					$this->request['data'] = [
						'merchantReferenceNo' => $transaction->getReferenceNumber(),
						'applyAmount' => (float) $transaction->getAmount(),
						'callbackUrl' => $this->init->getCallbackURL(),
						'bankCode' => $transaction->getCustomerBankCode(),
						'beneficiaryAccountNo' => $transaction->getCustomerBankAccountNumber(),
						'beneficiaryAccountName' => $transaction->getCustomerBankAccountName(),
						'customerRemark' => $transaction->getRemark(),
					];
					break;
				case 'usd':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						'/v2/api/merchant/withdraw/usd'
					);
					$this->request['data'] = [
						'merchantReferenceNo' => $transaction->getReferenceNumber(),
						'applyAmount' => (float) $transaction->getAmount(),
						'callbackUrl' => $this->init->getCallbackURL(),
						'bankCode' => $transaction->getCustomerBankCode(),
						'beneficiaryAccountNo' => $transaction->getCustomerBankAccountNumber(),
						'beneficiaryAccountName' => $transaction->getCustomerBankAccountName(),
						'customerRemark' => $transaction->getRemark(),
					];
					break;
				case 'usdt':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						'/v2/api/merchant/withdraw/usdt'
					);
					$this->request['data'] = [
						'merchantReferenceNo' => $transaction->getReferenceNumber(),
						'applyAmount' => (float) $transaction->getAmount(),
						'callbackUrl' => $this->init->getCallbackURL(),
						'network' => $transaction->getParam('NETWORK'),
						'walletAddress' => $transaction->getParam('WALLET_ADDRESS'),
						'customerRemark' => $transaction->getRemark(),
					];
					break;
				default:
					throw new \Exception('Transfer method not defined/unknown', 900);
					break;
			}
			//
			$payload = json_encode($this->request['data']);
			$string_to_sign = implode('|', [
				$this->init->getMID(),
				$payload,
				$timestamp,
				$request_id,
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$this->request['headers'] = [
				'X-Request-ID' => $request_id,
				'X-Timestamp' => $timestamp,
				'X-MerchantId' => $this->init->getMID(),
				'X-Signature' => base64_encode($signature),
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
					(isset($content->code) && $content->code == '200')
					&& isset($content->data->beneficiaryAccountName)
				) {
					/* // Success withdrawal
						{
							"code": "200",
							"message": "success",
							"data": {
								"merchantId": 100013,
								"referenceNo": "WDBK10001326091300123456789ABCDEF",
								"merchantReferenceNo": "WD-USD-20260913-001",
								"bankCode": "BCA",														// Kode bank IDR/USD; null untuk USDT
								"bankName": "Bank Central Asia",										// Nama bank IDR/USD; null untuk USDT
								"beneficiaryAccountNo": "0123456789",								// Nomor rekening beneficiary IDR/USD; null untuk USDT
								"beneficiaryAccountName": "Example Merchant",					// Nama beneficiary IDR/USD; null untuk USDT
								"status": "Processing",
								"remarks": "September USD settlement",
								"applyAmount": "10000",
								"currency": "IDR",
								"merchantFee": "0",
								"merchantFeeRate": "0.00",
								"chargeFee": "0",
								"deductAmount": "10000",
								"finishedAt": null,
								"createdAt": "2026-09-13 10:00:00"
							}
						}
					*/
					$res = [
						'status' => '000',
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

	public function RequestWithdrawalCheck(object $request)
	{
		try {
			$time = time();
			$timestamp = (strlen($time) < 13) ? ($time * 1000) : $time;
			$request_id = $time . mt_rand(10000, 99999);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v2/api/merchant/withdraw/query'
			);
			$this->request['data'] = [
				'merchantReferenceNo' => $request->merchantReferenceNo,
			];
			//
			$payload = json_encode($this->request['data']);
			$string_to_sign = implode('|', [
				$this->init->getMID(),
				$payload,
				$timestamp,
				$request_id,
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			//
			$this->request['headers'] = [
				'X-Request-ID' => $request_id,
				'X-Timestamp' => $timestamp,
				'X-MerchantId' => $this->init->getMID(),
				'X-Signature' => base64_encode($signature),
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
					(isset($content->code) && $content->code == '200')
					&& isset($content->data->status)
				) {
					/* // Success check status withdrawal
						{
							"code": "200",
							"message": "success",
							"data": {
								"merchantId": 100013,
								"referenceNo": "WDBK10001326091300123456789ABCDE0",
								"merchantReferenceNo": "WD-USDT-20260913-001",
								"bankCode": null,
								"bankName": null,
								"beneficiaryAccountNo": null,
								"beneficiaryAccountName": null,
								"status": "Success",
								"remarks": "Completed offline",
								"applyAmount": "10000",
								"currency": "IDR",
								"merchantFee": "0",
								"merchantFeeRate": "0.00",
								"chargeFee": "0",
								"deductAmount": "10000",
								"finishedAt": "2026-09-13 11:00:00",
								"createdAt": "2026-09-13 10:00:00"
							}
						}
					*/
					$res = [
						'status' => '000',
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

}
