<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['TCTP_MID'],
	$_ENV['TCTP_SECRET_KEY']
);
$init->setRequestURL($_ENV['TCTP_HOST_URL']);
$init->setCallbackURL($_ENV['TCTP_CALLBACK_URL']);
$init->setReturnURL($_ENV['TCTP_RETURN_URL']);

$vendor = new \Growinc\Payment\Vendors\TCTP($init);

$transaction = new \Growinc\Payment\Transaction();
$transaction->setInvoiceNo(time());
$transaction->setDescription('PAYMENT');
$transaction->setAmount(rand(50000,1000000));
$transaction->setCurrency('IDR');
$transaction->setCurrencyCode(360);

try {
	$payment_token = $vendor->CreatePaymentToken($transaction);
	extract($payment_token);
	// print_r($response);
	print_r($payment_token);
	// Return array
	/*
	Array
	(
		[content] =>
			{
				"status": "000",
				"data": {
					"webPaymentUrl": "https:\/\/sandbox-pgw-ui.2c2p.com\/payment\/4.1\/#\/token\/kSAops9Zwhos8hSTSeLTUUYuqiXfK95FnAqodKIjJGtmLk%2bI3JQ10wEakjkHhcXAcY9jckQJJUDz4P9vPh%2ba%2faHOnxla0ftPnA5aobXus5pBjs50xU5WPT5Kf%2bxdcOIK",
					"paymentToken": "kSAops9Zwhos8hSTSeLTUUYuqiXfK95FnAqodKIjJGtmLk+I3JQ10wEakjkHhcXAcY9jckQJJUDz4P9vPh+a\/aHOnxla0ftPnA5aobXus5pBjs50xU5WPT5Kf+xdcOIK",
					"respCode": "0000",
					"respDesc": "Success"
				}
			}
		[status_code] => 200
	)
	*/
} catch (\Throwable $e) {
	echo 'Create payment token failed: ' . $e->getMessage() . ':' . $e->getCode();
}