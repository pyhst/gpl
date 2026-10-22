<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['WINPAY_PRIVATE_KEY_1'],
	$_ENV['WINPAY_PRIVATE_KEY_2']
);
$init->setMerchantKey($_ENV['WINPAY_MERCHANT_KEY']);
$init->setRequestURL($_ENV['WINPAY_HOST_URL']);

$vendor = new \PyGPaymentLib\Vendors\Winpay($init);

try {
	$result = $vendor->Inquiry((object) [
			'order_id' => 'D21G2021090517346677', // Pick one of 4
			// 'order_id' => '0029004319', // Pick one of 4
			// 'id_transaction' => '312968772', // Pick one of 4
			// 'id_transaction_inquiry' => '312968772', // Pick one of 4
			// 'id_transaction_payment' => '312968772', // Pick one of 4
			'is_qris' => 'yes',
		]);
	extract($result);
	print_r($response);
	// Success
	/*
	*/
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Inquiry failed: ' . $e->getMessage();
}
