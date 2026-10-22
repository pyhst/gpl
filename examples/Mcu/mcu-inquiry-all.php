<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['MACAU_AGENT_CODE'],
	$_ENV['MACAU_AGENT_DIVISION']
);
$init->setRequestURL($_ENV['MACAU_HOST_URL']);

$vendor = new \Growinc\Payment\Vendors\Mcu($init);

try {
	$result = $vendor->InquiryAll((object) [
			'type' => 'deposit',
			// 'type' => 'withdraw',
		]);
	extract($result);
	print_r($response);
} catch (\Throwable $e) {
	echo $vendor->ThrowError($e);
}
