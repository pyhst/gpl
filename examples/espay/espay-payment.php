<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['ESPAY_MERCHANT_CODE'],
	$_ENV['ESPAY_API_KEY']
);
$init->setPaymentURL('https://sandbox-api.espay.id/');
$init->setParams([
	'signature' => $_ENV['ESPAY_SIGNATURE'],
	'password' => $_ENV['ESPAY_PASSWORD'],
]);
$transaction = new \PyGPaymentLib\Transaction();
$transaction->setInvoiceNo('TEST' . time());
$transaction->setCustomerName('Testing');
$transaction->setCustomerEmail('testing@email.com');
$transaction->setCustomerPhone('6281213141516');
//
$transaction->setItem('Product');
// $transaction->setAmount(rand(5000,10000) * 100);
$transaction->setAmount(150000);
$transaction->setDescription('Product Description');
//
// $transaction->setPaymentMethod('va,008');
//
$transaction->setPaymentMethod('ibank,014'); // BCA VA Online
// $transaction->setPaymentMethod('ibank,022'); // VA CIMB Niaga
// $transaction->setPaymentMethod('ibank,011'); // Danamon Online Banking
// $transaction->setPaymentMethod('ibank,046'); // DBS VA
// $transaction->setPaymentMethod('ibank,008'); // Mandiri VA
// $transaction->setPaymentMethod('ibank,016'); // Maybank VA
// $transaction->setPaymentMethod('ibank,013'); // Permata VA
//
// $transaction->setPaymentMethod('ewallet,ovo');
// $transaction->setPaymentMethod('ewallet,jenius');
// $transaction->setPaymentMethod('ewallet,gopay');
// $transaction->setPaymentMethod('ewallet,linkaja');
//
// $transaction->setPaymentMethod('cstore,066'); // indomaret
// $transaction->setPaymentMethod('cstore,026'); // alfamart

$vendor = new \PyGPaymentLib\Vendors\Espay($init);

try {
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($response);
	/* // Success
	{
		"status": "000",
		"data": {
			"rq_uuid": "INV24011177",
			"rs_datetime": "2021-06-18 17:13:05",
			"error_code": "0000",
			"error_message": "",
			"va_number": "8920800481110560",
			"expired": "2021-06-18 19:13:04",
			"description": "Payment",
			"total_amount": "100000.00",
			"amount": "100000.00",
			"fee": "0.00",
			"bank_code": "008"
		}
	}
	 */
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage() . ':' . $e->getCode();
}
