<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init($_ENV['OY_USERNAME'], $_ENV['OY_API_KEY']);
$init->setPaymentURL($_ENV['OY_HOST_URL']);
$init->setRequestURL($_ENV['OY_HOST_URL']);
$init->setCallbackURL($_ENV['OY_CALLBACK_URL']);
$init->setReturnURL($_ENV['OY_RETURN_URL']);

$vendor = new \Growinc\Payment\Vendors\Oy($init);



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
// 			"data": {
// 				"trx_id": "d73fecb7-b202-486c-b89e-141df7208133",
// 				"partner_trx_id": "0078090538",
// 				"receive_amount": 10000,
// 				"trx_expiration_time": "2023-03-06 16:15:38",
// 				"payment_info": {
// 					"qris_url": "https:\/\/i.snipboard.io\/kYLlFf.jpg"
// 				}
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
// 		{
// 			"status": "000",
// 			"data": {
// 				"id": "dae76e43-8511-4169-8eaa-ab039aadc2b5",
// 				"amount": 10000,
// 				"va_number": "8255600000009291",
// 				"bank_code": "009",
// 				"is_open": false,
// 				"is_single_use": true,
// 				"expiration_time": 1678091013000,
// 				"va_status": "WAITING_PAYMENT",
// 				"username_display": "Testing",
// 				"partner_user_id": "1678090412",
// 				"counter_incoming_payment": 0,
// 				"trx_expiration_time": 1678091013000,
// 				"partner_trx_id": "0078090412",
// 				"trx_counter": 1
// 			}
// 		}
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
// 		{
// 			"status": "000",
// 			"data": {
// 				"ewallet_trx_status": "WAITING_PAYMENT",
// 				"trx_id": "07bcf2de-b33f-45f8-84ad-9233b4e768a5",
// 				"ref_number": "2303CSL90597",
// 				"customer_id": "085776554250",
// 				"partner_trx_id": "0078090726",
// 				"amount": 10000,
// 				"ewallet_code": "shopeepay_ewallet",
// 				"ewallet_url": "https:\/\/api-stg.oyindonesia.com\/"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Inquiry trx
//  *
//  */
// try {
// 	$result = $vendor->Inquiry((object) [
// 		'trx_id' => '0077950175',
// 	]);
// 	extract($result);
// 	print_r($response);
// 	/*
// 		{
// 			"status": "000",
// 			"data": {
// 				"trx_id": "adbf625a-9ad0-4f6d-b820-ed5db81fcb96",
// 				"partner_trx_id": "0077950175",
// 				"request_amount": 10000,
// 				"received_amount": 0,
// 				"payment_status": "WAITING_PAYMENT",
// 				"trx_expiration_time": "2023-03-05 01:16:15",
// 				"need_frontend": false,
// 				"payment_info": {
// 					"qris_url": "https:\/\/i.snipboard.io\/kYLlFf.jpg"
// 				},
// 				"payment_routing": []
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }
