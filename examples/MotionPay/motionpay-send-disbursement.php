<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init($_ENV['MOTIONPAY_DISBURSEMENT_CLIENT_KEY'], $_ENV['MOTIONPAY_DISBURSEMENT_SERVER_KEY']);
$init->setPaymentURL($_ENV['MOTIONPAY_DISBURSEMENT_HOST_URL']);
$init->setRequestURL($_ENV['MOTIONPAY_DISBURSEMENT_HOST_URL']);

$vendor = new \Growinc\Payment\Vendors\MotionPay($init);



/**
 *
 * Get Token
 *
 */
try {
	$result = $vendor->GetTokenDisbursement();
	extract($result);
	// print_r($response);
	$init->setParams([
		'token' => json_decode($response['content'])->token,
	]);
	/*
		{
			"token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJjbGllbnRfa2V5IjoiUHNnWGFOVGxlSXhoajkzdVdMb2RLTnBSdUEwIiwic2VydmVyX2tleSI6InVkME93dklEODJDX2NkMXVud2RyNFdHYXFEayIsImV4cCI6MTY4MDM2MjA3Nn0.Xw-v2RyN00sWY-kbZ4APNRhg5QJD3mRfpToCsO1wklg"
		}
	*/
} catch (\Throwable $e) {
	$vendor->ThrowErrorException($e);
}



// /**
//  *
//  * Get Balance
//  *
//  */
// try {
// 	$result = $vendor->CheckBalance();
// 	extract($result);
// 	print_r($response);
// 	/* // Success
// 		{
// 			"status": "0001",
// 			"data": {
// 				"disbursement_id": 251,
// 				"deposit": 500000000
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
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setCustomerBankAccountNumber('6044222099');
// 	$transaction->setCustomerBankCode('014');
// 	$transaction->setOrderID(time());
// 	$result = $vendor->BankAccountInquiry($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success
// 		{
// 			"status": "0001",
// 			"data": {
// 				"bank_code": "014",
// 				"bank_account": "1239812390",
// 				"name": "John Doe"
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
// 	$transaction->setCustomerBankCode('014');
// 	$transaction->setCustomerBankAccountNumber('6044222099');
// 	$transaction->setCustomerName('TESTER');
// 	$transaction->setAmount(10500);
// 	$transaction->setDescription('TESTING DISBURSEMENT');
// 	$transaction->setOrderID(time());
// 	$result = $vendor->OnlineTransferExec($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success disb
// 		{
// 			"status": "0002",
// 			"data": {
// 				"transaction_id": "A176E870F2F79621202229F5477191C9B74502C3537D0E7C",
// 				"external_id": "1680276102"
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
// 		'external_id' => '1680276102',
// 	];
// 	//
// 	$result = $vendor->CheckTransfer($request);
// 	extract($result);
// 	print_r($response);
// 	/* // Success
// 		{
// 			"status": "0001",
// 			"data": {
// 				"transaction_id": "A176E870F2F79621202229F5477191C9B74502C3537D0E7C",
// 				"external_id": "1680276102",
// 				"status": "IN_PROGRESS"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }
