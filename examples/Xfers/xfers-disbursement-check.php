<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['XFERS_API_KEY'],
	$_ENV['XFERS_SECRET_KEY']
);
$init->setRequestURL($_ENV['XFERS_HOST_URL']);
$vendor = new \Growinc\Payment\Vendors\Xfers($init);

/**
 *
 * Check Transfer
 *
 */
try {
	$result = $vendor->CheckTransfer((object) [
		'id' => 'contract_0574f4df86d3422fba33f58588f47194'
	]);
	extract($result);
	print_r($response);
	/*
		{
			"status": "000",
			"data": {
				"id": "contract_0574f4df86d3422fba33f58588f47194",
				"type": "disbursement",
				"attributes": {
					"referenceId": "TT20220601224051",
					"description": "Test Transfer",
					"amount": "10000.0",
					"status": "completed",
					"createdAt": "2022-06-01T22:40:51+07:00",
					"fees": "3330.0",
					"disbursementMethod": {
						"type": "bank_transfer",
						"bankAccountNo": "6044222099",
						"bankShortCode": "BCA",
						"bankName": "Bank Central Asia",
						"bankAccountHolderName": "BCA",
						"serverBankAccountHolderName": "MAJU SELARAS KOMPUTER PT",
						"failureReason": "",
						"bankReferenceNumber": "08419480"
					}
				}
			}
		}
	*/
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}