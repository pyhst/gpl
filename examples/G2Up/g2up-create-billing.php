<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init($_ENV['G2UP_APP_KEY'], $_ENV['G2UP_SECRET_KEY']);
$init->setPaymentURL($_ENV['G2UP_HOST_URL']);
$init->setRequestURL($_ENV['G2UP_HOST_URL']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \PyGPaymentLib\Vendors\G2Up($init);



// /**
//  *
//  * Create billing
//  *
//  */
// try {
// 	$transaction = new \PyGPaymentLib\Transaction();
// 	$transaction->setAmount(15000);
// 	$transaction->setReferenceNumber(time());
// 	$transaction->setCustomerID('TESTER');
// 	$result = $vendor->SecurePayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success QRIS
// 		{
// 			"status": "000",
// 			"data": {
// 				"status": "success",
// 				"message": "QRIS Merchant",
// 				"qrcode": "00020101021226670016ID.CO.MBAYAR.WWW01189360082931683196300214E2P168319630630303UMI51460016ID.CO.MBAYAR.WWW0215ID10210651519170303UMI520407425303360540815000.005802ID5910Risang Pay6015JAKARTA SELATAN6105121506234050622479907062110330810Risang Pay6304425B",
// 				"topUpAmount": 15000,
// 				"memberNo": "TESTER",
// 				"referenceNo": "1716263621"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



/**
 *
 * Inquiry trx
 *
 */
try {
	$result = $vendor->Inquiry((object) [
		'startDate' => date('Y-m-d', '1716267137'),
		'endDate' => date('Y-m-d', '1716267137'),
		'referenceNo' => '1716267137',
		'memberNo' => 'TESTER',
	]);
	extract($result);
	print_r($response);
	/* // Success inquiry QRIS
		{
			"status": "000",
			"data": [
				{
						"referenceNo": "1716267137",
						"memberNo": "TESTER",
						"topUpAmount": 15000,
						"requestDate": "2024-05-21 11:52:20",
						"responseDate": "2024-05-21 11:52:21",
						"paymentNo": null,
						"paymentDate": null,
						"paymentStatus": null
				}
			]
		}
	*/
	/* // Success inquiry of paid QRIS
		{
			"status": "000",
			"data": [
				{
						"referenceNo": "1716267137",
						"memberNo": "TESTER",
						"topUpAmount": 15000,
						"requestDate": "2024-05-21 11:52:20",
						"responseDate": "2024-05-21 11:52:21",
						"paymentNo": "QRP24052100001",
						"paymentDate": "2024-05-21 11:56:54",
						"paymentStatus": "00"
				}
			]
		}
	*/
} catch (\Throwable $e) {
	$vendor->ThrowErrorException($e);
}
