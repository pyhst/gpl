<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['PLINK_API_KEY'],
	$_ENV['PLINK_SECRET_KEY']
);
$init->setMerchantKey($_ENV['PLINK_MERCHANT_CODE']);
$init->setPaymentURL($_ENV['PLINK_HOST_URL']);
// $init->setResponseUrl('https://www.prismalink.co.id/'); // frontend_callback_url
// $init->setBackendURL('https://www.prismalink.co.id/'); // backend_callback_url
$init->setResponseUrl($_ENV['PLINK_REDIRECT_URL']); // frontend_callback_url
$init->setBackendURL($_ENV['PLINK_CALLBACK_URL']); // backend_callback_url
$vendor = new \Growinc\Payment\Vendors\PrismaLink($init);
//
$transaction = new \Growinc\Payment\Transaction();
$transaction->setInvoiceNo('TEST-INV-' . time());
$transaction->setCustomerUserID('123');
$transaction->setCustomerName('testing');
$transaction->setCustomerEmail('testing@gmail.com');
$transaction->setCustomerPhone('+6288812131415');
$transaction->setCustomerAddress('');
$transaction->setIPAddress('1.1.1.1');
$transaction->setItem('Product');
$transaction->setAmount(rand(5000,10000) * 10);
$transaction->setDescription('PRODUCT#' . rand(1, 100));
$transaction->setExpireIn(24);
$transaction->setExpireAt(strtotime('+2 hours')); // Override --^
//
$transaction->setPaymentMethod('qris');
/* // QRIS
{
	"status": "000",
	"data": {
		"response_description": "SUCCESS",
		"payment_page_url": null,
		"response_code": "PL000",
		"transaction_status": "PNDNG",
		"va_number_list": null,
		"debitin_session_token": null,
		"creditcard_form_url": null,
		"qris_data": "00020101021226670016COM.NOBUBANK.WWW01189360050300000488240214070500000312780303UMI51440014ID.CO.QRIS.WWW0215ID20200814000960303UMI5204549953033605405694505502015802ID5914CLOTHING STORE60032606105158116239011405310000018063061000539801200703A0163047017",
		"plink_ref_no": "31052263957156",
		"creditcard_session_token": null,
		"debitin_form_url": null,
		"response_message": "SUCCESS",
		"initial_data_customer": null,
		"validity": null,
		"timestamp": "2022-05-31 13:55:19.819 +0700"
	}
}
*/
// $transaction->setPaymentMethod('');
/* // VA
{
	"status": "000",
	"data": {
		"order_id": "0053980147",
		"invoice_no": "TEST-INV-1653980147",
		"currency": "IDR",
		"item": "Product",
		"amount": 88950,
		"description": "PRODUCT#1",
		"customer_name": "testing",
		"customer_email": "testing@gmail.com",
		"customer_phone": "+6288812131415",
		"customer_address": null,
		"country_code": null,
		"payment_method": "",
		"expired_in": "24"
	}
}
*/
try {
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($response);
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}