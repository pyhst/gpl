<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init($_ENV['IBBV2_EMAIL'], $_ENV['IBBV2_PASSWORD']);
$init->setMerchantKey($_ENV['IBBV2_MERCHANT_UUID']);
$init->setPaymentURL($_ENV['IBBV2_HOST_URL']);
$init->setRequestURL($_ENV['IBBV2_HOST_URL']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$init->setReturnURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \PyGPaymentLib\Vendors\IBBv2($init);



/**
 *
 * Check Balance
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'CheckBalance') {
	try {
		$request = (object) [
			'service_code' => $_ENV['IBBV2_DISBURSEMENT_SERVICE_CODE'],
		];
		$result = $vendor->CheckBalance($request);
		extract($result);
		print_r($response);
		/* // Success check balance
			{
				"status": "000",
				"data": {
					"merchant_uuid": "019a7277-4006-7100-a149-abab4f3ed558",
					"balances": {
							"pending_balance": 0,
							"effective_balance": 0,
							"disbursement_balance": 9950
					}
				},
				"request": {
					"url": "https:\/\/api-ibbv2.topglobal.my.id\/api\/v1\/CheckMerchantBalance",
					"data": {
							"merchant_uuid": "019a7277-4006-7100-a149-abab4f3ed558",
							"service_code": "DISBURSEMENT"
					},
					"headers": {
							"Accept": "application\/json",
							"Authorization": "Bearer "
					},
					"option": {
							"as_json": true
					}
				}
			}
		*/
	} catch (\Throwable $e) {
		print_r($vendor::ThrowError($e));
	}
}



/**
 *
 * Bank account inquiry
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'BankAccountInquiry') {
	try {
		$transaction = new \PyGPaymentLib\Transaction();
		$transaction->setParam('SERVICE_CODE', $_ENV['IBBV2_DISBURSEMENT_SERVICE_CODE']);
		$transaction->setCustomerBankCode('014');
		$transaction->setCustomerBankAccountNumber('5020359309');
		$result = $vendor->BankAccountInquiry($transaction);
		extract($result);
		print_r($response);
		/* // Success
		*/
	} catch (\Throwable $e) {
		print_r($vendor::ThrowError($e));
	}
}



/**
 *
 * Send disbursement
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'OnlineTransferExec') {
	try {
		$time = time();
		$transaction = new \PyGPaymentLib\Transaction();
		$transaction->setParam('SERVICE_CODE', $_ENV['IBBV2_DISBURSEMENT_SERVICE_CODE']);
		$transaction->setReferenceNumber('TF' . $time . rand(1000,9999));
		$transaction->setRemark('TF' . $time . rand(1000,9999));
		$transaction->setAmount(10000);
		//
		$transaction->setTransferMethod('RTOL');
		$transaction->setCustomerBankCode('014');
		$transaction->setCustomerBankAccountNumber('5020359309');
		//
		// $transaction->setTransferMethod('RTOL');
		// $transaction->setCustomerBankCode('DANA');
		// $transaction->setCustomerBankAccountNumber('62811742234');
		//
		$result = $vendor->OnlineTransferExec($transaction);
		extract($result);
		print_r($response);
		/* // Success disb
		*/
	} catch (\Throwable $e) {
		print_r($vendor::ThrowError($e));
	}
}



/**
 *
 * Check Transfer Status
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'OnlineTransferCheckTransfer') {
	try {
		$request = (object) [
			'service_code' => $_ENV['IBBV2_ACCEPTANCE_SERVICE_CODE'],
			'reference_num' => 'QRIS1764497085',
		];
		$result = $vendor->OnlineTransferCheckTransfer($request);
		extract($result);
		print_r($response);
		/* // Success check disb
		*/
	} catch (\Throwable $e) {
		print_r($vendor::ThrowError($e));
	}
}