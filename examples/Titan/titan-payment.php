<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init($_ENV['TITAN_CLIENT_KEY'], $_ENV['TITAN_PARTNER_ID']);
$init->setPrivateKey($_ENV['TITAN_PRIVATE_KEY']);
$init->setPublicKey($_ENV['TITAN_PUBLIC_KEY']);
$init->setPaymentURL($_ENV['TITAN_HOST_URL']);
$init->setRequestURL($_ENV['TITAN_HOST_URL']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$init->setReturnURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \Growinc\Payment\Vendors\Titan($init);


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
		$transaction = new \Growinc\Payment\Transaction();
		$transaction->setPaymentMethod('qris');
		$transaction->setPaymentChannel('qris');
		$transaction->setReferenceNumber('QRIS' . $time);
		$transaction->setAmount(100);
		$result = $vendor->SecurePayment($transaction);
		extract($result);
		print_r($response);
		/* // Success QRIS
			{
				"status": "000",
				"data": {
					"responseCode": "200000",
					"responseMessage": "Request has been processed successfully",
					"referenceNo": "01JY6FMHSR5RGQ2N37FFK9XV5",
					"partnerReferenceNo": "QRIS1750416901",
					"qrContent": "00020101021226670016COM.NOBUBANK.WWW01189360050322500000300214250616000000010303UKE51370014ID.CO.QRIS.WWW0215ID20251706338215204754253033605409120000.005802ID5923Merchant Testing UAT 196009TANGERANG61051581062270108MB12F33O0703A010804POSP6304AF8E",
					"additionalInfo": "{}",
					"merchantName": "",
					"storeId": "ID2025170633821"
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
			'setPaymentMethod' => 'qris',
			'originalReferenceNo' => '01K73PFXNAFD8B5N3TJ67MA0C',
			'originalPartnerReferenceNo' => 'QRIS1759987102',
		];
		$result = $vendor->Inquiry($request);
		extract($result);
		print_r($response);
		/* // Success inquiry QRIS
			{
				"status": "000",
				"data": {
					"responseCode": "2005100",
					"responseMessage": "Request has been processed successfully",
					"originalReferenceNo": "01K1E258113VFZNBN2A563VP7",
					"serviceCode": "51",
					"latestTransactionStatus": "00",
					"paidTime": "2025-07-30T16:25:55Z",
					"amount": {
							"value": "119999",
							"currency": "IDR"
					},
					"feeAmount": {
							"value": "1",
							"currency": "IDR"
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
 * Simulate payment
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'SimulatePayment') {
	try {
		$result = $vendor->SimulatePayment((object) [
			'setPaymentMethod' => 'qris',
			'originalPartnerReferenceNo' => 'QRIS1753893329',
		]);
		extract($result);
		print_r($response);
		/* // Success simulate payment QRIS
			{
				"status":"000"
			}
		*/
	} catch (\Throwable $e) {
		$vendor->ThrowErrorException($e);
	}
}
