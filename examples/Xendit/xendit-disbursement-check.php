<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['XENDIT_API_KEY'],
	$_ENV['XENDIT_PUBLIC_KEY']
);
$init->setRequestURL($_ENV['XENDIT_HOST_URL']);
$vendor = new \PyGPaymentLib\Vendors\Xendit($init);

/**
 *
 * Check Transfer
 *
 */
try {
	$result = $vendor->CheckTransfer((object) [
		// 'id' => '629849c5d3dcca6caacdb8b7',
		'id' => '63512ff50e8510b68d51404c',
	]);
	extract($result);
	print_r($response);
	/*
	{"status":"000","data":{"status":"COMPLETED","user_id":"5fc89b828c3f5a408eef8f78","external_id":"TT20220602122522","amount":10000,"bank_code":"BCA","account_holder_name":"MAJU SELARAS KOMPUTER PT","disbursement_description":"Test Transfer","is_instant":true,"id":"629849c5d3dcca6caacdb8b7"}}
	{"status":"000","data":{"status":"COMPLETED","user_id":"5fc89b828c3f5a408eef8f78","external_id":"TT20221020182433","amount":10000,"bank_code":"BCA","account_holder_name":"MAJU SELARAS KOMPUTER PT","disbursement_description":"Test Transfer","is_instant":true,"id":"63512ff50e8510b68d51404c"}}
	*/
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}