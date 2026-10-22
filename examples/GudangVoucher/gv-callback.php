<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['GV_MERCHANT_ID'],
	$_ENV['GV_MERCHANT_KEY']
);

$vendor = new \PyGPaymentLib\Vendors\GudangVoucher($init);

// $callback_data = '{"data":"<trans_doc><merchant_id>878<\/merchant_id><merchant>VPAY<\/merchant><reference>8782021031818150440<\/reference><voucher_code>202103182554031616065898<\/voucher_code><amount currency=\"IDR\" nominal=\"14400\"\/><purpose>Tr Mr Dummy<\/purpose><custom>202103182554031616065898<\/custom><status>SUCCESS<\/status><\/trans_doc>"}';
// $callback_data = (object) json_decode($callback_data);

$xml_data = '<trans_doc><merchant_id>878</merchant_id><merchant>VPAY</merchant><reference>8782021041313315126</reference><voucher_code>202104131722841618295369</voucher_code><amount currency="IDR" nominal="10150"/><purpose>Tr Mr Dummy</purpose><custom>202104131722841618295369</custom><status>SUCCESS</status></trans_doc>';

$callback_data = (object) json_decode(json_encode([
		'data' => $xml_data,
	]));

try {
	$result = $vendor->Callback($callback_data);
	extract($result);
	print_r($response);
	// Success
	/*
	{
		"status": "000",
		"data": {
			"merchant_id": "878",
			"merchant": "VPAY",
			"reference": "87820210208154546",
			"voucher_code": "0012773633",
			"amount": {
				"@attributes": {
					"currency": "IDR",
					"nominal": "560000"
				}
			},
			"purpose": "Apple",
			"custom": "0012773633",
			"status": "SUCCESS",
			"development": "YES"
		}
	}
	*/
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
