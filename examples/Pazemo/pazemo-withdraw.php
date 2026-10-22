<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['PAZEMO_API_KEY'],
	''
);
$init->setPaymentURL($_ENV['PAZEMO_HOST_URL']);
$init->setRequestURL($_ENV['PAZEMO_HOST_URL']);
$vendor = new \Growinc\Payment\Vendors\Pazemo($init);

/**
 *
 * Do Transfer
 *
 */
try {
	$result = $vendor->DoTransfer((object) [
		/*
		'account_id' => '186992eb-dc10-475a-ae72-fc442a0a15ff',
		'statusMessage' => 'string',
		'address' => '19009928584',
		'addressName' => 'Jakarta',
		'amount' => 11100,
		'notes' => 'Test',
		'prefix' => 'bca',
		'beneficiaryId' => 1642,
		'requestId' => date('YmdHis'),
		'requestData' => (object) [],
		*/
		'account_id' => $_ENV['PAZEMO_IDR_ACCOUNT_ID'],
		'statusMessage' => '',
		'prefix' => 'bca',
		'address' => '6043425795',
		'addressName' => 'PT Maju Selaras Komputer',
		'amount' => rand(10000,50000),
		'notes' => 'PT Maju Selaras Komputer',
		// 'beneficiaryId' => '',
		'requestId' => date('YmdHis'),
		'requestData' => (object) [],
	]);
	extract($result);
	print_r($response);
	// Success transfer MANDIRI #1
	/*
		{
			"status": "000",
			"data": {
				"id": "ec5a20d5-91ac-43ca-a809-5f1268a9ce25",
				"createdTime": "2022-06-16T12:26:36.492Z",
				"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
				"transactionId": "36765c40-c92b-466f-ab24-a4b07a3f084e",
				"status": "process",
				"statusMessage": "string",
				"address": "Indonesia",
				"addressName": "Jakarta",
				"amount": 10000,
				"feeAmount": 1500,
				"notes": "Test",
				"prefix": "demo",
				"beneficiaryId": 1642,
				"reference": null,
				"requestId": "20220616192636",
				"account": {
					"id": "186992eb-dc10-475a-ae72-fc442a0a15ff",
					"createdTime": "2022-05-22T07:09:36.920Z",
					"updatedTime": "2022-05-22T07:09:36.920Z",
					"userId": "bc9e97c3-e022-49f4-89f9-d627ed153867",
					"currencyId": "IDR",
					"number": "112712880220"
				},
				"beneficiary": {
					"id": 1642,
					"updatedTime": "2022-06-16T12:26:36.440Z",
					"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
					"bankId": "demo",
					"address": "Indonesia",
					"name": "Mr. Forest Hodkiewicz Jr.",
					"email": "admin@sikoma.games",
					"label": "SIKOMA",
					"status": "verified",
					"type": "corporate"
				}
			}
		}
	*/
	// Success transfer MANDIRI #2
	/*
		{
			"status": "000",
			"data": {
				"id": "1842f1af-37ba-44f7-bc9d-906323936c9b",
				"createdTime": "2022-06-16T18:34:49.978Z",
				"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
				"transactionId": "d88203c9-60b3-454c-9ce6-a9792f93191a",
				"status": "process",
				"statusMessage": "string",
				"address": "Indonesia",
				"addressName": "Jakarta",
				"amount": 11100,
				"feeAmount": 1500,
				"notes": "Test",
				"prefix": "demo",
				"beneficiaryId": 1642,
				"reference": null,
				"requestId": "20220617013449",
				"account": {
					"id": "186992eb-dc10-475a-ae72-fc442a0a15ff",
					"createdTime": "2022-05-22T07:09:36.920Z",
					"updatedTime": "2022-05-22T07:09:36.920Z",
					"userId": "bc9e97c3-e022-49f4-89f9-d627ed153867",
					"currencyId": "IDR",
					"number": "112712880220"
				},
				"beneficiary": {
					"id": 1642,
					"updatedTime": "2022-06-16T18:34:49.919Z",
					"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
					"bankId": "demo",
					"address": "Indonesia",
					"name": "Mr. Forest Hodkiewicz Jr.",
					"email": "admin@sikoma.games",
					"label": "SIKOMA",
					"status": "verified",
					"type": "corporate"
				}
			}
		}
	*/
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}

/**
 *
 * Check Transfer
 *
 */
try {
	$result = $vendor->CheckTransfer((object) [
		'account_id' => '186992eb-dc10-475a-ae72-fc442a0a15ff',
		// 'trx_id' => 'ec5a20d5-91ac-43ca-a809-5f1268a9ce25',
		'trx_id' => '1842f1af-37ba-44f7-bc9d-906323936c9b',
	]);
	extract($result);
	print_r($response);
	// Success check transfer MANDIRI #1
	/*
		{
			"status": "000",
			"data": {
				"id": "ec5a20d5-91ac-43ca-a809-5f1268a9ce25",
				"createdTime": "2022-06-16T12:26:36.492Z",
				"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
				"transactionId": "36765c40-c92b-466f-ab24-a4b07a3f084e",
				"status": "succeed",
				"statusMessage": "Failed",
				"address": "Indonesia",
				"addressName": "Jakarta",
				"amount": "10000.00000000",
				"feeAmount": "1500.00000000",
				"notes": "Test",
				"prefix": "demo",
				"beneficiaryId": 1642,
				"reference": "11e93b57bd594888b0bfc0862a531d58",
				"requestId": "20220616192636"
			}
		}
	*/
	// Success check transfer MANDIRI #2
	/*
		{
			"status": "000",
			"data": {
				"id": "1842f1af-37ba-44f7-bc9d-906323936c9b",
				"createdTime": "2022-06-16T18:34:49.978Z",
				"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
				"transactionId": "d88203c9-60b3-454c-9ce6-a9792f93191a",
				"status": "succeed",
				"statusMessage": "Failed",
				"address": "Indonesia",
				"addressName": "Jakarta",
				"amount": "11100.00000000",
				"feeAmount": "1500.00000000",
				"notes": "Test",
				"prefix": "demo",
				"beneficiaryId": 1642,
				"reference": "a19ce02e7b14484b9c7cf5a07543e9c6",
				"requestId": "20220617013449"
			}
		}
	*/
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}
