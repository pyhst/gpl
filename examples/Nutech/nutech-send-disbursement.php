<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init($_ENV['NUTECH_MERCHANT_ID'], $_ENV['NUTECH_PRIVATE_KEY']);
$init->setMerchantKey($_ENV['NUTECH_ALGO_SALT']);
$init->setPaymentURL($_ENV['NUTECH_HOST_URL']);
$init->setRequestURL($_ENV['NUTECH_HOST_URL']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$init->setReturnURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \PyGPaymentLib\Vendors\Nutech($init);



// /**
//  *
//  * Bank account inquiry
//  *
//  */
// try {
// 	$transaction = new \PyGPaymentLib\Transaction();
// 	$transaction->setCustomerBankCode('BCA');
// 	$transaction->setCustomerBankAccountNumber('6044222099');
// 	$result = $vendor->BankAccountInquiry($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* Success INQUIRY
// 		{
// 			"status": 0,
// 			"message": "SUCCESS",
// 			"data": {
// 				"accountName": "MAJU SELARAS KOMPUTER PT",
// 				"code": 0,
// 				"msg": "SUCCESS",
// 				"sign": "8d6c657cb8160a5f4cd59f5f99426ffcaca18f8452d80d16de93bb2247f890fdijjkzajsbsqyedansAYo7nHbDY%2FVrxaQpALtg2IuFEO9FXFeBtn78sodQcnKv0Mjsd5bspYV3G6EHoK0"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Send disbursement
//  *
//  */
// try {
// 	$transaction = new \PyGPaymentLib\Transaction();
// 	$transaction->setOrderID(time());
// 	$transaction->setCustomerBankAccountNumber('7700173383');
// 	$transaction->setCustomerBankCode('DANAMON');
// 	$transaction->setAmount(10500);
// 	$transaction->setCustomerPhone('081212121314');
// 	$transaction->setCustomerName('tester');
// 	$result = $vendor->OnlineTransferExec($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success DISBURSEMENT
// 		{
// 			"status": 0,
// 			"message": "SUCCESS",
// 			"data": {
// 				"merTransactionId": "1692611766",
// 				"transactionId": "1692611766038b0d71a1e",
// 				"code": 0,
// 				"msg": "SUCCESS",
// 				"sign": "rH6Ei8Jwvg+shGtcfF\/eBAkqkOUDNad9H0Knsyn7vW0By4EsoGHKTA=="
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Check Transfer Status
//  *
//  */
// try {
// 	$request = (object) [
// 		'transactionId' => '1692611766038b0d71a1e',
// 		'merTransactionId' => '1692611766',
// 	];
// 	//
// 	$result = $vendor->CheckTransfer($request);
// 	extract($result);
// 	print_r($response);
// 	/* // Success check DISBURSEMENT
// 		{
// 			"status": 0,
// 			"message": "SUCCESS",
// 			"data": {
// 				"merTransactionId": "1692611766",
// 				"amount": 10500,
// 				"bankCode": "DANAMON",
// 				"bankAccount": "7700173383",
// 				"mobile": "081212121314",
// 				"name": "tester",
// 				"status": 0,
// 				"createTime": "2023-08-21T09:56:06.000+00:00",
// 				"updateTime": "2023-08-21T09:56:11.000+00:00",
// 				"code": 0,
// 				"msg": "SUCCESS",
// 				"sign": "sEOsYSKqhsEla+CDNH0TnAIwctRwPgGGNdG+Ye7SOrtKnKgkdCCBNw=="
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }



// /**
//  *
//  * Check Merchant
//  *
//  */
// try {
// 	$request = (object) [];
// 	//
// 	$result = $vendor->CheckBalance($request);
// 	extract($result);
// 	print_r($response);
// 	/* // Success check balance
// 		{
// 			"status": 0,
// 			"message": "SUCCESS",
// 			"data": {
// 				"availableBalance": 0,
// 				"disabledBalance": 0,
// 				"code": 0,
// 				"msg": "SUCCESS",
// 				"sign": "Oiyo6OlU7IQXO98IYEiPOQkiqOw29cwdoCpR2kPiv5GR8EjMs0YsKA=="
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }