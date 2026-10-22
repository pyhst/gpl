<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init($_ENV['TITAN_CLIENT_KEY'], $_ENV['TITAN_PARTNER_ID']);
$init->setPrivateKey($_ENV['TITAN_PRIVATE_KEY']);
$init->setPublicKey($_ENV['TITAN_PUBLIC_KEY']);
$init->setParam('TITAN_ACCOUNT_NO', $_ENV['TITAN_ACCOUNT_NO']);
$init->setPaymentURL($_ENV['TITAN_HOST_URL']);
$init->setRequestURL($_ENV['TITAN_HOST_URL']);
// $init->setParam('TITAN_DISBURSEMENT_SECRET_KEY', $_ENV['TITAN_DISBURSEMENT_SECRET_KEY']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$init->setReturnURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \PyGPaymentLib\Vendors\Titan($init);


/**
 *
 * Get AccessTokenB2B
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'GetToken') {
	try {
		$result = $vendor->GetToken([]);
		print_r(json_encode($result));
		/* // Success
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}


/** // Not applicable
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
					"accountInfo": [
							{
								"availableBalance": {
									"currency": "IDR",
									"value": "0.00"
								}
							}
					],
					"responseCode": "2001100",
					"responseMessage": "Request has been processed successfully"
				}
			}
		*/
	} catch (\Throwable $e) {
		print_r($vendor::ThrowError($e));
	}
}


/** // Not applicable
 *
 * Get Bank List
 *
 */
// if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'BankList') {
// 	try {
// 		$result = $vendor->BankList();
// 		extract($result);
// 		print_r($response);
// 		/* // Success
// 		*/
// 	} catch (\Throwable $e) {
// 		print_r($vendor::ThrowError($e));
// 	}
// }


/**
 *
 * Bank Account Inquiry
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'BankAccountInquiry') {
	try {
		$transaction = new \PyGPaymentLib\Transaction();
		$transaction->setCustomerBankCode('014');
		$transaction->setCustomerBankAccountNumber('5020359309');
		$transaction->setAmount(10000);
		$transaction->setCurrency('IDR');
		//
		$result = $vendor->BankAccountInquiry($transaction);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": {
					"responseCode": "2001600",
					"responseMessage": "Request has been processed successfully",
					"partnerReferenceNo": "20251029222617908944200",
					"beneficiaryAccountName": "R BAMBANG ARI SETIYOKO",
					"beneficiaryAccountNo": "5020359309",
					"beneficiaryBankCode": "014",
					"beneficiaryBankName": "bca",
					"currency": "IDR",
					"additionalInfo": {
							"isFeeChargedToUser": false,
							"amount": {
								"value": "10000.00",
								"currency": "IDR"
							}
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
 * Bank Transfer Exec
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'OnlineTransferExec') {
	try {
		$time = time();
		$transaction = new \PyGPaymentLib\Transaction();
		$transaction->setReferenceNumber('TRANSFER_' . $time . '_' . rand(1000,9999));
		$transaction->setAmount(10000);
		//
		$transaction->setTransferMethod('bank');
		$transaction->setCustomerBankCode('014');
		$transaction->setCustomerBankAccountNumber('5020359309');
		$transaction->setCustomerBankAccountName('R BAMBANG ARI SETIYOKO');
		//
		// $transaction->setTransferMethod('ewallet');
		// $transaction->setCustomerBankCode('dana');
		// $transaction->setCustomerBankAccountNumber('62811742234');
		//
		$result = $vendor->OnlineTransferExec($transaction);
		extract($result);
		print_r($response);
		/* // Success disb
			{
				"status": "000",
				"data": {
					"additionalInfo": {
						"validationReferenceNo": "TRANSFER1761751756-7531",
						"isFeeChargedToUser": false
					},
					"amount": {
						"value": "10000.00",
						"currency": "IDR"
					},
					"beneficiaryAccountNo": "",
					"beneficiaryBankCode": "014",
					"partnerReferenceNo": "TRANSFER1761751756-7531",
					"referenceNo": "DIS_01K8R9CX0Y4SV4EVXDDDK1S0GM",
					"responseCode": "202000",
					"responseMessage": "Request has been processed successfully"
				}
			}
		*/
	} catch (\Throwable $e) {
		print_r($vendor::ThrowError($e));
	}
}


/**
 *
 * Bank Transfer Check Status
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'OnlineTransferCheckTransfer') {
	try {
		$request = (object) [
			'originalPartnerReferenceNo' => 'TRANSFER1761751756-7531',
			// 'serviceCode' => '0',
			// 'amount' => 100000,
			// 'currency' => 'IDR',
			// 'originalReferenceNo' => 'DIS_01K8R9CX0Y4SV4EVXDDDK1S0GM',
		];
		$result = $vendor->OnlineTransferCheckTransfer($request);
		extract($result);
		print_r($response);
		/* // Success check disb
			{
				"status": "000",
				"data": {
					"additionalInfo": {
						"isFeeChargedToUser": false,
						"amount": {
								"value": "",
								"currency": ""
						}
					},
					"amount": {
						"value": "10000.00",
						"currency": "IDR"
					},
					"beneficiaryAccountNo": "5020359309",
					"beneficiaryBankCode": "014",
					"currency": "IDR",
					"latestTransactionStatus": "06",
					"transactionStatusDesc": "FAILED",
					"originalPartnerReferenceNo": "TRANSFER1761751756-7531",
					"originalReferenceNo": "DIS_01K8R9CX0Y4SV4EVXDDDK1S0GM",
					"serviceCode": "",
					"sourceAccountNo": "01K3YZV7WFYFNHEWW9T1R81MD7",
					"transactionId": "01K8R9CX12NSWB2ESQKE90AX9M",
					"responseCode": "200000",
					"responseMessage": "Request has been processed successfully"
				}
			}
		*/
	} catch (\Throwable $e) {
		print_r($vendor::ThrowError($e));
	}
}