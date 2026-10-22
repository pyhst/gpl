<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(null, null);
$init->setMID($_ENV['COINPAYMENTS_CLIENT_ID']);
$init->setSecret($_ENV['COINPAYMENTS_CLIENT_SECRET']);
$init->setPaymentURL($_ENV['COINPAYMENTS_HOST_URL']);
$init->setRequestURL($_ENV['COINPAYMENTS_HOST_URL']);
$init->setCallbackURL($_ENV['COINPAYMENTS_WEBHOOK_URL']);
$init->setReturnURL($_ENV['COINPAYMENTS_WEBHOOK_URL']);

$vendor = new \Growinc\Payment\Vendors\CoinPayments($init);


/*------------------------------v BEGIN OF Currencies v------------------------------------------------------------ */

/**
 *
 * Check currencies
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'GetCurrencies') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$transaction->setParam('QUERY_FILTER', 'USDT.ERC20');
		$result = $vendor->GetCurrencies($transaction);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": [
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
					}
				]
			}
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}

/**
 *
 * Check accepted merchant currencies
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'GetMerchantCurrencies') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$result = $vendor->GetMerchantCurrencies($transaction);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": [
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
				]
			}
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}

/**
 *
 * Check accepted merchant currencies
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'GetConversionPairs') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$result = $vendor->GetConversionPairs($transaction);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": [
					{
							"from": "4:0xdac17f958d2ee523a2206206994597c13d831ec7",
							"fromSymbol": "USDT.ERC20",
							"to": "2",
							"toSymbol": "LTC"
					},
					{
							"from": "4:0xdac17f958d2ee523a2206206994597c13d831ec7",
							"fromSymbol": "USDT.ERC20",
							"to": "9",
							"toSymbol": "TRX"
					},
					{
							"from": "4:0xdac17f958d2ee523a2206206994597c13d831ec7",
							"fromSymbol": "USDT.ERC20",
							"to": "55",
							"toSymbol": "SOL"
					},
					{
							"from": "4:0xdac17f958d2ee523a2206206994597c13d831ec7",
							"fromSymbol": "USDT.ERC20",
							"to": "1",
							"toSymbol": "BTC"
					},
					{
							"from": "4:0xdac17f958d2ee523a2206206994597c13d831ec7",
							"fromSymbol": "USDT.ERC20",
							"to": "4",
							"toSymbol": "ETH"
					}
				]
			}
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}

/*------------------------------^ END OF Currencies ^-------------------------------------------------------------- */

/*------------------------------v BEGIN OF Rates v------------------------------------------------------------ */

/**
 *
 * Check rates
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'GetExchangeRates') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		//
		// // $transaction->setParam('FROM', '1002'); // Litecoin Test
		// // $transaction->setParam('FROM', '4'); // Ethereum
		// $transaction->setParam('FROM', '4:0xdac17f958d2ee523a2206206994597c13d831ec7'); // Tether USD
		// $transaction->setParam('TO', '4577'); // Indonesian Rupiah
		//
		$transaction->setParam('FROM', '4577'); // Indonesian Rupiah
		$transaction->setParam('TO', '4:0xdac17f958d2ee523a2206206994597c13d831ec7'); // Tether USD
		//
		$result = $vendor->GetExchangeRates($transaction);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": {
					"items": [
							{
								"baseCurrencyId": "4577",
								"baseSymbol": "IDR",
								"quoteCurrencyId": "4:0xdac17f958d2ee523a2206206994597c13d831ec7",
								"quoteSymbol": "USDT.ERC20",
								"rate": "0.000056318178147895659731085"
							}
					]
				}
			}
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}

/*------------------------------^ END OF Rates ^-------------------------------------------------------------- */

/*------------------------------v BEGIN OF Fees v------------------------------------------------------------ */

/**
 *
 * Check fees
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'GetBlockchainFees') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$transaction->setParam('ID', '4'); // Ethereum
		// $transaction->setParam('ID', '4:0xdac17f958d2ee523a2206206994597c13d831ec7'); // Tether USD
		// $transaction->setParam('ID', '1002'); // Litecoin Test
		$result = $vendor->GetBlockchainFees($transaction);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": "0.000085229583768"
			}
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}

/*------------------------------^ END OF Fees ^-------------------------------------------------------------- */

/*------------------------------v BEGIN OF Wallets v------------------------------------------------------------ */

/**
 *
 * Get wallets
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'GetWallets') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$result = $vendor->GetWallets($transaction);
		extract($result);
		print_r($response);
		/* // Success
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}

/**
 *
 * Create a wallet
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'CreateWallet') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$transaction->setParam('CURRENCY', 'IDR');
		$transaction->setParam('LABEL', 'IDR WALLET');
		$result = $vendor->CreateWallet($transaction);
		extract($result);
		print_r($response);
		/* // Success
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}

/*------------------------------^ END OF Wallets ^-------------------------------------------------------------- */



/**
 *
 * Create billing
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'SecurePayment') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$transaction->setCustomerName('Tester Account');
		$transaction->setCustomerPhone('081212121314');
		$transaction->setCustomerEmail('tester@gmail.com');
		$transaction->setAmount(15000);
		$transaction->setReferenceNumber(time());
		$transaction->setParam('INVOICE_CURRENCY', '4577');
		$transaction->setParam('PAYMENT_CURRENCY', '1002');
		// $transaction->setParam('PAYOUT_OVERRIDE_FROM_CURRENCY', '4577');
		// $transaction->setParam('PAYOUT_OVERRIDE_TO_CURRENCY', '4577');
		// $transaction->setParam('PAYOUT_OVERRIDE_ADDRESS', '4577');
		// $transaction->setParam('PAYOUT_OVERRIDE_FREQUENCY', '[]');
		$transaction->setExpireIn(10);
		$result = $vendor->SecurePayment($transaction);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": {
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
			}
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}



/**
 *
 * Inquiry trx
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'Inquiry') {
	try {
		$result = $vendor->Inquiry((object) [
			'id' => '86d4d4a2-7812-47ce-a1e3-0c808b6e138f',
			'currency' => '1002',
		]);
		extract($result);
		print_r($response);
		/* // Success inquiry
			{
				"status": "000",
				"data": {
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
			}
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}
