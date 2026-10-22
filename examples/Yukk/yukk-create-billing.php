<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init($_ENV['YUKK_CLIENT_ID'], $_ENV['YUKK_CLIENT_SECRET']);
$init->setPaymentURL($_ENV['YUKK_HOST_URL']);
$init->setRequestURL($_ENV['YUKK_HOST_URL']);

$vendor = new \Growinc\Payment\Vendors\Yukk($init);



// /**
//  *
//  * Create billing QRIS
//  *
//  */
// try {
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setCustomerName('Testing');
// 	$transaction->setCustomerEmail('testing@email.com');
// 	$transaction->setCustomerPhone('081212121314');
// 	$transaction->setAmount(10000);
// 	$transaction->setOrderID(time());
// 	//
// 	$transaction->setPaymentMethod('qris');
// 	$transaction->setPaymentChannel('qris');
// 	$transaction->setExpireIn(30);
// 	//
// 	$result = $vendor->SecurePayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/*
// 		{
// 			"status": "000",
// 			"message": "Your Order Request Saved. Please make payment within 1800 second(s).",
// 			"data": {
// 				"order_id": "1679055624",
// 				"nominal": 10000,
// 				"additional_field": "{\"pos_id\":\"024\",\"transaction_time\":\"2023-03-17T12:20:24\"}",
// 				"timeout_in_seconds": 1800,
// 				"timeout_datetime": "2023-03-17T19:50:24+07:00",
// 				"qris_string": "00020101021226660014ID.CO.YUKK.WWW011893600817023001408602152302230000140860303UME51440014ID.CO.QRIS.WWW0215ID20232498839190303UME5204541153033605405100005802ID5918MALTI (TARUMAJAYA)6006BEKASI610517215624101077678132051016790556240712DO2YUQO7FMP06304B207"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Create billing VA
//  *
//  */
// try {
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setCustomerName('Testing');
// 	$transaction->setCustomerEmail('testing@email.com');
// 	$transaction->setCustomerPhone('081212121314');
// 	$transaction->setAmount(10000);
// 	//
// 	/*
// 		BNI	009	Closed Amount
// 		Bank Mandiri	008	Open Amount, Closed Amount
// 		BRI	O02	Open Amount, Closed Amount
// 		BCA	014	Open Amount, Closed Amount
// 		Bank Permata/Permata Syariah	013	Open Amount, Closed Amount
// 		CIMB Niaga/CIMB Niaga Syariah	022	Open Amount, Closed Amount
// 		BTPN	213	Open Amount, Closed Amount
// 		BSI (Bank Syariah Indonesia)	451	Closed Amount
// 		Maybank BII	016	Open Amount, Closed Amount
// 		KEB Hana	484	Open Amount, Closed Amount
// 		Bank Danamon	011	Open Amount, Closed Amount
// 	*/
// 	$transaction->setPaymentMethod('va');
// 	$transaction->setPaymentChannel('009');
// 	$transaction->setExpireIn(10);
// 	$transaction->setCustomerID(time());
// 	//
// 	$result = $vendor->SecurePayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/*
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Create billing e-wallet
//  *
//  */
// try {
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setCustomerName('Testing');
// 	$transaction->setCustomerEmail('testing@email.com');
// 	$transaction->setCustomerPhone('085776554250');
// 	$transaction->setCustomerID('085776554250');
// 	$transaction->setAmount(10000);
// 	$transaction->setExpireIn(30);
// 	/*
// 	ovo_ewallet
// 	shopeepay_ewallet
// 	linkaja_ewallet
// 	dana_ewallet
// 	*/
// 	$transaction->setPaymentMethod('ewallet');
// 	$transaction->setPaymentChannel('shopeepay_ewallet');
// 	//
// 	$result = $vendor->SecurePayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/*
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



/**
 *
 * Inquiry trx
 *
 */
try {
	$result = $vendor->Inquiry((object) [
		'order_id' => '1679055213',
	]);
	extract($result);
	print_r($response);
	/*
		{
			"status": "000",
			"message": "Inquiry Success",
			"data": {
				"id": 7677599,
				"order_id": "1679055213",
				"edc_id": 16878,
				"nominal": 10000,
				"additional_field": "{\"pos_id\":\"333\",\"transaction_time\":\"2023-03-17T12:13:33\"}",
				"max_payment": 1,
				"status": "NOT_PAID",
				"total_payment": 0,
				"is_timeout": false,
				"timeout_in_seconds": 1143,
				"timeout_datetime": "2023-03-17T19:43:33+07:00"
			}
		}
	*/
} catch (\Throwable $e) {
	$vendor->ThrowErrorException($e);
}
