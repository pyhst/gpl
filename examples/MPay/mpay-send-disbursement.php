<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(null, null);
$init->setMerchantKey($_ENV['MPAY_KEY']);
$init->setMerchantToken($_ENV['MPAY_TOKEN']);
$init->setPaymentURL($_ENV['MPAY_HOST_URL']);
$init->setRequestURL($_ENV['MPAY_HOST_URL']);
// $init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$init->setDisbursementCallbackURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \PyGPaymentLib\Vendors\MPay($init);



// /**
//  *
//  * Bank account inquiry
//  *
//  */
// try {
// 	$transaction = new \PyGPaymentLib\Transaction();
// 	$transaction->setOrderID(time());
// 	$transaction->setCustomerBankAccountNumber('7700173383');
// 	$transaction->setCustomerBankCode('DANAMON');
// 	$result = $vendor->BankAccountInquiry($transaction);
// 	extract($result);
// 	print_r($response);
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
// 	$transaction->setTransferMethod('disburst_c2');
// 	$result = $vendor->OnlineTransferExec($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success disb
// 		{
// 			"status": "000",
// 			"data": {
// 				"success": true,
// 				"data_payment": {
// 					"id": "DGP-ID-4784922",
// 					"create_time": 1674652232,
// 					"status": "pending",
// 					"statustrx_time": 1674652232,
// 					"referenceId": "1674652233",
// 					"paymentType": "send",
// 					"Amount": 10500,
// 					"fee": 6500,
// 					"totalAmount": 17000,
// 					"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo",
// 					"paymentMethod": {
// 						"trans_hash": "DGP-7ce798b9-4216-4775-ad6a-60ed66c91867",
// 						"trxid_bank": "DGP-7ce798b9-4216-4775-ad6a-60ed66c91867",
// 						"bankShort": "DANAMON",
// 						"recipient_name": "",
// 						"recipient_account": "7700173383",
// 						"amount": 10500,
// 						"note": "",
// 						"email": "",
// 						"schedule_date": ""
// 					}
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
// 		'account_no' => '7700173383',
// 		'reference_id' => '1674652233',
// 	];
// 	//
// 	$result = $vendor->CheckTransfer($request);
// 	extract($result);
// 	print_r($response);
// 	/*
// 		{
// 			"status": "000",
// 			"data": [{
// 				"id": "DGP-ID-4784922",
// 				"create_time": 1674652232,
// 				"status": "sukses",
// 				"statustrx_time": 1674652252,
// 				"referenceId": "1674652233",
// 				"paymentType": "disburst_c2",
// 				"Amount": 10500,
// 				"fee": "6500",
// 				"totalAmount": 17000,
// 				"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo",
// 				"paymentMethod": {
// 					"trans_hash": "b579dd09a412a6f9860b4f1cf3b7cec6",
// 					"trxid_bank": "DGP-7ce798b9-4216-4775-ad6a-60ed66c91867",
// 					"bankShort": "disburst_c2",
// 					"recipient_account": "7700173383",
// 					"recipient_name": null,
// 					"amount": 10500,
// 					"note": ""
// 				}
// 			}]
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }



// /**
//  *
//  * Check Balance
//  *
//  */
// try {
// 	$result = $vendor->CheckBalance();
// 	extract($result);
// 	print_r($response);
// 	/*
// 		{
// 			"status": "000",
// 			"data": {
// 				"data_user": {
// 					"username": "newgame",
// 					"email": "newgame@gmail.com",
// 					"alamat": null,
// 					"balance": 30136311210,
// 					"balance_pending": "0"
// 				},
// 				"data_project": {
// 					"NameProject": "Newgame",
// 					"DomainProject": "newgame.digippob.com",
// 					"StatusProject": "active",
// 					"ipAddressProject": "180.210.206.235,190.92.212.111,180.210.206.235,190.92.212.111,114.119.181.209,180.210.205.125"
// 				}
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }

