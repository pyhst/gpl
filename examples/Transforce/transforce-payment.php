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
 * Create billing
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'SecurePayment') {
	try {
		$time = time();
		$transaction = new \Growinc\Payment\Transaction();
		$transaction->setReferenceNumber($time);
		$transaction->setAmount(15000);
		$transaction->setDescription('BILLING1');
		$transaction->setCustomerName('TESTER');
		//
		/* // Channel list:
			VA: BCA, MANDIRI, PERMATA, BRI, BNI, BNC, DANAMON
			QRIS
			EWALLET: DANA, OVO, SHOPEEPAY
		*/
		// $transaction->setPaymentMethod('VA');
		// $transaction->setPaymentChannel('PERMATA');
		//
		// $transaction->setPaymentMethod('EWALLET');
		// $transaction->setPaymentChannel('DANA');
		//
		$transaction->setPaymentMethod('QRIS');
		$transaction->setPaymentChannel('QRIS');
		//
		$result = $vendor->SecurePayment($transaction);
		extract($result);
		print_r($response);
		/* // Success VA
			{
				"status": "000",
				"request": {
					"url": "https:\/\/staging.payment.transforce.co.id\/v1\/pay\/va\/dynamic",
					"data": {
							"applyAmount": 15000,
							"bankCode": "PERMATA",
							"callbackUrl": "http:\/\/tester.com\/secure\/callback\/demo",
							"details": "BILLING1",
							"displayName": "TESTER",
							"expiry": "120",
							"merchantReferenceNo": "1727688580"
					},
					"headers": {
							"Content-Type": "application\/json;charset=utf-8",
							"X-Request-ID": "172768858058719",
							"X-Timestamp": "1727688580",
							"X-MerchantId": "100025",
							"X-Signature": "d6Bs7jvgHY9iiL1LocVHtiI+S4VTYySea5dRuuGWwYnwnwYjKgbqlgFaGGSDEEYL37s8fz2dMMmiI9VSepevWA=="
					},
					"options": {
							"as_json": true
					}
				},
				"data": {
					"code": "200",
					"message": "success",
					"data": {
							"merchantReferenceNo": "1727688580",
							"applyAmount": "15000",
							"expiry": 120,
							"bankCode": "PERMATA",
							"displayName": "TESTER",
							"details": "BILLING1",
							"callbackUrl": "http:\/\/tester.com\/secure\/callback\/demo",
							"referenceNo": "InVA1000252409302904422681tMfQ",
							"virtualAccount": "8856511700010110"
					}
				}
			}
		*/
		/* // Success QRIS
			{
				"status": "000",
				"request": {
					"url": "https:\/\/staging.payment.transforce.co.id\/v1\/pay\/qr\/generate",
					"data": {
							"applyAmount": 15000,
							"callbackUrl": "http:\/\/tester.com\/secure\/callback\/demo",
							"expiry": "120",
							"merchantReferenceNo": "1727758342"
					},
					"headers": {
							"Content-Type": "application\/json;charset=utf-8",
							"X-Request-ID": "172775834220310",
							"X-Timestamp": "1727758342",
							"X-MerchantId": "100025",
							"X-Signature": "Acm713uWVNVIgm6JmykNxcpGkAFA09fDq096Y4uc85BltV4jfPSS3p64Zxj+boh+\/PajpFn3Eyjx+4g9Lrl+qA=="
					},
					"options": {
							"as_json": true
					}
				},
				"data": {
					"code": "200",
					"message": "success",
					"data": {
							"merchantReferenceNo": "1727758342",
							"applyAmount": "15000",
							"expiry": 120,
							"callbackUrl": "http:\/\/tester.com\/secure\/callback\/demo",
							"referenceNo": "QRIS100025241001521122990vwG7c",
							"qrCode": "00020101021226520012COM.DOKU.WWW011793600899000008705020487050303URE51440014ID.CO.QRIS.WWW0215ID20240116387050303URE520115303360540815000.005802ID5904Tony6007jakarta61051175062410703A015030QRIS100025241001521122990vwG7c6304C087"
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
 * Inquiry trx
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'Inquiry') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$result = $vendor->Inquiry((object) [
			'merchantReferenceNo' => '1758718319',
		]);
		extract($result);
		print_r($response);
		/* // Success inquiry QRIS
			{
				"status": "000",
				"request": {
					"url": "https:\/\/staging.payment.transforce.co.id\/v1\/pay\/payin\/query",
					"data": {
							"merchantReferenceNo": "1727758342"
					},
					"headers": {
							"X-Request-ID": "172776199971315",
							"X-Timestamp": "1727761999",
							"X-MerchantId": "100025",
							"X-Signature": "I\/HSeEn7SIdu4mpyIM9kz4Aqdqt8djYaS5+6LHnXegi\/rKZmqwxjKvxe7uGSNE3cRnK76xWwd7\/Je6a6v+ni3Q==",
							"__debug": {
								"payload": "{\"merchantReferenceNo\":\"1727758342\"}",
								"string_to_sign": "100025|{\"merchantReferenceNo\":\"1727758342\"}|1727761999|172776199971315"
							}
					},
					"options": {
							"as_json": true
					}
				},
				"data": {
					"code": "200",
					"message": "success",
					"data": {
							"referenceNo": null,
							"merchantReferenceNo": null,
							"channel": "QRIS",
							"expireDate": "2024-10-01 13:52:22",
							"status": "Pending",
							"message": null,
							"merchantFee": "5000",
							"merchantRate": "0.01",
							"merchantChargeFee": "5150",
							"creditAmount": "9850",
							"applyAmount": "15000",
							"currency": "IDR",
							"settleStatus": false,
							"settleTime": null,
							"createdAt": "2024-10-01 11:52:22",
							"finishedAt": null
					}
				}
			}
		*/
		/* // Success inquiry VA
			{
				"status": "000",
				"request": {
					"url": "https:\/\/staging.payment.transforce.co.id\/v1\/pay\/payin\/query",
					"data": {
							"merchantReferenceNo": "1727688580"
					},
					"headers": {
							"X-Request-ID": "172776197978872",
							"X-Timestamp": "1727761979",
							"X-MerchantId": "100025",
							"X-Signature": "hXeMqgAji+h7wCUUgtrQqzS2wpGhYViHpoVv45QkMfckUHMVhwVa5TSHwgW6GoQuvfhqENuKcfDDy4zIAu+lcA==",
							"__debug": {
								"payload": "{\"merchantReferenceNo\":\"1727688580\"}",
								"string_to_sign": "100025|{\"merchantReferenceNo\":\"1727688580\"}|1727761979|172776197978872"
							}
					},
					"options": {
							"as_json": true
					}
				},
				"data": {
					"code": "200",
					"message": "success",
					"data": {
							"referenceNo": null,
							"merchantReferenceNo": null,
							"channel": "DynamicVA",
							"expireDate": "2024-09-30 18:29:42",
							"status": "Pending",
							"message": null,
							"merchantFee": "5000",
							"merchantRate": "0.01",
							"merchantChargeFee": "5150",
							"creditAmount": "9850",
							"applyAmount": "15000",
							"currency": "IDR",
							"settleStatus": false,
							"settleTime": "2024-09-30 16:29:42",
							"createdAt": "2024-09-30 16:29:42",
							"finishedAt": null
					}
				}
			}
		*/
	} catch (\Throwable $e) {
		print_r($vendor::ThrowError($e));
	}
}