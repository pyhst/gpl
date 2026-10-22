<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init($_ENV['MAWARPAY_MERCHANT_ID']);
$init->setPrivateKey($_ENV['MAWARPAY_MERCHANT_PRIVATE_KEY']);
$init->setPublicKey($_ENV['MAWARPAY_PUBLIC_KEY']);
$init->setPaymentURL($_ENV['MAWARPAY_HOST_URL']);
$init->setRequestURL($_ENV['MAWARPAY_HOST_URL']);
$init->setCallbackURL($_ENV['MAWARPAY_WEBHOOK_URL']);
$init->setReturnURL($_ENV['MAWARPAY_WEBHOOK_URL']);

$vendor = new \Growinc\Payment\Vendors\MawarPay($init);



/**
 *
 * Create billing
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'SecurePayment') {
	try {
		$time = time();
		$transaction = new \Growinc\Payment\Transaction();
		$transaction->setReferenceNumber($time);
		$transaction->setAmount(15000);
		$transaction->setDescription('BILLING1');
		$transaction->setCustomerName('TESTER');
		$transaction->setCustomerPhone('081212121314');
		$transaction->setCustomerEmail('tester@email.com');
		//
		/* // Channel list:
			QRIS QR Code:
				QRIS,StaticQRIS

			Virtual Account:
				BNIVA,CIMBVA,PermataVA,BRIVA,MandiriVA,BCAVA,BSIVA,BNCVA,INAVA,BNISyariahVA,ALTOVA,SinarmasVA,PrimaVA,
				MUAMALATVA,DanamonVA,NobuVA

			Ewallet:
				DANAWALLET
				OVOWALLET
				SHOPEEPAY-APP
				LINKAJA-APP
		*/
		$transaction->setPaymentChannel('QRIS');
		//
		$result = $vendor->SecurePayment($transaction);
		extract($result);
		print_r($response);
		/* // Success QRIS
			{
				"status": "000",
				"data": {
					"returnCode": "SUCCESS",
					"mchId": "410022",
					"outTradeNo": "1792311038",
					"outChannelNo": "2026101802200001417",
					"amount": 15000,
					"payContentType": "04",
					"payContent": "{\"qrisUrl\":\"https://id-payer.smmslink.com/payer/en-qris-index-v2.html?a=S3h6TXdCU3BQR04xQ2lUcC9CWkdLUW92amxKeVBhdE9nVk9RUzVlZmphdz0=\",\"qrCode\":\"00020101021226670016COM.NOBUBANK.WWW01189360050300000850300214260909000007850303UBE51440014ID.CO.QRIS.WWW0303UBE0215ID2026590064880520465335303360540815000.005802ID5917Swiftplus payment6013JAKARTA TIMUR61051314062720108MB8TADP3061920261018054007935020703A010804POSP991800020001082026101863049486\"}",
					"transTime": "20261018151039",
					"expiredTime": "20261019151040"
				}
			}
		*/
		/* // Success VA
		*/
	} catch (\Throwable $e) {
		print_r($vendor::ThrowError($e));
	}
}

/**
 *
 * Inquiry trx
 *
 */
if (!empty($_REQUEST['a']) && $_REQUEST['a'] == 'Inquiry') {
	try {
		$transaction = new \Growinc\Payment\Transaction();
		$result = $vendor->Inquiry((object) [
			'outTradeNo' => '1792311038',
		]);
		extract($result);
		print_r($response);
		/* // Success inquiry QRIS
			{
				"status": "000",
				"data": {
					"returnCode": "SUCCESS",
					"outTradeNo": "1792311038",
					"outChannelNo": "2026101802200001417",
					"transTime": "20261018151039",
					"expiredTime": "20261019151040",
					"payContentType": "04",
					"payContent": "{\"qrisUrl\":\"https://id-payer.smmslink.com/payer/en-qris-index-v2.html?a=S3h6TXdCU3BQR04xQ2lUcC9CWkdLUW92amxKeVBhdE9nVk9RUzVlZmphdz0=\",\"qrCode\":\"00020101021226670016COM.NOBUBANK.WWW01189360050300000850300214260909000007850303UBE51440014ID.CO.QRIS.WWW0303UBE0215ID2026590064880520465335303360540815000.005802ID5917Swiftplus payment6013JAKARTA TIMUR61051314062720108MB8TADP3061920261018054007935020703A010804POSP991800020001082026101863049486\"}",
					"amount": 15000,
					"transFeeRate": 0.008,
					"transFee": 0,
					"totalTransFee": 120,
					"mchId": "410022",
					"status": "01",
					"payChannelType": "QRIS"
				}
			}
		*/
		/* // Success inquiry VA
		*/
	} catch (\Throwable $e) {
		print_r($vendor::ThrowError($e));
	}
}