<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(null, null);
$init->setPaymentURL($_ENV['NUSAPAY_HOST_URL']);
$init->setRequestURL($_ENV['NUSAPAY_HOST_URL']);
$init->setClientID('G-PAY.TO');
$vendor = new \Growinc\Payment\Vendors\Nusapay($init);

/**
 *
 * Payment Inquiry
 *
 */
try {
	$time = time();
	$date = date('ymdHis', $time);
	//
	$result = $vendor->Inquiry((object) [
		'bank' => 'mandiri',
		'va_number' => '89811220719141909', // Check by VA
		'trx_id' => '1658215149', // OR by trx ID
	]);
	extract($result);
	print_r($response);
	//
	/*
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
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}
