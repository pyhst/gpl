<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['IBANK_PGA_UUID'],
	$_ENV['IBANK_PGA_SECRET_KEY'],
);
$init->setPaymentURL($_ENV['IBANK_PGA_HOST_URL']);
$init->setRequestURL($_ENV['IBANK_PGA_HOST_URL']);
$init->setParams([
	'service_code' => $_ENV['IBANK_PGA_SERVICE_CODE'],
]);
$vendor = new \PyGPaymentLib\Vendors\ibankPGA($init);

/**
 *
 * Make qris inquiry
 *
 */
$inquiry = (object) [
		'merchant_uuid' => $init->getMID(),
		'service_code' => 'NSIQRIS',
		'qr_string' => base64_encode('00020101021151430013ID.OR.DBS.WWW0215IDE0190000001140303UME5204581253033605802ID5920KOPI LIYOUNG JAMAICA6013JAKARTA PUSAT61051034062070703A016304D0F0'),
];
try {
	$result = $vendor->MakeInquiry($inquiry);
	extract($result);
	print_r($response);
	/* // Success
		{
			"status": "000",
			"message": "Inquiry success",
			"data": {
				"issuer_id": "93600836",
				"qr_string": "000201010211264801189360011410005191210215IDE0190000001140303UME51430013ID.OR.DBS.WWW0215IDE0190000001140303UME5204581253033605802ID5920KOPI LIYOUNG JAMAICA6013JAKARTA PUSAT61051034062070703A016304D5BA",
				"qr_decompiled": {
					"00_payloadFormatIndicator": "01",
					"01_pointOfInitiationMethod": "11",
					"26_merchantAccountInformation26": {
						"merchantPan": "936001141000519121",
						"merchantId": "IDE019000000114",
						"merchantCriteria": "UME"
					},
					"51_merchantAccountInformation51": {
						"globallyUniqueIdentifier": "ID.OR.DBS.WWW",
						"merchantId": "IDE019000000114",
						"merchantCriteria": "UME"
					},
					"52_merchantCategoryCode": "5812",
					"53_transactionCurrency": "360",
					"58_countryCode": "ID",
					"59_merchantName": "KOPI LIYOUNG JAMAICA",
					"60_merchantCity": "JAKARTA PUSAT",
					"61_postalCode": "10340",
					"62_additionalDataField": {
						"terminalLabel": "A01"
					},
					"63_CRC": "D5BA"
				}
			},
			"debug": {
				"execution_time_ms": 270.8
			}
		}
	*/
} catch (\Throwable $e) {
	$vendor->ThrowErrorException($e);
}
