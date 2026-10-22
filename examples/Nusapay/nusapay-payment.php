<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['NUSAPAY_CLIENT_ID'],
	$_ENV['NUSAPAY_CLIENT_SECRET']
);
$vendor = new \Growinc\Payment\Vendors\Nusapay($init);

/**
 *
 * Payment
 *
 */
try {
	$time = time();
	$date = date('ymdHis', $time);
	$transaction = new \Growinc\Payment\Transaction();
	$transaction->setTransactionID($time);
	$transaction->setReferenceNumber($date);
	$transaction->setAmount(rand(500,1000));
	$transaction->setDescription('TESTING-' . $date);
	$transaction->setRemark('TEST ' . $date);
	$transaction->setCustomerUserID('TESTING');
	$transaction->setCustomerName('TESTING');
	$transaction->setCustomerEmail('testing@gmail.com');
	$transaction->setCustomerPhone('081319116374');
	// $transaction->setExpireAt(date('YmdHis', strtotime('+2 hours')));
	$transaction->setExpireAt('+2 hours');
	//
	// va MANDIRI
		// $init->setPaymentURL($_ENV['NUSAPAY_IBANK_URL']);
		// $init->setClientID('G-PAY.TO');
		// $transaction->setVirtualAccountNumber('89811' . $date);
		// $transaction->setPaymentMethod('va');
		// $transaction->setPaymentBank('mandiri');
	// // va BNI
		// $init->setPaymentURL($_ENV['NUSAPAY_IBANK_URL']);
		// $init->setClientID('G-PAY.TO');
		// $transaction->setVirtualAccountNumber('8611' . $date);
		// $transaction->setPaymentMethod('va');
		// $transaction->setPaymentBank('bni');
	// emoney NUSAPAY
		// $init->setPaymentURL($_ENV['NUSAPAY_PAYMENT_URL']);
		// $init->setRequestURL($_ENV['NUSAPAY_ACCOUNT_URL']);
		// $init->setCallbackURL($_ENV['NUSAPAY_CALLBACK_URL']);
		// $transaction->setMerchantID('67');
		// $transaction->setPaymentMethod('emoney');
	// cart NUSAPAY
		$init->setPaymentURL($_ENV['NUSAPAY_CART_URL']);
		$init->setRequestURL($_ENV['NUSAPAY_ACCOUNT_URL']);
		$init->setCallbackURL($_ENV['NUSAPAY_CALLBACK_URL']);
		$init->setParams([
			'NUSAPAY_X_ID' => $_ENV['NUSAPAY_X_ID'],
			'NUSAPAY_X_AUTH' => $_ENV['NUSAPAY_X_AUTH'],
		]);
		$transaction->setMerchantID('67');
		$transaction->setPaymentMethod('cart');
	//
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($response);
	//
	/* // Success VA ibank
		{
			"status": "000",
			"data": {
				"bank": "MANDIRI",
				"trx_id": "1658215149",
				"refnum": "220719141909",
				"va_number": "89811220719141909",
				"va_type": "C",
				"client_id": "G-PAY.TO",
				"customer_id": "TESTING",
				"customer_name": "TESTING",
				"customer_email": "testing@gmail.com",
				"customer_phone": "088812131415",
				"amount": 626100,
				"fee": 0,
				"paid_amount": 626100,
				"cumulative_amount": 0,
				"currency": "IDR",
				"status": 0,
				"created_at": "2022-07-19 14:19:08"
			}
		}
	*/
	/* // Success EMONEY cart
		{
			"status": "000",
			"data": {
				"orde_id": "PO000000003663",
				"order_id": "PO000000003663",
				"reffnum": "220728170534"
			}
		}
	*/
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}
