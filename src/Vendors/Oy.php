<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

use Growinc\Payment\Helper;

class Oy extends Requestor implements VendorInterface
{
	use Helper;

	protected $form;
	protected $timeout = 15;

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
			$payment_method = $transaction->getPaymentMethod();
			$payment_channel = $transaction->getPaymentChannel();
			switch ($payment_method) {
				case 'qris':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/api/payment-routing/create-transaction'
					);
					$this->request['data'] = [
						'partner_user_id' => $transaction->getCustomerID(),
						'partner_trx_id' => $transaction->getOrderID(),
						'need_frontend' => 0,
						'list_enable_payment_method' => 'QRIS',
						'list_enable_sof' => 'QRIS',
						// 'sender_email' => $transaction->getCustomerEmail(),
						'sender_email' => $transaction->getMerchantEmail(),
						'receive_amount' => (float) $transaction->getAmount(),
						'va_display_name' => $transaction->getCustomerName(),
						'trx_expiration_time' => date('Y-m-d H:i:s', $transaction->getTime() + (60 * $transaction->getExpireIn())),
						'trx_counter' => '',
						'success_redirect_url' => $this->init->getCallbackURL(), // for EWALLET
						'failed_redirect_url' => $this->init->getReturnURL(), // for CARDS
						// 'payment_routing' => [
						// 	'recipient_bank' => '',
						// 	'recipient_account' => '',
						// 	'recipient_amount' => '',
						// 	'recipient_note' => '',
						// 	'recipient_email' => '',
						// ],
					];
					/*
						// Success // QRIS
						{
							"status": {
								"code": "000",
								"message": "Success"
							},
							"trx_id": "adbf625a-9ad0-4f6d-b820-ed5db81fcb96",
							"partner_trx_id": "0077950175",
							"receive_amount": 10000,
							"trx_expiration_time": "2023-03-05 01:16:15",
							"payment_info": {
								"qris_url": "https://i.snipboard.io/kYLlFf.jpg"
							}
						}
					*/
					break;
				case 'va':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/api/generate-static-va'
					);
					$this->request['data'] = [
						'partner_user_id' => $transaction->getCustomerID(),
						'bank_code' => strtoupper($payment_channel),
						'amount' => (float) $transaction->getAmount(),
						'is_open' => 0,
						'is_single_use' => 1,
						'expiration_time' => $transaction->getExpireIn(), // In minutes, def 120
						'is_lifetime' => 0,
						'username_display' => $transaction->getCustomerName(),
						'email' => $transaction->getCustomerEmail(),
						'trx_expiration_time' => $transaction->getExpireIn(), // date('Y-m-d H:i:s', $transaction->getTime() + (60 * $transaction->getExpireIn())),
						'partner_trx_id' => $transaction->getOrderID(),
						'trx_counter' => 1,
					];
					/*
						// Success // VA
						{
							"id": "dae76e43-8511-4169-8eaa-ab039aadc2b5",
							"amount": 10000.0000,
							"status": {
								"code": "000",
								"message": "Success"
							},
							"va_number": "8255600000009291",
							"bank_code": "009",
							"is_open": false,
							"is_single_use": true,
							"expiration_time": 1678091013000,
							"va_status": "WAITING_PAYMENT",
							"username_display": "Testing",
							"partner_user_id": "1678090412",
							"counter_incoming_payment": 0,
							"trx_expiration_time": 1678091013000,
							"partner_trx_id": "0078090412",
							"trx_counter": 1
						}
					*/
					break;
				case 'ewallet':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/api/e-wallet-aggregator/create-transaction'
					);
					$this->request['data'] = [
						'customer_id' => $transaction->getCustomerID(),
						// 'sub_merchant_id' => null,
						'partner_trx_id' => $transaction->getOrderID(),
						'amount' => (float) $transaction->getAmount(),
						'email' => $transaction->getCustomerEmail(),
						'ewallet_code' => strtolower($payment_channel), // ovo_ewallet // shopeepay_ewallet // linkaja_ewallet // dana_ewallet
						'mobile_number' => $transaction->getCustomerPhone(),
						'success_redirect_url' => $this->init->getCallbackURL(), // for EWALLET
						'expiration_time' => $transaction->getExpireIn(), // e-wallet max 1 min // In minutes, def 120
					];
					/*
						// Success // E-wallet
						{
							"status": {
								"code": "000",
								"message": "Success"
							},
							"ewallet_trx_status": "WAITING_PAYMENT",
							"trx_id": "07bcf2de-b33f-45f8-84ad-9233b4e768a5",
							"ref_number": "2303CSL90597",
							"customer_id": "085776554250",
							"partner_trx_id": "0078090726",
							"amount": 10000,
							"ewallet_code": "shopeepay_ewallet",
							"ewallet_url": "https://api-stg.oyindonesia.com/"
						}
					*/
					break;
				default:
					throw new \Exception('Undefined payment method', 800);
					break;
			}
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-Oy-Username' => $this->init->getMID(),
				'X-Api-Key' => $this->init->getSecret(),
			];
			$this->request['option'] = [
				'as_json' => true,
				'timeout' => $this->timeout,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				$status = $content->status;
				//
				$content = (array) $content;
				unset($content['status']);
				$res = [
					'status' => $status->code,
					'message' => $status->message,
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
		/*
			// Example incoming data // VA
			// Example incoming data // QRIS
		*/
		try {
			if ($request->success == 'true') {
				$res = [
					'status' => '000',
					'data' => (array) $request->data_payment,
				];
			} else {
				throw new \Exception('Callback failed', 902);
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
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-Oy-Username' => $this->init->getMID(),
				'X-Api-Key' => $this->init->getSecret(),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/payment-routing/check-status'
			);
			$this->request['data'] = [
				'partner_trx_id' => $request->trx_id,
				'send_callback' => false,
			];
			$this->request['option'] = [
				'as_json' => true,
				'timeout' => $this->timeout,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/*
					{
						"status": {
							"code": "000",
							"message": "Success"
						},
						"trx_id": "adbf625a-9ad0-4f6d-b820-ed5db81fcb96",
						"partner_trx_id": "0077950175",
						"request_amount": 10000,
						"received_amount": 0,
						"payment_status": "WAITING_PAYMENT",
						"trx_expiration_time": "2023-03-05 01:16:15",
						"need_frontend": false,
						"payment_info": {
							"qris_url": "https://i.snipboard.io/kYLlFf.jpg"
						},
						"payment_routing": []
					}
				*/
				$content = (object) json_decode($content);
				$status = $content->status;
				//
				$content = (array) $content;
				unset($content['status']);
				$res = [
					'status' => $status->code,
					'message' => $status->message,
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
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-Oy-Username' => $this->init->getMID(),
				'X-Api-Key' => $this->init->getSecret(),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/balance'
			);
			$this->request['option'] = [
				'as_json' => false,
				'timeout' => $this->timeout,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/*
					{
						"status": {
							"code": "000",
							"message": "Success"
						},
						"balance": 9703595.0000,
						"overdraftBalance": 0,
						"overbookingBalance": 0,
						"pendingBalance": 0.0000,
						"availableBalance": 9703595.0000,
						"timeStamp": "07-03-2023 06:54:52"
					}
				*/
				$content = (object) json_decode($content);
				$status = $content->status;
				//
				$content = (array) $content;
				unset($content['status']);
				$res = [
					'status' => $status->code,
					'message' => $status->message,
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

	public function BankAccountInquiry(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-Oy-Username' => $this->init->getMID(),
				'X-Api-Key' => $this->init->getSecret(),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/account-inquiry'
			);
			$this->request['data'] = [
				'bank_code' => $transaction->getCustomerBankCode(),
				'account_number' => $transaction->getCustomerBankAccountNumber(),
			];
			$this->request['option'] = [
				'as_json' => true,
				'timeout' => $this->timeout,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/*
					{
						"status": {
							"code": "000",
							"message": "Success"
						},
						"bank_code": "011",
						"account_number": "7700173383",
						"account_name": "Cristopher Cronin",
						"timestamp": "2023-03-06T11:22:26",
						"id": "0d35e3bd-0e11-4f26-b5ba-95e09f4d9907",
						"invoice_id": "e56c328e-5cba-4352-bbe7-a27fa92a3829"
					}
				*/
				$content = (object) json_decode($content);
				$status = $content->status;
				//
				$content = (array) $content;
				unset($content['status']);
				$res = [
					'status' => $status->code,
					'message' => $status->message,
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

	public function OnlineTransferExec(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-Oy-Username' => $this->init->getMID(),
				'X-Api-Key' => $this->init->getSecret(),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/remit'
			);
			$this->request['data'] = [
				'recipient_bank' => $transaction->getCustomerBankCode(),
				'recipient_account' => $transaction->getCustomerBankAccountNumber(),
				'amount' => (float) $transaction->getAmount(),
				'note' => $transaction->getDescription(),
				'partner_trx_id' => $transaction->getOrderID(),
				'email' => $transaction->getCustomerEmail(),
				'child_balance' => null,
			];
			$this->request['option'] = [
				'as_json' => true,
				'timeout' => $this->timeout,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/*
					000	Final	Transaction has been completed (success). Note: only happens for overbooking flow.
					101	Non-Final	Transaction is Processed
					102	Non-Final	Transaction is In Progress
					206	Final	Transaction is FAILED (Partner Deposit Balance is Not Enough)
					225	Final	Transaction is FAILED (Transaction amount exceeds the maximum limit)
					300	Final	Transaction is FAILED
					301	Non-Final	Pending (When there is an unclear answer from Banks Network)
					//
					{
						"status": {
							"code": "101",
							"message": "Request is Processed"
						},
						"amount": 10500,
						"timestamp": "06-03-2023 11:25:59",
						"recipient_bank": "011",
						"recipient_account": "7700173383",
						"trx_id": "26de30ce-0ead-4626-8799-a179b5e1a58a",
						"partner_trx_id": "1678101958"
					}
				*/
				$content = (object) json_decode($content);
				$status = $content->status;
				//
				$content = (array) $content;
				unset($content['status']);
				$res = [
					'status' => $status->code,
					'message' => $status->message,
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
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-Oy-Username' => $this->init->getMID(),
				'X-Api-Key' => $this->init->getSecret(),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/remit-status'
			);
			$this->request['data'] = [
				'partner_trx_id' => $request->reference_id,
				'send_callback' => $request->send_callback,
			];
			$this->request['option'] = [
				'as_json' => true,
				'timeout' => $this->timeout,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/*
					{
						"status": {
							"code": "000",
							"message": "Success"
						},
						"amount": 10500.0000,
						"timestamp": "06-03-2023 11:44:39",
						"tx_status_description": null,
						"recipient_name": "Golden Kunze",
						"recipient_bank": "011",
						"recipient_account": "7700173383",
						"trx_id": "26de30ce-0ead-4626-8799-a179b5e1a58a",
						"partner_trx_id": "1678101958",
						"created_date": "06-03-2023 11:25:59",
						"last_updated_date": "06-03-2023 11:25:59"
					}
				*/
				$content = (object) json_decode($content);
				$status = $content->status;
				//
				$content = (array) $content;
				unset($content['status']);
				$res = [
					'status' => $status->code,
					'message' => $status->message,
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

}
