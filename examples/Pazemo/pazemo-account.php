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
 * Check Accounts
 *
 */
try {
	$result = $vendor->Accounts((object) [
		// 'account_id' => '186992eb-dc10-475a-ae72-fc442a0a15ff',
	]);
	extract($result);
	print_r($response);
	// Check Accounts All
	/* PROD
	{
		"status": "000",
		"data": {
			"0": {
				"id": "d2912439-63c1-48dd-82e3-71467f72d785",
				"createdTime": "2022-05-11T07:22:33.485Z",
				"updatedTime": "2022-05-11T07:22:33.485Z",
				"userId": "0fa26d72-74c6-4be9-b1d2-a9f5e9962973",
				"currencyId": "IDR",
				"number": "112715502828",
				"user": {
					"id": "0fa26d72-74c6-4be9-b1d2-a9f5e9962973",
					"email": "support@sikoma.games",
					"name": "sikoma.games"
				},
				"balance": 0,
				"locked": 0
			}
		}
	}
	*/
	/* DEV
		{
			"status": "000",
			"data": {
				"0": {
					"id": "186992eb-dc10-475a-ae72-fc442a0a15ff",
					"createdTime": "2022-05-22T07:09:36.920Z",
					"updatedTime": "2022-05-22T07:09:36.920Z",
					"userId": "bc9e97c3-e022-49f4-89f9-d627ed153867",
					"currencyId": "IDR",
					"number": "112712880220",
					"user": {
						"id": "bc9e97c3-e022-49f4-89f9-d627ed153867",
						"email": "support@sikoma.games",
						"name": "Sikoma Games"
					},
					"balance": 100091040,
					"locked": 0
				}
			}
		}
	*/
	// Check Single Account
	/*
		{
			"status": "000",
			"data": {
				"id": "186992eb-dc10-475a-ae72-fc442a0a15ff",
				"createdTime": "2022-05-22T07:09:36.920Z",
				"updatedTime": "2022-05-22T07:09:36.920Z",
				"userId": "bc9e97c3-e022-49f4-89f9-d627ed153867",
				"currencyId": "IDR",
				"number": "112712880220",
				"balance": 100091040,
				"locked": 0
			}
		}
	*/
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}

// /**
//  *
//  * Check Transactions
//  *
//  */
// try {
// 	$result = $vendor->Transactions((object) [
// 		'account_id' => '186992eb-dc10-475a-ae72-fc442a0a15ff', // Account ID
// 		'trx_id' => '3593cdaa-33b5-40b8-94bb-ecda32415e93',
// 	]);
// 	extract($result);
// 	print_r($response);
// 	// Check Transactions All
// 	/*
// 		{
// 			"status": "000",
// 			"data": {
// 				"0": {
// 					"id": "b1434c93-a3d6-4e46-9f92-d83f0604a691",
// 					"createdTime": "2022-06-02T06:37:10.575Z",
// 					"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
// 					"type": "credit",
// 					"status": "active",
// 					"amount": "91040.00000000",
// 					"notes": "DP\/VA\/SIM\/1441"
// 				},
// 				"1": {
// 					"id": "3593cdaa-33b5-40b8-94bb-ecda32415e93",
// 					"createdTime": "2022-05-22T07:15:14.146Z",
// 					"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
// 					"type": "credit",
// 					"status": "active",
// 					"amount": "100000000.00000000",
// 					"notes": "demo balance"
// 				}
// 			}
// 		}
// 	*/
// 	// Check Single Transaction
// 	/*
// 		{
// 			"status": "000",
// 			"data": {
// 				"id": "3593cdaa-33b5-40b8-94bb-ecda32415e93",
// 				"createdTime": "2022-05-22T07:15:14.146Z",
// 				"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
// 				"type": "credit",
// 				"status": "active",
// 				"amount": "100000000.00000000",
// 				"notes": "demo balance"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }