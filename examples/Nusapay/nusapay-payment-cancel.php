<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(null, null);
$init->setPaymentURL($_ENV['NUSAPAY_HOST_URL']);
$init->setRequestURL($_ENV['NUSAPAY_HOST_URL']);
$init->setClientID('G-PAY.TO');
$vendor = new \PyGPaymentLib\Vendors\Nusapay($init);

/**
 *
 * Payment Cancel
 *
 */
try {
	$time = time();
	$date = date('ymdHis', $time);
	//
	$result = $vendor->Cancel((object) [
		'bank' => 'mandiri',
		'va_number' => '89811220719143810',
	]);
	extract($result);
	print_r($response);
	//
	/*
	{
		"status": "000",
		"message": "Bill is cancelled 89811220719143810"
	}
	*/
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}
