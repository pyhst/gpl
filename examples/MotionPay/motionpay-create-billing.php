<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init($_ENV['MOTIONPAY_CLIENT_KEY'], $_ENV['MOTIONPAY_SERVER_KEY']);
$init->setPaymentURL($_ENV['MOTIONPAY_HOST_URL']);
$init->setRequestURL($_ENV['MOTIONPAY_HOST_URL']);

$vendor = new \PyGPaymentLib\Vendors\MotionPay($init);



/**
 *
 * Get Token
 *
 */
try {
	$result = $vendor->GetToken();
	extract($result);
	// print_r($response);
	$init->setParams([
		'token' => json_decode($response['content'])->token,
	]);
	/*
		{
			"token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJjbGllbnRfa2V5IjoiRk0tMDIyNC02MTZiZjU3OTQ1ZGU2MjNjIiwic2VydmVyX2tleSI6IkZNUEEyNTVkNjk2MGNkZjg0YTUyZDY2ODM1NTgxYjAwZTBhMTc1MWMiLCJleHAiOjE2ODAyNzY1OTJ9.qq5vQ8LBFO58ofMJ6g7MyH9hPVw6epBvRV7Kfih8eAc"
		}
	*/
} catch (\Throwable $e) {
	$vendor->ThrowErrorException($e);
}



// /**
//  *
//  * Create billing
//  *
//  */
// try {
// 	$transaction = new \PyGPaymentLib\Transaction();
// 	$transaction->setPaymentMethod('qris');
// 	$transaction->setPaymentChannel('');
// 	$transaction->setReferenceNumber($transaction->getTime());

// 	$transaction->setMerchantID('TERMINAL-1');
// 	$transaction->setOrderID($transaction->getTime());
// 	$transaction->setAmount(10000);
// 	$transaction->setDescription('Test Payment');
// 	/* // Success
// 		{
// 			"status": "000",
// 			"data": {
// 				"transaction_id": "1680190325997957D10E0F87D0C64CB0943A071AA06C1F49",
// 				"external_id": "1680190325",
// 				"amount": 10000,
// 				"qr_string": "R7FMgofJAmQVDnIGZ5DJ3PVBhawX4tDQZM8i8tSR7QVduQLiS5xYmKnUraIdvY8yyLrZVWIvEMrfNFzTi9vIVv0JKZPBqIDTA5GrHoiUJFnRYOXue3JD6LW7dhnjndwVAOqS8L5OtFvBvCNtByltsZGWuHmZ8rMgwaRjDuS8nbTZ96LoIoceScb2CQhjN4kH4nNas50MDFBy6VtvT9agtIqnSwUZqXMSVY6Dff1u0Wd5xrhQ5kswq0Kf4JPD3PM4",
// 				"type": "QRIS_DYNAMIC",
// 				"status": "PROCESSING",
// 				"created_date": "2023-03-30 22:32:06",
// 				"email": null,
// 				"phone_number": null,
// 				"fullname": null,
// 				"session_time": 120
// 			}
// 		}
// 	*/
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
//  * Inquiry trx
//  *
//  */
// try {
// 	$result = $vendor->Inquiry((object) [
// 		'transaction_id' => '1680190325997957D10E0F87D0C64CB0943A071AA06C1F49',
// 		'payment_method' => 'qris',
// 	]);
// 	extract($result);
// 	print_r($response);
// 	/* // Success
// 		{
// 			"status": "000",
// 			"data": {
// 				"transaction_id": "1680190325997957D10E0F87D0C64CB0943A071AA06C1F49",
// 				"external_id": "1680190325",
// 				"amount": 10000,
// 				"qr_string": "R7FMgofJAmQVDnIGZ5DJ3PVBhawX4tDQZM8i8tSR7QVduQLiS5xYmKnUraIdvY8yyLrZVWIvEMrfNFzTi9vIVv0JKZPBqIDTA5GrHoiUJFnRYOXue3JD6LW7dhnjndwVAOqS8L5OtFvBvCNtByltsZGWuHmZ8rMgwaRjDuS8nbTZ96LoIoceScb2CQhjN4kH4nNas50MDFBy6VtvT9agtIqnSwUZqXMSVY6Dff1u0Wd5xrhQ5kswq0Kf4JPD3PM4",
// 				"type": "QRIS_DYNAMIC",
// 				"status": "SUCCESS",
// 				"created_date": "2023-03-30T22:32:06",
// 				"channel": "QRIS"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



/**
 *
 * Testing payment
 *
 */
try {
	$result = $vendor->TestPayment((object) [
		'transaction_id' => '1680190325997957D10E0F87D0C64CB0943A071AA06C1F49',
		'callback_url' => $_ENV['MOTIONPAY_CALLBACK_URL'],
	]);
	extract($result);
	print_r($response);
	/*
	*/
} catch (\Throwable $e) {
	$vendor->ThrowErrorException($e);
}
