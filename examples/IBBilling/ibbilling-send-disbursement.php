<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init($_ENV['IBBILLING_MERCHANT_UUID'], null);
$init->setPaymentURL($_ENV['IBBILLING_HOST_URL']);
$init->setRequestURL($_ENV['IBBILLING_HOST_URL']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$init->setReturnURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \Growinc\Payment\Vendors\IBBilling($init);



// /**
//  *
//  * Check Balance
//  *
//  */
// try {
// 	$request = (object) [
// 		'service_code' => 'DISBURSEMENT',
// 		'owner_id' => 'PGA',
// 		'type' => 'pg',
// 	];
// 	//
// 	$result = $vendor->CheckBalance($request);
// 	extract($result);
// 	print_r($response);
// 	/* // Success check balance
// 		{
// 			"status": "000",
// 			"message": "Request complete",
// 			"data": [
// 				{
// 						"credential_id": 1,
// 						"credential_name": "CRONOS",
// 						"available_balance": 1352880,
// 						"pending_balance": 0
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
// 	$transaction->setServiceCode('DISBURSEMENT');
// 	$transaction->setMerchantID('PGA');
// 	$transaction->setPaymentType('pg');
// 	//
// 	$transaction->setCustomerBankCode('011');
// 	$transaction->setCustomerBankAccountNumber('7700173383');
// 	$result = $vendor->BankAccountInquiry($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success bank account inquiry
// 		{
// 			"status": "000",
// 			"message": "Request complete",
// 			"data": {
// 				"credential_id": 1,
// 				"credential_name": "CRONOS",
// 				"transaction_id": "17418c13-08c8-4e1c-9f0f-e7a9980ea7fb",
// 				"beneficiary_bank_code": "014",
// 				"beneficiary_bank_account_number": "0092292191",
// 				"beneficiary_bank_account_name": "Ben Turner"
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
// 	$transaction->setServiceCode('DISBURSEMENT');
// 	$transaction->setMerchantID('PGA');
// 	$transaction->setPaymentType('pg');
// 	// $transaction->setParam('service_id', 20);
// 	// $transaction->setParam('credential_id', 6);
// 	//
// 	$transaction->setReferenceNumber($time);
// 	$transaction->setTransactionRemark('DISB' . $time);
// 	$transaction->setDescription('DISB' . $time);
// 	$transaction->setCustomerID('1234567890');
// 	$transaction->setCustomerName('Testing');
// 	$transaction->setCustomerPhone('081212121314');
// 	$transaction->setCustomerEmail('tester@gmail.com');
// 	$transaction->setAmount(50000);
// 	$transaction->setCustomerBankCode('011');
// 	$transaction->setCustomerBankAccountNumber('7700173383');
// 	$result = $vendor->OnlineTransferExec($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success disb
// 		{
// 			"status": "000",
// 			"message": "Disbursement successfully created",
// 			"data": {
// 				"uuid": "9c8b3fbe-11f4-497f-8b93-3e4db4c2d52d",
// 				"reference_num": "1721218837",
// 				"invoice_num": "0DGAVSDRQK9G650I",
// 				"transaction_id": "44af5f26-9b85-43fc-9e0d-49e9ef8cfd0b",
// 				"type": "pg",
// 				"name": "DISB1721218837",
// 				"description": "DISB1721218837",
// 				"items": null,
// 				"currency": "360",
// 				"net_amount": 50000,
// 				"fee_amount": 3900,
// 				"surcharge_amount": 0,
// 				"service_fee_amount": 0,
// 				"fee_total": 3900,
// 				"fee_waived": 0,
// 				"amount": 46100,
// 				"sent_amount": 0,
// 				"status": 0,
// 				"status_descr": "PENDING",
// 				"created_at": "2024-07-17 19:20:38",
// 				"additional_info": {
// 						"customer_id": "1234567890",
// 						"customer_name": "TESTING",
// 						"customer_phone": "081212121314",
// 						"customer_email": "tester@gmail.com",
// 						"customer_details": null,
// 						"beneficiary_id": null,
// 						"beneficiary_name": null,
// 						"beneficiary_bank_code": "011",
// 						"beneficiary_bank_account_number": "7700173383",
// 						"beneficiary_bank_account_name": null,
// 						"beneficiary_swift_code": "011",
// 						"beneficiary_type": null
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
// 		'service_code' => 'DISBURSEMENT',
// 		'owner_id' => 'PGA',
// 		'type' => 'pg',
// 		'reference_num' => '1721218837',
// 	];
// 	//
// 	$result = $vendor->CheckTransfer($request);
// 	extract($result);
// 	print_r($response);
// 	/* // Success check transfer
// 		{
// 			"status": "000",
// 			"message": "Request complete",
// 			"data": {
// 				"credential_id": 1,
// 				"credential_name": "CRONOS",
// 				"uuid": "9c8b3fbe-11f4-497f-8b93-3e4db4c2d52d",
// 				"reference_num": "1721218837",
// 				"invoice_num": "0DGAVSDRQK9G650I",
// 				"provider_transaction_id": "44af5f26-9b85-43fc-9e0d-49e9ef8cfd0b",
// 				"type": "pg",
// 				"amount": 46100,
// 				"status": 0,
// 				"status_descr": "PENDING",
// 				"created_at": "2024-07-17 19:20:38",
// 				"sent_at": null,
// 				"settled_at": null,
// 				"additional_info": {
// 						"customer_id": "1234567890",
// 						"customer_name": "TESTING",
// 						"customer_phone": "081212121314",
// 						"customer_email": "tester@gmail.com",
// 						"customer_details": null,
// 						"beneficiary_id": null,
// 						"beneficiary_name": null,
// 						"beneficiary_bank_code": "011",
// 						"beneficiary_bank_account_number": "7700173383",
// 						"beneficiary_bank_account_name": null,
// 						"beneficiary_swift_code": "011",
// 						"beneficiary_type": null
// 				}
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }