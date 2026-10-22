<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;

use PyGPaymentLib\Helper;

class Titan extends Requestor implements VendorInterface
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
		if (!$cert_store = file_get_contents($_ENV['TITAN_PRIVATE_KEY'])) {
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

	public function SetAccessToken($access_token)
	{
		$this->access_token = $access_token;
	}

	public function GetAccessToken() // : string
	{
		return $this->access_token;
	}

	public function GetTokenFile($assoc_file = 'token') : string
	{
		return __DIR__ . '/_temp_' . $assoc_file . '_' .
			(new \ReflectionClass($this))->getShortName() . '_' .
			$this->init->getMID() . '_' .
			'.txt';
	}

	public function GetTokenFileContent($assoc_file = 'token')
	{
		if (file_exists($this->GetTokenFile($assoc_file))) {
			return file_get_contents($this->GetTokenFile($assoc_file));
		}
	}

	//

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args = null)
	{
		try {
			$time = time();
			$timestamp = date("Y-m-d", $time) . "T" . date("H:i:s", $time) . "+07:00";
			$url = '/auth/v1.0/access-token/b2b';
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				$url
			);
			$this->request['data'] = [
				'grantType' => 'client_credentials',
			];
			//
			$string_to_sign = implode('|', [
				$this->init->getMID(),
				$timestamp,
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $timestamp,
				'X-SIGNATURE' => base64_encode($signature),
				'X-CLIENT-KEY' => $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 200
			) {
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content->accessToken)
				) {
					/* // Success
						{
							"responseCode": "2007300",
							"responseStatus": "Successful",
							"responseMessage": "Berhasil di proses dan mendapatkan accessToken.",
							"accessToken": "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MCwiYnVzaW5lc3NfaWQiOjIsInByb2plY3RfaWQiOiIwMUpSN00zNVdUUFhNOUQ4MzNOUTlBMkFSMSIsInR5cGUiOiJhY2Nlc3NfdG9rZW4iLCJpc19hZG1pbiI6ZmFsc2UsImlzcyI6ImRvaXRfZGV2IiwiZXhwIjoxNzQ3MDU4NDk2LCJpYXQiOjE3NDcwNTc1OTYsImp0aSI6IjAzMTRkOWMwLWJlZjItNDJkYi1iMDQxLTA2MGUxNjdkYzdlMCJ9.w5jDzhvyNq6L3w5IdH2aufkTUj6qql5fWhap23tDtzbNJZwl1lMhD-FO8Bv3D3L1ZPcxpOdI37kgwXoSccPtwe2GH6zspniYOkbjTcVIYV5JSuzrkqWSMv2_GL3C_kn2Q_2m31HNVdjQ63k9l7iyv6Vg2Kh555Ibos3eA5DT64eYxh-xOkrjO1xBiXOCncsvlY1NdtWckap9v_TB3eSprIt9ot5wJLvvx_kvYFvciPwu5goRrmeRqzAN95mY-2XrPCv2d4fR7CobOBRQVFXe23PBwGbrnX6p10uAxpTZhp33QNngJDdyA5RIkfELsMY29jZ28yc5qfSfbxrGq1BEzw",
							"tokenType": "BearerToken",
							"expiresIn": "900",
							"additionalData": "{}"
						}
					*/
					// file_put_contents($this->GetTokenFile(), json_encode($content));
					$this->SetAccessToken($content->accessToken);
					//
					$res = (string) $content->accessToken;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
			throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return $res ?? [];
	}

	public function CheckToken()
	{
		// // Get temp token
		// if (!empty($this->GetTokenFileContent()) && SELF::is_JSON($this->GetTokenFileContent())) {
		// 	$token = json_decode($this->GetTokenFileContent(), true);
		// 	$this->SetAccessToken($token['accessToken']);
		// } else {
		// 	// Get token
		// 	if (empty($this->GetAccessToken())) {
		// 		// Request new
		// 		$this->SetAccessToken($this->GetToken());
		// 	}
		// }
		$this->GetToken();
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

	public function RedirectPayment(\PyGPaymentLib\Transaction $transaction)
	{
		// Not applicable
	}

	public function SecurePayment(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->CheckToken();
			retry:
			//
			$time = time();
			$timestamp = date("Y-m-d\TH:i:s", $time) . "+07:00";
			$payment_method = trim(strtolower($transaction->getPaymentMethod()));
			$payment_channel = trim(strtolower($transaction->getPaymentChannel()));
			switch ($payment_method) {
				case 'va':
					break;
				case 'qris':
					$url = '/pjp/qris/v1.0/qr/qr-mpm-generate';
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						$url
					);
					$this->request['data'] = [
						'partnerReferenceNo' => $transaction->getReferenceNumber(),
						'amount' => [
							'value' => (string) $transaction->getAmount() . '.00',
							'currency' => 'IDR',
						],
						'validityPeriod' => date('Y-m-d\TH:i:sP', $time + (60 * $transaction->getExpireIn())),
						// 'validityPeriod' => date('Y-m-d\TH:i:s\Z', $time) . '+07:00',
						// 'validityPeriod' => '2025-06-20T09:54:17.110Z',
					];
					break;
				case 'ewallet':
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
			// $string_to_sign = implode('|', [
			// 	$this->init->getMID(),
			// 	$timestamp,
			// ]);
			// $signature = '';
			// $private_key = $this->RetrievePrivateKey();
			// $private_key = openssl_pkey_get_private($private_key);
			// if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
			// 	throw new \Exception('Failed to getToken', 800);
			// }
			// $signature = base64_encode($signature);
			//
			$data = json_encode($this->request['data']);
			$hashed_data = strtolower(bin2hex(hash("sha256", $data, true)));
			$string_to_sign = implode(':', [
				'POST',
				$url,
				$this->GetAccessToken(),
				$hashed_data,
				$timestamp,
			]);
			$signature = base64_encode(hash_hmac('sha512', $string_to_sign, $this->init->getMID(), true));
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $timestamp,
				'X-SIGNATURE' => $signature,
				'X-CLIENT-KEY' => $this->init->getMID(),
				'Authorization' => 'Bearer ' . $this->GetAccessToken(),
				// 'X-PARTNER-ID' => $this->init->getSecret(),
				'X-EXTERNAL-ID' => date('YmdHis', $time) . hrtime()[1],
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
					&& strtolower($content->responseCode) == "200000"
					&& !empty($content->responseMessage)
					&& strpos(strtolower($content->responseMessage), 'success') !== false
					&& !empty($content->qrContent)
				) {
					/* // Success QRIS
						{
							"responseCode": "200000",
							"responseMessage": "Request has been processed successfully",
							"referenceNo": "01K1E258113VFZNBN2A563VP7",
							"partnerReferenceNo": "QRIS1753892427",
							"qrContent": "00020101021226670016COM.NOBUBANK.WWW01189360050300000493330214051500000321410303UKE51440014ID.CO.QRIS.WWW0215ID20200814127250303UKE52045499530336054061000005802ID5908INDORAYA6015Banten - Kota T6105158116262011410030003556562062501J98ZV3H1EGEZ3CNKG0THPE30703A010804POSP6304E5C8",
							"additionalInfo": "{}",
							"merchantName": "",
							"storeId": "ID2020081412725"
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
			if (strpos($e->getMessage(), '"responseCode":"401001"') !== false) {
				/* // Failed token
					{
						"responseCode": "401001",
						"responseMessage": "Invalid Access Token"
					}
				*/
				$this->SetAccessToken($this->GetToken([]));
				goto retry;
			}
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
			$this->CheckToken();
			retry:
			//
			$time = time();
			$timestamp = date("Y-m-d\TH:i:s", $time) . "+07:00";
			$payment_method = trim(strtolower($request->setPaymentMethod));
			switch ($payment_method) {
				case 'va':
					break;
				case 'qris':
					$url = '/pjp/qris/v1.0/qr/qr-mpm-query';
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						$url
					);
					$this->request['data'] = [
						'originalReferenceNo' => $request->originalReferenceNo,
						'originalPartnerReferenceNo' => $request->originalPartnerReferenceNo,
					];
					break;
				case 'ewallet':
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
			$data = json_encode($this->request['data']);
			$hashed_data = strtolower(bin2hex(hash("sha256", $data, true)));
			$string_to_sign = implode(':', [
				'POST',
				$url,
				$this->GetAccessToken(),
				$hashed_data,
				$timestamp,
			]);
			$signature = base64_encode(hash_hmac('sha512', $string_to_sign, $this->init->getMID(), true));
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $timestamp,
				'X-SIGNATURE' => $signature,
				'X-CLIENT-KEY' => $this->init->getMID(),
				'Authorization' => 'Bearer ' . $this->GetAccessToken(),
				// 'X-PARTNER-ID' => $this->init->getSecret(),
				'X-EXTERNAL-ID' => date('YmdHis', $time) . hrtime()[1],
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
					&& strtolower($content->responseCode) == "2005100"
					&& !empty($content->responseMessage)
					&& strpos(strtolower($content->responseMessage), 'success') !== false
					&& !empty($content->latestTransactionStatus)
				) {
					/* // Success inquiry QRIS
						{
							"responseCode": "2005100",
							"responseMessage": "Request has been processed successfully",
							"originalReferenceNo": "01K1E258113VFZNBN2A563VP7",
							"serviceCode": "51",
							"latestTransactionStatus": "00",
							"paidTime": "2025-07-30T16:25:55Z",
							"amount": {
								"value": "119999",
								"currency": "IDR"
							},
							"feeAmount": {
								"value": "1",
								"currency": "IDR"
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
			if (strpos($e->getMessage(), '"responseCode":"401001"') !== false) {
				/* // Failed token
					{
						"responseCode": "401001",
						"responseMessage": "Invalid Access Token"
					}
				*/
				$this->SetAccessToken($this->GetToken([]));
				goto retry;
			}
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

	public function SimulatePayment(object $request)
	{
		try {
			$this->CheckToken();
			retry:
			//
			$time = time();
			$timestamp = date("Y-m-d\TH:i:s", $time) . "+07:00";
			$payment_method = trim(strtolower($request->setPaymentMethod));
			switch ($payment_method) {
				case 'va':
					break;
				case 'qris':
					$url = '/pjp/qris/v1.0/qr/simulate';
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						$url
					);
					$this->request['data'] = [
						'referenceNo' => $request->originalPartnerReferenceNo,
					];
					break;
				case 'ewallet':
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
			$data = json_encode($this->request['data']);
			$hashed_data = strtolower(bin2hex(hash("sha256", $data, true)));
			$string_to_sign = implode(':', [
				'POST',
				$url,
				$this->GetAccessToken(),
				$hashed_data,
				$timestamp,
			]);
			$signature = base64_encode(hash_hmac('sha512', $string_to_sign, $this->init->getMID(), true));
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $timestamp,
				'X-SIGNATURE' => $signature,
				'X-CLIENT-KEY' => $this->init->getMID(),
				'Authorization' => 'Bearer ' . $this->GetAccessToken(),
				// 'X-PARTNER-ID' => $this->init->getSecret(),
				'X-EXTERNAL-ID' => date('YmdHis', $time) . hrtime()[1],
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code == 200) {
				$res = [
					'status' => '000',
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 902);
			}
		} catch (\Throwable $e) {
			if (strpos($e->getMessage(), '"responseCode":"401001"') !== false) {
				/* // Failed token
					{
						"responseCode": "401001",
						"responseMessage": "Invalid Access Token"
					}
				*/
				$this->SetAccessToken($this->GetToken([]));
				goto retry;
			}
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
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
			$this->CheckToken();
			retry:
			//
			$time = time();
			$timestamp = date("Y-m-d\TH:i:s", $time) . "+07:00";
			$url = '/pjp/disbursement/v1.0/balance-inquiry';
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				$url
			);
			$this->request['data'] = [
				'accountNo' => $this->init->getParam('TITAN_ACCOUNT_NO'),
			];
			//
			$data = json_encode($this->request['data']);
			$hashed_data = strtolower(bin2hex(hash("sha256", $data, true)));
			$string_to_sign = implode(':', [
				'POST',
				$url,
				$this->GetAccessToken(),
				$hashed_data,
				$timestamp,
			]);
			$signature = base64_encode(hash_hmac('sha512', $string_to_sign, $this->init->getMID(), true));
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $timestamp,
				'X-SIGNATURE' => $signature,
				'X-CLIENT-KEY' => $this->init->getMID(),
				'Authorization' => 'Bearer ' . $this->GetAccessToken(),
				'X-PARTNER-ID' => $this->init->getSecret(),
				'X-EXTERNAL-ID' => date('YmdHis', $time) . hrtime()[1],
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
					!empty($content->responseCode) && strtolower($content->responseCode) == "2001100"
					&& !empty($content->responseMessage) && stripos($content->responseMessage, 'success') !== false
					&& isset($content->accountInfo[0]->availableBalance->value)
				) {
					/* // Success check balance
						{
							"accountInfo": [
								{
										"availableBalance": {
											"currency": "IDR",
											"value": "0.00"
										}
								}
							],
							"responseCode": "2001100",
							"responseMessage": "Request has been processed successfully"
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
			if (strpos($e->getMessage(), '"responseCode":"401001"') !== false) {
				/* // Failed token
					{
						"responseCode": "401001",
						"responseMessage": "Invalid Access Token"
					}
				*/
				$this->SetAccessToken($this->GetToken([]));
				goto retry;
			}
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function BankAccountInquiry(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->CheckToken();
			retry:
			//
			$time = time();
			$timestamp = date("Y-m-d\TH:i:s", $time) . "+07:00";
			$external_id = date('YmdHis', $time) . hrtime()[1];
			$url = '/disbursement/v1.0/account-inquiry-external';
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				$url
			);
			$this->request['data'] = [
				'partnerReferenceNo' => $transaction->getReferenceNumber() ?? $external_id,
				'beneficiaryBankCode' => $transaction->getCustomerBankCode(),
				'beneficiaryAccountNo' => $transaction->getCustomerBankAccountNumber(),
				'additionalInfo' => [
					'amount' => [
						'currency' => $transaction->getCurrency() ?? 'IDR',
						'value' => (string) ($transaction->getAmount() . '.00'),
					],
				],
			];
			//
			$data = json_encode($this->request['data']);
			$hashed_data = strtolower(bin2hex(hash("sha256", $data, true)));
			$string_to_sign = implode(':', [
				'POST',
				$url,
				$this->GetAccessToken(),
				$hashed_data,
				$timestamp,
			]);
			$signature = base64_encode(hash_hmac('sha512', $string_to_sign, $this->init->getMID(), true));
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $timestamp,
				'X-SIGNATURE' => $signature,
				'X-CLIENT-KEY' => $this->init->getMID(),
				'Authorization' => 'Bearer ' . $this->GetAccessToken(),
				'X-PARTNER-ID' => $this->init->getSecret(),
				'X-EXTERNAL-ID' => date('YmdHis', $time) . hrtime()[1],
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
					&& strtolower($content->responseCode) == "2001600"
					&& !empty($content->responseMessage)
					&& strpos(strtolower($content->responseMessage), 'success') !== false
				) {
					/* // Success inquiry
						{
							"responseCode": "2001600",
							"responseMessage": "Request has been processed successfully",
							"partnerReferenceNo": "20251029222457286621200",
							"beneficiaryAccountName": "R BAMBANG ARI SETIYOKO",
							"beneficiaryAccountNo": "5020359309",
							"beneficiaryBankCode": "014",
							"beneficiaryBankName": "bca",
							"currency": "IDR",
							"additionalInfo": {
								"isFeeChargedToUser": false,
								"amount": {
										"value": "10000.00",
										"currency": "IDR"
								}
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
			if (strpos($e->getMessage(), '"responseCode":"401001"') !== false) {
				/* // Failed token
					{
						"responseCode": "401001",
						"responseMessage": "Invalid Access Token"
					}
				*/
				$this->SetAccessToken($this->GetToken([]));
				goto retry;
			}
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function OnlineTransferExec(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->CheckToken();
			retry:
			//
			$time = time();
			$timestamp = date("Y-m-d\TH:i:s", $time) . "+07:00";
			$transfer_method = trim(strtolower($transaction->getTransferMethod()));
			switch ($transfer_method) {
				case 'bank':
					$url = '/pjp/disbursement/v1.0/transfer-interbank';
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						$url
					);
					$this->request['data'] = [
						'partnerReferenceNo' => $transaction->getReferenceNumber(),
						'bankCode' => $transaction->getCustomerBankCode(),
						'accountNumber' => $transaction->getCustomerBankAccountNumber(),
						'accountName' => $transaction->getCustomerBankAccountName(),
						'sourceAccountNo' => $transaction->getParam('SOURCE_ACCOUNT_NO'),
						'amount' => $transaction->getAmount(),
					];
					break;
				case 'ewallet':
					$url = '/pjp/disbursement/v1.0/transfer-interbank';
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						$url
					);
					$this->request['data'] = [
						'partnerReferenceNo' => $transaction->getReferenceNumber(),
						'channelCode' => strtoupper($transaction->getCustomerBankCode()),
						'accountNumber' => $transaction->getCustomerBankAccountNumber(),
						'accountName' => $transaction->getCustomerBankAccountName(),
						'sourceAccountNo' => $transaction->getParam('SOURCE_ACCOUNT_NO'),
						'amount' => $transaction->getAmount(),
					];
					break;
				default:
					throw new \Exception('Undefined payment method', 800);
					break;
			}
			//
			$data = json_encode($this->request['data']);
			$hashed_data = strtolower(bin2hex(hash("sha256", $data, true)));
			$string_to_sign = implode(':', [
				'POST',
				$url,
				$this->GetAccessToken(),
				$hashed_data,
				$timestamp,
			]);
			$signature = base64_encode(hash_hmac('sha512', $string_to_sign, $this->init->getMID(), true));
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $timestamp,
				'X-SIGNATURE' => $signature,
				'X-CLIENT-KEY' => $this->init->getMID(),
				'Authorization' => 'Bearer ' . $this->GetAccessToken(),
				'X-PARTNER-ID' => $this->init->getSecret(),
				'X-EXTERNAL-ID' => date('YmdHis', $time) . hrtime()[1],
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
					&&
						(strtolower($content->responseCode) == "202000" || strtolower($content->responseCode) == "2020000")
					&& !empty($content->responseMessage)
					&& strpos(strtolower($content->responseMessage), 'success') !== false
				) {
					/* // Success disb
						{
							"additionalInfo": {
								"validationReferenceNo": "TRANSFER1761751756-7531",
								"isFeeChargedToUser": false
							},
							"amount": {
								"value": "10000.00",
								"currency": "IDR"
							},
							"beneficiaryAccountNo": "",
							"beneficiaryBankCode": "014",
							"partnerReferenceNo": "TRANSFER1761751756-7531",
							"referenceNo": "DIS_01K8R9CX0Y4SV4EVXDDDK1S0GM",
							"responseCode": "202000",
							"responseMessage": "Request has been processed successfully"
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
			if (strpos($e->getMessage(), '"responseCode":"401001"') !== false) {
				/* // Failed token
					{
						"responseCode": "401001",
						"responseMessage": "Invalid Access Token"
					}
				*/
				$this->SetAccessToken($this->GetToken([]));
				goto retry;
			}
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function OnlineTransferCheckTransfer(object $request)
	{
		try {
			$this->CheckToken();
			retry:
			//
			$time = time();
			$timestamp = date("Y-m-d\TH:i:s", $time) . "+07:00";
			$url = '/pjp/disbursement/v1.0/transfer/status';
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				$url
			);
			$this->request['data'] = [
				'originalPartnerReferenceNo' => $request->originalPartnerReferenceNo,
				// 'serviceCode' => $request->serviceCode,
				// 'amount' => [
				// 	'value' => $request->amount,
				// 	'currency' => $request->currency ?? 'IDR',
				// ],
				// 'originalReferenceNo' => $request->originalReferenceNo,
			];
			//
			$data = json_encode($this->request['data']);
			$hashed_data = strtolower(bin2hex(hash("sha256", $data, true)));
			$string_to_sign = implode(':', [
				'POST',
				$url,
				$this->GetAccessToken(),
				$hashed_data,
				$timestamp,
			]);
			$signature = base64_encode(hash_hmac('sha512', $string_to_sign, $this->init->getMID(), true));
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $timestamp,
				'X-SIGNATURE' => $signature,
				'X-CLIENT-KEY' => $this->init->getMID(),
				'Authorization' => 'Bearer ' . $this->GetAccessToken(),
				'X-PARTNER-ID' => $this->init->getSecret(),
				'X-EXTERNAL-ID' => date('YmdHis', $time) . hrtime()[1],
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
					&& strtolower($content->responseCode) == "200000"
					&& !empty($content->responseMessage)
					&& strpos(strtolower($content->responseMessage), 'success') !== false
				) {
					/* // Success check disb
						{
							"additionalInfo": {
								"isFeeChargedToUser": false,
								"amount": {
										"value": "",
										"currency": ""
								}
							},
							"amount": {
								"value": "10000.00",
								"currency": "IDR"
							},
							"beneficiaryAccountNo": "5020359309",
							"beneficiaryBankCode": "014",
							"currency": "IDR",
							"latestTransactionStatus": "06",
							"transactionStatusDesc": "FAILED",
							"originalPartnerReferenceNo": "TRANSFER1761751756-7531",
							"originalReferenceNo": "DIS_01K8R9CX0Y4SV4EVXDDDK1S0GM",
							"serviceCode": "",
							"sourceAccountNo": "01K3YZV7WFYFNHEWW9T1R81MD7",
							"transactionId": "01K8R9CX12NSWB2ESQKE90AX9M",
							"responseCode": "200000",
							"responseMessage": "Request has been processed successfully"
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
			if (strpos($e->getMessage(), '"responseCode":"401001"') !== false) {
				/* // Failed token
					{
						"responseCode": "401001",
						"responseMessage": "Invalid Access Token"
					}
				*/
				$this->SetAccessToken($this->GetToken([]));
				goto retry;
			}
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

}
