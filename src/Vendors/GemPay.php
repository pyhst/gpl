<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

use Growinc\Payment\Helper;

class GemPay extends Requestor implements VendorInterface
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
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v1/direct'
			);
			$signature = md5(
				$transaction->getReferenceNumber() .
				(int) $transaction->getAmount() .
				$this->init->getMID() .
				$transaction->getPaymentChannel() .
				$this->init->getSecret() .
				$this->init->getParam('GEMPAY_PROJECT_NO')
			);
			$this->request['data'] = [
				'merchant_id' => $this->init->getMID(),
				'project_no' => $this->init->getParam('GEMPAY_PROJECT_NO'),
				'request_id' => $transaction->getReferenceNumber(),
				'amount' => (int) $transaction->getAmount(),
				'signature' => $signature,
				'channel' => $transaction->getPaymentChannel(),
				'description' => $transaction->getDescription(),
				'callback_url' => $this->init->getCallbackURL(),
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
			];
			$this->request['option'] = [
				// 'as_json' => true,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->status)
					&& trim(strtolower($content->status)) == true
				) {
					/*
						// Success // VA
							{
								"status": true,
								"error_code": "P00",
								"error_desc": "Data format is good",
								"ref_id": "20240423154336-662774B805BC7",
								"request_id": "1713861815",
								"channel": "cimb_va",
								"amount": "15000",
								"admin_fee": "4900",
								"total_amount": "19900",
								"virtual_account": "9999900000000003",
								"qrcode": null
							}
						// Success // QRIS
							{
								"status": true,
								"error_code": "P00",
								"error_desc": "Data format is good",
								"ref_id": "20240423162310-66277DFE23CDA",
								"request_id": "1713864189",
								"channel": "MBayar_QR",
								"amount": "15000",
								"admin_fee": "117",
								"total_amount": "15117",
								"virtual_account": null,
								"qrcode": "https:\/\/sandbox-api.gempay.online\/v1\/qrcode?UzRNUDFsRWRUTldXTVhjbmVZdU1qWSs0blhrYyswSzRKMU5HK1p6ZU9aMD0="
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
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v1/history'
			);
			$signature = md5(
				$this->init->getMID() .
				$this->init->getSecret() .
				$this->init->getParam('GEMPAY_PROJECT_NO')
			);
			$this->request['data'] = [
				'merchant_id' => $this->init->getMID(),
				'project_no' => $this->init->getParam('GEMPAY_PROJECT_NO'),
				'start' => $request->start,
				'length' => $request->length,
				'ref_id' => $request->ref_id,
				'order' => $request->order,
				'signature' => $signature,
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
			];
			$this->request['option'] = [
				// 'as_json' => true,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			$response = (array) $get['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->status)
					&& trim(strtolower($content->status)) == true
				) {
					/* // Success inquiry QRIS
						{
							"status": true,
							"error_code": "00",
							"error_desc": "Data found",
							"total_rows": 1,
							"data": [
								{
										"transaction_id": "9052",
										"channel": "Qris M-Bayar",
										"amount": "15000",
										"status": "Success",
										"ref_id": "20240423162310-66277DFE23CDA",
										"order_datetime": "2024-04-23 16:23:10",
										"payment_datetime": "2024-04-23 16:24:11",
										"request_id": "1713864189",
										"merchant_id": "KMB0001",
										"project_no": "UJK87AA",
										"admin_fee": "117",
										"total_amount": "15117"
								}
							]
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content->data,
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
		$time = time();
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/balance_query'
			);
			$signature = md5(
				'CB' . $time .
				$this->init->getMID() .
				$this->init->getSecret() .
				'balance_query'
			);
			$this->request['data'] = [
				'merchant_id' => $this->init->getMID(),
				'request_id' => 'CB' . $time,
				'signature' => $signature,
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
			];
			$this->request['option'] = [
				// 'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->result)
					&& trim(strtolower($content->result)) == true
				) {
					/* // Success check balance
						{
							"result": true,
							"error_code": "000",
							"error_desc": "Data found",
							"data": {
								"merchant_id": "KMB0001",
								"active_balance": "1079400",
								"pending_balance": "800000"
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => $content->data,
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
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/inquiry'
			);
			$signature = md5(
				'BAI' . $time .
				$this->init->getMID() .
				$this->init->getSecret() .
				'inquiry'
			);
			$this->request['data'] = [
				'merchant_id' => $this->init->getMID(),
				'project_no' => $this->init->getParam('GEMPAY_PROJECT_NO'),
				'request_id' => 'BAI' . $time,
				'amount' => $transaction->getAmount(),
				'remit_type' => $transaction->getTransferMethod(),
				'partner_ref_id' => $transaction->getReferenceNumber(),
				'account_no' => $transaction->getCustomerBankAccountNumber(),
				'bank_code' => $transaction->getCustomerBankCode(),
				'transaction_datetime' => $transaction->getReqDateTime(),
				'signature' => $signature,
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
			];
			$this->request['option'] = [
				// 'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->result)
					&& trim(strtolower($content->result)) == true
				) {
					/* // Success bank account inquiry
						{
							"result": true,
							"error_code": "000",
							"error_desc": "Data Found",
							"data": {
								"partner_ref_id": "1716270784",
								"inquiry_id": "24052112530718Y6",
								"bank_code": "011",
								"account_name": "7700173383",
								"account_no": "7700173383",
								"account_bank": "Bank Danamon & Danamon Syariah",
								"amount": 10000,
								"admin_fee": 2000,
								"total_amount": 12000,
								"transaction_datetime": "2024-05-21 12:53:04",
								"merchant_id": "KMB0001",
								"project_no": "UJK87AA"
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => $content->data,
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
			$time = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/transfer'
			);
			$signature = md5(
				'OTE' . $time .
				$this->init->getMID() .
				$this->init->getSecret() .
				'transfer'
			);
			$this->request['data'] = [
				'merchant_id' => $this->init->getMID(),
				'project_no' => $this->init->getParam('GEMPAY_PROJECT_NO'),
				'request_id' => 'OTE' . $time,
				'inquiry_id' => $transaction->getOrderID(),
				'description' => $transaction->getDescription(),
				'transaction_datetime' => $transaction->getReqDateTime(),
				'signature' => $signature,
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
			];
			$this->request['option'] = [
				// 'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->result)
					&& trim(strtolower($content->result)) == true
				) {
					/* // Success disb
						{
							"result": true,
							"error_code": "000",
							"error_desc": "Disburse Successful",
							"data": {
								"partner_ref_id": "1716271492",
								"bank_code": "011",
								"account_no": "7700173383",
								"account_bank": "Bank Danamon & Danamon Syariah",
								"amount": 10000,
								"admin_fee": 2000,
								"total_amount": 12000,
								"transaction_datetime": "2024-05-21 13:05:04",
								"merchant_id": "KMB0001",
								"project_no": "UJK87AA",
								"ref_id": "240521130507N64P",
								"status": "success"
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => $content->data,
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
			$time = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/status_query'
			);
			$signature = md5(
				'CT' . $time .
				$this->init->getMID() .
				$this->init->getSecret() .
				'status_query'
			);
			$this->request['data'] = [
				'merchant_id' => $this->init->getMID(),
				'request_id' => 'CT' . $time,
				'partner_ref_id' => $request->partner_ref_id,
				'signature' => $signature,
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
			];
			$this->request['option'] = [
				// 'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->result)
					&& trim(strtolower($content->result)) == true
				) {
					/* // Success check status
						{
							"result": true,
							"error_code": "000",
							"error_desc": "Data found",
							"data": {
								"transaction_datetime": "2024-05-21 13:05:04",
								"account_no": "7700173383",
								"account_bank": "Bank Danamon & Danamon Syariah",
								"amount": "10000",
								"description": "TEST-TRANSFER",
								"merchant_id": "KMB0001",
								"status": "Success",
								"admin_fee": "2000",
								"partner_ref_id": "1716271492",
								"ref_id": "240521130507N64P"
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => $content->data,
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
