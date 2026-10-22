<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init($_ENV['GEMPAY_MERCHANT_ID'], $_ENV['GEMPAY_SECRET']);
$init->setParam('GEMPAY_PROJECT_NO', $_ENV['GEMPAY_PROJECT_NO']);
$init->setRequestURL($_ENV['GEMPAY_HOST_URL_DISBURSEMENT']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \Growinc\Payment\Vendors\GemPay($init);



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
// 				"merchant_id": "KMB0001",
// 				"active_balance": "1079400",
// 				"pending_balance": "800000"
// 			}
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
// 	$time = time();
// 	$transaction = new \Growinc\Payment\Transaction();
// 	// $transaction->setRequestID($time);
// 	$transaction->setReferenceNumber($time);
// 	$transaction->setAmount('10000');
// 	$transaction->setTransferMethod('bank'); // bank / wallet
// 	$transaction->setCustomerBankAccountNumber('7700173383');
// 	$transaction->setCustomerBankCode('011');
// 	$transaction->setReqDateTime(date('Y-m-d H:i:s', $time));
// 	$result = $vendor->BankAccountInquiry($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success bank account inquiry
// 		{
// 			"status": "000",
// 			"data": {
// 				"partner_ref_id": "1716270835",
// 				"inquiry_id": "240521125358QZHN",
// 				"bank_code": "011",
// 				"account_name": "7700173383",
// 				"account_no": "7700173383",
// 				"account_bank": "Bank Danamon & Danamon Syariah",
// 				"amount": 10000,
// 				"admin_fee": 2000,
// 				"total_amount": 12000,
// 				"transaction_datetime": "2024-05-21 12:53:55",
// 				"merchant_id": "KMB0001",
// 				"project_no": "UJK87AA"
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
// 	$time = time();
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setOrderID('240521130455ZGLJ');
// 	$transaction->setDescription('TEST-TRANSFER');
// 	$transaction->setReqDateTime(date('Y-m-d H:i:s', $time));
// 	$result = $vendor->OnlineTransferExec($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success disb
// 		{
// 			"status": "000",
// 			"data": {
// 				"partner_ref_id": "1716271492",
// 				"bank_code": "011",
// 				"account_no": "7700173383",
// 				"account_bank": "Bank Danamon & Danamon Syariah",
// 				"amount": 10000,
// 				"admin_fee": 2000,
// 				"total_amount": 12000,
// 				"transaction_datetime": "2024-05-21 13:05:04",
// 				"merchant_id": "KMB0001",
// 				"project_no": "UJK87AA",
// 				"ref_id": "240521130507N64P",
// 				"status": "success"
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
// 		'partner_ref_id' => '1716271492',
// 	];
// 	//
// 	$result = $vendor->CheckTransfer($request);
// 	extract($result);
// 	print_r($response);
// 	/* // Success check status
// 		{
// 			"status": "000",
// 			"data": {
// 				"transaction_datetime": "2024-05-21 13:05:04",
// 				"account_no": "7700173383",
// 				"account_bank": "Bank Danamon & Danamon Syariah",
// 				"amount": "10000",
// 				"description": "TEST-TRANSFER",
// 				"merchant_id": "KMB0001",
// 				"status": "Success",
// 				"admin_fee": "2000",
// 				"partner_ref_id": "1716271492",
// 				"ref_id": "240521130507N64P"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }