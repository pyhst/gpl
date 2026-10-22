<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['IPAY_MERCHANT_CODE'],
	$_ENV['IPAY_MERCHANT_KEY']
);

$vendor = new \PyGPaymentLib\Vendors\Ipay88($init);

try {
	$raw_data = file_get_contents("php://input");
	if (!$raw_data) {
		$raw_data = "MerchantCode=ID01676&PaymentId=26&RefNo=0018824953&Amount=700000&Currency=IDR&Remark=Transaction%200018824953&TransId=T0053259100&AuthCode=8228024900002200&Status=1&ErrDesc=&Signature=0h8XPhRJN%2FioARBGwoiGdy5DwP8%3D&VirtualAccountAssigned=8228024900002200&TransactionExpiryDate=20-04-2021%2016%3A35&PaymentDate=19-04-2021%2016%3A36";
		$raw_data = parse_str($raw_data, $data);
		// print_r(json_encode($data));
		// exit();
		/*
		{
			"MerchantCode": "ID01676",
			"PaymentId": "26",
			"RefNo": "0018824953",
			"Amount": "700000",
			"Currency": "IDR",
			"Remark": "Transaction 0018824953",
			"TransId": "T0053259100",
			"AuthCode": "8228024900002200",
			"Status": "1",
			"ErrDesc": "",
			"Signature": "0h8XPhRJN\/ioARBGwoiGdy5DwP8=",
			"VirtualAccountAssigned": "8228024900002200",
			"TransactionExpiryDate": "20-04-2021 16:35",
			"PaymentDate": "19-04-2021 16:36"
		}
		*/
	}
	$request = (object) $data;
	$result = $vendor->Callback($request);
	extract($result);
	print_r($response);
	/*
	// Success
	{
		"status": "000",
		"data": {
			"MerchantCode": "ID01676",
			"PaymentId": "26",
			"RefNo": "0018824953",
			"Amount": "700000",
			"Currency": "IDR",
			"Remark": "Transaction 0018824953",
			"TransId": "T0053259100",
			"AuthCode": "8228024900002200",
			"Status": "1",
			"ErrDesc": "",
			"Signature": "0h8XPhRJN\/ioARBGwoiGdy5DwP8=",
			"VirtualAccountAssigned": "8228024900002200",
			"TransactionExpiryDate": "20-04-2021 16:35",
			"PaymentDate": "19-04-2021 16:36"
		}
	}
	*/
} catch (\Throwable $e) {
	echo 'Callback failed: ' . $e->getMessage() . ':' . $e->getCode();
}
