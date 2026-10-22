<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

use Growinc\Payment\Helper;

class CoinPayments extends Requestor implements VendorInterface
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

	public function _GetSignature(string $method, string $url, string $timestamp, $data)
	{
		$string_to_sign = implode('', [
			"\xEF\xBB\xBF",
			$method,
			$url,
			$this->init->getMID(),
			$timestamp,
			json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
		]);
		$hashed = hash_hmac('sha256', $string_to_sign, $this->init->getSecret(), true);
		$base64 = base64_encode($hashed);
		return $base64;
	}

	//

	/*------------------------------v BEGIN OF Currencies v------------------------------------------------------------ */

	public function GetCurrencies(\Growinc\Payment\Transaction $transaction)
	{
		$time = time();
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v2/currencies' // lists platform supported currencies and their capabilities.
			);
			$this->request['data'] = [
				'q' => $transaction->getParam('QUERY_FILTER'),
				'types' => [
					'coin',
					'token',
					'fiat',
				],
				'capabilities' => [
					'payments',
					'singleSigAccounts',
					'pooledAccounts',
					'utxo',
					'conversion',
					'onRamp',
					'offRamp'
				],
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-CoinPayments-Client' => $this->init->getMID(),
				'X-CoinPayments-Timestamp' => $timestamp,
				'X-CoinPayments-Signature' => $this->_GetSignature('GET', $this->request['url'], $timestamp, $this->request['data']),
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
						[
							{
								"id": "1",
								"type": "crypto",
								"symbol": "BTC",
								"name": "Bitcoin",
								"logo": {
									"imageUrl": "https://a-api.coinpayments.net/api/v1/currencies/1/logosvg"
								},
								"decimalPlaces": 8,
								"rank": 1,
								"status": "active",
								"capabilities": [
									"singleSigAccounts",
									"utxo",
									"conversion",
									"onRamp",
									"offRamp",
									"payments"
								],
								"urls": {
									"websites": [
										"https://bitcoin.org"
									],
									"explorers": [
										"https://blockchain.info"
									]
								},
								"requiredConfirmations": 2,
								"isEnabledForPayment": true
							},
							{
								"id": "4",
								"type": "crypto",
								"symbol": "ETH",
								"name": "Ethereum",
								"logo": {
										"imageUrl": "https://a-api.coinpayments.net/api/v1/currencies/4/logosvg"
								},
								"decimalPlaces": 18,
								"rank": 2,
								"status": "active",
								"capabilities": [
										"singleSigAccounts",
										"pooledAccounts",
										"conversion",
										"onRamp",
										"offRamp",
										"payments"
								],
								"urls": {
										"websites": [
											"https://www.ethereum.org/"
										],
										"explorers": [
											"https://etherscan.io/"
										]
								},
								"requiredConfirmations": 3,
								"isEnabledForPayment": true
							},
							{
								"id": "4:0xdac17f958d2ee523a2206206994597c13d831ec7",
								"type": "token",
								"symbol": "USDT.ERC20",
								"name": "Tether USD",
								"logo": {
									"imageUrl": "https://a-api.coinpayments.net/api/v1/currencies/4:0xdac17f958d2ee523a2206206994597c13d831ec7/logosvg"
								},
								"decimalPlaces": 6,
								"rank": 2,
								"status": "active",
								"capabilities": [
									"singleSigAccounts",
									"pooledAccounts",
									"conversion",
									"onRamp",
									"offRamp",
									"payments"
								],
								"urls": {
									"websites": [
										"https://www.ethereum.org/"
									],
									"explorers": [
										"https://etherscan.io/"
									]
								},
								"requiredConfirmations": 3,
								"isEnabledForPayment": true
							},
							{
								"id": "5057",
								"type": "fiat",
								"symbol": "USD",
								"name": "United States Dollar",
								"logo": {
									"imageUrl": "https://a-api.coinpayments.net/api/v1/currencies/5057/logosvg"
								},
								"decimalPlaces": 2,
								"rank": 8000,
								"status": "active",
								"capabilities": [
									"conversion",
									"onRamp",
									"offRamp"
								],
								"urls": {
									"websites": [],
									"explorers": []
								},
								"requiredConfirmations": 0,
								"isEnabledForPayment": false
							},
							{
								"id": "2",
								"type": "crypto",
								"symbol": "LTC",
								"name": "Litecoin",
								"logo": {
									"imageUrl": "https://a-api.coinpayments.net/api/v1/currencies/2/logosvg"
								},
								"decimalPlaces": 8,
								"rank": 8,
								"status": "active",
								"capabilities": [
									"singleSigAccounts",
									"utxo",
									"conversion",
									"onRamp",
									"offRamp",
									"payments"
								],
								"urls": {
									"websites": [
										"https://litecoin.org"
									],
									"explorers": [
										"https://blockchair.com/litecoin/"
									]
								},
								"requiredConfirmations": 3,
								"isEnabledForPayment": true
							},
							{
								"id": "1002",
								"type": "crypto",
								"symbol": "LTCT",
								"name": "Litecoin Test",
								"logo": {
									"imageUrl": "https://a-api.coinpayments.net/api/v1/currencies/1002/logosvg"
								},
								"decimalPlaces": 8,
								"rank": 8,
								"status": "active",
								"capabilities": [
									"singleSigAccounts",
									"utxo",
									"conversion",
									"onRamp",
									"offRamp",
									"payments"
								],
								"urls": {
									"websites": [
										"https://litecoin.org"
									],
									"explorers": [
										"https://tltc.bitaps.com/"
									]
								},
								"requiredConfirmations": 3,
								"isEnabledForPayment": true
							},
							{
								"id": "4577",
								"type": "fiat",
								"symbol": "IDR",
								"name": "Indonesian Rupiah",
								"logo": {
										"imageUrl": "https://a-api.coinpayments.net/api/v1/currencies/4577/logosvg"
								},
								"decimalPlaces": 2,
								"rank": 9180,
								"status": "active",
								"capabilities": [
										"conversion",
										"onRamp",
										"offRamp"
								],
								"urls": {
										"websites": [],
										"explorers": []
								},
								"requiredConfirmations": 0,
								"isEnabledForPayment": false
							}
						]
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

	public function GetMerchantCurrencies(\Growinc\Payment\Transaction $transaction)
	{
		$time = time();
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v1/merchant/currencies' // Gets the merchant's currently accepted currencies. Currencies that are ranked (ordered) will be returned at the top of the list.
			);
			$this->request['data'] = [];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-CoinPayments-Client' => $this->init->getMID(),
				'X-CoinPayments-Timestamp' => $timestamp,
				'X-CoinPayments-Signature' => $this->_GetSignature('GET', $this->request['url'], $timestamp, $this->request['data']),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if ($status_code == 200 && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (!empty($content->items)) {
					/* // Success
						{
							"items": [
								{
										"currency": {
											"id": "1002",
											"type": "crypto",
											"symbol": "LTCT",
											"name": "Litecoin Test",
											"logo": {
												"imageUrl": "https://a-api.coinpayments.net/api/v1/currencies/1002/logosvg"
											},
											"decimalPlaces": 8,
											"rank": 8,
											"status": "active",
											"capabilities": [
												"singleSigAccounts",
												"utxo",
												"conversion",
												"onRamp",
												"offRamp"
											],
											"urls": {
												"websites": [
														"https://litecoin.org"
												],
												"explorers": [
														"https://tltc.bitaps.com/"
												]
											},
											"requiredConfirmations": 3,
											"isEnabledForPayment": true
										},
										"payoutFrequency": "asSoonAsPossible",
										"order": 8,
										"discountPercent": 0,
										"markupPercent": 0,
										"payoutConfigurations": [
											{
												"currencyId": "1002",
												"walletId": "926ce76f-ccbd-4c2e-8f52-85dfde81a928"
											}
										],
										"switcherStatus": "enabled"
								}
							],
							"paging": {
								"cursors": null
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content->items,
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

	public function GetConversionPairs(\Growinc\Payment\Transaction $transaction)
	{
		$time = time();
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v2/currencies/conversions' // gets a list of all possible currency conversions
			);
			$this->request['data'] = [];
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-CoinPayments-Client' => $this->init->getMID(),
				'X-CoinPayments-Timestamp' => $timestamp,
				'X-CoinPayments-Signature' => $this->_GetSignature('GET', $this->request['url'], $timestamp, $this->request['data']),
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
						[
							{
								"from": "35:0x55d398326f99059ff775485246999027b3197955",
								"fromSymbol": "USDT.BEP20",
								"to": "4:0xdac17f958d2ee523a2206206994597c13d831ec7",
								"toSymbol": "USDT.ERC20"
							},
							{
								"from": "35:0x55d398326f99059ff775485246999027b3197955",
								"fromSymbol": "USDT.BEP20",
								"to": "4:0xa0b86991c6218b36c1d19d4a2e9eb0ce3606eb48",
								"toSymbol": "USDC.ERC20"
							},
							{
								"from": "35:0x55d398326f99059ff775485246999027b3197955",
								"fromSymbol": "USDT.BEP20",
								"to": "55:Es9vMFrzaCERmJfrF4H2FYD4KCoNkY11McCe8BenwNYB",
								"toSymbol": "USDT.SOL"
							}
						]
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

	/*------------------------------v BEGIN OF Rates v------------------------------------------------------------ */

	public function GetExchangeRates(\Growinc\Payment\Transaction $transaction)
	{
		$time = time();
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v2/rates' // lists the current conversion rates between currencies
			);
			$this->request['data'] = [
				'from' => $transaction->getParam('FROM'),
				'to' => $transaction->getParam('TO'),
				'pointInTime' => null,
			];
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-CoinPayments-Client' => $this->init->getMID(),
				'X-CoinPayments-Timestamp' => $timestamp,
				'X-CoinPayments-Signature' => $this->_GetSignature('GET', $this->request['url'], $timestamp, $this->request['data']),
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
					/* // Success // LTCT TO IDR
						{
							"items": [
								{
										"baseCurrencyId": "1002",
										"baseSymbol": "LTCT",
										"quoteCurrencyId": "4577",
										"quoteSymbol": "IDR",
										"rate": "800011.488498669494816063876"
								}
							]
						}
					*/
					/* // Success // USDT.ERC20 TO IDR
						{
							"items": [
								{
										"baseCurrencyId": "4:0xdac17f958d2ee523a2206206994597c13d831ec7",
										"baseSymbol": "USDT.ERC20",
										"quoteCurrencyId": "4577",
										"quoteSymbol": "IDR",
										"rate": "17684.534223066694821515498702"
								}
							]
						}
					*/
					/* // Success // IDR TO USDT.ERC20
						{
							"items": [
								{
										"baseCurrencyId": "4577",
										"baseSymbol": "IDR",
										"quoteCurrencyId": "4:0xdac17f958d2ee523a2206206994597c13d831ec7",
										"quoteSymbol": "USDT.ERC20",
										"rate": "0.0000565465840030809069091423"
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

	/*------------------------------^ END OF Rates ^-------------------------------------------------------------- */

	/*------------------------------v BEGIN OF Fees v------------------------------------------------------------ */

	public function GetBlockchainFees(\Growinc\Payment\Transaction $transaction)
	{
		$time = time();
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v2/fees/blockchain/' . $transaction->getParam('ID') // lists the current conversion rates between currencies
			);
			$this->request['data'] = [];
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-CoinPayments-Client' => $this->init->getMID(),
				'X-CoinPayments-Timestamp' => $timestamp,
				'X-CoinPayments-Signature' => $this->_GetSignature('GET', $this->request['url'], $timestamp, $this->request['data']),
			];
			$this->request['option'] = [
				'as_json' => false,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if ($status_code == 200) {
				if (!empty($content)) {
					/* // Success // LTCT
						0.0000052
					*/
					/* // Success // Tether USD
						0.019936
					*/
					/* // Success // Ethereum
						0.000008168807976
					*/
					$res = [
						'status' => '000',
						'data' => (string) $content,
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

	/*------------------------------^ END OF Fees ^-------------------------------------------------------------- */

	/*------------------------------v BEGIN OF Wallets v------------------------------------------------------------ */

	public function GetWallets(\Growinc\Payment\Transaction $transaction)
	{
		$time = time();
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v2/merchant/wallets' // Lists merchant client wallets (up to 100 wallets per response)
				// '/v2/merchant/wallets/count' // Get merchant client wallets count
			);
			$this->request['data'] = [
				'skip' => 0,
				'take' => 100,
			];
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-CoinPayments-Client' => $this->init->getMID(),
				'X-CoinPayments-Timestamp' => $timestamp,
				'X-CoinPayments-Signature' => $this->_GetSignature('GET', $this->request['url'], $timestamp, $this->request['data']),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if ($status_code == 200) {
				if (isset($content)) {
					/* // Success
					*/
					$res = [
						'status' => '000',
						'data' => $content,
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

	public function CreateWallet(\Growinc\Payment\Transaction $transaction)
	{
		$time = time();
		$timestamp = gmdate('Y-m-d\TH:i:s', $time);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v2/merchant/wallets'
			);
			$this->request['data'] = [
				'currency' => $transaction->getParam('CURRENCY'),
				'label' => $transaction->getParam('LABEL'),
				'webhookUrl' => $this->init->getCallbackURL(),
				'usePermanentAddresses' => true,
			];
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-CoinPayments-Client' => $this->init->getMID(),
				'X-CoinPayments-Timestamp' => $timestamp,
				'X-CoinPayments-Signature' => $this->_GetSignature('POST', $this->request['url'], $timestamp, $this->request['data']),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if ($status_code == 200 && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == "success"
				) {
					/* // Success
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content->responseData,
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

	/*------------------------------^ END OF Wallets ^-------------------------------------------------------------- */

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
		//
		$full_name = explode(' ', $transaction->getCustomerName());
		$first_name = array_shift($full_name);
		$last_name = array_pop($full_name);
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v2/merchant/invoices'
			);
			$this->request['data'] = [
				'currency' => $transaction->getParam('INVOICE_CURRENCY'),
				'items' => [[
					'customId' => $transaction->getReferenceNumber(),
					'sku' => '',
					'name' => $transaction->getDescription(),
					'description' => $transaction->getDescription(),
					'quantity' => [
						'value' => 1,
						// 'type' => 'quantity',
						'type' => 2, // type of product sold (1 = hours, 2 = units)
					],
					'originalAmount' => (string) $transaction->getAmount(),
					'amount' => (string) $transaction->getAmount(),
					'tax' => '0',
				]],
				'amount' => [
					'breakdown' => [
						'subtotal' => (string) $transaction->getAmount(),
						'shipping' => '0',
						'handling' => '0',
						'taxTotal' => '0',
						'discount' => '0',
					],
					'total' => (string) $transaction->getAmount(),
				],
				'isEmailDelivery' => true,
				'emailDelivery' => [
					'to' => $transaction->getCustomerEmail(),
					'cc' => '',
					'bcc' => '',
				],
				'dueDate' => gmdate('Y-m-d\TH:i:s', $transaction->getTime() + (60 * $transaction->getExpireIn())),
				'invoiceDate' => $timestamp,
				'draft' => false,
				'clientId' => '',
				'invoiceId' => $transaction->getReferenceNumber(),
				'buyer' => [
					'companyName' => '',
					'name' => [
						'firstName' => $first_name,
						'lastName' => $last_name,
					],
					'emailAddress' => $transaction->getCustomerEmail(),
					'phoneNumber' => $transaction->getCustomerPhone(),
					'address' => [
						'address1' => $transaction->getCustomerAddress(),
						'address2' => '',
						'address3' => '',
						'provinceOrState' => '',
						'city' => '',
						'suburbOrDistrict' => '',
						'countryCode' => 'ID',
						'postalCode' => '',
					],
				],
				'description' => $transaction->getDescription(),
				'shipping' => [
					'method' => '',
					'companyName' => '',
					'name' => [
						'firstName' => $first_name,
						'lastName' => $last_name,
					],
					'emailAddress' => $transaction->getCustomerEmail(),
					'phoneNumber' => $transaction->getCustomerPhone(),
					'address' => [
						'address1' => $transaction->getCustomerAddress(),
						'address2' => '',
						'address3' => '',
						'provinceOrState' => '',
						'city' => '',
						'suburbOrDistrict' => '',
						'countryCode' => 'ID',
						'postalCode' => '',
					],
					// 'hasData' => false,
				],
				'requireBuyerNameAndEmail' => false,
				'buyerDataCollectionMessage' => '',
				'notes' => $transaction->getDescription(),
				'notesToRecipient' => '',
				'termsAndConditions' => '',
				'merchantOptions' => [
					'showAddress' => false,
					'showEmail' => false,
					'showPhone' => false,
					'showRegistrationNumber' => false,
					// 'showTaxId' => false,
					'additionalInfo' => '',
				],
				'customData' => [
					'additionalProp1' => '',
					'additionalProp2' => '',
					'additionalProp3' => '',
				],
				// 'metadata' => [
				// 	'integration' => '',
				// 	'hostname' => '',
				// ],
				'poNumber' => '',
				'webhooks' => [[
					'notificationsUrl' => $this->init->getCallbackURL(),
					'notifications' => [
						'invoiceCreated',
						'invoicePending',
						'invoicePaid',
						'invoiceCompleted',
						'invoiceCancelled',
						'invoiceTimedOut',
						'invoicePaymentCreated',
						'invoicePaymentTimedOut'
					],
				]],
				// 'payoutOverrides' => [[
				// 	'fromCurrency' => $transaction->getParam('PAYOUT_OVERRIDE_FROM_CURRENCY') ?? '',
				// 	'toCurrency' => $transaction->getParam('PAYOUT_OVERRIDE_TO_CURRENCY') ?? '',
				// 	'address' => $transaction->getParam('PAYOUT_OVERRIDE_ADDRESS') ?? '',
				// 	'frequency' => $transaction->getParam('PAYOUT_OVERRIDE_FREQUENCY') ?? '[]',
				// ]],
				'useCoinReservation' => false,
				'payment' => [
					'paymentCurrency' => $transaction->getParam('PAYMENT_CURRENCY'),
					'refundEmail' => $transaction->getCustomerEmail(),
				],
				'hideShoppingCart' => false,
				// 'affiliateId' => '',
				'isSimpleQR' => false,
				// 'successUrl' => $this->init->getCallbackURL(),
				// 'cancelUrl' => $this->init->getCallbackURL(),
			];
			//
			$this->request['headers'] = [
				// 'Content-Type' => 'application/json',
				'X-CoinPayments-Client' => $this->init->getMID(),
				'X-CoinPayments-Timestamp' => $timestamp,
				'X-CoinPayments-Signature' => $this->_GetSignature('POST', $this->request['url'], $timestamp, $this->request['data']),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (!empty($content->invoices)
					&& !empty($content->invoices[0]->id)
				) {
					/* //
						{
							"invoices": [
								{
									"id": "86d4d4a2-7812-47ce-a1e3-0c808b6e138f",
									"link": "https://a-dashboard.coinpayments.net/en/invoices/86d4d4a2-7812-47ce-a1e3-0c808b6e138f",
									"checkoutLink": "https://a-checkout.coinpayments.net/checkout/?invoice-id=86d4d4a2-7812-47ce-a1e3-0c808b6e138f",
									"payment": {
										"paymentId": "cd83206f-2dc5-4e8e-b298-7ce69c71b0c0",
										"expires": "2026-06-16T08:37:05+00:00",
										"paymentCurrencies": [
												{
													"currency": {
														"id": "1002",
														"type": "crypto",
														"symbol": "LTCT",
														"name": "Litecoin Test",
														"logo": {
																"imageUrl": "https://a-api.coinpayments.net/api/v1/currencies/1002/logosvg"
														},
														"decimalPlaces": 8,
														"rank": 8,
														"status": "active",
														"capabilities": [
																"singleSigAccounts",
																"utxo",
																"conversion",
																"onRamp",
																"offRamp"
														],
														"urls": {
																"websites": [
																	"https://litecoin.org"
																],
																"explorers": [
																	"https://tltc.bitaps.com/"
																]
														},
														"requiredConfirmations": 3,
														"isEnabledForPayment": true
													},
													"amount": "0.01872947",
													"approximateNetworkAmount": "0",
													"remainingAmount": "0.01872947"
												}
										],
										"refundEmail": "t*****@gmail.com"
									},
									"hotWallet": {
										"currency": {
												"id": "1002",
												"type": "crypto",
												"symbol": "LTCT",
												"name": "Litecoin Test",
												"logo": {
													"imageUrl": "https://a-api.coinpayments.net/api/v1/currencies/1002/logosvg"
												},
												"decimalPlaces": 8,
												"rank": 8,
												"status": "active",
												"capabilities": [
													"singleSigAccounts",
													"utxo",
													"conversion",
													"onRamp",
													"offRamp"
												],
												"urls": {
													"websites": [
														"https://litecoin.org"
													],
													"explorers": [
														"https://tltc.bitaps.com/"
													]
												},
												"requiredConfirmations": 3,
												"isEnabledForPayment": true
										},
										"amount": "0.01872947",
										"remainingAmount": "0.01872947",
										"addresses": {
												"address": "muUfxEnERW2uSuAkXgbMWmBamUWenNtVE3",
												"bip21": ""
										},
										"expires": "2026-06-16T08:37:05+00:00"
									}
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
			// Go
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/v1/invoices/' . $request->id . '/payment-currencies/' . $request->currency . '/status'
			);
			$this->request['data'] = [];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-CoinPayments-Client' => $this->init->getMID(),
				'X-CoinPayments-Timestamp' => $timestamp,
				'X-CoinPayments-Signature' => $this->_GetSignature('GET', $this->request['url'], $timestamp, $this->request['data']),
			];
			$this->request['option'] = [
				'as_json' => false,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if ($status_code == 200 && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (isset($content->status)) {
					/* // Success inquiry
						{
							"created": "2026-06-16T07:37:04+00:00",
							"expires": "2026-06-16T08:37:05+00:00",
							"status": "unpaid",
							"payment": {
								"currencyId": 1002,
								"confirmations": 0,
								"requiredConfirmations": 0,
								"confirmedAmount": {
										"displayValue": "0",
										"value": "0",
										"currencyId": "1002"
								},
								"unconfirmedAmount": {
										"displayValue": "0",
										"value": "0",
										"currencyId": "1002"
								},
								"expectedAmount": {
										"rate": 0.00000123933596070285,
										"breakdown": [
											{
												"name": "Subtotal",
												"displayValue": "0.01859004",
												"value": "1859004",
												"currencyId": "1002"
											},
											{
												"name": "Buyer's Fees",
												"displayValue": "0.00013943",
												"value": "13943",
												"currencyId": "1002"
											}
										],
										"displayValue": "0.01872947",
										"value": "1872947",
										"currencyId": "1002"
								},
								"remainingAmount": {
										"displayValue": "0.01872947",
										"value": "1872947",
										"currencyId": "1002"
								},
								"status": "created"
							},
							"partialAcceptAvailable": false
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
		// Inapplicable
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
