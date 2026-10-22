<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['XFERS_API_KEY'],
	$_ENV['XFERS_SECRET_KEY']
);

$vendor = new \PyGPaymentLib\Vendors\Xfers($init);

try {
	$raw_data = file_get_contents("php://input");
	/* // Sample incoming
	{
		"data": {
			"id": "contract_ea9c4b10df49446eafd4fb3f25a713bd",
			"type": "payment",
			"attributes": {
				"status": "paid",
				"amount": "100000.0",
				"createdAt": "2020-10-19T09:12:48+07:00",
				"description": null,
				"expiredAt": "2020-10-20T02:12:48Z",
				"referenceId": "ORDER_0001",
				"fees": "4400.0",
				"paymentMethod": {
					"id": "871767762ccded4a92461ac0d3376c1e",
					"type": "retail_outlet",
					"referenceId": "ORDER_0001",
					"instructions": {
						"retailOutletName": "ALFAMART",
						"paymentCode": "8887322"
					}
				}
			}
		}
	}
	*/
	if (!empty($raw_data)) {
		$data = (object) json_decode($raw_data)->data;
		//
		$file = '../_log/xfers_log_notif_' . time();
		file_put_contents($file . '.txt',
			json_encode($data, JSON_PRETTY_PRINT),
			FILE_APPEND | LOCK_EX);
		//
		$result = $vendor->Callback($data);
	}
	extract($result);
	print_r($response);
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}