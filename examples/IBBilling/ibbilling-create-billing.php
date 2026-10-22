<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init($_ENV['IBBILLING_MERCHANT_UUID'], null);
$init->setPaymentURL($_ENV['IBBILLING_HOST_URL']);
$init->setRequestURL($_ENV['IBBILLING_HOST_URL']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$init->setReturnURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \Growinc\Payment\Vendors\IBBilling($init);



// /**
//  *
//  * Create billing
//  *
//  */
// try {
// 	$time = time();
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setServiceCode('QRIS');
// 	$transaction->setMerchantID('GPL');
// 	$transaction->setPaymentType('pg');
// 	// $transaction->setParam('service_id', 6);
// 	// $transaction->setParam('credential_id', 6);
// 	//
// 	$transaction->setReferenceNumber($time);
// 	$transaction->setTransactionRemark('TRX' . $time);
// 	$transaction->setDescription('TRX' . $time);
// 	$transaction->setCustomerID('1234567890');
// 	$transaction->setCustomerName('Testing');
// 	$transaction->setCustomerPhone('081212121314');
// 	$transaction->setCustomerEmail('tester@gmail.com');
// 	$transaction->setAmount(10000);
// 	$transaction->setExpireIn(10);
// 	//
// 	$result = $vendor->SecurePayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success QRIS
// 		{
// 			"status": "000",
// 			"message": "Billing successfully created",
// 			"data": {
// 				"uuid": "9c3642c8-3321-4b8a-8d7c-23818c671699",
// 				"reference_num": "1717568625",
// 				"invoice_num": "0DFSBN3TC56QZ12W",
// 				"billing_num": "5320986110794456",
// 				"type": "pg",
// 				"name": "TRX1717568625",
// 				"description": "TRX1717568625",
// 				"items": null,
// 				"qr": "00020101021226670016ID.CO.MBAYAR.WWW01189360082901714465890214E2P171446589520303UMI51460016ID.CO.MBAYAR.WWW0215ID10243227058630303UMI520458165303360540810000.005802ID5910Berkatgoup6009SUKOHARJO61055755262500507602141207062121200825PT Berkah Berkat Bersatu 6304763C",
// 				"url": "http:\/\/ib-billing.test\/qr\/g2up\/qris\/2024\/06\/05\/1717568625.png",
// 				"expire_in": 10,
// 				"currency": "360",
// 				"net_amount": 10000,
// 				"fee_fixed": 0,
// 				"fee_percentage": 0,
// 				"fee_amount": 0,
// 				"surcharge_fixed": 0,
// 				"surcharge_percentage": 0,
// 				"surcharge_amount": 0,
// 				"amount": 10000,
// 				"customer_id": "1234567890",
// 				"customer_name": "TESTING",
// 				"customer_phone": "081212121314",
// 				"customer_email": "tester@gmail.com",
// 				"customer_details": null,
// 				"created_at": "2024-06-05 13:23:45",
// 				"updated_at": "2024-06-05 13:23:46",
// 				"expiring_at": "2024-06-05 13:33:45"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Inquiry trx
//  *
//  */
// try {
// 	$result = $vendor->Inquiry((object) [
// 		'service_code' => 'QRIS',
// 		'owner_id' => 'PGA',
// 		'type' => 'pg',
// 		'reference_num' => 'TEST1721207001846',
// 	]);
// 	extract($result);
// 	print_r($response);
// 	/* // Success inquiry QRIS
// 		{
// 			"status": "000",
// 			"message": "Request complete",
// 			"data": {
// 				"credential_id": 1,
// 				"credential_name": "CRONOS",
// 				"uuid": "9c8af932-9e6c-4118-bbbb-fce684c68d4f",
// 				"reference_num": "TEST1721207001846",
// 				"invoice_num": "0DG12IR7TK9FVCP4",
// 				"provider_transaction_id": "72d6f033-a639-4e8e-b2f1-d2e2f6b2876d",
// 				"billing_num": null,
// 				"type": "pg",
// 				"qr": "Velit accusantium dolores eos incidunt repellat. Dolorum quas eligendi debitis doloremque. Molestiae laboriosam ratione aperiam temporibus.",
// 				"url": "\/qr\/cronos\/acceptance_qris\/2024\/07\/17\/TEST1721207001846.png",
// 				"amount": 50000,
// 				"status": 1,
// 				"status_descr": "PAID",
// 				"created_at": "2024-07-17 16:03:22",
// 				"expired_at": null,
// 				"paid_at": null,
// 				"settled_at": null,
// 				"additional_info": {
// 						"customer_id": "1234567890",
// 						"customer_name": "PGA",
// 						"customer_phone": "081212121314",
// 						"customer_email": "tester@gmail.com",
// 						"customer_details": null
// 				}
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Inquiry trx alt
//  *
//  */
// try {
// 	$result = $vendor->InquiryAlt((object) [
// 		'owner_id' => 'GPL',
// 		'type' => 'pg',
// 		'uuid' => '9c3642c8-3321-4b8a-8d7c-23818c671699',
// 		'filter' => null,
// 		'status' => '*',
// 		'start_date' => date('Ymd'),
// 		'end_date' => date('Ymd'),
// 		// 'per_page' => 25,
// 		// 'page' => 1,
// 		// 'limit' => 0,
// 	]);
// 	extract($result);
// 	print_r($response);
// 	/* // Success inquiry QRIS
// 		{
// 			"status": "000",
// 			"current_page": 1,
// 			"data": [
// 				{
// 						"db_table": "billing_pg_20240605",
// 						"db_index": "20240605-38",
// 						"id": 38,
// 						"owner_id": "GPL",
// 						"client_id": null,
// 						"domain_id": 3,
// 						"provider_id": 4,
// 						"company_id": 1,
// 						"merchant_id": 1,
// 						"channel_id": 6,
// 						"credential_id": 4,
// 						"pricing_id": 1,
// 						"service_id": 4,
// 						"uuid": "9c3642c8-3321-4b8a-8d7c-23818c671699",
// 						"reference_num": "1717568625",
// 						"invoice_num": "0DFSBN3TC56QZ12W",
// 						"billing_num": "5320986110794456",
// 						"type": "pg",
// 						"name": "TRX1717568625",
// 						"description": "TRX1717568625",
// 						"items": null,
// 						"qr": "00020101021226670016ID.CO.MBAYAR.WWW01189360082901714465890214E2P171446589520303UMI51460016ID.CO.MBAYAR.WWW0215ID10243227058630303UMI520458165303360540810000.005802ID5910Berkatgoup6009SUKOHARJO61055755262500507602141207062121200825PT Berkah Berkat Bersatu 6304763C",
// 						"url": "http:\/\/ib-billing.test\/qr\/g2up\/qris\/2024\/06\/05\/1717568625.png",
// 						"expire_in": 10,
// 						"currency": "360",
// 						"net_amount": "10000.00",
// 						"fee_fixed": "0.00",
// 						"fee_percentage": "0.00",
// 						"fee_amount": "0.00",
// 						"surcharge_fixed": "0.00",
// 						"surcharge_percentage": "0.00",
// 						"surcharge_amount": "0.00",
// 						"tips_fixed": "0.00",
// 						"tips_percentage": "0.00",
// 						"tips_amount": "0.00",
// 						"amount": "10000.00",
// 						"paid_amount": "0.00",
// 						"refunded_amount": "0.00",
// 						"customer_id": "1234567890",
// 						"customer_name": "TESTING",
// 						"customer_phone": "081212121314",
// 						"customer_email": "tester@gmail.com",
// 						"customer_details": null,
// 						"param": null,
// 						"status": 0,
// 						"log": null,
// 						"created_at": "2024-06-05T06:23:45.000000Z",
// 						"updated_at": "2024-06-05T06:23:46.000000Z",
// 						"suspended_at": null,
// 						"paid_at": null,
// 						"refunded_at": null,
// 						"cancelled_at": null,
// 						"settling_at": null,
// 						"settled_at": null,
// 						"expiring_at": "2024-06-05 13:33:45",
// 						"expired_at": null,
// 						"deleted_at": null,
// 						"payment_tables": null,
// 						"payment_table_ids": null,
// 						"refund_tables": null,
// 						"refund_table_ids": null,
// 						"inbound_tables": "[\"inbound_pg_20240605\"]",
// 						"inbound_table_ids": "[60]",
// 						"outbound_tables": "[\"outbound_pg_20240605\"]",
// 						"outbound_table_ids": "[8]",
// 						"provider": {
// 							"id": 4,
// 							"name": "G2UP",
// 							"providergroup": null,
// 							"domain": null
// 						},
// 						"company": {
// 							"id": 1,
// 							"name": "PT INTERNAL TESTER",
// 							"domain": null,
// 							"companygroup": null,
// 							"credential": []
// 						},
// 						"merchant": {
// 							"id": 1,
// 							"name": "BRANDS OF PGA 1",
// 							"merchantgroup": null,
// 							"domain": null
// 						},
// 						"channel": {
// 							"id": 6,
// 							"name": "G2QRIS",
// 							"code": "QRIS",
// 							"provider": null,
// 							"channelgroup": null,
// 							"pricing": null,
// 							"pricinggroup": null
// 						},
// 						"credential": {
// 							"id": 4,
// 							"name": "PT INTERNAL TESTER x G2UP",
// 							"provider": null,
// 							"company": null
// 						},
// 						"service": {
// 							"id": 4,
// 							"name": "QRIS",
// 							"merchantgroup": null,
// 							"merchant": null,
// 							"credential": null,
// 							"channel": null,
// 							"provider": null,
// 							"pricing": null,
// 							"pricinggroup": null
// 						},
// 						"pricing": {
// 							"id": 1,
// 							"name": "QRIS",
// 							"currency": "360",
// 							"fee_fixed": "0.00",
// 							"fee_percentage": "0.78",
// 							"surcharge_fixed": "0.00",
// 							"surcharge_percentage": "0.00",
// 							"pricinggroup": null
// 						}
// 				}
// 			],
// 			"first_page_url": "http:\/\/ib-billing.test\/api-v1\/billingpg\/get?page=1",
// 			"from": 1,
// 			"last_page": 1,
// 			"last_page_url": "http:\/\/ib-billing.test\/api-v1\/billingpg\/get?page=1",
// 			"links": [
// 				{
// 						"url": null,
// 						"label": "&laquo; Previous",
// 						"active": false
// 				},
// 				{
// 						"url": "http:\/\/ib-billing.test\/api-v1\/billingpg\/get?page=1",
// 						"label": "1",
// 						"active": true
// 				},
// 				{
// 						"url": null,
// 						"label": "Next &raquo;",
// 						"active": false
// 				}
// 			],
// 			"next_page_url": null,
// 			"path": "http:\/\/ib-billing.test\/api-v1\/billingpg\/get",
// 			"per_page": 25,
// 			"prev_page_url": null,
// 			"to": 1,
// 			"total": 1
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }
