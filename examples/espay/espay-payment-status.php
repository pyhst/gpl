<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['ESPAY_MERCHANT_CODE'],
	$_ENV['ESPAY_API_KEY']
);
$init->setRequestURL('https://sandbox-api.espay.id/');
$init->setParams([
	'signature' => $_ENV['ESPAY_SIGNATURE'],
	'password' => $_ENV['ESPAY_PASSWORD'],
]);

$vendor = new \PyGPaymentLib\Vendors\Espay($init);

try {
	$result = $vendor->Inquiry((object) [
			'order_id' => $_REQUEST['order_id'] ?? '0008032194',
		]);
	// extract($result);
	// print_r($response);
	// Success
	/*
	{
		"status": "000",
		"data": {
			"rq_uuid": "1624632024",
			"rs_datetime": "2021-06-25 21:40:26",
			"error_code": "0000",
			"error_message": "",
			"comm_code": "SGW",
			"member_code": null,
			"tx_id": "ESP1608032198KBSU",
			"order_id": "0008032194",
			"ccy_id": "IDR",
			"amount": "180000",
			"refund_amount": 0,
			"tx_status": "IP",
			"tx_reason": "EXPIRED",
			"tx_date": "2020-12-15",
			"created": "2020-12-15 18:36:35",
			"expired": "2020-12-16 18:36:38",
			"bank_name": "BANK MANDIRI",
			"product_name": "Link Aja QR Pay",
			"product_value": "",
			"payment_ref": "",
			"merchant_code": "1002",
			"token": "",
			"member_cust_id": "SYSTEM",
			"member_cust_name": "SYSTEM",
			"debit_from_name": "",
			"debit_from_bank": "008",
			"credit_to": "1150000059313",
			"credit_to_name": "1150000059313",
			"credit_to_bank": "008",
			"payment_datetime": "2020-12-15 18:36:38"
		}
	}
	*/
	print_r($result);
} catch (\Throwable $e) {
	echo 'Payment status failed: ' . $e->getMessage();
}
