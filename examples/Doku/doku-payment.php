<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['DOKU_STORE_ID'],
	$_ENV['DOKU_SHARED_KEY']
);
$init->setPaymentURL($_ENV['DOKU_HOST_URL']);
$init->setParserURL($_ENV['DOKU_PARSER']);

$transaction = new \PyGPaymentLib\Transaction();
$transaction->setCustomerName('Lorem Ipsum');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('081212345678');
$transaction->setCountrycode('360'); // ID
//
$transaction->setItem('Item Name');
$transaction->setAmount(rand(5000,10000) * 100);
$transaction->setDescription('Product Description');
//
// Payment Method Supported:
// 1. bank_transfer (VA) ----------- CURRENTLY ONLY THIS SUPPORTED
// 	bca
// 	permata
// 	bni
// 	cimb_niaga
// 	atm_bersama
// 2. qris
// 3. cstore (retail)
// 	alfamart
// 	indomaret
//
// $transaction->setPaymentMethod('va,danamon');
// $transaction->setPaymentMethod('va,bca');
$transaction->setPaymentMethod('va,permata');
// $transaction->setPaymentMethod('va,bni');
// $transaction->setPaymentMethod('va,cimb_niaga');
// $transaction->setPaymentMethod('va,atm_bersama');

$vendor = new \PyGPaymentLib\Vendors\Doku($init);

try {
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($response);
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage() . ':' . $e->getCode();
}