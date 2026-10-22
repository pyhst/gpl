<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['NUSAPAY_CLIENT_ID'],
	$_ENV['NUSAPAY_CLIENT_SECRET']
);
$vendor = new \PyGPaymentLib\Vendors\Nusapay($init);

// /**
//  *
//  * Check Account
//  *
//  */
// try {
// 	$result = $vendor->GetCustomerNP([
// 		'request_url' => $_ENV['NUSAPAY_ACCOUNT_URL'],
// 		'token' => $vendor->GetToken([
// 			'client_id' => $_ENV['NUSAPAY_CLIENT_ID'],
// 			'client_secret' => $_ENV['NUSAPAY_CLIENT_SECRET'],
// 			'request_url' => $_ENV['NUSAPAY_ACCOUNT_URL'],
// 		]),
// 		'search' => '081212121314',
// 		// 'search' => '081334023320',
// 	]);
// 	print_r($result);
// 	//
// 	/*
// 		52
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }

/**
 *
 * Check Account with no token presets
 *
 */
try {
	$init->setRequestURL($_ENV['NUSAPAY_ACCOUNT_URL']);
	$result = $vendor->GetCustomerNP([
		'search' => '081212121314',
		// 'search' => '081334023320',
	]);
	print_r($result);
	//
	/*
		52
	*/
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}
