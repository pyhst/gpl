<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['GV_MERCHANT_ID'],
	$_ENV['GV_MERCHANT_KEY']
);
$init->setRequestURL($_ENV['GV_INQUIRY_URL']);

$vendor = new \PyGPaymentLib\Vendors\GudangVoucher($init);

try {
	$result = $vendor->Inquiry((object) [
			'order_id' => 'INVN2021100123846211',
		]);
	extract($result);
	print_r($response);
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
