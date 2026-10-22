<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['XENDIT_API_KEY'],
	$_ENV['XENDIT_PUBLIC_KEY']
);
$init->setRequestURL($_ENV['XENDIT_HOST_URL']);

$vendor = new \Growinc\Payment\Vendors\Xendit($init);

try {
	$result = $vendor->Inquiry((object) [
			'id' => 'qr_46f6f3fc-c72c-4ff3-b323-bd03ac527396',
		]);
	// extract($result);
	// print_r($response);
	// Success
	/*
	{
		"status": "000",
		"data": {
			"is_closed": true,
			"status": "ACTIVE",
			"currency": "IDR",
			"owner_id": "5f706881fefc961e3f708f02",
			"external_id": "0008115320",
			"bank_code": "BCA",
			"merchant_code": "10766",
			"name": "LOREM IPSUM",
			"account_number": "107669999020779",
			"expected_amount": 100000,
			"expiration_date": "2020-12-16T12:22:00.000Z",
			"is_single_use": false,
			"id": "5fd9e479ed81dd402014403c"
		}
	}
	*/
	print_r($result);
} catch (\Throwable $e) {
	echo 'Inquiry failed: ' . $e->getMessage();
}
