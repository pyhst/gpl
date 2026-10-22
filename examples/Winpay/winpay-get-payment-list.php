<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['WINPAY_PRIVATE_KEY_1'],
	$_ENV['WINPAY_PRIVATE_KEY_2']
);
$init->setMerchantKey($_ENV['WINPAY_MERCHANT_KEY']);
$init->setRequestURL($_ENV['WINPAY_HOST_URL']);

$vendor = new \PyGPaymentLib\Vendors\Winpay($init);

try {
	$result = $vendor->InquiryPaymentChannel();
	extract($result);
	print_r($response);
	// Success
	/*
	{
		"status": "000",
		"data": {
			"rc": "00",
			"rd": "Success",
			"request_time": "2021-02-25 15:35:01.462169",
			"data": {
				"token": "000f1f4cb5118390cc2ec79af671d61719c6f7a74281b16c2e70ba485dcf1750",
				"products": {
					"clickpay": [{
						"payment_code": "BCAKP",
						"payment_name": "BCA Klik Pay",
						"payment_description": "Bayar dengan BCA Klik Pay",
						"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-bca-klikpay.png",
						"payment_url": "https:\/\/secure-payment.winpay.id\/api\/BCAKP",
						"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/BCAKP",
						"is_direct": false
					}, {
						"payment_code": "CIMBC",
						"payment_name": "CIMB Clicks",
						"payment_description": "Bayar dengan CIMB Clicks",
						"payment_logo": "https:\/\/secure-payment.speedcash.co.id\/img\/spi-cimb-clicks.png",
						"payment_url": "https:\/\/secure-payment.winpay.id\/api\/CIMBC",
						"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/CIMBC",
						"is_direct": false
					}, {
						"payment_code": "BTNONLINE",
						"payment_name": "Debit Online BTN",
						"payment_description": "Bayar dengan Debit Online BTN",
						"payment_logo": "https:\/\/secure-payment.plasamall.com\/img\/spi-btnonline.png",
						"payment_url": "https:\/\/secure-payment.winpay.id\/api\/BTNONLINE",
						"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/BTNONLINE",
						"is_direct": false
					}],
					"modern store": [{
						"payment_code": "ALFAMART",
						"payment_name": "Alfamart",
						"payment_description": "Bayar di gerai Alfamart",
						"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-alfamart.png",
						"payment_url": "https:\/\/secure-payment.winpay.id\/api\/ALFAMART",
						"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/ALFAMART",
						"is_direct": true
					}, {
						"payment_code": "FASTPAY",
						"payment_name": "Fastpay",
						"payment_description": "Bayar di Outlet Fastpay",
						"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-fastpay.png",
						"payment_url": "https:\/\/secure-payment.winpay.id\/api\/FASTPAY",
						"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/FASTPAY",
						"is_direct": true
					}],
					"bank transfer": [{
						"payment_code": "MANDIRIPC",
						"payment_name": "Mandiri Pay Code",
						"payment_description": "Bayar dengan Mandiri Payment Code",
						"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-mandiri.png",
						"payment_url": "https:\/\/secure-payment.winpay.id\/api\/MANDIRIPC",
						"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/MANDIRIPC",
						"is_direct": true
					}, {
						"payment_code": "BCAPC",
						"payment_name": "ATM BCA",
						"payment_description": "Bayar di ATM BCA",
						"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-atm-bca.png",
						"payment_url": "https:\/\/secure-payment.winpay.id\/api\/BCAPC",
						"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/BCAPC",
						"is_direct": true
					}],
					"virtual account": [{
						"payment_code": "BNIVA",
						"payment_name": "BNI VIRTUAL ACCOUNT",
						"payment_description": "Bayar dengan BNI VIRTUAL ACCOUNT",
						"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-bni-va.png",
						"payment_url": "https:\/\/secure-payment.winpay.id\/api\/BNIVA",
						"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/BNIVA",
						"is_direct": true
					}, {
						"payment_code": "BRIVA",
						"payment_name": "BRI VIRTUAL ACCOUNT",
						"payment_description": "Bayar dengan BRI VIRTUAL ACCOUNT",
						"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-bri-va.png",
						"payment_url": "https:\/\/secure-payment.winpay.id\/api\/BRIVA",
						"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/BRIVA",
						"is_direct": true
					}, {
						"payment_code": "PERMATAVA",
						"payment_name": "PERMATA VIRTUAL ACCOUNT",
						"payment_description": "Bayar dengan PERMATA VIRTUAL ACCOUNT",
						"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-permata-va.png",
						"payment_url": "https:\/\/secure-payment.winpay.id\/api\/PERMATAVA",
						"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/PERMATAVA",
						"is_direct": true
					}, {
						"payment_code": "MANDIRIVA",
						"payment_name": "MANDIRI VIRTUAL ACCOUNT",
						"payment_description": "Bayar dengan MANDIRI VIRTUAL ACCOUNT",
						"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-mandiriva.png",
						"payment_url": "https:\/\/secure-payment.winpay.id\/api\/MANDIRIVA",
						"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/MANDIRIVA",
						"is_direct": true
					}]
				}
			},
			"response_time": "2021-02-25 15:35:01.646438"
		}
	}
	*/
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
