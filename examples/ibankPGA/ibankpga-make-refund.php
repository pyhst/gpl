<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['IBANK_PGA_UUID'],
	$_ENV['IBANK_PGA_SECRET_KEY'],
);
$init->setPaymentURL($_ENV['IBANK_PGA_HOST_URL']);
$init->setRequestURL($_ENV['IBANK_PGA_HOST_URL']);
$init->setParams([
	'service_code' => $_ENV['IBANK_PGA_SERVICE_CODE'],
]);
$vendor = new \Growinc\Payment\Vendors\ibankPGA($init);



// /**
//  *
//  * Make refund
//  *
//  */
// $refund = (object) [
// 	'payment_method' => 'qris',
// 		'merchant_uuid' => $init->getMID(),
// 		'service_code' => 'NSIQRIS',
// 		'amount' => 200000,
// 		'customer_id' => '1234567890',
// 		'customer_name' => 'CUSTOMERAAA',
// 		'customer_phone' => '081212121314',
// 		'customer_email' => 'customeraaa@gmail.com',
// 		'invoice_num' => 'P12150000001',
// 		'rrn' => 'P12150000001', // Switching's RRN number
// 		'reference_num' => time(), // Client's refund reference num
// ];
// try {
// 	$result = $vendor->Refund($refund);
// 	extract($result);
// 	print_r($response);
// 	/* // Success
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Make status check to AJ
//  *
//  */
// $refund = (object) [
// 	'payment_method' => 'qris',
// 		'merchant_uuid' => $init->getMID(),
// 		'service_code' => 'NSIQRIS',
// 		'amount' => 200000,
// 		'customer_id' => '1234567890',
// 		'customer_name' => 'CUSTOMERAAA',
// 		'customer_phone' => '081212121314',
// 		'customer_email' => 'customeraaa@gmail.com',
// 		'invoice_num' => 'P12150000001',
// 		'rrn' => 'P12150000001',
// ];
// try {
// 	$result = $vendor->MakeStatusCheck($refund);
// 	extract($result);
// 	print_r($response);
// 	/* // Success
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



/**
 *
 * Check refund status (local ib)
 *
 */
$refund = (object) [
	'payment_method' => 'qris',
		'merchant_uuid' => $init->getMID(),
		'service_code' => 'NSIQRIS',
		'invoice_num' => 'R03070000003',
];
try {
	$result = $vendor->CheckRefund($refund);
	extract($result);
	print_r($response);
	/* // Success
	*/
} catch (\Throwable $e) {
	$vendor->ThrowErrorException($e);
}

