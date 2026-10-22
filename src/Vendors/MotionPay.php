<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

use Growinc\Payment\Helper;
class MotionPay extends Requestor implements VendorInterface
{
	use Helper;

	protected $form;

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args = null)
	{
		try {
			$this->request['time'] = time();
			$this->request['data'] = [
				'client_key' => $this->init->getMID(),
				'server_key' => $this->init->getSecret(),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/priv/v1/pg/token'
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			//
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (!empty($content->data->token)) {
					/* // Success
						{
							"data": {
								"token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJjbGllbnRfa2V5IjoiRk0tMDIyNC02MTZiZjU3OTQ1ZGU2MjNjIiwic2VydmVyX2tleSI6IkZNUEEyNTVkNjk2MGNkZjg0YTUyZDY2ODM1NTgxYjAwZTBhMTc1MWMiLCJleHAiOjE2ODAyNzY0NDN9.drPCoqfUdAxIuqqUGJuB8JvN5G0uSGHkl2yxAq1x2bU"
							},
							"meta": {},
							"status": {
								"code": 200,
								"message": "Token received."
							}
						}
					*/
					$res = $content->data;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed, token is empty', $content ?? 'Unknown error']), 901);
				}
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
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
			$this->request['time'] = $transaction->getTime();
			$payment_method = $transaction->getPaymentMethod();
			$payment_channel = $transaction->getPaymentChannel();
			//
			switch ($payment_method) {
				case 'qris':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/payment/api/v1/qris/payment'
					);
					$this->request['data'] = [
						'terminal_id' => $transaction->getMerchantID(),
						'external_id' => $transaction->getOrderID(),
						'amount' => (int) $transaction->getAmount(),
						'description' => $transaction->getDescription(),
						'session_time' => $transaction->getExpireIn(), // In minutes
						'fullname' => $transaction->getCustomerName(),
						'email' => $transaction->getCustomerEmail(),
						'phone_number' => $transaction->getCustomerPhone(),
					];
					$this->request['headers'] = [
						'Content-Type' => 'application/json',
						'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
					];
					$this->request['option'] = [
						'as_json' => 1,
					];
					$post = $this->DoRequest('POST', $this->request);
					$response = (array) $post['response'];
					extract($response);
					//
					if (!empty($content) && Helper::is_JSON($content)) {
						/* // Success // QRIS
							{
								"data": {
									"transaction_id": "168019025086587EC190044416B494D9FFEDA0AE7812A62",
									"external_id": "1680190250",
									"amount": 10000,
									"qr_string": "dDO38DCAyKLB316Dek4s7xzVTtsS2pSiO7RqYJ6jnpK6BiHngCdrA5mV6bYZbVJC4iWpwE6GyCnA3misAQrAtrt74eccwa2FDXPVfZLT5uu4JoK0UvUvz02VJsE9c8wDmdqtARG6BiCtCmsvnZ18iUWjQ3SM8EPU9i8hSg7AVw25cwKfjSEcHaKT644mULfNVh8xihjjc1EU2KNlTv2GwNNO1bHteDeWgTretjvIHCk5kNmyEnSTEZyG1Z8XBrys",
									"type": "QRIS_DYNAMIC",
									"status": "PROCESSING",
									"created_date": "2023-03-30 22:30:50",
									"email": null,
									"phone_number": null,
									"fullname": null,
									"session_time": 120
								},
								"meta": {},
								"status": {
									"code": 0,
									"message": "Transaction Successfully Created."
								}
							}
						*/
						$content = (object) json_decode($content);
						$res = [
							// 'status' => str_pad($content->status->code, 3, '0', STR_PAD_LEFT),
							'status' => $content->status->code ?? 999,
							'message' => $content->status->message ?? '',
							'data' => (array) $content->data,
						];
					} else {
						throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
					}
					break;
				case 'va':
					// Go
					$this->request['url'] = '';
					$this->request['data'] = $this->request['form'];
					$this->request['headers'] = [];
					$this->request['option'] = [];
					//
					$res = [
						'status' => '000',
						'data' => $this->request['data'],
					];
					break;
				default:
					throw new \Exception('Payment method not defined/unknown', 900);
					break;
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
			SELF::Validate($request, [
				'payment_method',
			]);
			$this->request['time'] = time();
			switch ($request->payment_method) {
				case 'qris':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						'/payment/api/v1/qris/payment-status/' . $request->transaction_id
					);
					$this->request['headers'] = [
						'Content-Type' => 'application/json',
						'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
					];
					$this->request['option'] = [
						'as_json' => 0,
					];
					//
					$get = $this->DoRequest('GET', $this->request);
					$response = (array) $get['response'];
					extract($response);
					//
					if (!empty($content) && Helper::is_JSON($content)) {
						/* // Success
							{
								"data": {
									"transaction_id": "1680190325997957D10E0F87D0C64CB0943A071AA06C1F49",
									"external_id": "1680190325",
									"amount": 10000,
									"qr_string": "R7FMgofJAmQVDnIGZ5DJ3PVBhawX4tDQZM8i8tSR7QVduQLiS5xYmKnUraIdvY8yyLrZVWIvEMrfNFzTi9vIVv0JKZPBqIDTA5GrHoiUJFnRYOXue3JD6LW7dhnjndwVAOqS8L5OtFvBvCNtByltsZGWuHmZ8rMgwaRjDuS8nbTZ96LoIoceScb2CQhjN4kH4nNas50MDFBy6VtvT9agtIqnSwUZqXMSVY6Dff1u0Wd5xrhQ5kswq0Kf4JPD3PM4",
									"type": "QRIS_DYNAMIC",
									"status": "SUCCESS",
									"created_date": "2023-03-30T22:32:06",
									"channel": "QRIS"
								},
								"meta": {},
								"status": {
									"code": 0,
									"message": "Transaction Found."
								}
							}
						*/
						$content = (object) json_decode($content);
						$res = [
							// 'status' => str_pad($content->status->code, 3, '0', STR_PAD_LEFT),
							'status' => $content->status->code ?? 999,
							'message' => $content->status->message ?? '',
							'data' => (array) $content->data,
						];
					} else {
						throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
					}
					break;
				default:
					throw new \Exception('Payment method not defined/unknown', 900);
					break;
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
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

	public function TestPayment(object $request)
	{
		try {
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v1/simulation/qris-dynamic'
			);
			$this->request['data'] = [
				'transaction_id' => $request->transaction_id,
				'set_success_callback_qris_dynamic' => $request->callback_url,
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
			];
			$this->request['option'] = [
				'as_json' => 1,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			//
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success
				*/
				$content = (object) json_decode($content);
				$res = [
					// 'status' => str_pad($content->status->code, 3, '0', STR_PAD_LEFT),
					'status' => $content->status->code ?? 999,
					'message' => $content->status->message ?? '',
					'data' => (array) $content->data,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}



	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\
	|
	| Disbursement
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\ ///////////////////////*/

	public function GetTokenDisbursement($args = null)
	{
		try {
			$this->request['time'] = time();
			$this->request['data'] = [
				'client_id' => $this->init->getMID(),
				'secret_key' => $this->init->getSecret(),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v2/auth'
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			//
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (!empty($content->data->token)) {
					/* // Success
						{
							"data": {
								"token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJjbGllbnRfa2V5IjoiUHNnWGFOVGxlSXhoajkzdVdMb2RLTnBSdUEwIiwic2VydmVyX2tleSI6InVkME93dklEODJDX2NkMXVud2RyNFdHYXFEayIsImV4cCI6MTY4MDM2MjAyNn0.fyoM-sD1jAKyzM3rtW2gYpZOir1ccFyEBGmrGHxaE3k"
							},
							"meta": {},
							"status": {
								"code": 200,
								"message": "Token Received."
							}
						}
					*/
					$res = $content->data;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed, token is empty', $content ?? 'Unknown error']), 901);
				}
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function BankList()
	{
		// Inapplicable
	}

	public function CheckBalance()
	{
		try {
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v2/balance/deposit'
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
			];
			$this->request['option'] = [
				'as_json' => 0,
			];
			//
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			//
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success
					{
						"data": {
							"disbursement_id": 251,
							"deposit": 500000000
						},
						"meta": null,
						"status": {
							"success": true,
							"code": "0001",
							"message": "Success"
						}
					}
				*/
				$content = (object) json_decode($content);
				$res = [
					// 'status' => str_pad($content->status->code, 3, '0', STR_PAD_LEFT),
					'status' => $content->status->code ?? 999,
					'message' => $content->status->message ?? '',
					'data' => (array) $content->data,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function BankAccountInquiry(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v2/inquiry'
			);
			$this->request['data'] = [
				'bank_code' => $transaction->getCustomerBankCode(),
				'bank_account' => $transaction->getCustomerBankAccountNumber(),
				'external_id' => $transaction->getOrderID(),
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
			];
			$this->request['option'] = [
				'as_json' => 1,
			];
			//
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			//
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success
					{
						"data": {
							"bank_code": "014",
							"bank_account": "1239812390",
							"name": "John Doe"
						},
						"meta": {},
						"status": {
							"success": true,
							"code": "0001",
							"message": "Success"
						}
					}
				*/
				$content = (object) json_decode($content);
				$res = [
					// 'status' => str_pad($content->status->code, 3, '0', STR_PAD_LEFT),
					'status' => $content->status->code ?? 999,
					'message' => $content->status->message ?? '',
					'data' => (array) $content->data,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function OnlineTransferExec(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v2/disbursement/fund-disbursement'
			);
			$this->request['data'] = [
				'recipient_bank' => $transaction->getCustomerBankCode(),
				'recipient_account' => $transaction->getCustomerBankAccountNumber(),
				'recipient_name' => $transaction->getCustomerName(),
				'amount' => (float) $transaction->getAmount(),
				'note' => $transaction->getDescription(),
				'external_id' => $transaction->getOrderID(),
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
			];
			$this->request['option'] = [
				'as_json' => 1,
			];
			//
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			//
			if (!empty($content) && Helper::is_JSON($content)) {
				// 0001 Success Transaction is successfully processed
				// 0002 In Progress Transfer request is currently in progress
				// 400 Bad Request Invalid parameter format
				// 401 Unauthorised Invalid token or expired token
				// 402 Access Forbidden Merchant is request is invalid
				// 422 Unprocessable Entity Invalid parameter entity format
				// 500 Internal Server Error Error in server
				// 999 General Error Undefined error
				/* // Success
					{
						"data": {
							"transaction_id": "A176E870F2F79621202229F5477191C9B74502C3537D0E7C",
							"external_id": "1680276102"
						},
						"meta": {},
						"status": {
							"success": true,
							"code": "0002",
							"message": "On Process"
						}
					}
				*/
				$content = (object) json_decode($content);
				$res = [
					// 'status' => str_pad($content->status->code, 3, '0', STR_PAD_LEFT),
					'status' => $content->status->code ?? 999,
					'message' => $content->status->message ?? '',
					'data' => (array) $content->data,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function CheckTransfer(object $request)
	{
		try {
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v2/transaction/id/' . $request->external_id
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
			];
			$this->request['option'] = [
				'as_json' => 0,
			];
			//
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			//
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success
					{
						"data": {
							"transaction_id": "A176E870F2F79621202229F5477191C9B74502C3537D0E7C",
							"external_id": "1680276102",
							"status": "IN_PROGRESS"
						},
						"meta": {},
						"status": {
							"success": true,
							"code": "0001",
							"message": "Success"
						}
					}
				*/
				$content = (object) json_decode($content);
				$res = [
					// 'status' => str_pad($content->status->code, 3, '0', STR_PAD_LEFT),
					'status' => $content->status->code ?? 999,
					'message' => $content->status->message ?? '',
					'data' => (array) $content->data,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

}
