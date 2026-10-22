<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(null, null);
$init->setMID($_ENV['NOWPAYMENTS_API_KEY']);
$init->setPublicKey($_ENV['NOWPAYMENTS_PUBLIC_KEY']);
$init->setPaymentURL($_ENV['NOWPAYMENTS_HOST_URL']);
$init->setRequestURL($_ENV['NOWPAYMENTS_HOST_URL']);
$init->setCallbackURL($_ENV['NOWPAYMENTS_WEBHOOK_URL']);
$init->setReturnURL($_ENV['NOWPAYMENTS_WEBHOOK_URL']);
$init->setParam('NOWPAYMENTS_EMAIL', $_ENV['NOWPAYMENTS_EMAIL']);
$init->setParam('NOWPAYMENTS_PASSWORD', $_ENV['NOWPAYMENTS_PASSWORD']);

$vendor = new \Growinc\Payment\Vendors\NowPayments($init);



/**
 *
 * Check Balance
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'CheckBalance') {
	try {
		$result = $vendor->CheckBalance();
		extract($result);
		print_r($response);
		/* // Success check balance
			{
				"status": "000",
				"data": {
					"active_balance": 0.495892,
					"pending_balance": 0,
					"total_balance": 0.495892
				}
			}
		*/
	} catch (\Throwable $e) {
		print_r($vendor::ThrowError($e));
	}
}



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
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }