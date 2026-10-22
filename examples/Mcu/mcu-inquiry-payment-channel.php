<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['MACAU_AGENT_CODE'],
	$_ENV['MACAU_AGENT_DIVISION']
);
$init->setRequestURL($_ENV['MACAU_HOST_URL']);

$vendor = new \PyGPaymentLib\Vendors\Mcu($init);

try {
	$result = $vendor->InquiryPaymentChannel();
	extract($result);
	print_r($response);
} catch (\Throwable $e) {
	echo $vendor->ThrowError($e);
}
