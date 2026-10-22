<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Init;
use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;

class Nusapay extends Requestor implements VendorInterface
{

	protected $form;

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args = [])
	{
		try {
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
			];
			$this->request['url'] = SELF::CleanURL(
				$args['request_url'] .
				'/auth/token'
			);
			$this->request['data'] = [
				'grant_type' => 'client_credentials',
				'client_id' => $args['client_id'],
				'client_secret' => $args['client_secret'],
				'scope' => 'pm_create pm_cancel pm_payment_push pm_payment_refund',
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
					!empty($content->access_token)
				) {
					/* // Success
						{
							"token_type": "Bearer",
							"expires_in": 604800,
							"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjI2NzAxNTcwZGE2MzZlZGJjNTU0YTg1NTBmMDFhMDFlNGJhODAxNmE0OTg3NDU5ZmRhMDQ3M2IzOTdmZjFiZTE3N2ViMzc3MGEwZWUwNWFkIn0.eyJhdWQiOiI1MSIsImp0aSI6IjI2NzAxNTcwZGE2MzZlZGJjNTU0YTg1NTBmMDFhMDFlNGJhODAxNmE0OTg3NDU5ZmRhMDQ3M2IzOTdmZjFiZTE3N2ViMzc3MGEwZWUwNWFkIiwiaWF0IjoxNjU4NzI5NTMxLCJuYmYiOjE2NTg3Mjk1MzEsImV4cCI6MTY1OTMzNDMzMSwic3ViIjoiIiwic2NvcGVzIjpbInBtX2NyZWF0ZSIsInBtX2NhbmNlbCIsInBtX3BheW1lbnRfcHVzaCIsInBtX3BheW1lbnRfcmVmdW5kIl19.rU_NA83-q7LYUqTs2mnuoZK7aYD_LYo3OUejGcG-_R9Xd_pHBCHpbRHa-K0A5vDRxKVdZ5TOjnYp8JurkOROBbNKCzSKRXQGwpBgvOE1HMQae9_BsAF5Gt2COHa9UCL9oUEf8lT_hSOT6eck1A-_p5poaN5QDli1-ojELvVtqkyp7VUU7iqLInOFDb7NVytnPKAzW9g7JkJ1bCm02MHbv-5dtPfdzXw-PdCR3_2ZTuXGXHhjAdnBkoqfjmRhL5bufn_pDfEi8BvVeyMVGyCwT4XyWHDRWm_BYqLjiwNLhp_pou68KzHbxwhx5XLPaSU_kQpyO_jGQQSEBokB4nUYsflcNOf7g4eDPBEBlNhkpJK7AdJQbl68UrKQwO4ec6GWRjtvnErOsPRdLpzDgS26MqDkxkmS4U1lpZMtTS21rCoAtpsYxDVkMnnyINhvPWYSwf7iuaJx9DaMcPlTXz74bCseUjEyzQl8T2-ez9q4Pz5P-_x_LDCZyA49TzDJlT82wnB54qXJvs9-SoGoLDoxFS_WROErDbLPu3QFemdBEsHGy9pZyblNOMSdpB0CcBXojbkLJ5CzVmsrqDw8N_f__lnTw4Ik-Bk18myP4o6MMkFBI038py4b8FbS8gE6qQN_51nazEkHB1U1zjaBXjgBpdlWCY2jal4VQHl1mE-_xVw"
						}
					*/
					$res = (string) $content->access_token;
				} else {
					throw new \Exception(__FUNCTION__ . '() failed (' . ($content->status ?? 'Unknown status') . ')', 901);
				}
			} else {
				throw new \Exception(__FUNCTION__ . '() failed (' . ($content ?? 'Unknown error') . ')', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return $res ?? [];
	}

		public function GetCustomerNP($args = [])
		{
			try {
				if (empty($args['token'])) {
					// Get Token
					$args['token'] = $this->GetToken([
						'client_id' => $this->init->getMID(),
						'client_secret' => $this->init->getSecret(),
						'request_url' => $this->init->getRequestURL(),
					]);
					if (empty($args['token'])) { throw new \Exception('GetToken failed', 800); }
					//
					$args['request_url'] = $this->init->getRequestURL();
				}
				$this->request['headers'] = [
					'Content-Type' => 'application/json',
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . $args['token'],
				];
				$this->request['url'] = SELF::CleanURL(
					$args['request_url'] .
					'/api/members?type=1&q=' . $args['search']
				);
				$this->request['data'] = [];
				$get = $this->DoRequest('GET', $this->request);
				$response = (array) $get['response'];
				extract($response);
				if (
					!empty($status_code)
					&& $status_code === 200
				) {
					// Parse data
					$content = (object) json_decode($content);
					if (
						!empty($content->data[0]->id)
					) {
						/* // Success
							{
								"success": true,
								"response_code": 200,
								"data": [{
									"id": 100,
									"name": "XXX",
									"phone": "081212121314",
									"phonecode": 62,
									"avatar": "https:\/\/media.um0phbmlhwn0qyay58pf.click\/image\/\/XXX.jpg",
									"is_active": true
								}]
							}
						*/
						// $res = (string) $content->data[0]->id;
						$res = [
							'status' => '000',
							'data' => (array) $content->data[0],
						];
					} else {
						// throw new \Exception(__FUNCTION__ . '() failed (' . ($content->status ?? 'Unknown status') . ')', 901);
						$res = [
							'status' => '900',
							'data' => null,
						];
					}
				} else {
					throw new \Exception(__FUNCTION__ . '() failed (' . ($content ?? 'Unknown error') . ')', 902);
				}
			} catch (\Throwable $e) {
				$this->ThrowErrorException($e);
			}
			return $res ?? [];
		}

	public function CreateDummyForm($args)
	{
		// Inapplicable
	}

	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////
	|
	| Accept Payment
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\*/

	public function RedirectPayment(\PyGPaymentLib\Transaction $transaction)
	{
		// Inapplicable
	}

	public function SecurePayment(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$payment_method = strtolower(trim($transaction->getPaymentMethod()));
			switch ($payment_method) {
				case 'va': /*--------------------------------------  Virtual Account  --------------------------------------*/
					$this->request['data'] = [
						'client_id' => $this->init->getClientID(),
						'va_number' => $transaction->getVirtualAccountNumber(),
						'bank' => $transaction->getPaymentBank(),
						'trx_id' => $transaction->getTransactionID(),
						'refnum' => $transaction->getReferenceNumber(),
						'trx_amount' => (float) $transaction->getAmount(),
						'customer_id' => $transaction->getCustomerUserID(),
						'customer_name' => $transaction->getCustomerName(),
						'customer_email' => $transaction->getCustomerEmail(),
						'customer_phone' => $transaction->getCustomerPhone(),
						'expired_at' => $transaction->getExpireAt(),
					];
					//
					switch (strtolower(trim($transaction->getPaymentBank()))) {
						case 'bni':
							$this->request['data'] += [
								'description' => $transaction->getDescription(),
							];
							break;
						default:
							$this->request['data'] += [
								'detail' => $transaction->getDescription(),
							];
							break;
					}
					//
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/' . $transaction->getPaymentBank() . '/create_'
					);
					$this->request['headers'] = [[
						'Content-Type' => 'application/json',
					]];
					$this->request['option'] = [
						'as_json' => true,
					];
					break;
				case 'emoney': /*--------------------------------------  Nusapay E-Money  --------------------------------------*/
					// Get Token
					$token = $this->GetToken([
						'client_id' => $this->init->getMID(),
						'client_secret' => $this->init->getSecret(),
						'request_url' => $this->init->getRequestURL(),
					]);
					if (empty($token)) { throw new \Exception('GetToken failed', 800); }
					// Get Customer ID
					$customer = $this->GetCustomerNP([
						'request_url' => $this->init->getRequestURL(),
						'token' => $token,
						'search' => $transaction->getCustomerPhone(),
					]);
					if ($customer['status'] !== '000') {
						throw new \Exception('GetCustomer failed', 800);
					}
					$customer = $customer['data']['id'];
					if (empty($customer)) {
						throw new \Exception('GetCustomer ID failed', 800);
					}
					//
					$this->request['data'] = [
						'description' => $transaction->getDescription(),
						'invoice' => $transaction->getInvoiceNo(),
						'amount' => (float) $transaction->getAmount(),
						'expires' => $transaction->getExpireAt(),
						'merchant' => $transaction->getMerchantID(),
						'user' => $customer,
						'currency' => 'IDR',
						'callback' => $this->init->getCallbackURL(),
						'wallet' => 'local',
					];
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() . '/api/bill'
					);
					$this->request['headers'] = [
						'Content-Type' => 'application/json',
						'Authorization' => 'Bearer ' . $token,
					];
					$this->request['option'] = [
						'as_json' => true,
					];
					break;
				case 'cart': /*--------------------------------------  Nusapay Cart  --------------------------------------*/
					// Get Token
					$token = $this->GetToken([
						'client_id' => $this->init->getMID(),
						'client_secret' => $this->init->getSecret(),
						'request_url' => $this->init->getRequestURL(),
					]);
					if (empty($token)) { throw new \Exception('GetToken failed', 800); }
					//
					$this->request['data'] = [
						'merchant' => $transaction->getMerchantID(),
						'amount' => (float) $transaction->getAmount(),
						'phone' => $transaction->getCustomerPhone(),
						'description' => $transaction->getDescription(),
						'note' => $transaction->getRemark(),
						'callback_url' => $this->init->getCallbackURL(),
						'reffnum' => $transaction->getReferenceNumber(),
					];
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() . '/api/v1/cart/webpay'
					);
					$this->request['headers'] = [
						'Content-Type' => 'application/json',
						'Authorization' => 'Bearer ' . $token,
						'X-Nusapay-ID' => $this->init->getParams()['NUSAPAY_X_ID'],
						'X-Auth' => $this->init->getParams()['NUSAPAY_X_AUTH'],
					];
					$this->request['option'] = [
						'as_json' => true,
					];
					break;
				default:
					throw new \Exception('Payment method not defined/unknown', 900);
					break;
			}
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
					switch ($payment_method) {
						case 'va':
							/* // Success
								{
									"status": "000",
									"data": {
										"bank": "MANDIRI",
										"trx_id": "1658215149",
										"refnum": "220719141909",
										"va_number": "89811220719141909",
										"va_type": "C",
										"client_id": "G-PAY.TO",
										"customer_id": "TESTING",
										"customer_name": "TESTING",
										"customer_email": "testing@gmail.com",
										"customer_phone": "088812131415",
										"amount": 626100,
										"fee": 0,
										"paid_amount": 626100,
										"cumulative_amount": 0,
										"currency": "IDR",
										"status": 0,
										"created_at": "2022-07-19 14:19:08"
									}
								}
							*/
							$res = [
								'status' => '000',
								'data' => (array) $content->data,
							];
							break;
						case 'emoney':
							/* // Success
								{
									"success": true,
									"response_code": 200,
									"message": "OK",
									"data": {
										"id": 34500,
										"user": {
											"id": 52,
											"phone": "081212121314",
											"avatar": "https:\/\/media.um0phbmlhwn0qyay58pf.click\/image\/\/nSRuoGJbxCA7YIo6pxVfNgjXM6Os1t.jpg",
											"status": {
												"label": "Unverified",
												"value": 2
											},
											"timezone": "Asia\/Jakarta",
											"updated": "2022-07-25T08:49:59+00:00",
											"created": "2018-03-22T12:40:53+00:00",
											"nickname": "xn",
											"name": "XN"
										},
										"merchant": {
											"id": 67,
											"name": "GII Exchange",
											"logo": "https:\/\/media.um0phbmlhwn0qyay58pf.click\/image\/\/BjQKaZbRapic087RuUsKaulEaTyE4I.png",
											"cover": null
										},
										"status": {
											"label": "Pending",
											"value": 1
										},
										"description": "TESTING-220727110443",
										"invoice": "INV58894683",
										"currency": "IDR",
										"amount": 9773.0,
										"paid": 0.0,
										"cashback": 0.0,
										"wallet": "local",
										"reason": null,
										"pushed": null,
										"refund": null,
										"expires": "2022-07-27T06:04:46+00:00",
										"updated": "2022-07-27T04:04:46+00:00",
										"created": "2022-07-27T04:04:46+00:00"
									},
									"errors": null
								}
							*/
							$res = [
								'status' => ($content->success == 'true' ? '000' : '001'),
								'data' => (array) $content->data,
							];
							break;
						case 'cart':
							/* // Success
								{
									"success": true,
									"response_code": 200,
									"data": {
										"orde_id": "PO000000003660",
										"order_id": "PO000000003660",
										"reffnum": "220728170122"
									},
									"message": "Success",
									"load_time": 0.23206090927124023
								}
							*/
							/*
								// Failed
								{
									"success": false,
									"response_code": 422,
									"message": "Phone not registered",
									"load_time": 0.10190892219543457
								}
							*/
							if ($content->success == 'true') {
								$res = [
									'status' => '000',
									'data' => (!empty($content->data) ? (array) $content->data : ''),
								];
							} elseif ($content->success == 'false' || empty($content->success)) {
								$res = [
									'status' => '001',
									'data' => $content->message . ' (' . $content->response_code . ')',
								];
							}
							break;
					}
				} else {
					throw new \Exception('Parse failed (' . ($content->status ?? 'Unknown status') . ')', 901);
				}
			} else {
				throw new \Exception('Parse failed (' . ($content ?? 'Unknown error') . ')', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code);
	}

	public function Callback(object $request)
	{
		// Inapplicable
	}

	public function CallbackAlt(object $request)
	{
		// Inapplicable
	}

	public function Inquiry(object $request)
	{
		try {
			SELF::Validate($request, [
				'bank',
			]);
			$this->request['data'] = [
				'va_number' => $request->va_number ?? '',
				'trx_id' => $request->trx_id ?? '',
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/' . $request->bank . '/check_'
			);
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
			]];
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
							"status": "000",
							"data": {
								"bank": "MANDIRI",
								"trx_id": "1658215149",
								"refnum": "220719141909",
								"va_number": "89811220719141909",
								"va_type": "C",
								"client_id": "G-PAY.TO",
								"customer_id": "TESTING",
								"customer_name": "TESTING",
								"customer_email": "testing@gmail.com",
								"customer_phone": "088812131415",
								"amount": 626100,
								"fee": 0,
								"paid_amount": 626100,
								"cumulative_amount": 0,
								"currency": "IDR",
								"detail": "\"TESTING 220719141909\"",
								"log": {
									"log_20220719_141908_675600": {
										"name": "create",
										"value": {
											"created_at": "2022-07-19 14:19:08",
											"status": 0,
											"ip": "10.10.99.119"
										}
									}
								},
								"status": 0,
								"created_at": "2022-07-19 14:19:08"
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content->data,
					];
				} else {
					throw new \Exception('Parse failed (' . ($content->status ?? 'Unknown status') . ')', 901);
				}
			} else {
				throw new \Exception('Parse failed (' . ($content ?? 'Unknown error') . ')', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function Cancel(object $request)
	{
		try {
			SELF::Validate($request, [
				'bank',
				'va_number',
			]);
			$this->request['data'] = [
				'va_number' => $request->va_number,
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/' . $request->bank . '/cancel_'
			);
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
			]];
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
							"status": "000",
							"message": "Bill is cancelled 89811220719143636"
						}
					*/
					$res = [
						'status' => '000',
						'message' => $content->message,
					];
				} else {
					throw new \Exception('Parse failed (' . ($content->status ?? 'Unknown status') . ')', 901);
				}
			} else {
				throw new \Exception('Parse failed (' . ($content ?? 'Unknown error') . ')', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
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



	/*==========================================================================================
												BEGIN OF Disbursement
	==========================================================================================**/

	public function BankList()
	{
		try {
			$this->request['data'] = [];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'permata' . // $this->init->getParams()['bank'] . // Set to only use Permata
				'/get_bank_list'
			);
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
			]];
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
					!empty($content->data)
				) {
					$res = [
						'status' => '000',
						'data' => array_map(function($item) {
							return [
								'name' => strtoupper($item->name),
								'code' => $item->code,
							];
						}, $content->data),
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

	public function CheckBalance()
	{
		try {
			$this->request['data'] = [
				'accnum' => $this->init->getParams()['accnum'],
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				$this->init->getParams()['bank'] .
				'/get_saldo'

			);
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($this->request['data'])),
			]];
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
					!empty($content->data)
				) {
					/*
						{
							"account_number": "860008882900",
							"balance": 205469148.33
						}
					*/
					$res = [
						'status' => '000',
						'data' => [
							'account_number' => $content->data->account_number,
							'balance' => $content->data->balance,
						]
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

	public function InterbankTransferInquiry(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->request['data'] = [
				'to_accnum' => $transaction->getCustomerBankAccountNumber(),
				'to_bank_code' => $transaction->getCustomerBankName(),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'permata' . // $this->init->getParams()['bank'] . // Set to only use Permata
				'/chk_int_rekening'
			);
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
			]];
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
							"status": "000",
							"data": {
								"dest_account_number": "9639081212121314",
								"dest_account_name": "PAY XN",
								"dest_bank_name": "BANK CIMB NIAGA",
								"dest_bank_code": "220026"
							}
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

	public function InHouseTransfer(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->request['data'] = [
				'amount' => (int) $transaction->getAmount(),
				'from_accnum' => $this->init->getParams()['accnum'],
				'to_accnum' => $transaction->getCustomerBankAccountNumber(),
				'note' => $transaction->getDescription(),
				'refnum' => $transaction->getOrderID(),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				$this->init->getParams()['bank'] .
				'/do_transfer'
			);
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
			]];
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
							"status": "000",
							"data": {
								"trans_id": "1664552912",
								"trans_date": "20220930",
								"reference_id": "1664552912",
								"details": {
									"from_accnum": "860008882900",
									"to_accnum": "9639081212121314",
									"to_name": "9639081212121314",
									"to_bank": "CIMB",
									"to_bank_code": "022",
									"currency": "IDR",
									"amount": "1000",
									"note": "Test Transfer to VA"
								}
							}
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

	public function CheckTransfer(\PyGPaymentLib\Transaction $transaction)
	{
		// try {
		// 	$this->request['data'] = [
		// 		'refnum' => $transaction->getReferenceNumber(),
		// 		'accnum' => $this->init->getParams()['accnum'],
		// 		'tx_date' => $transaction->getReqDateTime(),
		// 		'channel_code' => $transaction->getPaymentChannel(),
		// 	];
		// 	$this->request['url'] = SELF::CleanURL(
		// 		$this->init->getRequestURL() .
		// 		$this->init->getParams()['bank'] .
		// 		'/chk_transfer'
		// 	);
		// 	$this->request['headers'] = [[
		// 		'Content-Type' => 'application/json',
		// 	]];
		// 	$this->request['option'] = [
		// 		'as_json' => true,
		// 	];
		// 	$post = $this->DoRequest('POST', $this->request);
		// 	$response = (array) $post['response'];
		// 	extract($response);
		// 	if (
		// 		!empty($status_code)
		// 		&& $status_code === 200
		// 	) {
		// 		// Parse data
		// 		$content = (object) json_decode($content);
		// 		if (
		// 			!empty($content)
		// 		) {
		// 			/* // Success
		// 			*/
		// 			$res = [
		// 				'status' => '000',
		// 				'data' => (array) $content,
		// 			];
		// 		} else {
		// 			throw new \Exception('Data is empty', 901);
		// 		}
		// 	} else {
		// 		throw new \Exception('Status code is not 200', 902);
		// 	}
		// } catch (\Throwable $e) {
		// 	$this->ThrowErrorException($e);
		// }
		// return SELF::JSONResult($this->request, $res, $status_code);
	}

	/*=================================   END OF Disbursement   ==================================*/

}
