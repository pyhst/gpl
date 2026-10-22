<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

use Growinc\Payment\Helper;

class NowPayments extends Requestor implements VendorInterface
{
	use Helper;

	protected $form;
	protected $access_token;
	protected $max_retry = 3;

	//

	public function SetAccessToken($access_token)
	{
		$this->access_token = $access_token;
	}

	public function GetAccessToken() // : string
	{
		return $this->access_token;
	}

	public function GetTokenFile($assoc_file = 'token') : string
	{
		return __DIR__ . '/_temp_' . $assoc_file . '_' .
			(new \ReflectionClass($this))->getShortName() . '_' .
			$this->init->getMID() . '_' .
			'.txt';
	}

	public function GetTokenFileContent($assoc_file = 'token')
	{
		if (file_exists($this->GetTokenFile($assoc_file))) {
			return file_get_contents($this->GetTokenFile($assoc_file));
		}
	}

	public function CheckToken()
	{
		// Get temp token
		if (!empty($this->GetTokenFileContent()) && SELF::is_JSON($this->GetTokenFileContent())) {
			$token = json_decode($this->GetTokenFileContent(), true);
			$this->SetAccessToken($token['data']['access_token']);
		} else {
			// Get token
			if (empty($this->GetAccessToken())) {
				// Request new
				$this->SetAccessToken($this->GetToken());
			}
		}
	}

