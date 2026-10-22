<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['DUITKU_MERCHANT_ID'],
	$_ENV['DUITKU_API_KEY']
);
$init->setRequestURL($_ENV['DUITKU_HOST_URL']);

$vendor = new \PyGPaymentLib\Vendors\Duitku($init);

// try {
	$result = $vendor->Inquiry((object) [
			'order_id' => $_REQUEST['order_id'] ?? 'D7214IWKS1S07YCBIUKR',
			// 'order_id' => '202011270347671606452319',
			// 'order_id' => '202011302718391606720920',
		]);
	extract($result);
	print_r($response);
// } catch (\Throwable $e) {
// 	echo 'Inquiry failed: ' . $e->getMessage() . ':' . $e->getCode();
// }
