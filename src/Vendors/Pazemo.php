<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;

class Pazemo extends Requestor implements VendorInterface
{

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
	 * Account
	 *
	 */

	public function Accounts(object $request)
	{
		try {
			$this->request['url'] = SELF::CleanURL($this->init->getRequestURL() .
				'/accounts/' . ($request->account_id ?? '')
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
				'Authorization' => 'Bearer ' . $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
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
					!empty($content)
				) {
					/* // Single
						{
							"id": "186992eb-dc10-475a-ae72-fc442a0a15ff",
							"createdTime": "2022-05-22T07:09:36.920Z",
							"updatedTime": "2022-05-22T07:09:36.920Z",
							"userId": "bc9e97c3-e022-49f4-89f9-d627ed153867",
							"currencyId": "IDR",
							"number": "112712880220",
							"balance": 100000000,
							"locked": 0
						}
					*/

					/* // Multiple
						[{
							"id": "186992eb-dc10-475a-ae72-fc442a0a15ff",
							"createdTime": "2022-05-22T07:09:36.920Z",
							"updatedTime": "2022-05-22T07:09:36.920Z",
							"userId": "bc9e97c3-e022-49f4-89f9-d627ed153867",
							"currencyId": "IDR",
							"number": "112712880220",
							"user": {
								"id": "bc9e97c3-e022-49f4-89f9-d627ed153867",
								"email": "support@sikoma.games",
								"name": "Sikoma Games"
							},
							"balance": 100000000,
							"locked": 0
						}]
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

	public function Transactions(object $request)
	{
		try {
			SELF::Validate($request, [
				'account_id',
			]);
			$this->request['url'] = SELF::CleanURL($this->init->getRequestURL() .
				'/accounts/' . $request->account_id . '/transactions/' . ($request->trx_id ?? '')
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
				'Authorization' => 'Bearer ' . $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
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
					!empty($content)
				) {
					/* // Single
						{
							"id": "3593cdaa-33b5-40b8-94bb-ecda32415e93",
							"createdTime": "2022-05-22T07:15:14.146Z",
							"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
							"type": "credit",
							"status": "active",
							"amount": "100000000.00000000",
							"notes": "demo balance"
						}
					*/
					/* // Multiple
 						[{
							"id": "3593cdaa-33b5-40b8-94bb-ecda32415e93",
							"createdTime": "2022-05-22T07:15:14.146Z",
							"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
							"type": "credit",
							"status": "active",
							"amount": "100000000.00000000",
							"notes": "demo balance"
						}]
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




	public function BankList()
	{
		try {
			$this->request['url'] = SELF::CleanURL($this->init->getRequestURL() .
				'/banks'
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
				'Authorization' => 'Bearer ' . $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 200
			) {
				// Success
				/*
					[{
						"id": "bca",
						"networkType": "bank",
						"name": "BCA",
						"fullName": "Bank Central Asia",
						"swiftCode": "CENAID",
						"country": "IDN"
					}, {
						"id": "bni",
						"networkType": "bank",
						"name": "BNI",
						"fullName": "Bank Negara Indonesia",
						"swiftCode": "BNINID",
						"country": "IDN"
					}, {
						"id": "bri",
						"networkType": "bank",
						"name": "BRI",
						"fullName": "Bank BRI",
						"swiftCode": "BRINID",
						"country": "IDN"
					}, {
						"id": "btpn",
						"networkType": "bank",
						"name": "BTPN",
						"fullName": "Bank BTPN",
						"swiftCode": "SUNIDJA",
						"country": "IDN"
					}, {
						"id": "dana",
						"networkType": "bank",
						"name": "DANA",
						"fullName": "DANA",
						"swiftCode": "",
						"country": "IDN"
					}, {
						"id": "danamon",
						"networkType": "bank",
						"name": "Danamon",
						"fullName": "Bank Danamon",
						"swiftCode": "BDINID",
						"country": "IDN"
					}, {
						"id": "demo",
						"networkType": "bank",
						"name": "Demo Bank",
						"fullName": "Bank Demo Simulator",
						"swiftCode": "DEMOIDJA",
						"country": "IDN"
					}, {
						"id": "dki",
						"networkType": "bank",
						"name": "Bank DKI",
						"fullName": "Bank DKI Jakarta",
						"swiftCode": "BDKIID",
						"country": "IDN"
					}, {
						"id": "gopay",
						"networkType": "bank",
						"name": "GoPay",
						"fullName": "Go-Pay",
						"swiftCode": "",
						"country": "IDN"
					}, {
						"id": "jago",
						"networkType": "bank",
						"name": "Jago",
						"fullName": "Bank Jago",
						"swiftCode": "JAGOID",
						"country": "IDN"
					}, {
						"id": "mandiri",
						"networkType": "bank",
						"name": "Mandiri",
						"fullName": "Mandiri",
						"swiftCode": "BMRIID",
						"country": "IDN"
					}, {
						"id": "nagari",
						"networkType": "bank",
						"name": "NAGARI",
						"fullName": "Bank Nagari",
						"swiftCode": "PDSBIDSP",
						"country": "IDN"
					}, {
						"id": "ocbc",
						"networkType": "bank",
						"name": "OCBC",
						"fullName": "OCBC NISP",
						"swiftCode": "NISPIDJA",
						"country": "IDN"
					}, {
						"id": "permata",
						"networkType": "bank",
						"name": "Permata",
						"fullName": "Bank Permata & Syariah",
						"swiftCode": "BBBAID",
						"country": "IDN"
					}]
				*/
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content)
				) {
					$res = [
						'status' => '000',
						'data' => array_map(function($item) {
							return [
								'id' => $item->id,
								'type' => $item->networkType,
								'name' => $item->name,
								'code' => strtoupper($item->swiftCode),
							];
						}, (array) $content),
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




	public function Beneficiary(object $request)
	{
		try {
			$this->request['url'] = SELF::CleanURL($this->init->getRequestURL() .
				'/beneficiaries/' . ($request->beneficiary_id ?? '')
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
				'Authorization' => 'Bearer ' . $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 200
			) {
				// Success
				/*
				*/
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content)
				) {
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

	public function BeneficiaryAdd(object $request)
	{
		try {
			// Go
			$this->request['url'] = SELF::CleanURL($this->init->getRequestURL() .
				'/beneficiaries'
			);
			$this->request['data'] = (array) [
				'accountId' => $request->accountId,
				'bankId' => $request->bankId,
				'address' => $request->address,
				'email' => $request->email,
				'label' => $request->label,
				'type' => $request->type, // personal, individual, corporate
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
				'Authorization' => 'Bearer ' . $this->init->getMID(),
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
				// Success
				/*
					{
						"id": 1640,
						"updatedTime": "2022-06-16T08:20:36.087Z",
						"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
						"bankId": "demo",
						"address": "Indonesia",
						"name": null,
						"email": "admin@sikoma.games",
						"label": "SIKOMA",
						"status": "pending",
						"type": "corporate"
					}
				*/
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content)
				) {
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

	public function BeneficiaryDel(object $request)
	{
		try {
			SELF::Validate($request, [
				'beneficiary_id',
			]);
			$this->request['url'] = SELF::CleanURL($this->init->getRequestURL() .
				'/beneficiaries/' . $request->beneficiary_id
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
				'Authorization' => 'Bearer ' . $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$delete = $this->DoRequest('DELETE', $this->request);
			$response = (array) $delete['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 204 // Deleted
			) {
				$res = [
					'status' => '000',
					'data' => [
						'beneficiaryId' => $request->beneficiary_id,
						'status' => 'deleted',
					],
				];
			} else {
				throw new \Exception('Status code is not 204 (Delete Success)', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}




	public function AccountInquiry(\PyGPaymentLib\Transaction $transaction)
	{
		// try {
		// 	$this->request['data'] = [
		// 		'data' => [
		// 			'attributes' => [
		// 				'accountNo' => $transaction->getCustomerBankAccountNumber(),
		// 				'bankShortCode' => $transaction->getCustomerBankName(),
		// 			],
		// 		],
		// 	];
		// 	$this->request['url'] = SELF::CleanURL(
		// 		$this->init->getRequestURL() .
		// 		'/api/v4/validation_services/bank_account_validation'
		// 	);
		// 	$this->request['headers'] = [
		// 		'Accept' => 'application/json',
		// 		'Content-Type' => 'application/vnd.api+json',
		// 		'Authorization' => 'Basic ' . base64_encode( $this->init->getMID() . ':' . $this->init->getSecret() ),
		// 	];
		// 	$this->request['option'] = [
		// 		'as_json' => true,
		// 	];
		// 	$post = $this->DoRequest('POST', $this->request);
		// 	$response = (array) $post['response'];
		// 	extract($response);
		// 	if (
		// 		!empty($status_code)
		// 		&& $status_code === 201 // Created
		// 	) {
		// 		// Parse data
		// 		$content = (object) json_decode($content);
		// 		if (
		// 			!empty($content->data)
		// 		) {
		// 			/*
		// 			{
		// 				"data": {
		// 					"attributes": {
		// 						"accountName": "PROD ONLY",
		// 						"accountNo": "6044222099",
		// 						"bankShortCode": "BCA"
		// 					}
		// 				}
		// 			}
		// 			*/
		// 			$res = [
		// 				'status' => '000',
		// 				'data' => [
		// 					'customer_bank_name' => $content->data->attributes->bankShortCode,
		// 					'customer_bank_account_name' => $content->data->attributes->accountName,
		// 					'customer_bank_account_number' => $content->data->attributes->accountNo,
		// 				]
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

	public function CheckBalance()
	{
		// try {
		// 	$this->request['url'] = SELF::CleanURL(
		// 		$this->init->getRequestURL() .
		// 		'/api/v4/overviews/balance_overview'
		// 	);
		// 	$this->request['headers'] = [
		// 		'Accept' => 'application/json',
		// 		'Content-Type' => 'application/vnd.api+json',
		// 		'Authorization' => 'Basic ' . base64_encode( $this->init->getMID() . ':' . $this->init->getSecret() ),
		// 	];
		// 	$this->request['option'] = [
		// 		'as_json' => true,
		// 	];
		// 	$post = $this->DoRequest('GET', $this->request);
		// 	$response = (array) $post['response'];
		// 	extract($response);
		// 	if (
		// 		!empty($status_code)
		// 		&& $status_code === 200
		// 	) {
		// 		// Parse data
		// 		$content = (object) json_decode($content);
		// 		if (
		// 			!empty($content->data)
		// 		) {
		// 			/*
		// 			{
		// 				"data": {
		// 					"attributes": {
		// 						"totalBalance": "5045825.0",
		// 						"availableBalance": "4943825.0",
		// 						"pendingBalance": "102000.0"
		// 					}
		// 				}
		// 			}
		// 			*/
		// 			$res = [
		// 				'status' => '000',
		// 				'data' => [
		// 					'total_balance' => $content->data->attributes->totalBalance,
		// 					'available_balance' => $content->data->attributes->availableBalance,
		// 					'pending_balance' => $content->data->attributes->pendingBalance,
		// 				]
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

	public function DoTransfer(object $request)
	{
		try {
			SELF::Validate($request, [
				'account_id',
			]);
			$this->request['url'] = SELF::CleanURL($this->init->getRequestURL() .
				'/accounts/' . $request->account_id . '/withdraw'
			);
			$this->request['data'] = (array) [
				'statusMessage' => $request->statusMessage ?? '',
				'address' => $request->address ?? '',
				'addressName' => $request->addressName ?? '',
				'amount' => $request->amount ?? '',
				'notes' => $request->notes ?? '',
				'prefix' => $request->prefix ?? '',
				// 'beneficiaryId' => $request->beneficiaryId,
				'requestId' => $request->requestId ?? '',
				'requestData' => $request->requestData ?? '',
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
				'Authorization' => 'Bearer ' . $this->init->getMID(),
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
					&& $content->status == 'process'
				) {
					// Success
					/*
						{
							"id": "106cd687-1675-4f0a-bbc4-6f58114fdf35",
							"createdTime": "2022-06-16T12:21:42.357Z",
							"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
							"transactionId": "4d6fbc79-0ec0-41ec-a8bc-2d584ef60715",
							"status": "process",
							"statusMessage": "string",
							"address": "Indonesia",
							"addressName": "Jakarta",
							"amount": 10000,
							"feeAmount": 1500,
							"notes": "Test",
							"prefix": "demo",
							"beneficiaryId": 1642,
							"reference": null,
							"requestId": "20220616192142",
							"account": {
								"id": "186992eb-dc10-475a-ae72-fc442a0a15ff",
								"createdTime": "2022-05-22T07:09:36.920Z",
								"updatedTime": "2022-05-22T07:09:36.920Z",
								"userId": "bc9e97c3-e022-49f4-89f9-d627ed153867",
								"currencyId": "IDR",
								"number": "112712880220"
							},
							"beneficiary": {
								"id": 1642,
								"updatedTime": "2022-06-16T12:21:42.298Z",
								"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
								"bankId": "demo",
								"address": "Indonesia",
								"name": "Mr. Forest Hodkiewicz Jr.",
								"email": "admin@sikoma.games",
								"label": "SIKOMA",
								"status": "verified",
								"type": "corporate"
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
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function CheckTransfer(object $request)
	{
		try {
			SELF::Validate($request, [
				'account_id',
			]);
			$this->request['url'] = SELF::CleanURL($this->init->getRequestURL() .
				'/accounts/' . $request->account_id . '/withdraw/' . ($request->trx_id ?? '')
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
				'Authorization' => 'Bearer ' . $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
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
					!empty($content)
				) {
					// Success
					/*
						{
							"id": "ec5a20d5-91ac-43ca-a809-5f1268a9ce25",
							"createdTime": "2022-06-16T12:26:36.492Z",
							"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
							"transactionId": "36765c40-c92b-466f-ab24-a4b07a3f084e",
							"status": "succeed",
							"statusMessage": "Failed",
							"address": "Indonesia",
							"addressName": "Jakarta",
							"amount": "10000.00000000",
							"feeAmount": "1500.00000000",
							"notes": "Test",
							"prefix": "demo",
							"beneficiaryId": 1642,
							"reference": "11e93b57bd594888b0bfc0862a531d58",
							"requestId": "20220616192636"
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
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}



	/**
	 *
	 * Accept Payment
	 *
	 */

	public function RedirectPayment(\PyGPaymentLib\Transaction $transaction)
	{
		// Inapplicable
	}

	public function SecurePayment(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->transaction = $transaction;
			//
			$this->form['order_id'] = $this->transaction->getOrderID();
			$this->form['invoice_no'] = $this->transaction->getInvoiceNo();
			$this->form['currency'] = $this->transaction->getCurrency();
			//
			$this->form['item'] = $this->transaction->getItem();
			$this->form['amount'] = (int) $this->transaction->getAmount();
			$this->form['description'] = $this->transaction->getDescription();
			//
			$this->form['customer_name'] = $this->transaction->getCustomerName();
			$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			$this->form['customer_address'] = $this->transaction->getCustomerAddress();
			$this->form['country_code'] = $this->transaction->getCountryCode();
			//
			$this->form['payment_method'] = $this->transaction->getPaymentMethod();
			$this->form['expired_in'] = $this->transaction->getExpireIn();
			$this->form['expired_at'] = $this->transaction->getExpireAt();
			//
			$arr = explode(',', $this->transaction->getPaymentMethod());
			$payment_method = strtolower(trim( $arr[0] ?? '' ));
			$payment_channel = strtolower(trim( $arr[1] ?? '' ));
			//
			$this->form['payment_method'] = $payment_method;
			$this->form['payment_channel'] = $payment_channel;
			$this->form['amount'] = (float) $this->transaction->getAmount();
			$this->form['fee'] = (float) 0;
			//
			switch ($payment_method) {
				case 'transfer':
					break;
				case 'va':
					break;
				case 'qris':
					break;
				default:
					throw new \Exception('Payment method not defined/unknown', 900);
					break;
			}
			// Go
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/accounts/' . $this->transaction->getParams()['account_id'] . '/deposits/requests'
			);
			$this->request['data'] = [
				'type' => $this->form['payment_method'],
				'amount' => $this->form['amount'],
				'feeAmount' => $this->form['fee'],
				'bankId' => ($this->form['payment_channel'] == 'qris' ? '' : $this->form['payment_channel']),
				'addressName' => '',
				'networkId' => '',
				'requestId' => $this->form['order_id'],
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
				'Authorization' => 'Bearer ' . $this->init->getMID(),
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
					!empty($content->id)
					&& $content->status == 'pending'
				) {
					/* // Success transfer BCA
						{
							"status": "000",
							"data": {
								"id": "041101b4-8420-498c-b825-cc472d2b9414",
								"createdTime": "2022-06-16T07:42:32.598Z",
								"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
								"type": "transfer",
								"status": "pending",
								"amount": 90490,
								"feeAmount": 0,
								"bankId": "bca",
								"addressName": "IDNP Sikoma Games",
								"transactionId": "129c3370-3267-4026-9471-ca6a1a00f452",
								"requestId": "0055365351"
							}
						}
					*/
					/* // Success VA BCA
						{
							"id": "cdc40cd1-80c0-4086-a917-a54291e5533d",
							"createdTime": "2022-06-02T06:37:00.423Z",
							"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
							"type": "va",
							"status": "pending",
							"amount": 91040,
							"feeAmount": 0,
							"bankId": "bca",
							"address": "900407038368",
							"addressName": "IDNP Sikoma Games",
							"transactionId": "b1434c93-a3d6-4e46-9f92-d83f0604a691",
							"requestId": "0054151819"
						}
					*/
					/* // Success VA BNI
						{
							"id": "f7873e7d-1307-4f5e-ab78-d248469b5986",
							"createdTime": "2022-06-02T06:36:24.727Z",
							"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
							"type": "va",
							"status": "pending",
							"amount": 58900,
							"feeAmount": 1500,
							"bankId": "bni",
							"address": "9881802922060204",
							"addressName": "IDNP Sikoma Games",
							"transactionId": "6f1dfc53-1dd3-4eae-b9c8-d3b0d4739111",
							"requestId": "0054151784"
					}
					*/
					/* // Success QRIS
						{
							"id": "90f16741-8c40-4095-8177-ca4685c49366",
							"createdTime": "2022-06-02T06:37:34.573Z",
							"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
							"type": "qris",
							"status": "pending",
							"amount": 95040,
							"feeAmount": 0,
							"bankId": "",
							"addressName": "IDNP Sikoma Games",
							"transactionId": "4aa2a989-7db8-4691-89ad-848ba6c2d9fc",
							"requestId": "0054151854"
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
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

	public function CheckDeposit(object $request)
	{
		try {
			SELF::Validate($request, [
				'account_id',
				'trx_id',
			]);
			$this->request['url'] = SELF::CleanURL($this->init->getRequestURL() .
				'/accounts/' . $request->account_id .
				'/deposits/requests/' . $request->trx_id .
				($request->qr ? '/qr' : '')
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Charset' => 'utf-8',
				'Authorization' => 'Bearer ' . $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 200
			) {
				// Parse data
				if (
					!empty($content)
				) {
					$res = [
						'status' => '000',
						'data' => ($request->qr ? base64_encode($content) : json_decode($content)),
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

	public function Callback(object $request)
	{
		// /* // Sample incoming
		// {
		// 	"data": {
		// 		"id": "contract_ea9c4b10df49446eafd4fb3f25a713bd",
		// 		"type": "payment",
		// 		"attributes": {
		// 			"status": "paid",
		// 			"amount": "100000.0",
		// 			"createdAt": "2020-10-19T09:12:48+07:00",
		// 			"description": null,
		// 			"expiredAt": "2020-10-20T02:12:48Z",
		// 			"referenceId": "ORDER_0001",
		// 			"fees": "4400.0",
		// 			"paymentMethod": {
		// 				"id": "871767762ccded4a92461ac0d3376c1e",
		// 				"type": "retail_outlet",
		// 				"referenceId": "ORDER_0001",
		// 				"instructions": {
		// 					"retailOutletName": "ALFAMART",
		// 					"paymentCode": "8887322"
		// 				}
		// 			}
		// 		}
		// 	}
		// }
		// */
		// try {
		// 	SELF::Validate($request, [
		// 		'id',
		// 		'type',
		// 		'attributes',
		// 	]);
		// 	if (
		// 		$request->attributes->status == 'paid'
		// 	) {
		// 		$res = [
		// 			'status' => '000',
		// 			'data' => (array) $request,
		// 		];
		// 	} else {
		// 		// throw new \Exception('Callback status is not paid', 901);
		// 		throw new \Exception('Callback status is not paid', 902);
		// 	}
		// } catch (\Throwable $e) {
		// 	$this->ThrowErrorException($e);
		// }
		// return SELF::JSONResult($request, $res, 200);
	}

	public function CallbackAlt(object $request)
	{
		// Inapplicable
	}

	public function Inquiry(object $request)
	{
		// try {
		// 	if (empty($request)) {
		// 		throw new \Exception('Request is empty', 900);
		// 	}
		// 	// Validation
		// 	SELF::Validate($request, [
		// 		'id',
		// 	]);
		// 	// Go
		// 	$this->request['time'] = time();
		// 	$this->request['url'] = SELF::CleanURL(
		// 		$this->init->getRequestURL() .
		// 		'/api/v4/payments/' . $request->id
		// 	);
		// 	$this->request['data'] = [];
		// 	$this->request['headers'] = [
		// 		'Content-Type' => 'application/vnd.api+json',
		// 		'Authorization' => 'Basic ' . base64_encode( $this->init->getMID() . ':' . $this->init->getSecret() ),
		// 	];
		// 	$this->request['option'] = [
		// 		'as_json' => true,
		// 	];
		// 	$get = $this->DoRequest('GET', $this->request);
		// 	$response = (array) $get['response'];
		// 	extract($response);
		// 	if (
		// 		!empty($status_code)
		// 		&& $status_code === 200
		// 	) {
		// 		$content = (object) json_decode($content);
		// 		if (
		// 			!empty($content->data->attributes)
		// 		) {
		// 			// Success
		// 			/*
		// 			{
		// 				"data": {
		// 					"id": "contract_a047916c30a54e94a9b4b00f1d8ac022",
		// 					"type": "payment",
		// 					"attributes": {
		// 						"status": "pending",
		// 						"amount": "85650.0",
		// 						"createdAt": "2021-07-16T16:46:01+07:00",
		// 						"description": "Voucher #41",
		// 						"expiredAt": "2021-07-16T18:46:01+07:00",
		// 						"referenceId": "0026428761",
		// 						"fees": "3630.0",
		// 						"paymentMethod": {
		// 							"id": "va_c31715a0b94a093634a02d4a7e558d12",
		// 							"type": "virtual_bank_account",
		// 							"referenceId": "0026428761",
		// 							"instructions": {
		// 								"bankShortCode": "BCA",
		// 								"accountNo": "1304410000000441",
		// 								"displayName": "Voucher #41"
		// 							}
		// 						}
		// 					}
		// 				}
		// 			}
		// 			*/
		// 			$res = [
		// 				'status' => '000',
		// 				'data' => (array) $content->data,
		// 			];
		// 		} else {
		// 			// throw new \Exception('Data->Attributes is empty', 901);
		// 			throw new \Exception('Parse failed (' . ($content->data->attributes->status ?? 'Unknown status') . ')', 901);
		// 		}
		// 	} else {
		// 		// throw new \Exception('Status code is not 200', 902);
		// 		throw new \Exception('Parse failed (' . ($content ?? 'Unknown error') . ')', 902);
		// 	}
		// } catch (\Throwable $e) {
		// 	$this->ThrowErrorException($e);
		// }
		// return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function PaymentMethodList(object $request)
	{
		// try {
		// 	if (empty($request)) {
		// 		throw new \Exception('Request is empty', 900);
		// 	}
		// 	// Go
		// 	$this->request['time'] = time();
		// 	$this->request['url'] = SELF::CleanURL(
		// 		$this->init->getRequestURL() .
		// 		'api/v4/payment_methods/' . $request->type . '/' . $request->id
		// 	);
		// 	$this->request['data'] = [];
		// 	$this->request['headers'] = [
		// 		'Content-Type' => 'application/vnd.api+json',
		// 		'Authorization' => 'Basic ' . base64_encode( $this->init->getMID() . ':' . $this->init->getSecret() ),
		// 	];
		// 	$this->request['option'] = [
		// 		'as_json' => true,
		// 	];
		// 	$get = $this->DoRequest('GET', $this->request);
		// 	$response = (array) $get['response'];
		// 	extract($response);
		// 	if (
		// 		!empty($status_code)
		// 		&& $status_code === 200
		// 	) {
		// 		$content = (object) json_decode($content);
		// 		if (
		// 			!empty($content->data->attributes)
		// 		) {
		// 			// Success
		// 			/*
		// 			*/
		// 			$res = [
		// 				'status' => '000',
		// 				'data' => (array) $content->data,
		// 			];
		// 		} else {
		// 			throw new \Exception('Parse failed (' . ($content->data->attributes->status ?? 'Unknown status') . ')', 901);
		// 		}
		// 	} else {
		// 		throw new \Exception('Parse failed (' . ($content ?? 'Unknown error') . ')', 902);
		// 	}
		// } catch (\Throwable $e) {
		// 	$this->ThrowErrorException($e);
		// }
		// return SELF::JSONResult($this->request, $res, $status_code);
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
