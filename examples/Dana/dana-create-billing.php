<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['DANA_MERCHANT_ID']
);
$init->setPaymentURL($_ENV['DANA_HOST_URL']);
$init->setRequestURL($_ENV['DANA_HOST_URL']);
$init->setParams([
	'client_id' => $_ENV['DANA_CLIENT_ID'],
	'client_secret' => $_ENV['DANA_CLIENT_SECRET'],
	'private_key' => $_ENV['DANA_PRIVATE_KEY'],
]);
$vendor = new \Growinc\Payment\Vendors\Dana($init);



/**
 *
 * Get Token
 *
 */
try {
	$result = $vendor->GetToken([]);
	extract($result);
	print_r($response);
	/*
{"responseCode":"2007300","responseMessage":"Successful","accessToken":"eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE2NjUwODAyMjcsImlzcyI6ImlkLmRhbmEiLCJleHAiOjE2NjUwODA4Mjd9.iPcnyAKTTo9Yoh44vcUxYz-GXbCFxbZAOxllPoeQwFhbSptz0qSEMnpa9WNmFo8ZwDJieW2gQAjaKLG2fkZIxLykw8oRgY11UBYIwxsoFVmjrcd2gLNmLVdbbtxqtgeiZhr4POn5JOYCYaMmq0sf7w8RHTAkpTjy_xmOhIk73HKt59VQ1HcILH9tC9TU8RwXhT2sOSIWC3kt898BRjJ9rmd20Kd7nhDgMXsvwHWnCRPZep8e0Q73hoFSPKuTaBFWQ1quH_b13uIlb1L6YM_PSQgav8hcGJTr1cLmEqEeLOK-itYD0j1WhUWZP736t3_RlA4mS45IlxOL6ZwFdPHRrA","tokenType":"Bearer","expiresIn":"600"}
	*/
	$content = (object) json_decode($result['response']['content']);
	$init->setParams([
		'client_id' => $_ENV['DANA_CLIENT_ID'],
		'client_secret' => $_ENV['DANA_CLIENT_SECRET'],
		'private_key' => $_ENV['DANA_PRIVATE_KEY'],
		'token' => $content->accessToken,
	]);
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}



/**
 *
 * Create billing
 *
 */
try {
	$transaction = new \Growinc\Payment\Transaction();
	$transaction->setPaymentMethod('qris');
	$transaction->setPaymentChannel('');
	//
	$transaction->setMerchantID($_ENV['DANA_MERCHANT_ID']);
	$transaction->setOrderID($transaction->getTime());
	$transaction->setAmount(10000);
	$transaction->setCurrency('IDR');
	$transaction->setExpireIn(2);
	/* // Success
		{
			"status": "000",
			"data": {
				"transaction_id": "1680190325997957D10E0F87D0C64CB0943A071AA06C1F49",
				"external_id": "1680190325",
				"amount": 10000,
				"qr_string": "R7FMgofJAmQVDnIGZ5DJ3PVBhawX4tDQZM8i8tSR7QVduQLiS5xYmKnUraIdvY8yyLrZVWIvEMrfNFzTi9vIVv0JKZPBqIDTA5GrHoiUJFnRYOXue3JD6LW7dhnjndwVAOqS8L5OtFvBvCNtByltsZGWuHmZ8rMgwaRjDuS8nbTZ96LoIoceScb2CQhjN4kH4nNas50MDFBy6VtvT9agtIqnSwUZqXMSVY6Dff1u0Wd5xrhQ5kswq0Kf4JPD3PM4",
				"type": "QRIS_DYNAMIC",
				"status": "PROCESSING",
				"created_date": "2023-03-30 22:32:06",
				"email": null,
				"phone_number": null,
				"fullname": null,
				"session_time": 120
			}
		}
	*/
	//
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($response);
	/*
	*/
} catch (\Throwable $e) {
	$vendor->ThrowErrorException($e);
}



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



// /**
//  *
//  * Testing payment
//  *
//  */
// try {
// 	$result = $vendor->TestPayment((object) [
// 		'transaction_id' => '1680190325997957D10E0F87D0C64CB0943A071AA06C1F49',
// 		'callback_url' => $_ENV['MOTIONPAY_CALLBACK_URL'],
// 	]);
// 	extract($result);
// 	print_r($response);
// 	/*
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }
