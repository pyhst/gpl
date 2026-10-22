<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['XENDIT_API_KEY'],
	$_ENV['XENDIT_PUBLIC_KEY']
);
$init->setRequestURL($_ENV['XENDIT_HOST_URL']);

$vendor = new \PyGPaymentLib\Vendors\Xendit($init);

try {
	$result = $vendor->InquiryPayment((object) [
			'payment_id' => '5f9fb9758d65ab3c1141f230',
		]);
	// extract($result);
	// print_r($response);
	// Success
	/*
	{
		"status": "000",
		"data": {
			"payment_id": "5f9fb9758d65ab3c1141f230",
			"callback_virtual_account_id": "5f9fb82dbcbf722b71041f3f",
			"external_id": "VA_fixed-1604302892",
			"account_number": "9999000002",
			"bank_code": "MANDIRI",
			"amount": 50000,
			"transaction_timestamp": "2020-11-02T07:47:01.000Z",
			"merchant_code": "88608",
			"currency": "IDR",
			"id": "5f9fb9758940c131b3d7b96d"
		}
	}
	*/
	print_r($result);
} catch (\Throwable $e) {
	echo 'Inquiry failed: ' . $e->getMessage();
}
