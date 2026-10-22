<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init($_ENV['GEMPAY_MERCHANT_ID'], $_ENV['GEMPAY_SECRET']);
$init->setParam('GEMPAY_PROJECT_NO', $_ENV['GEMPAY_PROJECT_NO']);
$init->setPaymentURL($_ENV['GEMPAY_HOST_URL']);
$init->setRequestURL($_ENV['GEMPAY_HOST_URL']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \PyGPaymentLib\Vendors\GemPay($init);



// /**
//  *
//  * Create billing
//  *
//  */
// try {
// 	$transaction = new \PyGPaymentLib\Transaction();
// 	$transaction->setAmount(15000);
// 	$transaction->setReferenceNumber(time());
// 	$transaction->setDescription('BILLING1');
// 	//
// 	/* // Channel list:
// 		BNI_VA
// 		CIMB_VA
// 		Mandiri_VA
// 		Permata_VA
// 		BCA_VA
// 		BRI_VA
// 		MBayar_QR / QRIS
// 	*/
// 	// $transaction->setPaymentChannel('CIMB_VA');
// 	// $transaction->setPaymentChannel('BCA_VA');
// 	$transaction->setPaymentChannel('MBayar_QR');
// 	$result = $vendor->SecurePayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success VA
// 		{
// 			"status": "000",
// 			"data": {
// 				"status": true,
// 				"error_code": "P00",
// 				"error_desc": "Data format is good",
// 				"ref_id": "20240423154504-6627751009B73",
// 				"request_id": "1713861903",
// 				"channel": "cimb_va",
// 				"amount": "15000",
// 				"admin_fee": "4900",
// 				"total_amount": "19900",
// 				"virtual_account": "9999900000000006",
// 				"qrcode": null
// 			}
// 		}
// 	*/
// 	/* // Success QRIS
// 		{
// 			"status": "000",
// 			"data": {
// 				"status": true,
// 				"error_code": "P00",
// 				"error_desc": "Data format is good",
// 				"ref_id": "20240423162310-66277DFE23CDA",
// 				"request_id": "1713864189",
// 				"channel": "MBayar_QR",
// 				"amount": "15000",
// 				"admin_fee": "117",
// 				"total_amount": "15117",
// 				"virtual_account": null,
// 				"qrcode": "https:\/\/sandbox-api.gempay.online\/v1\/qrcode?UzRNUDFsRWRUTldXTVhjbmVZdU1qWSs0blhrYyswSzRKMU5HK1p6ZU9aMD0="
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Inquiry trx
//  *
//  */
// try {
// 	$result = $vendor->Inquiry((object) [
// 		'start' => 0,
// 		'length' => 1,
// 		'order' => 'ASC',
// 		'ref_id' => '20240423162310-66277DFE23CDA',
// 	]);
// 	extract($result);
// 	print_r($response);
// 	/* // Success inquiry QRIS
// 		{
// 			"status": "000",
// 			"data": [
// 				{
// 						"transaction_id": "9052",
// 						"channel": "Qris M-Bayar",
// 						"amount": "15000",
// 						"status": "Success",
// 						"ref_id": "20240423162310-66277DFE23CDA",
// 						"order_datetime": "2024-04-23 16:23:10",
// 						"payment_datetime": "2024-04-23 16:24:11",
// 						"request_id": "1713864189",
// 						"merchant_id": "KMB0001",
// 						"project_no": "UJK87AA",
// 						"admin_fee": "117",
// 						"total_amount": "15117"
// 				}
// 			]
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }
