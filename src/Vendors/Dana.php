<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;

use PyGPaymentLib\Helper;

class Dana extends Requestor implements VendorInterface
{
	use Helper;

	protected $form;

	public function Index()
	{
		// Inapplicable
	}

	public function getPrivateKey()
	{
		$private_key = $this->init->getParams()['private_key'];
		if (!empty($private_key)) {
			return
				"-----BEGIN RSA PRIVATE KEY-----\n" .
				chunk_split($private_key, 64, "\n") .
				"-----END RSA PRIVATE KEY-----\n";
		}
		if (!$cert_store = file_get_contents($_ENV['DANA_PRIVATE_KEY_FILE'])) {
			return "Error: Unable to read the cert file\n";
		}
		return $cert_store;
	}

	public function GetToken($args)
	{
		try {
			$time = time();
			$this->request['data'] = [
				'grantType' => 'client_credentials',
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v1.0/access-token/b2b.htm'
			);
$timestamp = date('Y-m-d\TH:i:sP', $time);
$payload = $this->init->getParams()['client_id'] . '|' . $timestamp;
$signature = '';
$private_key = $this->getPrivateKey();
if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
	throw new \Exception('Failed to getToken', 800);
}
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $timestamp,
				'X-CLIENT-KEY' => $this->init->getParams()['client_id'],
				'X-SIGNATURE' => base64_encode($signature),
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
					// Success
					/*
						{
							"responseCode": "2007300",
							"responseMessage": "Successful",
							"accessToken": "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE2NjUwODAwNTAsImlzcyI6ImlkLmRhbmEiLCJleHAiOjE2NjUwODA2NTB9.altuYuNGbpiPIlleUU7xSAppZzO3JCU4WjRaqkA3Qx_a4xTkJiHH1bYtScisKy4ZFyhcSJEcx2Gsr-htz7zmTzTEHZLW_80vcQj4oJFqKIXsbiiB-MZE_DDRG9MpICj-i3jsijhnkfo8acgPd10uZj5g-CmFCfyibGl2UEzVRnI5nfuTtLfCFDwIDruVEqqCcte8civ6Vqqjhx0tnSQvHEESfX1FxqFYUWJZDGHaz47ZZHQbHHiTvOnUPE_2xBS7TVXt-4rddRPMgbZ5xzd3-IQEMFeZ3WJcQNXh5kRyZrmnzcDEkH8LbqjK9KS9p1MFZ-YCTZ3HdPN43B2dNX3Bvw",
							"tokenType": "Bearer",
							"expiresIn": "600"
						}
					*/
					$res = $content;
				} else {
					throw new \Exception('Data is empty', 901);
				}
			} else {
				throw new \Exception('Status code is not 200', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}



	/**
	 *
	 * Payment
	 *
	 */

	public function CreateDummyForm($args)
	{
		// Inapplicable
	}

	public function RedirectPayment(\PyGPaymentLib\Transaction $transaction)
	{
		// Not applicable
	}

	public function SecurePayment(\PyGPaymentLib\Transaction $transaction)
	{
		// Not applicable
	}

	public function ParsePaymentPage($payment_url, $param = '')
	{
		// Not applicable
	}

	public function Callback(object $request)
	{
		// Not applicable
	}

	public function CallbackAlt(object $request)
	{
		// Inapplicable
	}

	public function Inquiry(object $request)
	{
		// Not applicable
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



	/**
	 *
	 * Disbursement
	 *
	 */

	public function BankList()
	{
		// Inapplicable
	}

	public function CheckBalance()
	{
		try {
			$time = time();
			$timestamp = date('Y-m-d\TH:i:sP', $time);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/dana/merchant/queryMerchantResource.htm'
			);
			$body = [
				'requestMerchantId' => $this->init->getParams()['merchant_id'],
				'merchantResourceInfoList' => [
					'MERCHANT_DEPOSIT_BALANCE',
					'MERCHANT_AVAILABLE_BALANCE',
					'MERCHANT_TOTAL_BALANCE',
				],
			];
			$head = [
				'version' => '2.0',
				'function' => 'dana.merchant.queryMerchantResource',
				'clientId' => $this->init->getParams()['client_id'],
				'reqTime' => $timestamp,
				'reqMsgId' => 'TIME' . $time,
				'clientSecret' => $this->init->getParams()['client_secret'],
				'reserve' => 'null',
			];
			$this->request['data'] = ["request" => [
				'head' => $head,
				'body' => $body,
			]];
$payload =
	'POST' .
	':' . '/dana/merchant/queryMerchantResource.htm' .
	':' . hash('sha256', json_encode($this->request['data'])) .
	':' . $timestamp;
$signature = hash_hmac('sha512', $payload, $this->init->getParams()['client_secret']);
$private_key = $this->getPrivateKey();
if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
	throw new \Exception('Failed to getToken', 800);
}
			$this->request['data']['signature'] = base64_encode($signature);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $timestamp,
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
					!empty($content)
				) {
					/* // Success
						{
							"response": {
								"head": {
										"function": "dana.merchant.queryMerchantResource",
										"clientId": "2023011299157589316709",
										"version": "2.0",
										"respTime": "2024-05-10T14:46:23+07:00",
										"reqMsgId": "TIME1715327183"
								},
								"body": {
										"resultInfo": {
											"resultCodeId": "00000000",
											"resultCode": "SUCCESS",
											"resultMsg": "SUCCESS",
											"resultStatus": "S"
										},
										"merchantResourceInformations": [
											{
												"resourceType": "MERCHANT_DEPOSIT_BALANCE",
												"value": "{\"amount\":\"190308000\",\"currency\":\"IDR\"}"
											},
											{
												"resourceType": "MERCHANT_AVAILABLE_BALANCE",
												"value": "{\"amount\":\"0\",\"currency\":\"IDR\"}"
											},
											{
												"resourceType": "MERCHANT_TOTAL_BALANCE",
												"value": "{\"amount\":\"0\",\"currency\":\"IDR\"}"
											}
										]
								}
							},
							"signature": "WQ/FrsZHWiK+ZSuuQ1kwWDKR8AQnHEK0tujYO/c/lcZDLoDmGKzEm8M9nisgPnlWPHPDvdZzirC6xu6v7tDNVpWqr6yjKZSAPkfvRafqxDx5a7GxEl2DB1XWoezDm1GRyRC4MZehFyXRoUcUFcss1GcZy+OS22u7Qr6/9dPeOs0MeWEVrsUK0sSN6I7JXmOJTbPKfbcAz7zyYXVtvuJ3l6Q8fDGaNV8Jl5+eV3kbkEAfxCm6zodwnXBoo8+rOEWJP1oR+4Ep70s4a5aF2MCcRChwgy5YiayutXI0FzZvBdfy3gJNQCfqSPM+9m2fMde1ArSC8FM+C9g5B3sq0aXSAA=="
						}
					*/
					$res = [
						'status' => '000',
						'data' => $content,
					];
				} else {
					throw new \Exception('Data is empty', 901);
				}
			} else {
				throw new \Exception('Status code is not 200', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function BankTransferInquiry(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$time = time();
			$timestamp = date('Y-m-d\TH:i:sP', $time);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v1.0/emoney/bank-account-inquiry.htm'
			);
			$this->request['data'] = [
				'partnerReferenceNo' => $transaction->getOrderID(),
				// 'customerNumber' => $transaction->getCustomerPhone(),
				'beneficiaryAccountNumber' => $transaction->getCustomerBankAccountNumber(),
				'amount' =>	[
					'value' => (int) $transaction->getAmount() . '.00',
					'currency' => $transaction->getCurrency(),
				],
				'additionalInfo' => [
					'fundType' => 'MERCHANT_WITHDRAW_FOR_CORPORATE',
					'beneficiaryBankCode' => $transaction->getCustomerBankCode(),
					// 'beneficiaryAccountName' => '',
					// 'accountType' => 'SETTLEMENT_ACCOUNT',
				],
			];
$payload =
	'POST' .
	':' . '/v1.0/emoney/bank-account-inquiry.htm' .
	':' . hash('sha256', json_encode($this->request['data'])) .
	':' . $timestamp;
$signature = hash_hmac('sha512', $payload, $this->init->getParams()['client_secret']);
$private_key = $this->getPrivateKey();
if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
	throw new \Exception('Failed to getToken', 800);
}
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				// 'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
				'X-TIMESTAMP' => $timestamp,
				'X-SIGNATURE' => base64_encode($signature),
				'ORIGIN' => 'gateway.nusapay.co.id',
				// 'X-PARTNER-ID' => $this->init->getMID(),
				'X-PARTNER-ID' => $this->init->getParams()['client_id'],
				'X-EXTERNAL-ID' => $time,
				'X-IP-ADDRESS' => '172.104.170.5',
				'CHANNEL-ID' => 'NP',
				'X-DEVICE-ID' => '09864ADCASA',
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
					!empty($content)
				) {
					/* // Success
						{
							"responseCode": "2004200",
							"responseMessage": "Success",
							"partnerReferenceNo": "1667310157",
							"beneficiaryAccountNumber": "164000***4677",
							"beneficiaryAccountName": "John Walker",
							"beneficiaryBankCode": "014",
							"beneficiaryBankShortName": "BCA",
							"beneficiaryBankName": "BCA BANK CENTRAL ASIA",
							"amount": {
								"value": "15000.00",
								"currency": "IDR"
							},
							"additionalInfo": {
								"feeAmount": {
									"value": "0.00",
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
					throw new \Exception('Data is empty', 901);
				}
			} else {
				throw new \Exception('Status code is not 200', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
			// $res = $this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function BankTransferExec(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$time = time();
			$this->request['data'] = [
				'partnerReferenceNo' => $transaction->getOrderID(),
				// 'customerNumber' => $transaction->getCustomerPhone(),
				// 'accountType' => 'SETTLEMENT_ACCOUNT',
				'beneficiaryAccountNumber' => $transaction->getCustomerBankAccountNumber(),
				'beneficiaryBankCode' => $transaction->getCustomerBankCode(),
				'amount' =>	[
					'value' => (int) $transaction->getAmount() . '.00',
					'currency' => $transaction->getCurrency(),
				],
				'additionalInfo' => [
					'fundType' => 'MERCHANT_WITHDRAW_FOR_CORPORATE',
					'needNotify' => 'true',
					// 'beneficiaryAccountName' => '',

				],
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v1.0/emoney/transfer-bank.htm'
			);
$timestamp = date('Y-m-d\TH:i:sP', $time);
$payload =
	'POST' .
	':' . '/v1.0/emoney/transfer-bank.htm' .
	':' . hash('sha256', json_encode($this->request['data'])) .
	':' . $timestamp;
$signature = hash_hmac('sha512', $payload, $this->init->getParams()['client_secret']);
$private_key = $this->getPrivateKey();
if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
	throw new \Exception('Failed to getToken', 800);
}
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				// 'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
				'X-TIMESTAMP' => $timestamp,
				'X-SIGNATURE' => base64_encode($signature),
				'ORIGIN' => 'gateway.nusapay.co.id',
				// 'X-PARTNER-ID' => $this->init->getMID(),
				'X-PARTNER-ID' => $this->init->getParams()['client_id'],
				'X-EXTERNAL-ID' => $time,
				'X-IP-ADDRESS' => '172.104.170.5',
				'CHANNEL-ID' => 'NP',
				'X-DEVICE-ID' => '09864ADCASA',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (
				!empty($status_code)
				&& (
					$status_code == 200
					|| $status_code === 202 // The request has been accepted for processing, but the processing has not been completed
				)
			) {
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content)
				) {
					/* // Success
						{
							"responseCode": "2024300",
							"responseMessage": "Request In Progress",
							"referenceNo": "2022110110121482010100166141400273577",
							"referenceNumber": "2022110110121482010100166141400273577",
							"transactionDate": "2022-11-01T20:44:08+07:00",
							"partnerReferenceNo": "1667310248",
							"additionalInfo": {
								"resultMsg": "PAYMENT_IN_PROCESS"
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception('Data is empty', 901);
				}
			} else {
				throw new \Exception('Status code is not 200/202', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
			// $res = $this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function BankTransferInquiryStatus(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$time = time();
			$timestamp = date('Y-m-d\TH:i:sP', $time);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v1.0/debit/emoney/transfer-bank/notify.htm'
			);
			$this->request['data'] = [
				'originalPartnerReferenceNo' => $transaction->getOrderID(),
				'originalReferenceNo' => $transaction->getReferenceNumber(),
				'latestTransactionStatus' => $transaction->getTransactionRemark(),
				'transactionStatusDesc' => $transaction->getTransactionRemark(),
				// 'createdTime' => null,
				// 'finishedTime' => null,
				// 'additionalInfo' => [],
			];
$payload =
	'POST' .
	':' . '/v1.0/debit/emoney/transfer-bank/notify.htm' .
	':' . hash('sha256', json_encode($this->request['data'])) .
	':' . $timestamp;
$signature = hash_hmac('sha512', $payload, $this->init->getParams()['client_secret']);
$private_key = $this->getPrivateKey();
if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
	throw new \Exception('Failed to getToken', 800);
}
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				// 'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
				'X-TIMESTAMP' => $timestamp,
				'X-SIGNATURE' => base64_encode($signature),
				'ORIGIN' => 'gateway.nusapay.co.id',
				// 'X-PARTNER-ID' => $this->init->getMID(),
				'X-PARTNER-ID' => $this->init->getParams()['client_id'],
				'X-EXTERNAL-ID' => $time,
				'X-IP-ADDRESS' => '172.104.170.5',
				'CHANNEL-ID' => 'NP',
				'X-DEVICE-ID' => '09864ADCASA',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
// print_r($this->request);
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
// print_r($post);
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 200
			) {
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content)
				) {
					/* // Success
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception('Data is empty', 901);
				}
			} else {
				throw new \Exception('Status code is not 200', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
			// $res = $this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function EmoneyTransferInquiry(\PyGPaymentLib\Transaction $transaction)
	{
// 		try {
// 			$time = time();
// 			$this->request['data'] = [
// 				'partnerReferenceNo' => $transaction->getOrderID(),
// 				'customerNumber' => $transaction->getCustomerPhone(),
// 				'amount' =>	[
// 					'value' => (int) $transaction->getAmount() . '.00',
// 					'currency' => $transaction->getCurrency(),
// 				],
// 				'transactionDate' => date('c', $transaction->getTime()),
// 				'additionalInfo' => [
// 					'fundType' => 'AGENT_TOPUP_FOR_USER_SETTLE',
// 					// 'externalDivisionId' => '91080916Division',
// 					// 'chargeTarget' => 'DIVISION',
// 				],
// 			];
// 			$this->request['url'] = SELF::CleanURL(
// 				$this->init->getRequestURL() .
// 				'/v1.0/emoney/account-inquiry.htm'
// 			);
// $timestamp = date('Y-m-d\TH:i:sP', $time);
// $payload =
// 	'POST' .
// 	':' . '/v1.0/emoney/account-inquiry.htm' .
// 	':' . hash('sha256', json_encode($this->request['data'])) .
// 	':' . $timestamp;
// $signature = hash_hmac('sha512', $payload, $this->init->getParams()['client_secret']);
// $private_key = $this->getPrivateKey();
// if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
// 	throw new \Exception('Failed to getToken', 800);
// }
// 			$this->request['headers'] = [
// 				'Content-Type' => 'application/json',
// 				// 'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
// 				'X-TIMESTAMP' => $timestamp,
// 				'X-SIGNATURE' => base64_encode($signature),
// 				'ORIGIN' => 'gateway.nusapay.co.id',
// 				// 'X-PARTNER-ID' => $this->init->getMID(),
// 				'X-PARTNER-ID' => $this->init->getParams()['client_id'],
// 				'X-EXTERNAL-ID' => $time,
// 				'X-IP-ADDRESS' => '172.104.170.5',
// 				'CHANNEL-ID' => 'NP',
// 				'X-DEVICE-ID' => '09864ADCASA',
// 			];
// 			$this->request['option'] = [
// 				'as_json' => true,
// 			];
// 			$post = $this->DoRequest('POST', $this->request);
// 			$response = (array) $post['response'];
// 			extract($response);
// 			if (
// 				!empty($status_code)
// 				&& $status_code === 200
// 			) {
// 				// Parse data
// 				$content = (object) json_decode($content);
// 				if (
// 					!empty($content)
// 				) {
// 					/* // Success
// 						{
// 							"responseCode": "2003700",
// 							"responseMessage": "Successful",
// 							"referenceNo": "d4954554cc6347c9be4426038717bad5",
// 							"partnerReferenceNo": "1668489410",
// 							"customerNumber": "628127416027",
// 							"customerName": "mm",
// 							"feeType": "Service fee",
// 							"amount": {
// 								"value": "15000.00",
// 								"currency": "IDR"
// 							},
// 							"feeAmount": {
// 								"value": "1620.00",
// 								"currency": "IDR"
// 							},
// 							"customerMonthlyInLimit": "20000000",
// 							"minAmount": {
// 								"value": "0.00",
// 								"currency": "IDR"
// 							},
// 							"maxAmount": {
// 								"value": "1386700.00",
// 								"currency": "IDR"
// 							}
// 						}
// 					*/
// 					$res = [
// 						'status' => '000',
// 						'data' => (array) $content,
// 					];
// 				} else {
// 					throw new \Exception('Data is empty', 901);
// 				}
// 			} else {
// 				throw new \Exception('Status code is not 200', 902);
// 			}
// 		} catch (\Throwable $e) {
// 			$this->ThrowErrorException($e);
// 			// $res = $this->ThrowErrorException($e);
// 		}
// 		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function EmoneyTransferExec(\PyGPaymentLib\Transaction $transaction)
	{
// 		try {
// 			$time = time();
// 			$this->request['data'] = [
// 				'partnerReferenceNo' => $transaction->getOrderID(),
// 				'customerNumber' => $transaction->getCustomerPhone(),
// 				'amount' =>	[
// 					'value' => (int) $transaction->getAmount() . '.00',
// 					'currency' => $transaction->getCurrency(),
// 				],
// 				'feeAmount' =>	[
// 					'value' => (int) 0 . '.00',
// 					'currency' => $transaction->getCurrency(),
// 				],
// 				'transactionDate' => date('c', $transaction->getTime()),
// 				'sessionId' => '',
// 				'categoryId' => '',
// 				'notes' => '',
// 				'additionalInfo' => [
// 					'extendInfo' => '',
// 					// 'accountType' => 'LAZADA_DEPOSIT',
// 					'fundType' => 'AGENT_TOPUP_FOR_USER_SETTLE',
// 					// 'externalDivisionId' => '91080916Division',
// 					// 'chargeTarget' => 'DIVISION',
// 				],
// 			];
// 			$this->request['url'] = SELF::CleanURL(
// 				$this->init->getRequestURL() .
// 				'/v1.0/emoney/topup.htm'
// 			);
// $timestamp = date('Y-m-d\TH:i:sP', $time);
// $payload =
// 	'POST' .
// 	':' . '/v1.0/emoney/topup.htm' .
// 	':' . hash('sha256', json_encode($this->request['data'])) .
// 	':' . $timestamp;
// $signature = hash_hmac('sha512', $payload, $this->init->getParams()['client_secret']);
// $private_key = $this->getPrivateKey();
// if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
// 	throw new \Exception('Failed to getToken', 800);
// }
// 			$this->request['headers'] = [
// 				'Content-Type' => 'application/json',
// 				// 'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
// 				'X-TIMESTAMP' => $timestamp,
// 				'X-SIGNATURE' => base64_encode($signature),
// 				'ORIGIN' => 'gateway.nusapay.co.id',
// 				// 'X-PARTNER-ID' => $this->init->getMID(),
// 				'X-PARTNER-ID' => $this->init->getParams()['client_id'],
// 				'X-EXTERNAL-ID' => $time,
// 				'X-IP-ADDRESS' => '172.104.170.5',
// 				'CHANNEL-ID' => 'NP',
// 				'X-DEVICE-ID' => '09864ADCASA',
// 			];
// 			$this->request['option'] = [
// 				'as_json' => true,
// 			];
// 			$post = $this->DoRequest('POST', $this->request);
// 			$response = (array) $post['response'];
// 			extract($response);
// 			if (
// 				!empty($status_code)
// 				&& $status_code === 200
// 			) {
// 				// Parse data
// 				$content = (object) json_decode($content);
// 				if (
// 					!empty($content)
// 				) {
// 					/* // Success
// 						{
// 							"responseCode": "2003800",
// 							"responseMessage": "Successful",
// 							"referenceNo": "2022111510121481030100166737700698580",
// 							"partnerReferenceNo": "1668489685",
// 							"sessionId": "",
// 							"customerNumber": "628127416027",
// 							"customerName": "mm",
// 							"amount": {
// 								"value": "15000.00",
// 								"currency": "IDR"
// 							},
// 							"additionalInfo": {}
// 						}
// 					*/
// 					$res = [
// 						'status' => '000',
// 						'data' => (array) $content,
// 					];
// 				} else {
// 					throw new \Exception('Data is empty', 901);
// 				}
// 			} else {
// 				throw new \Exception('Status code is not 202', 902);
// 			}
// 		} catch (\Throwable $e) {
// 			$this->ThrowErrorException($e);
// 			// $res = $this->ThrowErrorException($e);
// 		}
// 		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function EmoneyTransferExecCheckStatus(\PyGPaymentLib\Transaction $transaction)
	{
// 		try {
// 			$time = time();
// 			$this->request['data'] = [
// 				'originalPartnerReferenceNo' => $transaction->getTransactionID(),
// 				'originalReferenceNo' => $transaction->getReferenceNumber(),
// 				// 'originalExternalId' => '',
// 				'serviceCode' => '00',
// 				// 'additionalInfo' => [],
// 			];
// 			$this->request['url'] = SELF::CleanURL(
// 				$this->init->getRequestURL() .
// 				'/v1.0/emoney/topup-status.htm'
// 			);
// $timestamp = date('Y-m-d\TH:i:sP', $time);
// $payload =
// 	'POST' .
// 	':' . '/v1.0/emoney/topup-status.htm' .
// 	':' . hash('sha256', json_encode($this->request['data'])) .
// 	':' . $timestamp;
// $signature = hash_hmac('sha512', $payload, $this->init->getParams()['client_secret']);
// $private_key = $this->getPrivateKey();
// if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
// 	throw new \Exception('Failed to getToken', 800);
// }
// 			$this->request['headers'] = [
// 				'Content-Type' => 'application/json',
// 				// 'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
// 				'X-TIMESTAMP' => $timestamp,
// 				'X-SIGNATURE' => base64_encode($signature),
// 				'ORIGIN' => 'gateway.nusapay.co.id',
// 				// 'X-PARTNER-ID' => $this->init->getMID(),
// 				'X-PARTNER-ID' => $this->init->getParams()['client_id'],
// 				'X-EXTERNAL-ID' => $time,
// 				'CHANNEL-ID' => 'NP',
// 			];
// 			$this->request['option'] = [
// 				'as_json' => true,
// 			];
// 			$post = $this->DoRequest('POST', $this->request);
// 			$response = (array) $post['response'];
// 			extract($response);
// 			if (
// 				!empty($status_code)
// 				&& $status_code === 200
// 			) {
// 				// Parse data
// 				$content = (object) json_decode($content);
// 				if (
// 					!empty($content)
// 				) {
// 					/* // Success
// 						{
// 							"responseCode": "2003900",
// 							"responseMessage": "Successful",
// 							"originalPartnerReferenceNo": "1668489782",
// 							"originalReferenceNo": "2022111510121481030100166737700698583",
// 							"serviceCode": "00",
// 							"amount": {
// 								"value": "15000.00",
// 								"currency": "IDR"
// 							},
// 							"latestTransactionStatus": "00",
// 							"transactionStatusDesc": "Success",
// 							"additionalInfo": {}
// 						}
// 					*/
// 					$res = [
// 						'status' => '000',
// 						'data' => (array) $content,
// 					];
// 				} else {
// 					throw new \Exception('Data is empty', 901);
// 				}
// 			} else {
// 				throw new \Exception('Status code is not 202', 902);
// 			}
// 		} catch (\Throwable $e) {
// 			$this->ThrowErrorException($e);
// 			// $res = $this->ThrowErrorException($e);
// 		}
// 		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

}
