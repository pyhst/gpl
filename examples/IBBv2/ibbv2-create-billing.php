<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init($_ENV['IBBV2_EMAIL'], $_ENV['IBBV2_PASSWORD']);
$init->setMerchantKey($_ENV['IBBV2_MERCHANT_UUID']);
$init->setPaymentURL($_ENV['IBBV2_HOST_URL']);
$init->setRequestURL($_ENV['IBBV2_HOST_URL']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$init->setReturnURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \PyGPaymentLib\Vendors\IBBv2($init);



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



/**
 *
 * Create billing
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'SecurePayment') {
	try {
		$time = time();
		$transaction = new \PyGPaymentLib\Transaction();
		$transaction->setParam('SERVICE_CODE', $_ENV['IBBV2_ACCEPTANCE_SERVICE_CODE']);
		$transaction->setPaymentChannel('QRIS');
		$transaction->setPaymentMethod('QRIS');
		$transaction->setReferenceNumber('QRIS' . $time);
		$transaction->setAmount(100);
		$transaction->setRemark('QRIS' . $time);
		$result = $vendor->SecurePayment($transaction);
		extract($result);
		print_r($response);
		/* // Success QRIS
			{
				"status": "000",
				"data": {
					"billing_id": "019ad438-ce58-7000-8659-d893489f0313",
					"reference_num": "QRIS1764497085",
					"billing_num": null,
					"name": "QRIS1764497085",
					"description": "Payment for ",
					"qr_string": "00020101021226730021COM.GUDANGVOUCHER.WWW011893600916300452416802151202240826000010303UBE51450015ID.OR.GPNQR.WWW02151202240826000010303UBE52045812530336054031005802ID5904KIVO6009TANGERANG6105155106267011025300504440307KMB0001052025113004441050859xEc071417044420251130630430E5",
					"qr_url": "https:\/\/api-ibbv2.topglobal.my.id\/transaction\/qr\/2025\/11\/30\/QRIS1764497085.png",
					"deeplink_url": null,
					"net_amount": 100,
					"fee_total": 2,
					"fee_waived": 0,
					"fee_charged_to": "MERC",
					"amount": 100,
					"status": 0,
					"status_descr": "PENDING",
					"expire_in": "120",
					"expiring_at": "2025-11-30T11:04:43.000000Z",
					"created_at": "2025-11-30T10:04:43.000000Z",
					"customer_id": "0000",
					"customer_name": "CUSTOMER",
					"customer_phone": "0888",
					"customer_email": "email@customer.com"
				},
				"request": {
					"url": "https:\/\/api-ibbv2.topglobal.my.id\/api\/v1\/CreateBilling",
					"data": {
							"merchant_uuid": "019a7277-4006-7100-a149-abab4f3ed558",
							"service_code": "ACCEPTANCE",
							"channel_code": "QRIS",
							"amount": 100,
							"reference_num": "QRIS1764497085",
							"name": "QRIS1764497085",
							"expire_in": "120",
							"customer_id": "0000",
							"description": "Payment for ",
							"customer_name": "CUSTOMER",
							"customer_phone": "0888",
							"customer_email": "email@customer.com"
					},
					"headers": {
							"Accept": "application\/json",
							"Authorization": "Bearer "
					},
					"option": {
							"as_json": true
					}
				}
			}
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}



/**
 *
 * Inquiry trx
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'Inquiry') {
	try {
		$request = (object) [
			'service_code' => $_ENV['IBBV2_ACCEPTANCE_SERVICE_CODE'],
			'reference_num' => 'QRIS1764497085',
		];
		$result = $vendor->Inquiry($request);
		extract($result);
		print_r($response);
		/* // Success inquiry QRIS
			{
				"status": "000",
				"data": {
					"billing_id": "019ad438-ce58-7000-8659-d893489f0313",
					"reference_num": "QRIS1764497085",
					"amount": 100,
					"status": "0",
					"status_descr": "PENDING",
					"expired_at": null,
					"created_at": "2025-11-30T10:04:43.000000Z",
					"paid_at": null,
					"settled_at": null
				},
				"request": {
					"url": "https:\/\/api-ibbv2.topglobal.my.id\/api\/v1\/CheckBilling",
					"data": {
							"merchant_uuid": "019a7277-4006-7100-a149-abab4f3ed558",
							"service_code": "ACCEPTANCE",
							"reference_num": "QRIS1764497085"
					},
					"headers": {
							"Accept": "application\/json"
					},
					"option": {
							"as_json": true
					}
				}
			}
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}
