<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['PAZEMO_API_KEY'],
	''
);
$init->setPaymentURL($_ENV['PAZEMO_HOST_URL']);
$init->setRequestURL($_ENV['PAZEMO_HOST_URL']);
$vendor = new \PyGPaymentLib\Vendors\Pazemo($init);

// /**
//  *
//  * BankList
//  *
//  */
// try {
// 	$result = $vendor->BankList();
// 	extract($result);
// 	print_r($response);
// 	/*
// 	{"status":"000","data":[{"id":"bca","type":"bank","name":"BCA","code":"CENAID"},{"id":"bni","type":"bank","name":"BNI","code":"BNINID"},{"id":"bri","type":"bank","name":"BRI","code":"BRINID"},{"id":"btpn","type":"bank","name":"BTPN","code":"SUNIDJA"},{"id":"dana","type":"bank","name":"DANA","code":""},{"id":"danamon","type":"bank","name":"Danamon","code":"BDINID"},{"id":"demo","type":"bank","name":"Demo Bank","code":"DEMOIDJA"},{"id":"dki","type":"bank","name":"Bank DKI","code":"BDKIID"},{"id":"gopay","type":"bank","name":"GoPay","code":""},{"id":"jago","type":"bank","name":"Jago","code":"JAGOID"},{"id":"mandiri","type":"bank","name":"Mandiri","code":"BMRIID"},{"id":"nagari","type":"bank","name":"NAGARI","code":"PDSBIDSP"},{"id":"ocbc","type":"bank","name":"OCBC","code":"NISPIDJA"},{"id":"permata","type":"bank","name":"Permata","code":"BBBAID"}]}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }

/**
 *
 * Beneficiary
 *
 */
try {
	$result = $vendor->Beneficiary((object) [
	]);
	extract($result);
	print_r($response);
	/*
		{
			"status": "000",
			"data": {
				"0": {
					"id": 1640,
					"updatedTime": "2022-06-16T08:20:36.087Z",
					"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
					"bankId": "demo",
					"address": "Indonesia",
					"name": "Ms. Lydia Stanton MD",
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

// /**
//  *
//  * Beneficiary Add
//  *
//  */
// try {
// 	$result = $vendor->BeneficiaryAdd((object) [
// 		'accountId' => '186992eb-dc10-475a-ae72-fc442a0a15ff',
// 		'bankId' => 'demo',
// 		'address' => '19009928584',
// 		// 'address' => 'Indonesia',
// 		'email' => 'admin@sikoma.games',
// 		'label' => 'SIKOMA',
// 		'type' => 'corporate', // personal, individual, corporate
// 	]);
// 	extract($result);
// 	print_r($response);
// 	// Success added
// 	/*
// 		{
// 			"status": "000",
// 			"data": {
// 				"id": 1641,
// 				"updatedTime": "2022-06-16T08:35:41.833Z",
// 				"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
// 				"bankId": "demo",
// 				"address": "Indonesia",
// 				"name": null,
// 				"email": "admin@sikoma.games",
// 				"label": "SIKOMA",
// 				"status": "pending",
// 				"type": "corporate"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }

// /**
//  *
//  * Beneficiary
//  *
//  */
// try {
// 	$result = $vendor->BeneficiaryDel((object) [
// 		'beneficiary_id' => '1641',
// 	]);
// 	extract($result);
// 	print_r($response);
// 	// Success deleted
// 	/*
// 		{
// 			"status": "000",
// 			"data": {
// 				"beneficiaryId": "1641",
// 				"status": "deleted"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }
