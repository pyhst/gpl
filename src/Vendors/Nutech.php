<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;

use PyGPaymentLib\Helper;

class Nutech extends Requestor implements VendorInterface
{
	use Helper;

	protected $form;

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args)
	{
		// Inapplicable
	}

	public function CreateDummyForm($args)
	{
		// Inapplicable
	}

	/**
	 *
	 * Disbursement
	 *
	 */

	public function OnlineTransferExec(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$data = [
				'amount' => (float) $transaction->getAmount(),
				'bankCode' => $transaction->getCustomerBankCode(),
				'bankAccount' => $transaction->getCustomerBankAccountNumber(),
				'merTransactionId' => $transaction->getOrderID(),
				'merchantId' => $this->init->getMID(),
				'mobile' => $transaction->getCustomerPhone(),
				'name' => $transaction->getCustomerName(),
			];
			// Sign
			$data = $data + [
				'sign' => $this->_signature($data),
			];
			// Go
			$this->request['time'] = $transaction->getTime();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() . '/v1/api/disbursement/'
			);
			$this->request['data'] = $data;
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success DISBURSEMENT
					{
						"merTransactionId": "1692611766",
						"transactionId": "1692611766038b0d71a1e",
						"code": 0,
						"msg": "SUCCESS",
						"sign": "rH6Ei8Jwvg+shGtcfF\/eBAkqkOUDNad9H0Knsyn7vW0By4EsoGHKTA=="
					}
				*/
				$content = (object) json_decode($content);
				$res = [
					'status' => $content->code,
					'message' => $content->msg,
					'data' => $content,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function CheckTransfer(object $request)
	{
		try {
			SELF::Validate($request, [
				'transactionId',
				'merTransactionId',
			]);
			$data = [
				'merchantId' => $this->init->getMID(),
				'transactionId' => $request->transactionId,
				'merTransactionId' => $request->merTransactionId,
			];
			// Sign
			$data = $data + [
				'sign' => $this->_signature($data),
			];
			// Go
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() . '/v1/api/disbursement/queryStatus'
			);
			$this->request['data'] = $data;
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success check DISBURSEMENT
					{
						"merTransactionId": "1692611766",
						"amount": 10500,
						"bankCode": "DANAMON",
						"bankAccount": "7700173383",
						"mobile": "081212121314",
						"name": "tester",
						"status": 0,
						"createTime": "2023-08-21T09:56:06.000+00:00",
						"updateTime": "2023-08-21T09:56:11.000+00:00",
						"code": 0,
						"msg": "SUCCESS",
						"sign": "sEOsYSKqhsEla+CDNH0TnAIwctRwPgGGNdG+Ye7SOrtKnKgkdCCBNw=="
					}
				*/
				$content = (object) json_decode($content);
				$res = [
					'status' => $content->code,
					'message' => $content->msg,
					'data' => $content,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function CheckBalance(object $request)
	{
		try {
			$data = [
				'merchantId' => $this->init->getMID(),
			];
			// Sign
			$data = $data + [
				'sign' => $this->_signature($data),
			];
			// Go
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() . '/account/searchMerAccount'
			);
			$this->request['data'] = $data;
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success check balance
					{
						"availableBalance": 0,
						"disabledBalance": 0,
						"code": 0,
						"msg": "SUCCESS",
						"sign": "Oiyo6OlU7IQXO98IYEiPOQkiqOw29cwdoCpR2kPiv5GR8EjMs0YsKA=="
					}
				*/
				$content = (object) json_decode($content);
				$res = [
					'status' => $content->code,
					'message' => $content->msg,
					'data' => $content,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function BankAccountInquiry(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$data = [
				'bankAccount' => $transaction->getCustomerBankAccountNumber(),
				'bankCode' => $transaction->getCustomerBankCode(),
				'merchantId' => $this->init->getMID(),
			];
			// Sign
			$data = $data + [
				'sign' => $this->_signature($data),
			];
			// Go
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() . '/v1/api/queryAccountName'
			);
			$this->request['data'] = $data;
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success inquiry bank account
					{
						"accountName": "PT",
						"code": 0,
						"msg": "SUCCESS",
						"sign": "8d6c657cb8160a5f4cd59f5f99426ffcaca18f8452d80d16de93bb2247f890fdijjkzajsbsqyedansAYo7nHbDY%2FVrxaQpALtg2IuFEO9FXFeBtn78sodQcnKv0Mjsd5bspYV3G6EHoK0"
					}
				*/
				$content = (object) json_decode($content);
				$res = [
					'status' => $content->code,
					'message' => $content->msg,
					'data' => $content,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}



	/**
	 *
	 * Accept Payment
	 *
	 */

	private function _signature(array $data)
	{
		$a = $data;
		ksort($a);
		$key = $this->init->getSecret();
		$b = array_merge($a, [
			'key' => $key,
		]);
		// $b = http_build_query($b);
			foreach ($b as $k => $v) {
				$bb[] = "$k=$v";
			}
			$b = implode('&', $bb);
		$c = strtoupper(md5($b));
		$salt = $this->init->getMerchantKey();
		//
		$enc = openssl_encrypt($c, 'AES-128-CBC', $key, 0, $salt);
		$enc_str = $salt . $enc;
		$hmac = hash_hmac('sha256', $enc_str, $key, false);
		$final = urlencode($hmac . $enc_str);
		/*
		print_r([
			'key' => $key,
			'salt' => $salt,
			'ksort(a)' => $a,
			'b = http_build_query(array_merge(a, key))' => $b,
			'c = strtoupper(md5(b))' => $c,
			'enc = openssl_encrypt(c)' => $enc,
			'enc_str = (salt . enc)' => $enc_str,
			'hmac = hash_hmac(enc_str)' => $hmac,
			'final = urlencode(hmac . enc_str)' => $final,
		]);
		*/
		return $final;
	}

	public function RedirectPayment(\PyGPaymentLib\Transaction $transaction)
	{
		// Inapplicable
	}

	public function SecurePayment(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->transaction = $transaction;
			switch (strtolower($this->transaction->getPaymentMethod())) {
				case 'transfer':
					break;
				case 'va':
					$data = [
						'name' => $this->transaction->getCustomerName(),
						'mobile' => $this->transaction->getCustomerPhone(),
						'merTransactionId' => $this->transaction->getOrderID(),
						'merchantId' => $this->init->getMID(),
						'amount' => (int) $this->transaction->getAmount(),
						'bankCode' => $this->transaction->getPaymentChannel(),
					];
					/* // Success PERMATA
						{
							"merTransactionId": "1692610424",
							"code": 0,
							"msg": "SUCCESS",
							"va": "729361816181",
							"paymentUrl": null,
							"qrisImage": null,
							"sign": "FgsRWvdw12mSV+kSXFds6pCHjFf3sKDR6ugN4Ax6s8kEPVR+dkWMcQ=="
						}
					*/
					break;
				case 'qris':
					$data = [
						'name' => $this->transaction->getCustomerName(),
						'mobile' => $this->transaction->getCustomerPhone(),
						'merTransactionId' => $this->transaction->getOrderID(),
						'merchantId' => $this->init->getMID(),
						'amount' => (int) $this->transaction->getAmount(),
						'bankCode' => $this->transaction->getPaymentChannel(),
					];
					/* // Success QRIS
						{
							"merTransactionId": "1692609839",
							"code": 0,
							"msg": "SUCCESS",
							"va": null,
							"paymentUrl": null,
							"qrisImage": "data:image/png;base64,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",
							"sign": "B0sItalU6tk3fzMrpVmyBpMt2WZLMQmkI2WanOmkPsvXTa3Bn9c8uQ=="
						}
					*/
					break;
				default:
					throw new \Exception('Payment method not defined/unknown', 900);
					break;
			}
			// Sign
			$sign = $this->_signature($data);
			$data = $data + [
				'sign' => $sign,
			];
			// Go
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() . '/v1/api/deposit'
			);
			$this->request['data'] = $data;
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				$res = [
					'status' => $content->code,
					'message' => $content->msg,
					'data' => $content,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function Callback(object $request)
	{
		/* // Sample incoming
			{
				"amount": "10000.00",
				"merTransactionId": "1587009195867f8c6bd60",
				"merchantId": "M00001",
				"msg": "SUCCESS",
				"code": 0,
				"status": 3,
				"channelFee": "100.00",
				"createTime": "2023-05-04 15:23:46",
				"sign": "Test73CD15AD12941F5997BF257A7B39"
			}
		*/
		try {
			SELF::Validate($request, [
				'merTransactionId',
				'merchantId',
				'amount',
				'code',
			]);
			if (strtoupper(trim($request->msg)) == 'SUCCESS') {
				$res = [
					'status' => '000',
					'data' => (array) $request,
				];
			} else {
				// throw new \Exception('Callback status is not paid', 901);
				throw new \Exception('Callback status is not paid', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($request, $res, 200);
	}

	public function CallbackAlt(object $request)
	{
		// Inapplicable
	}

	public function Inquiry(object $request)
	{
		try {
			SELF::Validate($request, [
				// 'transactionId',
				'merTransactionId',
			]);
			$data = [
				'merchantId' => $this->init->getMID(),
				// 'transactionId' => $request->transactionId,
				'merTransactionId' => $request->merTransactionId,
			];
			// Sign
			$data = $data + [
				'sign' => $this->_signature($data),
			];
			// Go
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() . '/v1/api/deposit/queryStatus'
			);
			$this->request['data'] = $data;
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success PERMATA
					{
						"merTransactionId": "1692610424",
						"amount": 10000,
						"bankCode": "PERMATA",
						"va": "729361816181",
						"mobile": "081212121314",
						"name": "Testing",
						"status": 0,
						"createTime": "2023-08-21T09:33:48.000+00:00",
						"updateTime": "2023-08-21T09:33:47.000+00:00",
						"code": 0,
						"msg": "SUCCESS",
						"sign": "YGH7noxxhPAgUh6DVyGJjxOtWZniLuCLFw735b0xJN6RygCEIcBhGQ=="
					}
				*/
				$content = (object) json_decode($content);
				$res = [
					'status' => $content->code,
					'message' => $content->msg,
					'data' => $content,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
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

}
