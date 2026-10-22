<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['XENDIT_API_KEY'],
	$_ENV['XENDIT_PUBLIC_KEY']
);

$vendor = new \PyGPaymentLib\Vendors\Xendit($init);

try {
	$raw_data = file_get_contents("php://input");
	if (!$raw_data) {
		$raw_data = '{
			"id": "57fb4e076fa3fa296b7f5a97",
			"owner_id": "5824128aa6f9f9b648be9d76",
			"external_id": "va-1487156410",
			"merchant_code": "88608",
			"account_number": "886081000123456",
			"bank_code": "MANDIRI",
			"name": "John Doe",
			"is_closed": false,
			"is_single_use": false,
			"status": "ACTIVE",
			"expiration_date": "2048-02-15T11:01:52.722Z",
			"updated": "2016-10-10T08:15:03.404Z",
			"created": "2016-10-10T08:15:03.404Z"
		}';
	}
	$request = (object) json_decode($raw_data);
	$result = $vendor->CallbackAlt($request);
	extract($result);
	print_r($response);
} catch (\Throwable $e) {
	echo 'Callback failed: ' . $e->getMessage();
}
