<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['ESPAY_MERCHANT_CODE'],
	$_ENV['ESPAY_API_KEY']
);

$vendor = new \Growinc\Payment\Vendors\Espay($init);

try {
	$raw_data = file_get_contents("php://input");
	// Input dummy sample
	// $raw_data = '{
	// 	"rq_uuid": "e9b185e4-031d-4794-bc21-1a3282506051",
	// 	"rq_datetime": "2021-06-18 14:14:39",
	// 	"sender_id": "SGOPLUS",
	// 	"receiver_id": "SGWGROWINC",
	// 	"password": "Y0F,(5EM=#",
	// 	"comm_code": "SGWGROWINC",
	// 	"member_code": "",
	// 	"order_id": "0024000477",
	// 	"signature": "2163c0d2b0148d37b8c05630fe8bb39e5fbcbb6ad4303c1c25c66ff7db3337bb"
	// }';

	$file = '../_log/espay_log_inquiry_' . time();
	// file_put_contents($file . '_raw.txt', $raw_data, FILE_APPEND | LOCK_EX);

	if (!$raw_data) {
		print_r('{data:null}');
		// throw new \Exception('Inquiry request is empty');
	} else {
		$raw_data = parse_str($raw_data, $data);
		$request = (object) $data;
		// Check password
		if (strcmp($request->password, $_ENV['ESPAY_PASSWORD']) !== 0) {
			// Get the amount
			$amount = 0;
			// Get the currency
			$currency = 'IDR';
			// Get the description
			$description = 'Payment';
			// Result failed
			$data =
				'9;' .
				'Error, invalid password;' .
				$request->order_id . ';' .
				$amount . '.00;' .
				$currency . ';' .
				$description . ';' .
				$request->rq_datetime;
			exit($data);
			// throw new \Exception('Invalid password');
		}
		//
		file_put_contents($file . '.txt',
			json_encode($request, JSON_PRETTY_PRINT),
			FILE_APPEND | LOCK_EX);
		//
		$result = $vendor->IncomingInquiry($request);
		print_r($result);
	}
} catch (\Throwable $e) {
	echo 'Inquiry failed: ' . $e->getMessage();
}