	//

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args)
	{
		$time = time();
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v1/auth'
			);
			$this->request['data'] = [
				'email' => $this->init->getParam('NOWPAYMENTS_EMAIL'),
				'password' => $this->init->getParam('NOWPAYMENTS_PASSWORD'),
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				// Parse data
				$content = (object) json_decode($content);
				if (!empty($content->token)) {
					/* // Success
						{
							"token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjQ3NjUyNTAxOTkiLCJpYXQiOjE3ODE4NTMzOTYsImV4cCI6MTc4MTg1MzY5Nn0.iMBZIXj-ojbMEbjR6peobnzbEFRVu_qaEPMZTiwxRDI"
						}
					*/
					file_put_contents($this->GetTokenFile(), json_encode($content));
					$this->SetAccessToken($content->token);
					//
					$res = (string) $content->token;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
			throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 902);
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

	/*------------------------------v BEGIN OF Currencies v------------------------------------------------------------ */

	public function GetCurrencies(\Growinc\Payment\Transaction $transaction)
	{
		$time = time();
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v1/currencies?fixed_rate=true' // This is a method for obtaining information about all cryptocurrencies available for payments for your current setup.
			);
			$this->request['data'] = [
				'fixed_rate' => (string) $transaction->getParam('FIXED_RATE'),
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'x-api-key' => $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => false,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if ($status_code == 200 && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (!empty($content)
					&& !empty($content->currencies)
				) {
					/* // Success
						{
							"currencies": [
								{
										"min_amount": 0.0039898,
										"max_amount": 34.9265791415124,
										"currency": "eth"
								},
								{
										"min_amount": 7.243361,
										"max_amount": 61117.295731,
										"currency": "usdterc20"
								}
							]
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

	public function GetCurrenciesAlt(\Growinc\Payment\Transaction $transaction)
	{
		$time = time();
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v1/full-currencies' // This is a method to obtain detailed information about all cryptocurrencies available for payments.
			);
			$this->request['data'] = [
				'fixed_rate' => (string) $transaction->getParam('FIXED_RATE'),
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'x-api-key' => $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => false,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if ($status_code == 200 && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (!empty($content)
					&& !empty($content->currencies)
				) {
					/* // Success
						{
							"currencies": [
								{
										"id": 15,
										"code": "ETH",
										"name": "Ethereum",
										"enable": true,
										"wallet_regex": "^(0x)[0-9A-Fa-f]{40}$",
										"priority": 1,
										"extra_id_exists": false,
										"extra_id_regex": null,
										"logo_url": "/images/coins/eth.svg",
										"track": true,
										"cg_id": "ethereum",
										"is_maxlimit": false,
										"network": "eth",
										"smart_contract": null,
										"network_precision": "18",
										"explorer_link_hash": "https://eth-explorer.nownodes.io/tx/:hash",
										"precision": 8,
										"ticker": "eth",
										"is_defi": false,
										"is_popular": true,
										"is_stable": false,
										"available_for_to_conversion": true,
										"trust_wallet_id": "c60_t",
										"created_at": "2024-08-28T16:06:33.998Z",
										"updated_at": "2025-11-28T11:25:02.086Z",
										"available_for_payment": true,
										"available_for_payout": true,
										"extra_id_optional": false
								},
								{
										"id": 30,
										"code": "USDTERC20",
										"name": "Tether USD (Ethereum)",
										"enable": true,
										"wallet_regex": "^(0x)[0-9A-Fa-f]{40}$",
										"priority": 29,
										"extra_id_exists": false,
										"extra_id_regex": null,
										"logo_url": "/images/coins/usdterc20.svg",
										"track": true,
										"cg_id": "tether",
										"is_maxlimit": false,
										"network": "eth",
										"smart_contract": "0xdAC17F958D2ee523a2206206994597C13D831ec7",
										"network_precision": "6",
										"explorer_link_hash": "https://eth-explorer.nownodes.io/tx/:hash",
										"precision": 6,
										"ticker": "usdt",
										"is_defi": false,
										"is_popular": false,
										"is_stable": true,
										"available_for_to_conversion": true,
										"trust_wallet_id": null,
										"created_at": "2024-08-28T16:06:33.998Z",
										"updated_at": "2025-11-28T11:25:03.019Z",
										"available_for_payment": true,
										"available_for_payout": true,
										"extra_id_optional": false
								}
							]
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

	public function GetCheckedCurrencies(\Growinc\Payment\Transaction $transaction)
	{
		$time = time();
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v1/merchant/coins' // This is a method for obtaining information about the cryptocurrencies available for payments. Shows the coins you set as available for payments in the "coins settings" tab on your personal account.
			);
			$this->request['data'] = [];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'x-api-key' => $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => false,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if ($status_code == 200 && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (!empty($content)
					&& !empty($content->selectedCurrencies)
				) {
					/* // Success
						{
							"selectedCurrencies": [
								"ETH",
								"BTC",
								"DOGE",
								"SOL",
								"USDC",
								"USDTERC20"
							]
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

	/*------------------------------^ END OF Currencies ^-------------------------------------------------------------- */

	/*------------------------------v BEGIN OF Payments v------------------------------------------------------------ */

	public function GetMinPaymentAmount(\Growinc\Payment\Transaction $transaction)
	{
		$time = time();
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v1/min-amount' // Get the minimum payment amount for a specific pair.
			);
			$this->request['data'] = [
				'currency_from' => $transaction->getParam('CURRENCY_FROM'),
				'currency_to' => $transaction->getParam('CURRENCY_TO'),
				'fiat_equivalent' => $transaction->getParam('FIAT_EQUIVALENT'),
				'is_fixed_rate' => $transaction->getParam('IS_FIXED_RATE'),
				'is_fee_paid_by_user' => $transaction->getParam('IS_FEE_PAID_BY_USER'),
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'x-api-key' => $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => false,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if ($status_code == 200 && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (!empty($content)
					&& !empty($content->min_amount)
				) {
					/* // Success
						{
							"currency_from": "usdterc20",
							"currency_to": "idr",
							"min_amount": 7.468842,
							"fiat_equivalent": 132560.78776453
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

	public function GetEstimatedPrice(\Growinc\Payment\Transaction $transaction)
	{
		$time = time();
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v1/estimate' // This is a method for calculating the approximate price in cryptocurrency for a given value in Fiat currency. You will need to provide the initial cost in the Fiat currency (amount, currency_from) and the necessary cryptocurrency (currency_to). Currently following fiat currencies are available: USD, EUR, CAD, GBP, AUD, ILS, RON.
			);
			$this->request['data'] = [
				'currency_from' => $transaction->getParam('CURRENCY_FROM'),
				'currency_to' => $transaction->getParam('CURRENCY_TO'),
				'amount' => $transaction->getParam('AMOUNT'),
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'x-api-key' => $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => false,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if ($status_code == 200 && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (!empty($content)
					&& !empty($content->estimated_amount)
				) {
					/* // Success
						{
							"currency_from": "idr",
							"amount_from": 50000,
							"currency_to": "usdterc20",
							"estimated_amount": "2.80170249"
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

	/*------------------------------^ END OF Payments ^-------------------------------------------------------------- */

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
		$time = time();
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v1/invoice' // Creates a payment link. With this method, the customer is required to follow the generated url to complete the payment. Data must be sent as a JSON-object payload.
			);
			$this->request['data'] = [
				'price_amount' => (string) $transaction->getAmount(),
				'price_currency' => $transaction->getParam('PRICE_CURRENCY'),
				'pay_currency' => $transaction->getParam('PAY_CURRENCY'),
				'ipn_callback_url' => $transaction->getParam('IPN_CALLBACK_URL'),
				'order_id' => $transaction->getReferenceNumber(),
				'order_description' => $transaction->getDescription(),
				'success_url' => $this->init->getReturnURL(),
				'cancel_url' => $this->init->getCallbackURL(),
				'is_fixed_rate' => $transaction->getParam('IS_FIXED_RATE'),
				'is_fee_paid_by_user' => $transaction->getParam('IS_FEE_PAID_BY_USER'),
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'x-api-key' => $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if ($status_code == 200 && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (!empty($content)
					&& !empty($content->id)
				) {
					/* // Success
						{
							"id": "6080679611",
							"token_id": "5603406770",
							"order_id": "INV1781800664",
							"order_description": "Payment for ",
							"price_amount": "15000",
							"price_currency": "IDR",
							"pay_currency": "USDTERC20",
							"ipn_callback_url": "https://g-pay.to/secure/callback/sP9xHwzVs2JmPznkkkGw6kb60",
							"invoice_url": "https://nowpayments.io/payment/?iid=6080679611",
							"success_url": "https://g-pay.to/secure/callback/sP9xHwzVs2JmPznkkkGw6kb60",
							"cancel_url": "https://g-pay.to/secure/callback/sP9xHwzVs2JmPznkkkGw6kb60",
							"customer_email": null,
							"partially_paid_url": null,
							"payout_currency": null,
							"created_at": "2026-06-18T16:37:44.917Z",
							"updated_at": "2026-06-18T16:37:44.917Z",
							"is_fixed_rate": false,
							"is_fee_paid_by_user": false,
							"source": null,
							"collect_user_data": false
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
		/* // Success
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
		$time = time();
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->GetToken([]);
			$token_retry = 0;
			retry:
			// Go
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v1/payment' // Returns the entire list of all transactions created with certain API key.
			);
			$this->request['data'] = [
				'invoiceId' => $request->invoice_id,
				'limit' => $request->limit ?? 100,
				'page' => $request->page ?? 0,
				'sortBy' => $request->sort_by ?? 'created_at',
				'orderBy' => $request->order_by ?? 'DESC',
				'dateFrom' => $request->start_date,
				'dateTo' => $request->end_date,
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $this->GetAccessToken(),
				'x-api-key' => $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => false,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if ($status_code == 200 && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (isset($content->data)) {
					/* // Success inquiry
						{
							"data": [
								{
										"payment_id": 4717947654,
										"invoice_id": 5474867878,
										"payment_status": "finished",
										"pay_address": "0x96D19AB250e4fe8AB59efa434aa51d9b4998de82",
										"payin_extra_id": null,
										"price_amount": 15000,
										"price_currency": "idr",
										"pay_amount": 0.842143,
										"actually_paid": 0.842143,
										"pay_currency": "usdterc20",
										"order_id": "INV1781853882",
										"order_description": "Payment of INV1781853882",
										"purchase_id": 4915897946,
										"outcome_amount": 0.495892,
										"outcome_currency": "usdterc20",
										"payout_hash": "partner_liability_tx_a560ae84bc9bd32be416aa0016d66f9abe1001659e69e00233bbd38fb636b4ec",
										"payin_hash": "0xa10b0fe25b1bcae7f3e9dd811bad5122097cd5b2c9418d9ec4925dbbf0fdfe35",
										"created_at": "2026-06-19T07:25:15.802Z",
										"updated_at": "2026-06-19T07:31:20.233Z",
										"type": "crypto2crypto"
								}
							],
							"limit": 100,
							"page": 0,
							"pagesCount": 1,
							"total": 5
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} elseif (stripos($content->rm, 'INVALID_AUTH_TOKEN') !== false) {
					// Failed token
					$token_retry++;
					if ($token_retry > $this->max_retry) throw new \Exception('Max token retry reached', 800);
					// Request new
					$this->SetAccessToken($this->GetToken());
					goto retry;
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
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v1/balance'
			);
			$this->request['headers'] = [
				'On-Key' => $this->init->getMerchantKey(),
				'On-Token' => $this->init->getMerchantToken(),
				'On-Signature' => hash_hmac('sha512', $this->init->getMerchantKey(), $this->init->getMerchantToken()),
				'Accept' => 'application/json',
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'x-api-key' => $this->init->getMID(),
			];
			$this->request['option'] = [
				'as_json' => false,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if ($status_code == 200 && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (!empty($content)) {
					/* // Success
						{
							"usdterc20": {
								"amount": 0.495892,
								"pendingAmount": 0
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => [
							'active_balance' => $content->usdterc20->amount,
							'pending_balance' => $content->usdterc20->pendingAmount,
							'total_balance' => $content->usdterc20->amount + $content->usdterc20->pendingAmount,
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
		// Inapplicable
	}

	public function OnlineTransferExec(\Growinc\Payment\Transaction $transaction)
	{
		// Inapplicable
	}

	public function CheckTransfer(object $request)
	{
		// Inapplicable
	}

}
