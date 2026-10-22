<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['PLINK_API_KEY'],
	$_ENV['PLINK_SECRET_KEY']
);
$init->setMerchantKey($_ENV['PLINK_MERCHANT_CODE']);
$init->setRequestURL($_ENV['PLINK_HOST_URL']);
$init->setResponseUrl($_ENV['PLINK_REDIRECT_URL']); // frontend_callback_url
$init->setBackendURL($_ENV['PLINK_CALLBACK_URL']); // backend_callback_url
$vendor = new \PyGPaymentLib\Vendors\PrismaLink($init);

/**
 *
 * Payment Inquiry
 *
 */
try {
	$time = time();
	$date = date('ymdHis', $time);
	//
	$result = $vendor->Inquiry((object) [
		'merchant_ref_no' => 'N97B20220720940430300621363044',
		'plink_ref_no' => '20072283077165',
	]);
	extract($result);
	print_r($response);
	//
	/*
	*/
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}
