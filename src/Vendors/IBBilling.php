<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;

use PyGPaymentLib\Helper;

class IBBilling extends Requestor implements VendorInterface
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

	public function RedirectPayment(\PyGPaymentLib\Transaction $transaction)
	{
		// Not applicable
	}

	public function SecurePayment(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/ApiBilling/CreateBillingAPI'
			);
			$this->request['data'] = [
				'merchant_uuid' => $this->init->getMID(),
				'service_code' => $transaction->getServiceCode() ?? $transaction->getPaymentMethod(),
				'channel_code' => $transaction->getPaymentChannel(),
				'owner_id' => $transaction->getMerchantID(),
				'type' => $transaction->getPaymentType(),
				//
				'reference_num' => $transaction->getReferenceNumber(),
				'name' => $transaction->getTransactionRemark(),
				'description' => $transaction->getDescription(),
				'customer_id' => $transaction->getCustomerID(),
				'customer_name' => $transaction->getCustomerName(),
				'customer_phone' => $transaction->getCustomerPhone(),
				'customer_email' => $transaction->getCustomerEmail(),
				'customer_details' => [],
				'amount' => (int) $transaction->getAmount(),
				'expire_in' => $transaction->getExpireIn(),
			];
			if (!empty($transaction->getParam('service_id'))) {
				$this->request['data'] = array_merge($this->request['data'], [
					'service_id' => $transaction->getParam('service_id'),
				]);
			}
			if (!empty($transaction->getParam('credential_id'))) {
				$this->request['data'] = array_merge($this->request['data'], [
					'credential_id' => $transaction->getParam('credential_id'),
				]);
			}
			$this->request['headers'] = [
				'Accept' => 'application/json',
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
					!empty($content->status)
					&& trim(strtolower($content->status)) == true
				) {
					/*
						// Success // QRIS
						{
							"status": "000",
							"message": "Billing successfully created",
							"data": {
								"uuid": "9c3642c8-3321-4b8a-8d7c-23818c671699",
								"reference_num": "1717568625",
								"invoice_num": "0DFSBN3TC56QZ12W",
								"billing_num": "5320986110794456",
								"type": "pg",
								"name": "TRX1717568625",
								"description": "TRX1717568625",
								"items": null,
								"qr": "00020101021226670016ID.CO.MBAYAR.WWW01189360082901714465890214E2P171446589520303UMI51460016ID.CO.MBAYAR.WWW0215ID10243227058630303UMI520458165303360540810000.005802ID5910Berkatgoup6009SUKOHARJO61055755262500507602141207062121200825PT Berkah Berkat Bersatu 6304763C",
								"url": "http:\/\/ib-billing.test\/qr\/g2up\/qris\/2024\/06\/05\/1717568625.png",
								"expire_in": 10,
								"currency": "360",
								"net_amount": 10000,
								"fee_fixed": 0,
								"fee_percentage": 0,
								"fee_amount": 0,
								"surcharge_fixed": 0,
								"surcharge_percentage": 0,
								"surcharge_amount": 0,
								"amount": 10000,
								"customer_id": "1234567890",
								"customer_name": "TESTING",
								"customer_phone": "081212121314",
								"customer_email": "tester@gmail.com",
								"customer_details": null,
								"created_at": "2024-06-05 13:23:45",
								"updated_at": "2024-06-05 13:23:46",
								"expiring_at": "2024-06-05 13:33:45"
							}
						}
					*/
					$res = $content;
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
					'data' => (array) $request->data,
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
			SELF::Validate($request, [
				'service_code',
				'owner_id',
				'type',
				'reference_num',
			]);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/PGApi/InquiryPaymentAPI'
			);
			$this->request['data'] = [
				'merchant_uuid' => $this->init->getMID(),
				'service_code' => $request->service_code,
				'owner_id' => $request->owner_id,
				'type' => $request->type,
				//
				'reference_num' => $request->reference_num,
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
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
					!empty($content->status)
					&& trim($content->status) == '000'
				) {
					/* // Success inquiry QRIS
						{
							"status": "000",
							"message": "Request complete",
							"data": {
								"credential_id": 1,
								"credential_name": "CRONOS",
								"uuid": "9c8af932-9e6c-4118-bbbb-fce684c68d4f",
								"reference_num": "TEST1721207001846",
								"invoice_num": "0DG12IR7TK9FVCP4",
								"provider_transaction_id": "72d6f033-a639-4e8e-b2f1-d2e2f6b2876d",
								"billing_num": null,
								"type": "pg",
								"qr": "Velit accusantium dolores eos incidunt repellat. Dolorum quas eligendi debitis doloremque. Molestiae laboriosam ratione aperiam temporibus.",
								"url": "\/qr\/cronos\/acceptance_qris\/2024\/07\/17\/TEST1721207001846.png",
								"amount": 50000,
								"status": 1,
								"status_descr": "PAID",
								"created_at": "2024-07-17 16:03:22",
								"expired_at": null,
								"paid_at": null,
								"settled_at": null,
								"additional_info": {
										"customer_id": "1234567890",
										"customer_name": "PGA",
										"customer_phone": "081212121314",
										"customer_email": "tester@gmail.com",
										"customer_details": null
								}
							}
						}
					*/
					$res = $content;
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

	public function InquiryAlt(object $request)
	{
		try {
			SELF::Validate($request, [
				'owner_id',
				'type',
			]);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/BillingPG/get'
			);
			$this->request['data'] = [
				'owner_id' => $request->owner_id,
				'type' => $request->type,
				'uuid' => $request->uuid ?? null,
				'filter' => $request->filter ?? null,
				'status' => $request->status ?? '*',
				'start_date' => $request->start_date ?? date('Ymd'),
				'end_date' => $request->end_date ?? date('Ymd'),
				'per_page' => $request->per_page ?? 25,
				'page' => $request->page ?? 1,
				'limit' => $request->limit ?? 0,
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
					/* // Success inquiry QRIS
						{
							"status": "000",
							"current_page": 1,
							"data": [
								{
										"db_table": "billing_pg_20240605",
										"db_index": "20240605-38",
										"id": 38,
										"owner_id": "GPL",
										"client_id": null,
										"domain_id": 3,
										"provider_id": 4,
										"company_id": 1,
										"merchant_id": 1,
										"channel_id": 6,
										"credential_id": 4,
										"pricing_id": 1,
										"service_id": 4,
										"uuid": "9c3642c8-3321-4b8a-8d7c-23818c671699",
										"reference_num": "1717568625",
										"invoice_num": "0DFSBN3TC56QZ12W",
										"billing_num": "5320986110794456",
										"type": "pg",
										"name": "TRX1717568625",
										"description": "TRX1717568625",
										"items": null,
										"qr": "00020101021226670016ID.CO.MBAYAR.WWW01189360082901714465890214E2P171446589520303UMI51460016ID.CO.MBAYAR.WWW0215ID10243227058630303UMI520458165303360540810000.005802ID5910Berkatgoup6009SUKOHARJO61055755262500507602141207062121200825PT Berkah Berkat Bersatu 6304763C",
										"url": "http:\/\/ib-billing.test\/qr\/g2up\/qris\/2024\/06\/05\/1717568625.png",
										"expire_in": 10,
										"currency": "360",
										"net_amount": "10000.00",
										"fee_fixed": "0.00",
										"fee_percentage": "0.00",
										"fee_amount": "0.00",
										"surcharge_fixed": "0.00",
										"surcharge_percentage": "0.00",
										"surcharge_amount": "0.00",
										"tips_fixed": "0.00",
										"tips_percentage": "0.00",
										"tips_amount": "0.00",
										"amount": "10000.00",
										"paid_amount": "0.00",
										"refunded_amount": "0.00",
										"customer_id": "1234567890",
										"customer_name": "TESTING",
										"customer_phone": "081212121314",
										"customer_email": "tester@gmail.com",
										"customer_details": null,
										"param": null,
										"status": 0,
										"log": null,
										"created_at": "2024-06-05T06:23:45.000000Z",
										"updated_at": "2024-06-05T06:23:46.000000Z",
										"suspended_at": null,
										"paid_at": null,
										"refunded_at": null,
										"cancelled_at": null,
										"settling_at": null,
										"settled_at": null,
										"expiring_at": "2024-06-05 13:33:45",
										"expired_at": null,
										"deleted_at": null,
										"payment_tables": null,
										"payment_table_ids": null,
										"refund_tables": null,
										"refund_table_ids": null,
										"inbound_tables": "[\"inbound_pg_20240605\"]",
										"inbound_table_ids": "[60]",
										"outbound_tables": "[\"outbound_pg_20240605\"]",
										"outbound_table_ids": "[8]",
										"provider": {
											"id": 4,
											"name": "G2UP",
											"providergroup": null,
											"domain": null
										},
										"company": {
											"id": 1,
											"name": "PT INTERNAL TESTER",
											"domain": null,
											"companygroup": null,
											"credential": []
										},
										"merchant": {
											"id": 1,
											"name": "BRANDS OF PGA 1",
											"merchantgroup": null,
											"domain": null
										},
										"channel": {
											"id": 6,
											"name": "G2QRIS",
											"code": "QRIS",
											"provider": null,
											"channelgroup": null,
											"pricing": null,
											"pricinggroup": null
										},
										"credential": {
											"id": 4,
											"name": "PT INTERNAL TESTER x G2UP",
											"provider": null,
											"company": null
										},
										"service": {
											"id": 4,
											"name": "QRIS",
											"merchantgroup": null,
											"merchant": null,
											"credential": null,
											"channel": null,
											"provider": null,
											"pricing": null,
											"pricinggroup": null
										},
										"pricing": {
											"id": 1,
											"name": "QRIS",
											"currency": "360",
											"fee_fixed": "0.00",
											"fee_percentage": "0.78",
											"surcharge_fixed": "0.00",
											"surcharge_percentage": "0.00",
											"pricinggroup": null
										}
								}
							],
							"first_page_url": "http:\/\/ib-billing.test\/api-v1\/billingpg\/get?page=1",
							"from": 1,
							"last_page": 1,
							"last_page_url": "http:\/\/ib-billing.test\/api-v1\/billingpg\/get?page=1",
							"links": [
								{
										"url": null,
										"label": "&laquo; Previous",
										"active": false
								},
								{
										"url": "http:\/\/ib-billing.test\/api-v1\/billingpg\/get?page=1",
										"label": "1",
										"active": true
								},
								{
										"url": null,
										"label": "Next &raquo;",
										"active": false
								}
							],
							"next_page_url": null,
							"path": "http:\/\/ib-billing.test\/api-v1\/billingpg\/get",
							"per_page": 25,
							"prev_page_url": null,
							"to": 1,
							"total": 1
						}
					*/
					$res = $content;
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

	public function CheckBalance(object $request)
	{
		try {
			SELF::Validate($request, [
				'service_code',
				'owner_id',
				'type',
			]);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/PGApi/CheckAccountBalanceAPI'
			);
			$this->request['data'] = [
				'merchant_uuid' => $this->init->getMID(),
				'service_code' => $request->service_code,
				'owner_id' => $request->owner_id,
				'type' => $request->type,
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
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
					!empty($content->status)
					&& trim($content->status) == '000'
				) {
					/* // Success check balance
						{
							"status": "000",
							"message": "Request complete",
							"data": [
								{
										"credential_id": 1,
										"credential_name": "CRONOS",
										"available_balance": 1352880,
										"pending_balance": 0
								}
							]
						}
					*/
					$res = $content;
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

	public function BankAccountInquiry(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/PGApi/BankAccountInquiryAPI'
			);
			$this->request['data'] = [
				'merchant_uuid' => $this->init->getMID(),
				'service_code' => $transaction->getServiceCode(),
				'owner_id' => $transaction->getMerchantID(),
				'type' => $transaction->getPaymentType(),
				//
				'beneficiary_bank_code' => $transaction->getCustomerBankCode(),
				'beneficiary_bank_account_number' => $transaction->getCustomerBankAccountNumber(),
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
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
					!empty($content->status)
					&& trim($content->status) == '000'
				) {
					/* // Success bank account inquiry
						{
							"status": "000",
							"message": "Request complete",
							"data": {
								"credential_id": 1,
								"credential_name": "CRONOS",
								"transaction_id": "17418c13-08c8-4e1c-9f0f-e7a9980ea7fb",
								"beneficiary_bank_code": "014",
								"beneficiary_bank_account_number": "0092292191",
								"beneficiary_bank_account_name": "Ben Turner"
							}
						}
					*/
					$res = $content;
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

	public function OnlineTransferExec(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/ApiDisbursement/CreateDisbursementAPI'
			);
			$this->request['data'] = [
				'merchant_uuid' => $this->init->getMID(),
				'service_code' => $transaction->getServiceCode(),
				'owner_id' => $transaction->getMerchantID(),
				'type' => $transaction->getPaymentType(),
				//
				'reference_num' => $transaction->getReferenceNumber(),
				'name' => $transaction->getTransactionRemark(),
				'description' => $transaction->getDescription(),
				'customer_id' => $transaction->getCustomerID(),
				'customer_name' => $transaction->getCustomerName(),
				'customer_phone' => $transaction->getCustomerPhone(),
				'customer_email' => $transaction->getCustomerEmail(),
				'customer_details' => [],
				'beneficiary_bank_code' => $transaction->getCustomerBankCode(),
				'beneficiary_bank_account_number' => $transaction->getCustomerBankAccountNumber(),
				'amount' => (int) $transaction->getAmount(),
				'disbursement_type' => $transaction->getDisbursementType(),
			];
			if (!empty($transaction->getParam('service_id'))) {
				$this->request['data'] = array_merge($this->request['data'], [
					'service_id' => $transaction->getParam('service_id'),
				]);
			}
			if (!empty($transaction->getParam('credential_id'))) {
				$this->request['data'] = array_merge($this->request['data'], [
					'credential_id' => $transaction->getParam('credential_id'),
				]);
			}
			$this->request['headers'] = [
				'Accept' => 'application/json',
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
					!empty($content->status)
					&& trim($content->status) == '000'
				) {
					/* // Success disb
						{
							"status": "000",
							"message": "Disbursement successfully created",
							"data": {
								"uuid": "9c8b3fbe-11f4-497f-8b93-3e4db4c2d52d",
								"reference_num": "1721218837",
								"invoice_num": "0DGAVSDRQK9G650I",
								"transaction_id": "44af5f26-9b85-43fc-9e0d-49e9ef8cfd0b",
								"type": "pg",
								"name": "DISB1721218837",
								"description": "DISB1721218837",
								"items": null,
								"currency": "360",
								"net_amount": 50000,
								"fee_amount": 3900,
								"surcharge_amount": 0,
								"service_fee_amount": 0,
								"fee_total": 3900,
								"fee_waived": 0,
								"amount": 46100,
								"sent_amount": 0,
								"status": 0,
								"status_descr": "PENDING",
								"created_at": "2024-07-17 19:20:38",
								"additional_info": {
										"customer_id": "1234567890",
										"customer_name": "TESTING",
										"customer_phone": "081212121314",
										"customer_email": "tester@gmail.com",
										"customer_details": null,
										"beneficiary_id": null,
										"beneficiary_name": null,
										"beneficiary_bank_code": "011",
										"beneficiary_bank_account_number": "7700173383",
										"beneficiary_bank_account_name": null,
										"beneficiary_swift_code": "011",
										"beneficiary_type": null
								}
							}
						}
					*/
					$res = $content;
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
			SELF::Validate($request, [
				'service_code',
				'owner_id',
				'type',
				'reference_num',
			]);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/PGApi/CheckFundTransferStatusAPI'
			);
			$this->request['data'] = [
				'merchant_uuid' => $this->init->getMID(),
				'service_code' => $request->service_code,
				'owner_id' => $request->owner_id,
				'type' => $request->type,
				//
				'reference_num' => $request->reference_num,
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
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
					!empty($content->status)
					&& trim($content->status) == '000'
				) {
					/* // Success check transfer
						{
							"status": "000",
							"message": "Request complete",
							"data": {
								"credential_id": 1,
								"credential_name": "CRONOS",
								"uuid": "9c8b3fbe-11f4-497f-8b93-3e4db4c2d52d",
								"reference_num": "1721218837",
								"invoice_num": "0DGAVSDRQK9G650I",
								"provider_transaction_id": "44af5f26-9b85-43fc-9e0d-49e9ef8cfd0b",
								"type": "pg",
								"amount": 46100,
								"status": 0,
								"status_descr": "PENDING",
								"created_at": "2024-07-17 19:20:38",
								"sent_at": null,
								"settled_at": null,
								"additional_info": {
										"customer_id": "1234567890",
										"customer_name": "TESTING",
										"customer_phone": "081212121314",
										"customer_email": "tester@gmail.com",
										"customer_details": null,
										"beneficiary_id": null,
										"beneficiary_name": null,
										"beneficiary_bank_code": "011",
										"beneficiary_bank_account_number": "7700173383",
										"beneficiary_bank_account_name": null,
										"beneficiary_swift_code": "011",
										"beneficiary_type": null
								}
							}
						}
					*/
					$res = $content;
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
