<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['MACAU_AGENT_CODE'],
	$_ENV['MACAU_AGENT_DIVISION']
);
$init->setPaymentURL($_ENV['MACAU_HOST_URL']);

$order_id = time();
$invoice_no = 'INV' . $order_id;

$transaction = new \Growinc\Payment\Transaction();
$transaction->setOrderID($order_id);
$transaction->setInvoiceNo($invoice_no);
$transaction->setCurrency('IDR');
//
$transaction->setItem('Testing');
$transaction->setAmount(rand(5000,10000) * 10);
$transaction->setDescription('PAYMENT FOR ' . $invoice_no);
//
$transaction->setCustomerName('testing');
$transaction->setCustomerEmail('testing@gmail.com');
$transaction->setCustomerPhone('088812131415');
$transaction->setCustomerAddress('Jakarta Selatan');
$transaction->setCountrycode('ID');
//
$transaction->setPaymentMethod('va');
$transaction->setParams([
	'type' => 'deposit',
	// 'type' => 'withdraw',
	'bank_accnum' => '0372052647',
	'bank_accname' => 'SUPINAH',
	'customer_bank_name' => 'BCA',
	'customer_bank_accnum' => '1234567890',
	'customer_bank_accname' => 'HUMAN',
]);

$vendor = new \Growinc\Payment\Vendors\Mcu($init);

try {
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($response);
} catch (\Throwable $e) {
	echo $vendor->ThrowError($e);
}
