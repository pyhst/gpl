<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['DUITKU_MERCHANT_ID'],
	$_ENV['DUITKU_API_KEY']
);
$init->setPaymentURL($_ENV['DUITKU_HOST_URL']);
$init->setParserURL($_ENV['DUITKU_PARSER']);
$init->setCallbackURL($_ENV['DUITKU_CALLBACK_URL']);
$init->setReturnURL($_ENV['DUITKU_RETURN_URL']);

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('Testing');
$transaction->setCustomerEmail('testing@email.com');
$transaction->setCustomerPhone('6281213141516');
$transaction->setAmount(150000);
// VC	Credit Card (Visa / Master)
// BK	BCA KlikPay
// M2	Mandiri Virtual Account
// BT	Permata Bank Virtual Account
// A1	ATM Bersama
// B1	CIMB Niaga Virtual Account
// I1	BNI Virtual Account
// VA	Maybank Virtual Account
// FT	Ritel
// OV	OVO
// DN	Indodana Paylater
// SP	Shopee Pay
// SA	Shopee Pay Apps
// AG	Bank Artha Graha
// S1	Bank Sahabat Sampoerna
// ---------------------------------------------------------
// VC	Credit Card (Visa / Master)
// BK	BCA KlikPay
// BC	BCA Virtual Account
// M1	Mandiri Virtual Account (Deprecated)
// M2	Mandiri Virtual Account
// BT	Permata Bank Virtual Account
// A1	ATM Bersama
// B1	CIMB Niaga Virtual Account
// I1	BNI Virtual Account
// VA	Maybank Virtual Account
// FT	Ritel
// OV	OVO
// DN	Indodana Paylater
// SP	Shopee Pay
// SA	Shopee Pay Apps
// AG	Bank Artha Graha
// S1	Bank Sahabat Sampoerna
// LA	LinkAja Apps (Percentage Fee)
// LF	LinkAja Apps (Fixed Fee)
// LQ	LinkAja QRIS
// DA	DANA

// $transaction->setPaymentMethod('OV');
$transaction->setPaymentMethod('DA');
// $transaction->setPaymentMethod('SP');

/*
// Pre-set Credit Card information
// $transaction->setCardNumber('4811111111111114');
// $transaction->setCardExpMonth('12');
// $transaction->setCardExpYear('24');
// $transaction->setCardCvv('123');
$transaction->setCardHolderName($transaction->getCustomerName());
$transaction->setCardNumber('4000000000000044');
$transaction->setCardExpMonth('02');
$transaction->setCardExpYear('22');
$transaction->setCardCvv('123');
*/

$vendor = new \Growinc\Payment\Vendors\Duitku($init);

try {
	$result = $vendor->SecurePayment($transaction); // return payment URL
	// $result = $vendor->RedirectPayment($transaction); // redirect to vendor URL
	extract($result);
	print_r($response);

	if (
		$transaction->getPaymentMethod() == 'OV'
		|| $transaction->getPaymentMethod() == 'VC'
	) {
		$content = (object) json_decode($result['response']['content']);
		if (!empty($content->data->paymentUrl)) {
			$payment_url = $content->data->paymentUrl;
			$result = $vendor->ParsePaymentPage(
					$payment_url,
					$transaction->getCustomerPhone()
				);
			extract($result);
			print_r($response);
		}
	}
	/*
	VA
	{
		"status": "000",
		"data": {
			"merchantCode": "D8014",
			"reference": "D8014J43L38A4WBVK36H",
			"paymentUrl": "https:\/\/sandbox.duitku.com\/topup\/topupdirectv2.aspx?ref=M1XHKFQMVCHERAK1Q",
			"vaNumber": "8903980801508272",
			"amount": "100000",
			"statusCode": "00",
			"statusMessage": "SUCCESS"
		}
	}
	Ritel (Alfamart dll)
	{
		"status": "000",
		"data": {
			"merchantCode": "D8014",
			"reference": "D8014KAK0QMTAAL81W3L",
			"paymentUrl": "https:\/\/sandbox.duitku.com\/topup\/topupdirectv2.aspx?ref=FTLI1JHIESTF00MXQ",
			"vaNumber": "021132640865",
			"amount": "100000",
			"statusCode": "00",
			"statusMessage": "SUCCESS"
		}
	}
	*/
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()->getBody()->getContents()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage() . ':' . $e->getCode();
}
