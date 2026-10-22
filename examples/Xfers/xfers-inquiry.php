<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['XFERS_API_KEY'],
	$_ENV['XFERS_SECRET_KEY']
);
$init->setRequestURL($_ENV['XFERS_HOST_URL']);

$vendor = new \PyGPaymentLib\Vendors\Xfers($init);

try {
	$result = $vendor->Inquiry((object) [
		'id' => 'contract_a047916c30a54e94a9b4b00f1d8ac022',
	]);
	extract($result);
	print_r($response);
	/* // Success VA BCA
	{
		"status": "000",
		"data": {
			"id": "contract_a047916c30a54e94a9b4b00f1d8ac022",
			"type": "payment",
			"attributes": {
				"status": "pending",
				"amount": "85650.0",
				"createdAt": "2021-07-16T16:46:01+07:00",
				"description": "Voucher #41",
				"expiredAt": "2021-07-16T18:46:01+07:00",
				"referenceId": "0026428761",
				"fees": "3630.0",
				"paymentMethod": {
					"id": "va_c31715a0b94a093634a02d4a7e558d12",
					"type": "virtual_bank_account",
					"referenceId": "0026428761",
					"instructions": {
						"bankShortCode": "BCA",
						"accountNo": "1304410000000441",
						"displayName": "Voucher #41"
					}
				}
			}
		}
	}
	*/
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}
