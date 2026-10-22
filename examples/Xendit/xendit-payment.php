<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['XENDIT_API_KEY'],
	$_ENV['XENDIT_PUBLIC_KEY']
);
$init->setPaymentURL($_ENV['XENDIT_HOST_URL']);
/*------------------------------ V V V Start of Required by EWALLET and QRIS ---------- */
$init->setCallbackURL($_ENV['XENDIT_CALLBACK_URL']);
$init->setReturnURL($_ENV['XENDIT_RETURN_URL']);
/*------------------------------ A A A End of Required by EWALLET and QRIS ---------- */

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('testing');
$transaction->setCustomerEmail('testing@gmail.com');
$transaction->setCustomerPhone('+6288812131415');
$transaction->setCountrycode('IDN');
//
$transaction->setItem('Product');
$transaction->setAmount(100000);
$transaction->setDescription('Product');

// Payment Method Supported:
// 1. bank_transfer (VA) ----------- CURRENTLY ONLY THIS SUPPORTED
// 	bca
// 	bni
// 	bri
// 	mandiri
// 	permata
// 	sahabat_sampoerna
// 2. credit_card
// 3. ewallet
// 	ovo
// 	linkaja
// 	shopeepay
// 3. qr (QRIS)
// 4. cstore (retail)
// 	alfamart
// 	indomaret

// $transaction->setPaymentMethod('bank_transfer,bca');
$transaction->setPaymentMethod('bank_transfer,mandiri');
// $transaction->setPaymentMethod('bank_transfer,permata');
// $transaction->setPaymentMethod('credit_card'); // Currently Inapplicable
// $transaction->setPaymentMethod('ewallet,ovo');
// $transaction->setPaymentMethod('ewallet,dana');
// $transaction->setPaymentMethod('ewallet,linkaja');
// $transaction->setPaymentMethod('ewallet,shopeepay');
// $transaction->setPaymentMethod('qris');
// $transaction->setPaymentMethod('cstore,indomaret');
// $transaction->setPaymentMethod('cstore,alfamart');

$vendor = new \Growinc\Payment\Vendors\Xendit($init);

try {
	$result = $vendor->SecurePayment($transaction); // return payment URL
	// extract($result);
	// print_r($response);
	print_r($result);
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object

	/* Success
	OVO
	{
		"status": "000",
		"data": {
			"id": "ewc_d232e18d-057b-4f90-8c37-e35e948299f3",
			"business_id": "5fc89b828c3f5a408eef8f78",
			"reference_id": "0021186669",
			"status": "PENDING",
			"currency": "IDR",
			"charge_amount": 100000,
			"capture_amount": 100000,
			"refunded_amount": null,
			"checkout_method": "ONE_TIME_PAYMENT",
			"channel_code": "ID_OVO",
			"channel_properties": {
				"mobile_number": "+6288812345678"
			},
			"actions": null,
			"is_redirect_required": false,
			"callback_url": "https:\/\/a.g-dev.io\/secure\/callback\/demo",
			"created": "2021-05-16T17:37:52.621Z",
			"updated": "2021-05-16T17:37:52.621Z",
			"void_status": null,
			"voided_at": null,
			"capture_now": true,
			"customer_id": null,
			"payment_method_id": null,
			"failure_code": null,
			"basket": [{
				"reference_id": "1",
				"name": "Apple",
				"category": "DIGITAL",
				"currency": "IDR",
				"price": 100000,
				"quantity": 1,
				"type": "PRODUCT"
			}],
			"metadata": null
		}
	}

	DANA
	{
		"status": "000",
		"data": {
			"id": "ewc_5c7a455f-eb3e-46bf-808e-6afc43b5e01a",
			"business_id": "5fc89b828c3f5a408eef8f78",
			"reference_id": "0021186143",
			"status": "PENDING",
			"currency": "IDR",
			"charge_amount": 100000,
			"capture_amount": 100000,
			"refunded_amount": null,
			"checkout_method": "ONE_TIME_PAYMENT",
			"channel_code": "ID_DANA",
			"channel_properties": {
				"success_redirect_url": "https:\/\/a.g-dev.io\/secure\/callback\/demo"
			},
			"actions": {
				"desktop_web_checkout_url": "https:\/\/ewallet-mock-connector.xendit.co\/v1\/ewallet_connector\/checkouts?token=95b90268-189d-4de3-853a-e3c61834e779",
				"mobile_web_checkout_url": "https:\/\/ewallet-mock-connector.xendit.co\/v1\/ewallet_connector\/checkouts?token=95b90268-189d-4de3-853a-e3c61834e779",
				"mobile_deeplink_checkout_url": null,
				"qr_checkout_string": null
			},
			"is_redirect_required": true,
			"callback_url": "https:\/\/a.g-dev.io\/secure\/callback\/demo",
			"created": "2021-05-16T17:29:06.342Z",
			"updated": "2021-05-16T17:29:06.342Z",
			"void_status": null,
			"voided_at": null,
			"capture_now": true,
			"customer_id": null,
			"payment_method_id": null,
			"failure_code": null,
			"basket": [{
				"reference_id": "1",
				"name": "Apple",
				"category": "DIGITAL",
				"currency": "IDR",
				"price": 100000,
				"quantity": 1,
				"type": "PRODUCT"
			}],
			"metadata": null
		}
	}

	LINKAJA
	{
		"status": "000",
		"data": {
			"id": "ewc_1a0a5e4e-a236-49fd-80de-802a747895d9",
			"business_id": "5fc89b828c3f5a408eef8f78",
			"reference_id": "0021186221",
			"status": "PENDING",
			"currency": "IDR",
			"charge_amount": 100000,
			"capture_amount": 100000,
			"refunded_amount": null,
			"checkout_method": "ONE_TIME_PAYMENT",
			"channel_code": "ID_LINKAJA",
			"channel_properties": {
				"success_redirect_url": "https:\/\/a.g-dev.io\/secure\/callback\/demo"
			},
			"actions": {
				"desktop_web_checkout_url": "https:\/\/ewallet-linkaja-dev.xendit.co\/checkouts\/1a0a5e4e-a236-49fd-80de-802a747895d9",
				"mobile_web_checkout_url": "https:\/\/ewallet-linkaja-dev.xendit.co\/checkouts\/1a0a5e4e-a236-49fd-80de-802a747895d9",
				"mobile_deeplink_checkout_url": null,
				"qr_checkout_string": null
			},
			"is_redirect_required": true,
			"callback_url": "https:\/\/a.g-dev.io\/secure\/callback\/demo",
			"created": "2021-05-16T17:30:24.249Z",
			"updated": "2021-05-16T17:30:24.249Z",
			"void_status": null,
			"voided_at": null,
			"capture_now": true,
			"customer_id": null,
			"payment_method_id": null,
			"failure_code": null,
			"basket": [{
				"reference_id": "1",
				"name": "Apple",
				"category": "DIGITAL",
				"currency": "IDR",
				"price": 100000,
				"quantity": 1,
				"type": "PRODUCT"
			}],
			"metadata": null
		}
	}

	SHOPEEPAY
	{
		"status": "000",
		"data": {
			"id": "ewc_2fd4da7f-334d-4b5c-8990-4e788d582bc1",
			"business_id": "5fc89b828c3f5a408eef8f78",
			"reference_id": "0021186255",
			"status": "PENDING",
			"currency": "IDR",
			"charge_amount": 100000,
			"capture_amount": 100000,
			"refunded_amount": null,
			"checkout_method": "ONE_TIME_PAYMENT",
			"channel_code": "ID_SHOPEEPAY",
			"channel_properties": {
				"success_redirect_url": "https:\/\/a.g-dev.io\/secure\/callback\/demo"
			},
			"actions": {
				"desktop_web_checkout_url": null,
				"mobile_web_checkout_url": null,
				"mobile_deeplink_checkout_url": "https:\/\/ewallet-mock-connector.xendit.co\/v1\/ewallet_connector\/checkouts?token=71a3c734-a5e0-4438-8fab-2c4d07b87366",
				"qr_checkout_string": "test-qr-string"
			},
			"is_redirect_required": true,
			"callback_url": "https:\/\/a.g-dev.io\/secure\/callback\/demo",
			"created": "2021-05-16T17:30:58.739021Z",
			"updated": "2021-05-16T17:30:58.739021Z",
			"void_status": null,
			"voided_at": null,
			"capture_now": true,
			"customer_id": null,
			"payment_method_id": null,
			"failure_code": null,
			"basket": [{
				"reference_id": "1",
				"name": "Apple",
				"category": "DIGITAL",
				"currency": "IDR",
				"price": 100000,
				"quantity": 1,
				"type": "PRODUCT"
			}],
			"metadata": null
		}
	}
	*/
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
