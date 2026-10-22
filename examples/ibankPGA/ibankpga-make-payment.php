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
 * Instruct payment on a qris
 *
 */
$payment = (object) [
		'merchant_uuid' => $init->getMID(),
		'service_code' => 'NSIQRIS',
		/**
		 *
		 * Pick one, with invoice_num or qr_string
		 *
		 */
		//
		//'invoice_num' => 'I122900001',
		//
		'qr_string' => base64_encode('00020101021126670015ID.OR.GPNQR.WWW011893600114100051912102154BIO123456789150303UME5204581253033605405210005802ID5920KOPI LIYOUNG JAMAICA6013JAKARTA PUSAT61051034062070703A016304CBA2'),
		//
		'customer_name' => 'TESTER',
		'customer_phone' => '081112131415',
		'customer_email' => 'tester@nusapay.co.id',
		'customer_id' => '1234567890',
		//
		'amount' => 0,
		'fee_fixed' => 21000,
		'reference_num' => 'PY' . substr(time(), 0, 12),
];
try {
	$result = $vendor->MakePayment($payment);
	extract($result);
	print_r($response);
	/* // Success
		{
			"status": "000",
			"message": "Payment success",
			"data": {
				"merchant_uuid": "15c1c9ee-98db-3fb7-85ed-85ece14894cd",
				"service_code": "NSIQRIS",
				"transaction_uuid": "78275893-39d1-3cc7-ba06-1258cf020432",
				"payment_invoice_num": "P12290000003",
				"payment_reference_num": 1672284489,
				"description": "",
				"currency": "360",
				"net_amount": 40000,
				"fee_amount": 0,
				"surcharge_amount": 0,
				"amount": 40000,
				"customer_id": "1234567890",
				"customer_name": "TESTER",
				"customer_phone": "081112131415",
				"customer_email": "tester@nusapay.co.id",
				"billing_invoice_num": "I122900002",
				"billing_reference_num": "QR0000002184",
				"billing_qr": "https:\/\/ib.nusapay.co.id\/qr\/15c1c9ee-98db-3fb7-85ed-85ece14894cd\/nsiqris\/2022\/12\/29\/QR0000002184.png",
				"billing_qr_string": "00020101021126570017ID.CO.NUSAPAY.WWW011893600836000000000002031230303UKE51350017ID.CO.NUSAPAY.WWW02031230303UKE520465325303360540840000.005802ID5908NUSAPAY16009TANGERANG61051533162740110I12290000202120888123456780307NPLABEL0512QR00000021840703A010806BILL016304596D",
				"status": "1",
				"status_descr": "PAID",
				"paid_at": "2022-12-29 10:28:10"
			},
			"debug": {
				"execution_time_ms": 175.39
			}
		}
	*/
} catch (\Throwable $e) {
	$vendor->ThrowErrorException($e);
}



// /**
//  *
//  * Check payment status (local ib)
//  *
//  */
// $payment = (object) [
// 	'payment_method' => 'qris',
// 		'merchant_uuid' => $init->getMID(),
// 		'service_code' => 'NSIQRIS',
// 		'invoice_num' => 'P03070000007',
// ];
// try {
// 	$result = $vendor->CheckPayment($payment);
// 	extract($result);
// 	print_r($response);
// 	/* // Success
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }


