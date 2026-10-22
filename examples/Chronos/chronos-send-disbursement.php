<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(null, null);
$init->setMerchantKey($_ENV['CHRONOS_KEY']);
$init->setMerchantToken($_ENV['CHRONOS_TOKEN']);
$init->setPaymentURL($_ENV['CHRONOS_HOST_URL']);
$init->setRequestURL($_ENV['CHRONOS_HOST_URL']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$init->setReturnURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \Growinc\Payment\Vendors\Chronos($init);



// /**
//  *
//  * Check Balance
//  *
//  */
// try {
// 	$result = $vendor->CheckBalance();
// 	extract($result);
// 	print_r($response);
// 	/* // Success check balance
// 		{
// 			"status": "000",
// 			"data": {
// 				"active_balance": 0,
// 				"pending_balance": 0,
// 				"total_balance": 0
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }



// /**
//  *
//  * Check Balance ALT
//  *
//  */
// try {
// 	$result = $vendor->CheckBalanceAlt();
// 	extract($result);
// 	print_r($response);
// 	/* // Success check balance
// 		{
// 			"status": "000",
// 			"data": [
// 				{
// 						"name": "Nobu (Disbursement)",
// 						"account": "SPN - Nobu BI Fast Disbursement Group 1",
// 						"id": 1021,
// 						"balance": 0
// 				},
// 				{
// 						"name": "OY! Indonesia",
// 						"account": "PT KTT - taratara Disbursement Group 1",
// 						"id": 1030,
// 						"balance": 166381181201
// 				}
// 			]
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }



// /**
//  *
//  * Bank account inquiry
//  *
//  */
// try {
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setOrderID(time());
// 	$transaction->setCustomerBankAccountNumber('7700173383');
// 	$transaction->setCustomerBankCode('011');
// 	$result = $vendor->BankAccountInquiry($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success inq bank
// 		{
// 			"status": "000",
// 			"data": {
// 				"id": "69f5a60a-5df4-452a-a8bd-ad5d5ac88214",
// 				"status": "pending",
// 				"accountNumber": "7700173383",
// 				"accountName": "Lonzo Becker I",
// 				"bankCode": "011",
// 				"additionalInfo": {
// 					"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo"
// 				}
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
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setOrderID(time());
// 	$transaction->setCustomerBankAccountNumber('7700173383');
// 	$transaction->setCustomerBankCode('011');
// 	$transaction->setAmount(10500);
// 	$result = $vendor->OnlineTransferExec($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success disb
// 		{
// 			"status": "000",
// 			"data": {
// 				"id": "e8de8410-0ce7-43d0-bc4a-11eb02439f24",
// 				"merchantRef": "1695041561",
// 				"status": "pending",
// 				"feePayer": "customer",
// 				"amount": 10500,
// 				"fee": 3000,
// 				"totalAmount": 7500,
// 				"expiredDate": "2023-09-18T20:52:42+07:00",
// 				"additionalInfo": {
// 					"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo"
// 				},
// 				"disbursement": {
// 					"bankCode": "011",
// 					"recipientAccount": "7700173383",
// 					"recipientName": null
// 				}
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
// 		'id' => 'c1ddc1cc-7652-4bb7-bff8-7409aee76211',
// 		'resend_callback' => false,
// 	];
// 	//
// 	$result = $vendor->CheckTransfer($request);
// 	extract($result);
// 	print_r($response);
// 	/*
// 		{
// 			"status": "000",
// 			"data": {
// 				"id": "c1ddc1cc-7652-4bb7-bff8-7409aee76211",
// 				"merchantRef": "1695033936",
// 				"status": "pending",
// 				"feePayer": "customer",
// 				"amount": 10500,
// 				"fee": 3000,
// 				"totalAmount": 7500,
// 				"expiredDate": "2023-09-18T18:45:38+07:00",
// 				"additionalInfo": {
// 					"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo"
// 				},
// 				"disbursement": {
// 					"bankCode": "011",
// 					"recipientAccount": "7700173383",
// 					"recipientName": null
// 				}
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }