<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['XFERS_API_KEY'],
	$_ENV['XFERS_SECRET_KEY']
);
$init->setPaymentURL($_ENV['XFERS_HOST_URL']);
$init->setReturnURL($_ENV['XFERS_RETURN_URL']);

$transaction = new \Growinc\Payment\Transaction();
$transaction->setInvoiceNo('INV-' . time());
$transaction->setCustomerName('testing');
$transaction->setCustomerEmail('testing@gmail.com');
$transaction->setCustomerPhone('088812131415');
$transaction->setItem('Product');
$transaction->setAmount(rand(5000,10000) * 10);
$transaction->setDescription('Product' . rand(1, 100));
// $transaction->setExpireAt(2);
//
// BCA
// BRI
// BNI
// MANDIRI
// // CIMB_NIAGA - not supported closed VA
// // DANAMON - not supported closed VA
// // PERMATA - not supported closed VA
// // HANA - not supported closed VA
// SAHABAT_SAMPOERNA
// $transaction->setPaymentMethod('va,BCA');
$transaction->setPaymentMethod('va,BRI');
// $transaction->setPaymentMethod('va,BNI');
// $transaction->setPaymentMethod('va,MANDIRI');
// $transaction->setPaymentMethod('va,SAHABAT_SAMPOERNA');
//
// $transaction->setPaymentMethod('qris');
//
// $transaction->setPaymentMethod('cstore,alfamart');
// $transaction->setPaymentMethod('cstore,indomaret');
//
// $transaction->setPaymentMethod('ewallet,shopee');

$vendor = new \Growinc\Payment\Vendors\Xfers($init);

try {
	$result = $vendor->SecurePayment($transaction);
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
	/* // Success SHOPEE
	{
		"status": "000",
		"data": {
			"id": "contract_899e3995b81441909eac1ad5cd715b18",
			"type": "payment",
			"attributes": {
				"status": "pending",
				"amount": "728900.0",
				"createdAt": "2021-07-16T16:40:00+07:00",
				"description": "Voucher #44",
				"expiredAt": "2021-07-16T17:40:00+07:00",
				"referenceId": "0026428400",
				"fees": "12026.85",
				"paymentMethod": {
					"id": "shopeepay_90873820cab12adddd2166d898c376f99c138a37",
					"type": "e-wallet",
					"referenceId": "0026428400",
					"settlement": {
						"httpUrl": "https:\/\/wsa.uat.wallet.airpay.co.id\/universal-link\/wallet\/pay?deep_and_deferred=1&token=dFhkbmR1bTBIamhW0VqULhun6fTSt7NWCmPiyGIOQMJ-JhyypjBHTMXT0HRooJ0s-VYDWM9Nlex4qQlaCPYI7ywKgR7xG4qn1WibVqXY",
						"afterSettlementUrl": "https:\/\/a.g-dev.io\/secure\/callback\/demo"
					}
				}
			}
		}
	}
	*/
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}
