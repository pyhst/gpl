<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init($_ENV['MAWARPAY_MERCHANT_ID']);
$init->setPrivateKey($_ENV['MAWARPAY_MERCHANT_PRIVATE_KEY']);
$init->setPublicKey($_ENV['MAWARPAY_PUBLIC_KEY']);
$init->setPaymentURL($_ENV['MAWARPAY_HOST_URL']);
$init->setRequestURL($_ENV['MAWARPAY_HOST_URL']);
$init->setCallbackURL($_ENV['MAWARPAY_WEBHOOK_URL']);
$init->setReturnURL($_ENV['MAWARPAY_WEBHOOK_URL']);

$vendor = new \PyGPaymentLib\Vendors\MawarPay($init);


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
					"returnCode": "SUCCESS",
					"mchId": "410022",
					"withdrawnAmt": 0,
					"balanceAmt": 0,
					"rechargingAmt": 0,
					"totalBalance": 0
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
		$time = time();
		$transaction = new \PyGPaymentLib\Transaction();
		$transaction->setReferenceNumber($time);
		$transaction->setAmount(10000);
		$transaction->setCustomerBankCode('CENAIDJA');
		$transaction->setCustomerBankId('014');
		$transaction->setCustomerBankName('BANK BCA');
		$transaction->setCustomerBankAccountNumber('5020359309');
		$transaction->setCustomerBankAccountName('R BAMBANG ARI SETIYOKO');
		$transaction->setCustomerCity('JAKARTA');
		$transaction->setCustomerEmail('tester@email.com');
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
		$transaction = new \PyGPaymentLib\Transaction();
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
		$transaction = new \PyGPaymentLib\Transaction();
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