<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(null, null);
$init->setMerchantKey($_ENV['MPAY_KEY']);
$init->setMerchantToken($_ENV['MPAY_TOKEN']);
$init->setPaymentURL($_ENV['MPAY_HOST_URL']);
$init->setRequestURL($_ENV['MPAY_HOST_URL']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$init->setReturnURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \PyGPaymentLib\Vendors\MPay($init);



// /**
//  *
//  * Create billing
//  *
//  */
// try {
// 	$transaction = new \PyGPaymentLib\Transaction();
// 	$transaction->setCustomerName('Testing');
// 	$transaction->setCustomerEmail('testing@email.com');
// 	$transaction->setCustomerPhone('081212121314');
// 	$transaction->setAmount(10000);
// 	//
// 	/*
// 		va_mandiri
// 			oneoff / persistent
// 				Open Amount, Closed Amount
// 		va_bca
// 			oneoff / persistent
// 				Open Amount, Closed Amount
// 		va_bri
// 			oneoff / persistent
// 				Open Amount, Closed Amount
// 		va_bni
// 			oneoff
// 				Closed Amount
// 		va_permata
// 			oneoff / persistent
// 				Open Amount, Closed Amount
// 		va_danamon
// 			persistent
// 				Open Amount
// 		va_cimb
// 			oneoff
// 				Closed Amount
// 		va_sahabat_sampoerna
// 			oneoff / persistent
// 				Open Amount, Closed Amount
// 	*/
// 	$transaction->setPaymentMethod('va');
// 	$transaction->setPaymentChannel('va_mandiri');
// 	//
// 	$result = $vendor->SecurePayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/*
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Create billing QRIS
//  *
//  */
// try {
// 	$transaction = new \PyGPaymentLib\Transaction();
// 	$transaction->setCustomerName('Testing');
// 	$transaction->setCustomerEmail('testing@email.com');
// 	$transaction->setCustomerPhone('081212121314');
// 	$transaction->setAmount(10000);
// 	$transaction->setPaymentMethod('qris');
// 	$transaction->setReferenceNumber(time());
// 	//
// 	$result = $vendor->SecurePayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/*
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Create billing e-wallet
//  *
//  */
// try {
// 	$transaction = new \PyGPaymentLib\Transaction();
// 	$transaction->setCustomerName('Testing');
// 	$transaction->setCustomerEmail('testing@email.com');
// 	$transaction->setCustomerPhone('081212121314');
// 	$transaction->setAmount(10000);
// 	/*
// 	E-Wallet Providers		E_WALLET_CODE		Minimum (IDR)		Maximum (IDR)
// 	DANA							dana					1.000					10.000.000
// 	OVO							ovo					1.000					10.000.000
// 	SHOPEEPAY					shopeepay			1.000					10.000.000
// 	*/
// 	$transaction->setPaymentMethod('ewallet');
// 	$transaction->setPaymentChannel('dana');
// 	//
// 	$result = $vendor->SecurePayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/*
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Create billing convenience store
//  *
//  */
// try {
// 	$transaction = new \PyGPaymentLib\Transaction();
// 	$transaction->setCustomerName('Testing');
// 	$transaction->setCustomerEmail('testing@email.com');
// 	$transaction->setCustomerPhone('081212121314');
// 	$transaction->setAmount(10000);
// 	/*
// 		Retail Name		RETAIL_CHANNEL_CODE		Minimum (IDR)		Maximum (IDR)		Status Channel
// 		ALFAMART			alfamart						10.000				5.000.000			Ready
// 		INDOMARET		indomart						10.000				5.000.000			Cooming Soon
// 		DANDAN			dandan						10.000				5.000.000			Cooming Soon
// 	*/
// 	$transaction->setPaymentMethod('cstore');
// 	$transaction->setPaymentChannel('dandan');
// 	//
// 	$result = $vendor->SecurePayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/*
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
// 		'reference_id' => '0074563427',
// 		'account_no' => '8932560002191450',
// 	]);
// 	extract($result);
// 	print_r($response);
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }
