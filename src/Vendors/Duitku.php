<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;

use PyGPaymentLib\Helper;

class Duitku extends Requestor implements VendorInterface
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

	public function BankList()
	{
		try {
			$now = round(microtime(true) * 1000);
			$this->request['data'] = [
				'userId' => $this->init->getMID(),
				'email' => $this->init->getParams()['email'],
				'timestamp' => $now,
				'signature' => hash('sha256',
					$this->init->getParams()['email'] .
					$now .
					$this->init->getSecret()
				),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/webapi/api/disbursement/listBank'
			);
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($this->request['data'])),
			]];
			$this->request['option'] = [
				'as_json' => true,
				'timeout' => 60,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 200
			) {
				/*
					{
						"responseCode": "00",
						"responseDesc": "Success",
						"Banks": [{
							"bankCode": "014",
							"bankName": "PT BANK CENTRAL ASIA TBK.",
							"maxAmountTransfer": "25000000"
						}, {
							"bankCode": "002",
							"bankName": "PT BANK RAKYAT INDONESIA (PERSERO), TBK.",
							"maxAmountTransfer": "25000000"
						}, {
							"bankCode": "200",
							"bankName": "PT BANK TABUNGAN NEGARA (PERSERO)",
							"maxAmountTransfer": "0"
						}, {
							"bankCode": "008",
							"bankName": "PT. BANK MANDIRI (PERSERO), TBK.",
							"maxAmountTransfer": "25000000"
						}]
					}
				*/
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content->Banks)
				) {
					$res = [
						'status' => '000',
						'data' => array_map(function($item) {
							return [
								'name' => strtoupper($item->bankName),
								'code' => $item->bankCode,
								'limit' => (float) $item->maxAmountTransfer,
							];
						}, $content->Banks),
					];
				} else {
					throw new \Exception('Parse failed (' . ($content->responseDesc ?? 'Unknown status') . ')', 901);
				}
			} else {
				$content = is_string($content) ? $content :
					(is_array($content) ? json_encode($content) : 'UNKNOWN ERROR');
				throw new \Exception('Parse failed (' . $content . ')', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function CheckBalance()
	{
		try {
			$now = round(microtime(true) * 1000);
			$this->request['data'] = [
				'userId' => $this->init->getMID(),
				'email' => $this->init->getParams()['email'],
				'timestamp' => $now,
				'signature' => hash('sha256',
					$this->init->getParams()['email'] .
					$now .
					$this->init->getSecret()
				),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/webapi/api/disbursement/checkbalance'
			);
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($this->request['data'])),
			]];
			$this->request['option'] = [
				'as_json' => true,
				'timeout' => 60,
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
					/*
						{
							"userId": 26054,
							"email": "dev@vogame.co.id",
							"balance": 10000000.00,
							"effectiveBalance": 10000000.00,
							"responseCode": "00",
							"responseDesc": "Success"
						}
					*/
					$res = [
						'status' => '000',
						'data' => [
							'user_id' => $content->userId,
							'user_email' => $content->email,
							'total_balance' => $content->balance,
							'effective_balance' => $content->effectiveBalance,
						]
					];
				} else {
					throw new \Exception('Parse failed (' . ($content->responseDesc ?? 'Unknown status') . ')', 901);
				}
			} else {
				$content = is_string($content) ? $content :
					(is_array($content) ? json_encode($content) : 'UNKNOWN ERROR');
				throw new \Exception('Parse failed (' . $content . ')', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function OnlineTransferInquiry(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$now = round(microtime(true) * 1000);
			$this->request['data'] = [
				'userId' => $this->init->getMID(),
				'amountTransfer' => (int) $transaction->getAmount(),
				'bankAccount' => $transaction->getCustomerBankAccountNumber(),
				'bankCode' => $transaction->getCustomerBankName(),
				'email' => $this->init->getParams()['email'],
				'purpose' => $transaction->getDescription(),
				'timestamp' => $now,
				'senderId' => $transaction->getMerchantID(),
				'senderName' => $transaction->getMerchantName(),
				'signature' => hash('sha256',
					$this->init->getParams()['email'] .
					$now .
					$transaction->getCustomerBankName() .
					$transaction->getCustomerBankAccountNumber() .
					(int) $transaction->getAmount() .
					$transaction->getDescription() .
					$this->init->getSecret()
				),
				'custRefNumber' => $transaction->getOrderID(),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				// '/webapi/api/disbursement/inquirysandbox'
				'/webapi/api/disbursement/inquiry'
			);
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($this->request['data'])),
			]];
			$this->request['option'] = [
				'as_json' => true,
				'timeout' => 60,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 200
			) {
				// Parse data
				/*
					{
						"email": "dev@vogame.co.id",
						"bankCode": "014",
						"bankAccount": "8760673566",
						"amountTransfer": 10000.0,
						"accountName": "Test Account",
						"custRefNumber": "000000088986",
						"disburseId": 111273,
						"responseCode": "00",
						"responseDesc": "Success"
					}
				*/
				$content = (object) json_decode($content);
				if (
					!empty($content)
				) {
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception('Parse failed (' . ($content->responseDesc ?? 'Unknown status') . ')', 901);
				}
			} else {
				$content = is_string($content) ? $content :
					(is_array($content) ? json_encode($content) : 'UNKNOWN ERROR');
				throw new \Exception('Parse failed (' . $content . ')', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function OnlineTransferExec(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$now = round(microtime(true) * 1000);
			$this->request['data'] = [
				'disburseId' => $transaction->getTransactionID(),
				'userId' => $this->init->getMID(),
				'email' => $this->init->getParams()['email'],
				'bankCode' => $transaction->getCustomerBankName(),
				'bankAccount' => $transaction->getCustomerBankAccountNumber(),
				'amountTransfer' => (int) $transaction->getAmount(),
				'accountName' => $transaction->getCustomerBankAccountName(),
				'custRefNumber' => $transaction->getOrderID(),
				'purpose' => $transaction->getDescription(),
				'timestamp' => $now,
				'senderName' => $transaction->getMerchantName(),
				'signature' => hash('sha256',
					$this->init->getParams()['email'] .
					$now .
					$transaction->getCustomerBankName() .
					$transaction->getCustomerBankAccountNumber() .
					$transaction->getCustomerBankAccountName() .
					$transaction->getOrderID() .
					(int) $transaction->getAmount() .
					$transaction->getDescription() .
					$transaction->getTransactionID() .
					$this->init->getSecret()
				),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				// '/webapi/api/disbursement/transfersandbox'
				'/webapi/api/disbursement/transfer'
			);
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($this->request['data'])),
			]];
			$this->request['option'] = [
				'as_json' => true,
				'timeout' => 60,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 200
			) {
				// Parse data
				// Success
				/*
					{
						"email": "dev@vogame.co.id",
						"bankCode": "014",
						"bankAccount": "8760673566",
						"amountTransfer": 10000.0,
						"accountName": "Test Account",
						"custRefNumber": "000000089001",
						"responseCode": "00",
						"responseDesc": "Success"
					}
				*/
				$content = (object) json_decode($content);
				if (
					!empty($content)
				) {
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception('Parse failed (' . ($content->responseDesc ?? 'Unknown status') . ')', 901);
				}
			} else {
				$content = is_string($content) ? $content :
					(is_array($content) ? json_encode($content) : 'UNKNOWN ERROR');
				throw new \Exception('Parse failed (' . $content . ')', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function ClearingInquiry(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$now = round(microtime(true) * 1000);
			$this->request['data'] = [
				'userId' => $this->init->getMID(),
				'amountTransfer' => (int) $transaction->getAmount(),
				'bankAccount' => $transaction->getCustomerBankAccountNumber(),
				'bankCode' => $transaction->getCustomerBankName(),
				'email' => $this->init->getParams()['email'],
				'purpose' => $transaction->getDescription(),
				'type' => $transaction->getTransferMethod(),
				'timestamp' => $now,
				'senderId' => $transaction->getMerchantID(),
				'senderName' => $transaction->getMerchantName(),
				'signature' => hash('sha256',
					$this->init->getParams()['email'] .
					$now .
					$transaction->getCustomerBankName() .
					$transaction->getTransferMethod() .
					$transaction->getCustomerBankAccountNumber() .
					(int) $transaction->getAmount() .
					$transaction->getDescription() .
					$this->init->getSecret()
				),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				// '/webapi/api/disbursement/inquiryclearingsandbox'
				'/webapi/api/disbursement/inquiryclearing'
			);
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($this->request['data'])),
			]];
			$this->request['option'] = [
				'as_json' => true,
				'timeout' => 60,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 200
			) {
				// Parse data
				// Failed
				/*
					{
						"email": "dev@vogame.co.id",
						"bankCode": "014",
						"bankAccount": "8760673566",
						"amountTransfer": 10000.0,
						"accountName": "",
						"custRefNumber": "",
						"disburseId": 0,
						"type": "RTGS",
						"responseCode": "-141",
						"responseDesc": "Amount transfer cannot below 100.000.000"
					}
				*/
				// Success
				/*
					{
						"email": "dev@vogame.co.id",
						"bankCode": "014",
						"bankAccount": "8760673466",
						"amountTransfer": 150000000.0,
						"accountName": "Test Account",
						"custRefNumber": "000000088998",
						"disburseId": 111285,
						"type": "RTGS",
						"responseCode": "00",
						"responseDesc": "Approved or completed successfully"
					}
				*/
				$content = (object) json_decode($content);
				if (
					!empty($content)
				) {
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception('Parse failed (' . ($content->responseDesc ?? 'Unknown status') . ')', 901);
				}
			} else {
				$content = is_string($content) ? $content :
					(is_array($content) ? json_encode($content) : 'UNKNOWN ERROR');
				throw new \Exception('Parse failed (' . $content . ')', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function ClearingExec(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$now = round(microtime(true) * 1000);
			$signature =
				$this->init->getParams()['email'] .
				$now .
				$transaction->getCustomerBankName() .
				$transaction->getTransferMethod() .
				$transaction->getCustomerBankAccountNumber() .
				$transaction->getCustomerBankAccountName() .
				$transaction->getOrderID() .
				(int) $transaction->getAmount() .
				$transaction->getDescription() .
				$transaction->getTransactionID() .
				$this->init->getSecret();
			$this->request['data'] = [
				'disburseId' => $transaction->getTransactionID(),
				'userId' => $this->init->getMID(),
				'email' => $this->init->getParams()['email'],
				'bankCode' => $transaction->getCustomerBankName(),
				'bankAccount' => $transaction->getCustomerBankAccountNumber(),
				'amountTransfer' => (int) $transaction->getAmount(),
				'accountName' => $transaction->getCustomerBankAccountName(),
				'custRefNumber' => $transaction->getOrderID(),
				'purpose' => $transaction->getDescription(),
				'type' => $transaction->getTransferMethod(),
				'timestamp' => $now,
				'signature' => hash('sha256', $signature),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				// '/webapi/api/disbursement/transferclearingsandbox'
				'/webapi/api/disbursement/transferclearing'
			);
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($this->request['data'])),
			]];
			$this->request['option'] = [
				'as_json' => true,
				'timeout' => 60,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 200
			) {
				// Parse data
				// Success
				/*
					{
						"email": "dev@vogame.co.id",
						"bankCode": "014",
						"bankAccount": "8760673566",
						"amountTransfer": 10000.0,
						"accountName": "Test Account",
						"custRefNumber": "000000089001",
						"responseCode": "00",
						"responseDesc": "Success"
					}
				*/
				$content = (object) json_decode($content);
				if (
					!empty($content)
				) {
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception('Parse failed (' . ($content->responseDesc ?? 'Unknown status') . ')', 901);
				}
			} else {
				$content = is_string($content) ? $content :
					(is_array($content) ? json_encode($content) : 'UNKNOWN ERROR');
				throw new \Exception('Parse failed (' . $content . ')', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function CheckTransfer(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$now = round(microtime(true) * 1000);
			$signature =
				$this->init->getParams()['email'] .
				$now .
				$transaction->getTransactionID() .
				$this->init->getSecret();
			$this->request['data'] = [
				'disburseId' => $transaction->getTransactionID(),
				'userId' => $this->init->getMID(),
				'email' => $this->init->getParams()['email'],
				'timestamp' => $now,
				'signature' => hash('sha256', $signature),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/webapi/api/disbursement/inquirystatus'
			);
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($this->request['data'])),
			]];
			$this->request['option'] = [
				'as_json' => true,
				'timeout' => 60,
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
							"bankCode": "014",
							"bankAccount": "8760673566",
							"amountTransfer": 10000.00,
							"accountName": "Test Account",
							"custRefNumber": "000000089056",
							"responseCode": "00",
							"responseDesc": "Success"
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception('Parse failed (' . ($content->responseDesc ?? 'Unknown status') . ')', 901);
				}
			} else {
				$content = is_string($content) ? $content :
					(is_array($content) ? json_encode($content) : 'UNKNOWN ERROR');
				throw new \Exception('Parse failed (' . $content . ')', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}



	/**
	 *
	 * Payment
	 *
	 */

	public function RedirectPayment(\PyGPaymentLib\Transaction $transaction)
	{
		$data = (object) $this->SecurePayment($transaction);
		$content = (object) json_decode($data->response['content']);
		if ($content->data->paymentUrl) {
			$payment_url = $content->data->paymentUrl;
		}
		header('location: ' . $payment_url);
	}

	public function SecurePayment(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->transaction = $transaction;
			//
			$this->form['order_id'] = $this->transaction->getOrderID();
			$this->form['invoice_no'] = $this->transaction->getInvoiceNo();
			$this->form['amount'] = $this->transaction->getAmount();
			$this->form['description'] = $this->transaction->getDescription();
			$this->form['currency'] = $this->transaction->getCurrency();
			//
			$this->form['customer_name'] = $this->transaction->getCustomerName();
			$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			$this->form['customer_address'] = $this->transaction->getCustomerAddress();
			$this->form['customer_postal_code'] = $this->transaction->getPostalCode();
			$this->form['customer_city'] = $this->transaction->getCustomerCity();
			$this->form['customer_country_code'] = $this->transaction->getCountryCode();
			//
			$this->form['billing_address'] = [
				'firstName' => $this->form['customer_name'],
				'lastName' => '',
				'address' => $this->form['customer_address'],
				'city' => $this->form['customer_city'] ?? 'Jakarta',
				'postalCode' => $this->form['customer_postal_code'],
				'phone' => $this->form['customer_phone'],
				'countryCode' => $this->form['customer_country_code'],
			];
			$this->form['shipping_address'] = [
				'firstName' => $this->form['customer_name'],
				'lastName' => '',
				'address' => $this->form['customer_address'],
				'city' => $this->form['customer_city'] ?? 'Jakarta',
				'postalCode' => $this->form['customer_postal_code'],
				'phone' => $this->form['customer_phone'],
				'countryCode' => $this->form['customer_country_code'],
			];
			$this->form['customer_details'] = [
				'firstName' => $this->form['customer_name'],
				'lastName' => '',
				'email' => $this->form['customer_email'],
				'phoneNumber' => $this->form['customer_phone'],
				'billingAddress' => $this->form['billing_address'],
				'shippingAddress' => $this->form['shipping_address'],
			];
			// VC	Credit Card (Visa / Master)
			// BK	BCA KlikPay
			// BC	BCA Virtual Account
			// M1	Mandiri Virtual Account (Deprecated)
			// M2	Mandiri Virtual Account
			// BT	Permata Bank Virtual Account
			// A1	ATM Bersama
			// B1	CIMB Niaga Virtual Account
			// I1	BNI Virtual Account
			// VA	Maybank Virtual Account
			// FT	Ritel
			// OV	OVO
			// DN	Indodana Paylater
			// SP	Shopee Pay
			// SA	Shopee Pay Apps
			// AG	Bank Artha Graha
			// S1	Bank Sahabat Sampoerna
			// LA	LinkAja Apps (Percentage Fee)
			// LF	LinkAja Apps (Fixed Fee)
			// LQ	LinkAja QRIS
			// DA	DANA
			$this->form['payment_method'] = $this->transaction->getPaymentMethod();
			// $this->form['payment_url'] = $this->init->getPaymentURL() . '/v2/inquiry';
			$this->form['payment_url'] = $this->init->getPaymentURL() . '/webapi/api/merchant/v2/inquiry';
			$this->form['callback_url'] = $this->init->getCallbackURL();
			$this->form['parser_url'] = $this->init->getParserURL();
			// Redirect
			// merchantOrderId: Nomor transaksi dari merchant abcde12345
			// reference: Nomor referensi transaksi dari Duitku. Mohon disimpan untuk keperluan pencatatan atau pelacakan transaksi. d011111
			// resultCode: Hasil status transaksi
			// 00 - Success
			// 01 - Pending
			// 02 - Canceled
			$this->form['return_url'] = $this->init->getReturnURL();
			// Request argseter
			$this->form['expiry_period'] = (int) $this->transaction->getExpireIn(); // minutes
			$this->form['signature'] = md5(
				$this->init->getMID() .
					$this->form['order_id'] .
					(float) $this->form['amount'] .
					$this->init->getSecret()
			);
			// Go
			// $this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = SELF::CleanURL($this->form['payment_url']);
			$this->request['data'] = [
				'merchantCode' => $this->init->getMID(),
				'paymentAmount' => $this->form['amount'],
				'paymentMethod' => $this->form['payment_method'],
				'merchantOrderId' => $this->form['order_id'],
				'productDetails' => $this->form['description'],
				'additionalargs' => '', // optional
				'merchantUserInfo' => '', // optional
				'customerVaName' => $this->form['customer_name'],
				'email' => $this->form['customer_email'],
				'phoneNumber' => $this->form['customer_phone'],
				'itemDetails' => [],
				'customerDetail' => $this->form['customer_details'],
				'callbackUrl' => $this->form['callback_url'],
				'returnUrl' => $this->form['return_url'],
				'signature' => $this->form['signature'],
				'expiryPeriod' => $this->form['expiry_period'],
			];
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($this->request['data'])),
			]];
			$this->request['option'] = [
				'to_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			// Success
			/*
			{
				"merchantCode": "D6677",
				"reference": "D66774XLQY7KUDPP0WGA",
				"paymentUrl": "http://sandbox.duitku.com/topup/topupdirectv2.aspx?ref=B1Q3XQMO6CY44R7F2",
				"vaNumber": "11990140616361",
				"amount": "100000",
				"statusCode": "00",
				"statusMessage": "SUCCESS"
			}
			*/
			/*
			if (
				!empty($status_code)
				&& $status_code === 200
			) {
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content->statusMessage)
					&& $content->statusMessage == "SUCCESS"
				) {
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception('Parse failed (' . ($content->statusMessage ?? 'Unknown status') . ')', 901);
				}
			} else {
				$content = is_string($content) ? $content :
					(is_array($content) ? json_encode($content) : 'UNKNOWN ERROR');
				throw new \Exception('Parse failed (' . $content . ')', 902);
			}
			*/
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->statusMessage)
					&& $content->statusMessage == "SUCCESS"
				) {
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
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function ParsePaymentPage($payment_url, $param = '')
	{
		try {
			if (!empty($this->transaction->getPaymentMethod())
				&& $this->transaction->getPaymentMethod() == 'VC'
			) {
				$card = [
					'name' => $this->transaction->getCardHolderName(),
					'card' => $this->transaction->getCardNumber(),
					'month' => $this->transaction->getCardExpMonth(),
					'year' => $this->transaction->getCardExpYear(),
					'cvv' => $this->transaction->getCardCVV(),
				];
				$this->request['url'] = SELF::CleanURL(
					$this->init->getParserURL() .
					'duitku' . '/' .
					'cc' . '/' .
					'init' . '/' .
					base64_encode($payment_url) . '/' .
					base64_encode(json_encode($card))
				);
			} else { // OVO
				$this->request['url'] = SELF::CleanURL(
					$this->init->getParserURL() .
					'duitku' . '/' .
					'ewallet' . '/' .
					'ovo' . '/' .
					base64_encode($payment_url) . '/' .
					base64_encode($param)
				);
			}
			// Go
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
					!empty($content->status)
					&& $content->status == '000'
				) {
					// Success
					/*
					{
						"status": "000",
						"data": {
							"payment_url": "https://dev.faspay.co.id/pws/100003/0830000010100000/a377f2971f1f2d666e14d611fef2ed7af21cdec0?trx_id=3366080100000018&merchant_id=33660&bill_no=1612769989",
							"va_number": "3366080100000018"
						}
					}
					*/
					$res = $content;
				} else {
					throw new \Exception('Parse failed (' . ($content->status ?? 'Unknown status') . ')', 901);
				}
			} else {
				$content = is_string($content) ? $content :
					(is_array($content) ? json_encode($content) : 'UNKNOWN ERROR');
				throw new \Exception('Parse failed (' . $content . ')', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function Callback(object $request)
	{
		// Example incoming data
		/*
        {
			"merchantCode": "D6677",
			"amount": "100000",
			"merchantOrderId": "0001285662",
			"productDetail": "Payment for order 0001285662",
			"additionalParam": null,
			"resultCode": "00",
			"signature": "439030a6da086ee13558137f07d4a27d",
			"paymentCode": "VC",
			"merchantUserId": null,
			"reference": "D6677JXVYL752HMAV0AD"
        }
         */
		try {
			SELF::Validate($request, ['amount', 'merchantOrderId', 'signature']);
			$signature = md5(
				$this->init->getMID() .
				(float) $request->amount .
				$request->merchantOrderId .
				$this->init->getSecret()
			);
			if (strcmp($signature, $request->signature) === 0) {
				$res = [
					'status' => '000',
					'data' => (array) $request,
				];
			} else {
				throw new \Exception('Signature check failed', 902);
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
			SELF::Validate($request, ['order_id']);
			$signature = md5(
				$this->init->getMID() .
				$request->order_id .
				$this->init->getSecret()
			);
			// Go
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				// $this->init->getRequestURL() . '/transactionStatus'
				$this->init->getRequestURL() . '/webapi/api/merchant/transactionStatus'
			);
			$this->request['data'] = [
				'merchantCode' => $this->init->getMID(),
				'merchantOrderId' => $request->order_id,
				'signature' => $signature,
			];
			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($this->request['data'])),
			]];
			$this->request['option'] = [
				'to_json' => false,
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
					!empty($content->statusMessage)
					&& $content->merchantOrderId == $request->order_id
				) {
					// Success
					/*
					{
						"merchantOrderId": "0001297441",
						"reference": "D6677KW403DFH8VOFMRJ",
						"amount": "100000",
						"fee": "0.00",
						"statusCode": "00",
						"statusMessage": "SUCCESS"
					}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception('Parse failed (' . ($content->statusMessage ?? 'Unknown status') . ')', 901);
				}
			} else {
				$content = is_string($content) ? $content :
					(is_array($content) ? json_encode($content) : 'UNKNOWN ERROR');
				throw new \Exception('Parse failed (' . $content . ')', 902);
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
}
