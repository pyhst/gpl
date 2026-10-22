<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init($_ENV['TRANSFORCE_MERCHANT_ID']);
$init->setPrivateKey($_ENV['TRANSFORCE_MERCHANT_PRIVATE_KEY']);
$init->setPublicKey($_ENV['TRANSFORCE_MERCHANT_PUBLIC_KEY']);
$init->setPaymentURL($_ENV['TRANSFORCE_HOST_URL']);
$init->setRequestURL($_ENV['TRANSFORCE_HOST_URL']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$init->setReturnURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \Growinc\Payment\Vendors\Transforce($init);



/**
 *
 * Check Balance
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'CheckBalance') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$result = $vendor->CheckBalance($transaction);
		extract($result);
		print_r($response);
		/* // Success check balance
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
		$time = time();
		$transaction = new \Growinc\Payment\Transaction();
		$transaction->setReferenceNumber($time);
		$transaction->setAmount(10000);
		$transaction->setCustomerBankCode('BNINIDJA');
		$transaction->setCustomerBankAccountNumber('7700173383');
		$result = $vendor->BankAccountInquiry($transaction);
		extract($result);
		print_r($response);
		/* // Success bank account inquiry
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
		$transaction = new \Growinc\Payment\Transaction();
		$transaction->setReferenceNumber(date('YmdHis', $time));
		$transaction->setCustomerBankCode('BNINIDJA');
		$transaction->setCustomerBankAccountNumber('7700173383');
		$transaction->setCustomerBankAccountName('ABIL AHMAD ABDULLAH');
		$transaction->setAmount(10000);
		$transaction->setRemark('TEST-TRANSFER');
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
		$transaction = new \Growinc\Payment\Transaction();
		$transaction->setAmount(10000);
		$result = $vendor->OnlineTransferCheckTransfer((object) [
			'merchantReferenceNo' => '20250924195506',
		]);
		extract($result);
		print_r($response);
		/* // Success check status
		*/
	} catch (\Throwable $e) {
		print_r($vendor::ThrowError($e));
	}
}