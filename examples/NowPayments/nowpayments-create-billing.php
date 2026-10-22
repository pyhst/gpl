<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(null, null);
$init->setMID($_ENV['NOWPAYMENTS_API_KEY']);
$init->setPublicKey($_ENV['NOWPAYMENTS_PUBLIC_KEY']);
$init->setPaymentURL($_ENV['NOWPAYMENTS_HOST_URL']);
$init->setRequestURL($_ENV['NOWPAYMENTS_HOST_URL']);
$init->setCallbackURL($_ENV['NOWPAYMENTS_WEBHOOK_URL']);
$init->setReturnURL($_ENV['NOWPAYMENTS_WEBHOOK_URL']);
$init->setParam('NOWPAYMENTS_EMAIL', $_ENV['NOWPAYMENTS_EMAIL']);
$init->setParam('NOWPAYMENTS_PASSWORD', $_ENV['NOWPAYMENTS_PASSWORD']);

$vendor = new \Growinc\Payment\Vendors\NowPayments($init);


/*------------------------------v BEGIN OF Auth v------------------------------------------------------------ */

/**
 *
 * Check currencies
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'GetToken') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$result = $vendor->GetToken($transaction);
		print_r($result);
		/* // Success
			eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjQ3NjUyNTAxOTkiLCJpYXQiOjE3ODE4NTM3MjIsImV4cCI6MTc4MTg1NDAyMn0.gU83YA4XeVzMM4uPyTT1KfIAvzrhDYJ-NPiDargxxUM
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}

/*------------------------------^ END OF Auth ^-------------------------------------------------------------- */

/*------------------------------v BEGIN OF Currencies v------------------------------------------------------------ */

/**
 *
 * Check currencies
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'GetCurrencies') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$transaction->setParam('FIXED_RATE', 'true');
		$result = $vendor->GetCurrencies($transaction);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": {
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
			}
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}

/**
 *
 * Check currencies alt
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'GetCurrenciesAlt') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$result = $vendor->GetCurrenciesAlt($transaction);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": {
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
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'GetCheckedCurrencies') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$result = $vendor->GetCheckedCurrencies($transaction);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": {
					"selectedCurrencies": [
							"ETH",
							"BTC",
							"DOGE",
							"SOL",
							"USDC",
							"USDTERC20"
					]
				}
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
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'GetMinPaymentAmount') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$transaction->setParam('CURRENCY_FROM', 'USDTERC20');
		$transaction->setParam('CURRENCY_TO', 'IDR');
		$transaction->setParam('FIAT_EQUIVALENT', 'IDR');
		$transaction->setParam('IS_FIXED_RATE', 'true');
		$transaction->setParam('IS_FEE_PAID_BY_USER', 'false');
		$result = $vendor->GetMinPaymentAmount($transaction);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": {
					"currency_from": "usdterc20",
					"currency_to": "idr",
					"min_amount": 7.468842,
					"fiat_equivalent": 132560.78776453
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
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'GetEstimatedPrice') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$transaction->setParam('CURRENCY_FROM', 'IDR');
		$transaction->setParam('CURRENCY_TO', 'USDTERC20');
		$transaction->setParam('AMOUNT', 50000);
		$result = $vendor->GetEstimatedPrice($transaction);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": {
					"currency_from": "idr",
					"amount_from": 50000,
					"currency_to": "usdterc20",
					"estimated_amount": "2.8054055"
				}
			}
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}

/*------------------------------^ END OF Fees ^-------------------------------------------------------------- */

/*------------------------------v BEGIN OF Payments v------------------------------------------------------------ */

/**
 *
 * Create billing
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'SecurePayment') {
	try {
		$time = time();
		$transaction = new \Growinc\Payment\Transaction();
		$transaction->setParam('PRICE_CURRENCY', 'IDR');
		$transaction->setParam('PAY_CURRENCY', 'USDTERC20');
		$transaction->setAmount(15000);
		$transaction->setReferenceNumber('INV' . $time);
		$transaction->setDescription('Payment of INV' . $time);
		$transaction->setParam('IPN_CALLBACK_URL', $init->getCallbackURL());
		$transaction->setParam('IS_FIXED_RATE', 'false');
		$transaction->setParam('IS_FEE_PAID_BY_USER', 'false');
		$result = $vendor->SecurePayment($transaction);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": {
					"id": "5738385706",
					"token_id": "5603406770",
					"order_id": "INV1781800852",
					"order_description": "Payment of INV1781800852",
					"price_amount": "15000",
					"price_currency": "IDR",
					"pay_currency": "USDTERC20",
					"ipn_callback_url": "https://g-pay.to/secure/callback/sP9xHwzVs2JmPznkkkGw6kb60",
					"invoice_url": "https://nowpayments.io/payment/?iid=5738385706",
					"success_url": "https://g-pay.to/secure/callback/sP9xHwzVs2JmPznkkkGw6kb60",
					"cancel_url": "https://g-pay.to/secure/callback/sP9xHwzVs2JmPznkkkGw6kb60",
					"customer_email": null,
					"partially_paid_url": null,
					"payout_currency": null,
					"created_at": "2026-06-18T16:40:53.339Z",
					"updated_at": "2026-06-18T16:40:53.339Z",
					"is_fixed_rate": false,
					"is_fee_paid_by_user": false,
					"source": null,
					"collect_user_data": false
				}
			}
		*/
		/* // Success PAID
			{
				"status": "000",
				"data": {
					"id": "5474867878",
					"token_id": "5603406770",
					"order_id": "INV1781853882",
					"order_description": "Payment of INV1781853882",
					"price_amount": "15000",
					"price_currency": "IDR",
					"pay_currency": "USDTERC20",
					"ipn_callback_url": "https://g-pay.to/secure/callback/sP9xHwzVs2JmPznkkkGw6kb60",
					"invoice_url": "https://nowpayments.io/payment/?iid=5474867878",
					"success_url": "https://g-pay.to/secure/callback/sP9xHwzVs2JmPznkkkGw6kb60",
					"cancel_url": "https://g-pay.to/secure/callback/sP9xHwzVs2JmPznkkkGw6kb60",
					"customer_email": null,
					"partially_paid_url": null,
					"payout_currency": null,
					"created_at": "2026-06-19T07:24:42.694Z",
					"updated_at": "2026-06-19T07:24:42.694Z",
					"is_fixed_rate": false,
					"is_fee_paid_by_user": false,
					"source": null,
					"collect_user_data": false
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
			'invoice_id' => '5474867878',
		]);
		extract($result);
		print_r($response);
		/* // Success inquiry
			{
				"status": "000",
				"data": {
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
			}
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}

/*------------------------------^ END OF Payments ^-------------------------------------------------------------- */
