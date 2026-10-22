<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['IBANK_PGA_UUID'],
	$_ENV['IBANK_PGA_SECRET_KEY'],
);
$init->setPaymentURL($_ENV['IBANK_PGA_HOST_URL']);
$init->setRequestURL($_ENV['IBANK_PGA_HOST_URL']);
$init->setParams([
	'service_code' => $_ENV['IBANK_PGA_SERVICE_CODE'],
]);
$vendor = new \Growinc\Payment\Vendors\ibankPGA($init);



/**
 *
 * Create billing
 *
 */
$transaction = new \Growinc\Payment\Transaction();
$transaction->setPaymentMethod('qris');
$transaction->setServiceCode('NSIQRIS');
// $transaction->setInvoiceNo(time()); // Optional
$transaction->setReferenceNumber(time());
$transaction->setCustomerID('1234567890');
$transaction->setCustomerName('Tester');
$transaction->setCustomerEmail('tester@email.com');
$transaction->setCustomerPhone('081212121314');
$transaction->setAmount(150000);
	$transaction->setFeeFixed(0);
	$transaction->setFeePercentage(0);
		$transaction->setSurchargeFixed(0);
		$transaction->setSurchargePercentage(0);
			$transaction->setTipsFixed(0);
			$transaction->setTipsPercentage(0);
			$transaction->setTipsManual(0); // 1 or 0
$transaction->setDescription('Test Payment');
$transaction->setQrType('dynamic');
$transaction->setExpireIn(120);
try {
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($response);
	/* // Success
		{
			"status": "000",
			"data": {
				"merchant_uuid": "15c1c9ee-98db-3fb7-85ed-85ece14894cd",
				"transaction_uuid": "759544fe-e852-37ef-9c75-da12b6559c68",
				"reference_num": "QRNP120500001197",
				"invoice_num": "I120500001",
				"description": "TEST PAYMENT",
				"items": "",
				"currency": "360",
				"net_amount": 150000,
				"fee_amount": 1050,
				"surcharge_amount": 0,
				"amount": 151050,
				"customer_name": "TESTER",
				"customer_phone": "081212121314",
				"customer_email": "tester@email.com",
				"service_code": "NSIQRIS",
				"billing_qr": "https:\/\/ib.nusapay.co.id\/qr\/15c1c9ee-98db-3fb7-85ed-85ece14894cd\/nsiqris\/2022\/12\/05\/QRNP120500001197.png",
				"billing_qr_string": "00020101021226430003WWW011893600836000000000002031230303UKE51430003WWW011893600836000000000002031230303UKE5204653253033605409151050.005802ID5908NUSAPAY16009TANGERANG61051533162840110I12050000102120812121213140307NPLABEL0516QRNP1205000011970703A010812TEST PAYMENT6304F5C4",
				"status": "0",
				"status_descr": "PENDING",
				"created_at": "2022-12-05 09:22:24",
				"expired_at": "2022-12-05 11:22:24"
			}
		}
	*/
} catch (\Throwable $e) {
	$vendor->ThrowErrorException($e);
}



// /**
//  *
//  * Check billing
//  *
//  */
// $inquiry = (object) [
// 	'payment_method' => 'qris',
// 		'merchant_uuid' => $init->getMID(),
// 		'reference_num' => 'QRNP120500001197',
// 		'invoice_num' => 'I120500001',
// 		'with_payments' => 1,
// ];
// try {
// 	$result = $vendor->Inquiry($inquiry);
// 	extract($result);
// 	print_r($response);
// 	/* // Success
// 		{
// 			"status": "000",
// 			"data": {
// 				"merchant_uuid": "15c1c9ee-98db-3fb7-85ed-85ece14894cd",
// 				"transaction_uuid": "759544fe-e852-37ef-9c75-da12b6559c68",
// 				"reference_num": "QRNP120500001197",
// 				"invoice_num": "I120500001",
// 				"description": "TEST PAYMENT",
// 				"items": "",
// 				"currency": "360",
// 				"net_amount": 150000,
// 				"fee_amount": 1050,
// 				"surcharge_amount": 0,
// 				"amount": 151050,
// 				"paid_amount": 0,
// 				"customer_name": "TESTER",
// 				"customer_phone": "081212121314",
// 				"customer_email": "tester@email.com",
// 				"service_code": "NSIQRIS",
// 				"billing_qr": "https:\/\/ib.nusapay.co.id\/qr\/15c1c9ee-98db-3fb7-85ed-85ece14894cd\/nsiqris\/2022\/12\/05\/QRNP120500001197.png",
// 				"billing_qr_string": "00020101021226430003WWW011893600836000000000002031230303UKE51430003WWW011893600836000000000002031230303UKE5204653253033605409151050.005802ID5908NUSAPAY16009TANGERANG61051533162840110I12050000102120812121213140307NPLABEL0516QRNP1205000011970703A010812TEST PAYMENT6304F5C4",
// 				"status": "0",
// 				"status_descr": "PENDING",
// 				"created_at": "2022-12-05 09:22:24",
// 				"expired_at": "2022-12-05 11:22:24"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Cancel billing
//  *
//  */
// $inquiry = (object) [
// 	'payment_method' => 'qris',
// 		'merchant_uuid' => $init->getMID(),
// 		'service_code' => 'NSIQRIS',
// 		'reference_num' => 'QRNP120500003166',
// 		// 'invoice_num' => 'I120500001',
// ];
// try {
// 	$result = $vendor->Cancel($inquiry);
// 	extract($result);
// 	print_r($response);
// 	/* // Success
// 		{
// 			"status": "000",
// 			"message": "Bill was cancelled"
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }
