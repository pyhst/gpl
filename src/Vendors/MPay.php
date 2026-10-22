<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

use Growinc\Payment\Helper;

class MPay extends Requestor implements VendorInterface
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
			$payment_method = $transaction->getPaymentMethod();
			$payment_channel = $transaction->getPaymentChannel();
			//
			$signature = hash_hmac('sha512',
				$this->init->getMerchantKey() . $transaction->getReferenceNumber(),
				$this->init->getMerchantToken()
			);
			switch ($payment_method) {
				case 'va':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						$payment_method . '/' . $payment_channel
					);
					$this->request['data'] = [
						'key' => $this->init->getMerchantKey(),
						'token' => $this->init->getMerchantToken(),
							'referenceId' => $transaction->getReferenceNumber(),
							'amount' => (float) $transaction->getAmount(),
							'callbackUrl' => $this->init->getCallbackURL(),
							'signHash' => $signature,
							'reqType' => 'oneoff', // 'oneoff' for close amount and 'persistent' for open amount
							'viewName' => $transaction->getCustomerName(),
							'expTime' => $transaction->getExpireIn(), // minutes
							'useCase' => 'single',
							'vaExpired' => $transaction->getExpireIn(), // minutes
					];
					break;
				case 'qris':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'qris/created'
					);
					$this->request['data'] = [
						'key' => $this->init->getMerchantKey(),
						'token' => $this->init->getMerchantToken(),
							'referenceId' => $transaction->getReferenceNumber(),
							'signHash' => $signature,
							'amount' => (float) $transaction->getAmount(),
							'callbackUrl' => $this->init->getCallbackURL(),
							'viewName' => $transaction->getCustomerName(),
							'expTime' => $transaction->getExpireIn(), // minutes
					];
					break;
				case 'ewallet':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'ewallet/created/' . $payment_channel
					);
					$this->request['data'] = [
						'key' => $this->init->getMerchantKey(),
						'token' => $this->init->getMerchantToken(),
							'referenceId' => $transaction->getReferenceNumber(),
							'amount' => (float) $transaction->getAmount(),
							'callbackUrl' => $this->init->getCallbackURL(),
							'signHash' => $signature,
							'viewName' => $transaction->getCustomerName(),
							'successRedirectUrl' => $this->init->getReturnURL(),
							'mobileNumber' => $transaction->getCustomerPhone(),
							'expTime' => $transaction->getExpireIn(), // minutes
					];
					break;
				case 'cstore':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'retail/request/' . $payment_channel
					);
					$this->request['data'] = [
						'key' => $this->init->getMerchantKey(),
						'token' => $this->init->getMerchantToken(),
							'referenceId' => $transaction->getReferenceNumber(),
							'amount' => (float) $transaction->getAmount(),
							'callbackUrl' => $this->init->getCallbackURL(),
							'signHash' => $signature,
							'viewName' => $transaction->getCustomerName(),
							'mobileNumber' => $transaction->getCustomerPhone(),
					];
					break;
				default:
					throw new \Exception('Undefined payment method', 800);
					break;
			}
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
			]];
			$this->request['option'] = [
				'to_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->success)
					&& $content->success == "true"
				) {
					/*
						// Success // VA
						{
							"success": true,
							"data_payment": {
								"id": "DGP-ID-4764513",
								"create_time": 1674563427,
								"status": "pending",
								"statustrx_time": 1674563427,
								"referenceId": "0074563427",
								"paymentType": "receive",
								"paymentName": "VA MANDIRI",
								"feeTo": "merchant",
								"Amount": "10000",
								"fee": 5000,
								"totalAmount": 10000,
								"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo",
								"paymentMethod": {
									"trx_id": 4764513,
									"trans_hash": "DGP-a7c91c9e-7f1a-4adc-8838-659e9207870b",
									"bank": "va_mandiri",
									"viewName": "Testing",
									"accountNo": "8932560002191450",
									"reqType": "oneoff",
									"useCase": "single",
									"paidCounter": 0
								}
							}
						}
						// Success // QRIS
						{
							"success": true,
							"data_payment": {
								"id": "DGP-ID-4764672",
								"status": "pending",
								"referenceId": "0074564081",
								"paymentType": "receive",
								"paymentName": "Qris",
								"feeTo": "merchant",
								"amount": "10000",
								"fee": 100,
								"totalAmount": 10000,
								"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo",
								"paymentDetail": {
									"imageQris": "https:\/\/api-prod.mitrapayment.com\/image-qris\/MDAwMjAxMDEwMjEyMjY2MTAwMTZJRC5DTy5TSE9QRUUuV1dXMDExODkzNjAwOTE4MDAyMDQ5MDA3MzAyMDgyMDQ5MDA3MzAzMDNVTUU1MTQ0MDAxNElELkNPLlFSSVMuV1dXMDIxNUlEMjAyMjE3NDA3MzA2NjAzMDNVTUU1MjA0NTM5OTUzMDMzNjA1NDA4MTAwMDAuMDA1ODAySUQ1OTA0TVBBWTYwMTVLT1RBIEpBS0FSVEEgU0U2MTA1MTI3NjA2MjE2MDUxMjIzMDFLWk5EU0RVODYzMDRGMzcw",
									"contentQris": "00020101021226610016ID.CO.SHOPEE.WWW01189360091800204900730208204900730303UME51440014ID.CO.QRIS.WWW0215ID20221740730660303UME520453995303360540810000.005802ID5904MPAY6015KOTA JAKARTA SE610512760621605122301KZNDSDU86304F370",
									"displayName": "Testing"
								}
							}
						}
						// Success // E-wallet DANA
						{
							"success": true,
							"data_payment": {
								"id": "DGP-ID-4764749",
								"status": "pending",
								"referenceId": "0074564395",
								"paymentType": "receive",
								"paymentName": "DANA",
								"feeTo": "merchant",
								"amount": "10000",
								"fee": 200,
								"totalAmount": 10000,
								"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo",
								"paymentDetail": {
									"trx_id": 4764749,
									"trans_hash": "DGP-c82b438d-c193-4ab6-84d6-c2c07bec03fe",
									"wallet": "dana",
									"viewName": "Testing",
									"phone": "081212121314",
									"qrcode": "",
									"checkout_url": "https:\/\/link.dana.id\/pay?bizNo=20230124111212800110166874174371662&timestamp=1674564394911&originSourcePlatform=IPG&mid=216620000311085238996&did=216650000415902756999&sign=lj5Hdfi6x2Eoet2UICnR49A3q0dlkCfDgYfFHt9JZBBO6HA%2F2H9PoLxchWcjNzmhbOtJxpjAwHpVg1ksGiuWa6yjc6%2BIz%2FBwAKWgTcS7IWh%2BGEx3EThB5nd3VB9A%2BRFmoL2CJaeKC73pdtHbDzX%2BlsmuT9nxJlR44BdEtPiTXQyQSMAbPXSn7UPyvO%2BLJcPvoGMOip3U4EVNusCzRvA19KWmcx%2Bw%2FYACQAMRLLp6G46Jxo0iFl96cRZWxy80KBxQOx0JPyvECIDa8SdIhi7iSzzN7FLSQXvM4HAU4Fb2KlTEunrg%2F1MnKfxqRjAYXfGNBaZOjNtQcfy0%2FPzuJpu4Eg%3D%3D&forceToH5=false",
									"image_url": ""
								}
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content->data_payment,
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
					'data' => (array) $request,
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
			// Go
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() . '/va_status'
			);
			$this->request['data'] = [
				'key' => $this->init->getMerchantKey(),
				'token' => $this->init->getMerchantToken(),
					'referenceId' => $request->reference_id,
					'accountNo' => $request->account_no,
			];
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
			]];
			$this->request['option'] = [
				'to_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->success)
					&& $content->success == "true"
				) {
					/* // Success
						{
							"success": true,
							"total_trx": 1,
							"data_payment": [{
								"id": "DGP-ID-4764513",
								"create_time": 1674563427,
								"status": "pending",
								"statustrx_time": 1674563427,
								"referenceId": "0074563427",
								"paymentType": "receive",
								"paymentName": "VA MANDIRI",
								"feeTo": "merchant",
								"Amount": 10000,
								"fee": "5000",
								"totalAmount": 10000,
								"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo",
								"trx_exp": 1674563547,
								"va_exp": "2",
								"paymentMethod": {
									"trans_hash": "DGP-a7c91c9e-7f1a-4adc-8838-659e9207870b",
									"bank": "va_mandiri",
									"viewName": "Testing",
									"accountNo": "8932560002191450",
									"reqType": "oneoff",
									"idVa": null,
									"useCase": "single",
									"paidCounter": "0"
								}
							}]
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content->data_payment,
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
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/balance'
			);
			$this->request['data'] = [
				'key' => $this->init->getMerchantKey(),
				'token' => $this->init->getMerchantToken(),
			];
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
			]];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->success)
					&& $content->success == "true"
				) {
					/* // Success
						{
							"success": true,
							"data_user": {
								"username": "newgame",
								"email": "newgame@gmail.com",
								"alamat": null,
								"balance": 30131585845,
								"balance_pending": "0"
							},
							"data_project": {
								"NameProject": "Newgame",
								"DomainProject": "newgame.digippob.com",
								"StatusProject": "active",
								"ipAddressProject": "180.210.206.235,190.92.212.111,180.210.206.235,190.92.212.111,114.119.181.209,180.210.205.125"
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => [
							'data_user' => $content->data_user,
							'data_project' => $content->data_project,
						],
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
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/bank-account-inquiry'
			);
			$this->request['data'] = [
				'key' => $this->init->getMerchantKey(),
				'token' => $this->init->getMerchantToken(),
					'referenceId' => $transaction->getOrderID(),
					'accountNo' => $transaction->getCustomerBankAccountNumber(),
					'bankShort' => $transaction->getCustomerBankCode(),
			];
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
			]];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content)
				) {
					/* // Success
						{
							"status": true,
							"reffrenceId": "1674650124",
							"data": {
								"accountName": "LISA ANDRIYANI",
								"accountNo": "0092292191",
								"bankShort": "BCA",
								"bankCode": "014"
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => array_merge(
							(!empty($content->reffrenceId) ? ['reffrenceId' => $content->reffrenceId] : []),
							(!empty($content->data) ? (array) $content->data : []),
						),
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
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/disburst/' . $transaction->getTransferMethod()
			);
			//
			$signature = hash_hmac('sha512',
				$this->init->getMerchantKey() . $transaction->getOrderID(),
				$this->init->getMerchantToken()
			);
			//
			$this->request['data'] = [
				'key' => $this->init->getMerchantKey(),
				'token' => $this->init->getMerchantToken(),
					'referenceId' => $transaction->getOrderID(),
					'signHash' => $signature,
					'bankShort' => $transaction->getCustomerBankCode(),
					'recipient_account' => $transaction->getCustomerBankAccountNumber(),
					'amount' => (float) $transaction->getAmount(),
					'useCase' => 'single',
					'paidCounter' => '1',
					// 'callbackUrl' => $this->init->getCallbackURL(),
					'callbackUrl' => $this->init->getDisbursementCallbackURL(),
			];
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
			]];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content)
				) {
					// Success
					/*
						{
							"success": true,
							"data_payment": {
								"id": "DGP-ID-4784922",
								"create_time": 1674652232,
								"status": "pending",
								"statustrx_time": 1674652232,
								"referenceId": "1674652233",
								"paymentType": "send",
								"Amount": 10500,
								"fee": 6500,
								"totalAmount": 17000,
								"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo",
								"paymentMethod": {
									"trans_hash": "DGP-7ce798b9-4216-4775-ad6a-60ed66c91867",
									"trxid_bank": "DGP-7ce798b9-4216-4775-ad6a-60ed66c91867",
									"bankShort": "DANAMON",
									"recipient_name": "",
									"recipient_account": "7700173383",
									"amount": 10500,
									"note": "",
									"email": "",
									"schedule_date": ""
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
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function CheckTransfer(object $request)
	{
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/disburst_status'
			);
			$this->request['data'] = [
				'key' => $this->init->getMerchantKey(),
				'token' => $this->init->getMerchantToken(),
					'referenceId' => $request->reference_id,
					'accountNo' => $request->account_no,
			];
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
			]];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content)
				) {
					/* // Success
						{
							"success": true,
							"total_trx": 1,
							"data_payment": [{
								"id": "DGP-ID-4784922",
								"create_time": 1674652232,
								"status": "sukses",
								"statustrx_time": 1674652252,
								"referenceId": "1674652233",
								"paymentType": "disburst_c2",
								"Amount": 10500,
								"fee": "6500",
								"totalAmount": 17000,
								"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo",
								"paymentMethod": {
									"trans_hash": "b579dd09a412a6f9860b4f1cf3b7cec6",
									"trxid_bank": "DGP-7ce798b9-4216-4775-ad6a-60ed66c91867",
									"bankShort": "disburst_c2",
									"recipient_account": "7700173383",
									"recipient_name": null,
									"amount": 10500,
									"note": ""
								}
							}]
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) ($content->data_payment ?? $content),
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
