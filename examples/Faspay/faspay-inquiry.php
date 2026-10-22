<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['FASPAY_MERCHANT_NAME'] . ' : ' . $_ENV['FASPAY_MERCHANT_ID'],
	$_ENV['FASPAY_USER_ID'] . ' : ' . $_ENV['FASPAY_PASSWORD']
);
$init->setRequestURL($_ENV['FASPAY_HOST_URL']);

$vendor = new \Growinc\Payment\Vendors\Faspay($init);

try {
	$result = $vendor->Inquiry((object) [
			'order_id' => 'K37B2021090913242136',
			'trx_id' => '3366071130313189', // Faspay transaction ID
		]);
	extract($result);
	print_r($response);
	// Success
	/*
	{
		"status": "000",
		"data": {
			"response": "Inquiry Status Payment",
			"trx_id": "3366080100000042",
			"merchant_id": "33660",
			"merchant": "VoGame Indonesia",
			"bill_no": "1612772364",
			"payment_reff": "",
			"payment_date": "",
			"payment_status_desc": "Belum diproses",
			"payment_status_code": "0",
			"payment_total": "",
			"response_code": "00",
			"response_desc": "Sukses"
		}
	}
	{
		"status": "000",
		"data": {
			"response": "Inquiry Status Payment",
			"trx_id": "3366082500000166",
			"merchant_id": "33660",
			"merchant": "VoGame Indonesia",
			"bill_no": "1612772509",
			"payment_reff": "",
			"payment_date": "",
			"payment_status_desc": "Belum diproses",
			"payment_status_code": "0",
			"payment_total": "",
			"response_code": "00",
			"response_desc": "Sukses"
		}
	}
	*/
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
