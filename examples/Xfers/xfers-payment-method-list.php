<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['XFERS_API_KEY'],
	$_ENV['XFERS_SECRET_KEY']
);
$init->setRequestURL($_ENV['XFERS_HOST_URL']);

$vendor = new \Growinc\Payment\Vendors\Xfers($init);

try {
	$result = $vendor->PaymentMethodList((object) [
		'type' => 'virtual_bank_accounts',
		'id' => 'bca',
	]);
	extract($result);
	print_r($response);
	/* // Success
	*/
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}
