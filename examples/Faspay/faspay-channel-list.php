<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['FASPAY_MERCHANT_NAME'] . ':' . $_ENV['FASPAY_MERCHANT_ID'],
	$_ENV['FASPAY_USER_ID'] . ' : ' . $_ENV['FASPAY_PASSWORD']
);
$init->setRequestURL($_ENV['FASPAY_HOST_URL']);

$vendor = new \PyGPaymentLib\Vendors\Faspay($init);

// try {
	$result = $vendor->InquiryPaymentChannel();
	extract($result);
	print_r($response);
	// Success
	/*
	{
		"status": "000",
		"data": {
			"response": "Request List of Payment Gateway",
			"merchant_id": "33660",
			"merchant": "VoGame Indonesia",
			"payment_channel": [{
				"pg_code": "807",
				"pg_name": "Akulaku"
			}, {
				"pg_code": "801",
				"pg_name": "BNI Virtual Account"
			}, {
				"pg_code": "825",
				"pg_name": "CIMB VA"
			}, {
				"pg_code": "701",
				"pg_name": "DANAMON ONLINE BANKING"
			}, {
				"pg_code": "708",
				"pg_name": "Danamon VA"
			}, {
				"pg_code": "302",
				"pg_name": "LinkAja"
			}, {
				"pg_code": "802",
				"pg_name": "Mandiri Virtual Account"
			}, {
				"pg_code": "814",
				"pg_name": "Maybank2U"
			}, {
				"pg_code": "408",
				"pg_name": "MAYBANK VA"
			}, {
				"pg_code": "812",
				"pg_name": "OVO"
			}, {
				"pg_code": "402",
				"pg_name": "Permata"
			}, {
				"pg_code": "711",
				"pg_name": "Shopee Pay"
			}, {
				"pg_code": "818",
				"pg_name": "Sinarmas Virtual Account"
			}, {
				"pg_code": "420",
				"pg_name": "UNICount-Rupiah"
			}],
			"response_code": "00",
			"response_desc": "Sukses"
		}
	}
	*/
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
// } catch (\Throwable $e) {
// 	echo 'Inquiry payment channel failed: ' . $e->getMessage();
// }
