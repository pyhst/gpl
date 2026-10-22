<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init($_ENV['PAYOK_MERCHANT_ID'], null);
$init->setParams([
	'PAYOK_PRIVATE_KEY' => $_ENV['PAYOK_PRIVATE_KEY'],
	'PAYOK_PUBLIC_KEY' => $_ENV['PAYOK_PUBLIC_KEY'],
]);
$init->setPaymentURL($_ENV['PAYOK_HOST_URL']);
$init->setRequestURL($_ENV['PAYOK_HOST_URL']);
$init->setCallbackURL('http://tester.com/secure/callback/demo');
$init->setReturnURL('http://tester.com/secure/callback/demo');

$vendor = new \PyGPaymentLib\Vendors\PayOk($init);



/**
 *
 * Create billing
 *
 */
try {
	$transaction = new \PyGPaymentLib\Transaction();
	$transaction->setCustomerName('TESTER');
	$transaction->setCustomerPhone('081111111111');
	$transaction->setAmount(15000);
	$transaction->setReferenceNumber(time());
	$transaction->setDescription('BILLING1');
	//
	/* // Channel list:
		QRIS:
			QRIS
		EWALLET:
			DANABALANCE
			SHOPEEBALANCE
			LINKAJABALANCE
			OVOBALANCE
			GOPAYBALANCE
		VA:
			SinarmasVA,
			MaybankVA,
			DanamonVA,
			BNCVA,
			BCAVA,
			INAVA,
			BNIVA,
			PermataVA,
			MuamalatVA,
			BSIVA,
			BRIVA,
			MandiriVA,
			CIMBVA
	*/
	//
	$transaction->setPaymentMethod('QRIS');
	$transaction->setPaymentChannel('');
	//
	// $transaction->setPaymentMethod('EWALLET');
	// $transaction->setPaymentChannel('DANABALANCE');
	// //
	// $transaction->setPaymentMethod('VA');
	// $transaction->setPaymentChannel('MuamalatVA');
	//
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($response);
	/* // Success QRIS
		{
			"status": "000",
			"request": {
				"url": "https:\/\/sit-pay.paylabs.co.id\/payment\/v2.1\/qris\/create",
				"data": {
						"requestId": "20240621154844",
						"merchantId": "010430",
						"paymentType": "QRIS",
						"amount": "15000.00",
						"merchantTradeNo": "1718959724",
						"notifyUrl": "http:\/\/tester.com\/secure\/callback\/demo",
						"productName": "BILLING1",
						"expire": 120,
						"productInfo": [
							{
								"id": "1718959724",
								"name": "BILLING1",
								"price": "15000.00",
								"type": "QRIS",
								"quantity": 1
							}
						]
				},
				"headers": {
						"Content-Type": "application\/json;charset=utf-8",
						"X-TIMESTAMP": "2024-06-21T15:48:44.408+07:00",
						"X-SIGNATURE": "HeBvCm\/b734R92KlLhb+bJT\/AnQQ0tlXJPtdTwMvetlGbBJy\/ZVzjojVBkfzYYh1\/ix810f+FJq0OBR\/7KRL8a5c62urvAw6IkoSVh3ltENd0TgIdt70O1ii+9jpdI1bX16jTQCUveWz3du4qlvLme7Yl+uzOXHzoVXz93pyQhHSMmn8vyA4NgewwFJFkaSb1Q7Q9Mus72VPXfd63HFli7qLRNpE3N8zmI3lVyOf0sDV66GZvCf2ZKQCaYSz3G4Cb08vARBh56a83mwRnxgxezULIGyimEIEH5+tsyIdievI8Bg2YkzR01qu+EP2AiIH6CVlbKL3xpHdssFJp5Kv\/w==",
						"X-PARTNER-ID": "010430",
						"X-REQUEST-ID": "20240621154844"
				},
				"option": {
						"as_json": true
				}
			},
			"data": {
				"merchantId": "010430",
				"requestId": "20240621154844",
				"errCode": "0",
				"paymentType": "QRIS",
				"amount": "15000.00",
				"merchantTradeNo": "1718959724",
				"createTime": "20240621154847",
				"platformTradeNo": "2024062143000000032",
				"expiredTime": "20240622154847",
				"status": "01",
				"productName": "BILLING1",
				"productInfo": [
						{
							"id": "1718959724",
							"name": "BILLING1",
							"price": 15000,
							"type": "QRIS",
							"quantity": 1
						}
				],
				"transFeeRate": "0.000000",
				"transFeeAmount": "10000.00",
				"totalTransFee": "10000.00",
				"qrCode": "MOCK-QRIS:2024062143000000032",
				"qrisUrl": "https:\/\/sit-payer.paylabs.co.id\/payer-api\/qr?8736791888f5d6107abea94c42853b0aMOCK-QRIS%3A2024062143000000032"
			}
		}
	*/
	/* // Success EWALLET
		{
			"status": "000",
			"request": {
				"url": "https:\/\/sit-pay.paylabs.co.id\/payment\/v2.1\/ewallet\/create",
				"data": {
						"requestId": "20240621155035",
						"merchantId": "010430",
						"paymentType": "DANABALANCE",
						"amount": "15000.00",
						"merchantTradeNo": "1718959835",
						"notifyUrl": "http:\/\/tester.com\/secure\/callback\/demo",
						"paymentParams": {
							"redirectUrl": "http:\/\/tester.com\/secure\/callback\/demo",
							"phoneNumber": "081111111111"
						},
						"productName": "BILLING1",
						"productInfo": [
							{
								"id": "1718959835",
								"name": "BILLING1",
								"price": "15000.00",
								"type": "EWALLET",
								"quantity": 1
							}
						]
				},
				"headers": {
						"Content-Type": "application\/json;charset=utf-8",
						"X-TIMESTAMP": "2024-06-21T15:50:35.410+07:00",
						"X-SIGNATURE": "MonHliM59LUXJsdHcSJfMa5bQj8hOf5ByOgJ0Q21N3mXLr\/HyNS4WP45yudfJIZ4vOFcOBg36aL1dqrkcJIUixmZPIrd0Ze+0\/5lD\/RO+znSgplwfrAWU3NnTdoiDQ1c8caPtkr+59YCDSs\/AsnX9ffly2PGheJUvjitB\/x1pLVGT3EDkhWotFz7ysV7JVQ10Xyi0lCbHiSF1riQTiEMVUB1rkxB7ov\/JbkPamqa22kIfeg+8MD42GJJVWUDdjoCkhLjI3ENed17YXh+zDhAUkytlXkLkNvZuabd7zQ2VejTHO\/QeU4r+x2cTpxvzipH\/kfXPi2QrCh2aJi9IZkBpg==",
						"X-PARTNER-ID": "010430",
						"X-REQUEST-ID": "20240621155035"
				},
				"option": {
						"as_json": true
				}
			},
			"data": {
				"merchantId": "010430",
				"requestId": "20240621155035",
				"errCode": "0",
				"paymentType": "DANABALANCE",
				"amount": "15000.00",
				"merchantTradeNo": "1718959835",
				"createTime": "20240621155038",
				"platformTradeNo": "2024062143000000033",
				"expiredTime": "20240621155102",
				"status": "01",
				"productName": "BILLING1",
				"productInfo": [
						{
							"id": "1718959835",
							"name": "BILLING1",
							"price": 15000,
							"type": "EWALLET",
							"quantity": 1
						}
				],
				"redirectUrl": "http:\/\/tester.com\/secure\/callback\/demo",
				"transFeeRate": "0.001000",
				"transFeeAmount": "10000.00",
				"totalTransFee": "10015.00",
				"paymentActions": {
						"mobilePayUrl": "https:\/\/sit-payer.paylabs.co.id\/payer-api\/v1\/callback\/forward\/MDA5LTAwOQ==\/8f3edf2d126d97b0dc793331297246d06eecfedf13d8e24cd2f6165a13cf61ce"
				}
			}
		}
	*/
	/* // Success VA
		{
			"status": "000",
			"request": {
				"url": "https:\/\/sit-pay.paylabs.co.id\/payment\/v2.1\/va\/create",
				"data": {
						"requestId": "20240621155814",
						"merchantId": "010430",
						"paymentType": "MuamalatVA",
						"amount": "15000.00",
						"merchantTradeNo": "1718960294",
						"notifyUrl": "http:\/\/tester.com\/secure\/callback\/demo",
						"payer": "TESTER",
						"productName": "BILLING1",
						"productInfo": [
							{
								"id": "1718960294",
								"name": "BILLING1",
								"price": "15000.00",
								"type": "VA",
								"quantity": 1
							}
						]
				},
				"headers": {
						"Content-Type": "application\/json;charset=utf-8",
						"X-TIMESTAMP": "2024-06-21T15:58:14.415+07:00",
						"X-SIGNATURE": "QNK2b8kw+v3E0OaJ5aIsKZ4cuKqvrXByCtOii2+pXZqnDwOhGeHn2bQM8p0PtxWQMV4\/CjYIDPN5M3qhwJXgOlflGQrVvEQUdwWKcyDUAfe3GpiUsswZtP+yUdCwuJVqMDFbqjudG2g6lDQkDfgyob0qfAHHKDhZwQJtdTMC0Ay8eR5V0rz9\/jJERSGBwrn+dH40ZH2k649u6RoXUlRvh7aDVV79WDcKXxr20U3ZlxDcF2UXJ8TQGi25szRc59afJrDAmceCO91vO+oKVTFD2Z1knCFqNu2vMSICRc9B73BGWu3dPGhVXHFGnwBpdb8+cy1t+TcZS6IqNG844rxK\/Q==",
						"X-PARTNER-ID": "010430",
						"X-REQUEST-ID": "20240621155814"
				},
				"option": {
						"as_json": true
				}
			},
			"data": {
				"merchantId": "010430",
				"requestId": "20240621155814",
				"errCode": "0",
				"paymentType": "MuamalatVA",
				"amount": "15000.00",
				"merchantTradeNo": "1718960294",
				"createTime": "20240621155817",
				"platformTradeNo": "2024062143000000034",
				"expiredTime": "20240622155817",
				"status": "01",
				"productName": "BILLING1",
				"productInfo": [
						{
							"id": "1718960294",
							"name": "BILLING1",
							"price": 15000,
							"type": "VA",
							"quantity": 1
						}
				],
				"transFeeRate": "0.000000",
				"transFeeAmount": "10000.00",
				"totalTransFee": "10000.00",
				"vaCode": "9999900000000001"
			}
		}
	*/
} catch (\Throwable $e) {
	$vendor->ThrowErrorException($e);
}



// /**
//  *
//  * Inquiry trx
//  *
//  */
// try {
// 	$result = $vendor->Inquiry((object) [
// 		// 'payment_method' => 'QRIS',
// 		// 'reference_num' => '1718959724',
// 			// 'payment_method' => 'EWALLET',
// 			// 'payment_channel' => 'DANABALANCE',
// 			// 'reference_num' => '1718959835',
// 				'payment_method' => 'VA',
// 				'payment_channel' => 'MuamalatVA',
// 				'reference_num' => '1718960294',
// 	]);
// 	extract($result);
// 	print_r($response);
// 	/* // Success inquiry QRIS
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sit-pay.paylabs.co.id\/payment\/v2.1\/qris\/query",
// 				"data": {
// 						"requestId": "20240621160605",
// 						"merchantId": "010430",
// 						"merchantTradeNo": "1718959724",
// 						"paymentType": "QRIS"
// 				},
// 				"headers": {
// 						"Content-Type": "application\/json;charset=utf-8",
// 						"X-TIMESTAMP": "2024-06-21T16:06:05.420+07:00",
// 						"X-SIGNATURE": "WL2vtius3WF7kjuZeyV5rh67UbG6QwbW48D5IN0GhXQxQSXdiQ7JNpxPtnYVpvsKm\/\/0\/q+cumwG9aAbzGvB+E6sgmC8Il2bgK4HRq\/yhtDy9nUCZJNVYdWlMhFiqNfj+wYJtuZ6D43KwZRZNVbtXmITI9r8l1+ApmG3SXA4WHB1uK+JIjUjpZYcWnJE76KWMvUaj3SMRAA8KhWjstonkjsH4TkrBSSxrO5SeZwRXwFQao9\/tJmAOgwzh2mmuf8djsBh70PNlWylko8wzCu8\/NDjejRxUambPv3J6MKq4wClksCb2BaMODzH7cM6FlSyWRy8Ni2OZB1tqWQ80W7uMg==",
// 						"X-PARTNER-ID": "010430",
// 						"X-REQUEST-ID": "20240621160605"
// 				},
// 				"option": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"merchantId": "010430",
// 				"requestId": "20240621160605",
// 				"errCode": "0",
// 				"paymentType": "QRIS",
// 				"amount": "15000.00",
// 				"merchantTradeNo": "1718959724",
// 				"createTime": "20240621154847",
// 				"platformTradeNo": "2024062143000000032",
// 				"successTime": "20240621154948",
// 				"expiredTime": "20240622154847",
// 				"status": "02",
// 				"productName": "BILLING1",
// 				"productInfo": [
// 						{
// 							"id": "1718959724",
// 							"name": "BILLING1",
// 							"price": 15000,
// 							"type": "QRIS",
// 							"quantity": 1
// 						}
// 				],
// 				"transFeeRate": "0.000000",
// 				"transFeeAmount": "10000.00",
// 				"totalTransFee": "10000.00",
// 				"qrCode": "MOCK-QRIS:2024062143000000032",
// 				"qrisUrl": "https:\/\/sit-payer.paylabs.co.id\/payer-api\/qr?8736791888f5d6107abea94c42853b0aMOCK-QRIS%3A2024062143000000032"
// 			}
// 		}
// 	*/
// 	/* // Success inquiry EWALLET
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sit-pay.paylabs.co.id\/payment\/v2.1\/ewallet\/query",
// 				"data": {
// 						"requestId": "20240621160842",
// 						"merchantId": "010430",
// 						"merchantTradeNo": "1718959835",
// 						"paymentType": "DANABALANCE"
// 				},
// 				"headers": {
// 						"Content-Type": "application\/json;charset=utf-8",
// 						"X-TIMESTAMP": "2024-06-21T16:08:42.422+07:00",
// 						"X-SIGNATURE": "ERndhnn38YrZ4z7y1DL4VDC8T0sdS6OQ3Vx67lo8NXB7njg6dcJjQKv6K9v8tpn8MXIeN7Ui2G8e0PxkKiBDgXHueEdpC1sYGojl6Q7uIk4x4d6VMrNj\/Ximpb3ThTbI9en4MJH3xHxx+dWEtFl+HhgvL2y6CB7AltldEy25uTxjT+KRCZocAAPvlvCoUJsPkzPvvOTJhUD++s0suH26hIqvvGnvrOztBn5i89c8h9DDQvrhsRQajldU4AExq6KMesPP1LarjG407xfgWmk9Ykv2Q7fdnreGNkHai\/x2bxHHi1nsLGUuFZ28HFFwMnlEJO90eiL9weg81CZs9s3UFg==",
// 						"X-PARTNER-ID": "010430",
// 						"X-REQUEST-ID": "20240621160842"
// 				},
// 				"option": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"merchantId": "010430",
// 				"requestId": "20240621160842",
// 				"errCode": "0",
// 				"paymentType": "DANABALANCE",
// 				"amount": "15000.00",
// 				"merchantTradeNo": "1718959835",
// 				"createTime": "20240621155038",
// 				"platformTradeNo": "2024062143000000033",
// 				"successTime": "20240621155140",
// 				"expiredTime": "20240621155102",
// 				"status": "02",
// 				"productName": "BILLING1",
// 				"productInfo": [
// 						{
// 							"id": "1718959835",
// 							"name": "BILLING1",
// 							"price": 15000,
// 							"type": "EWALLET",
// 							"quantity": 1
// 						}
// 				],
// 				"redirectUrl": "http:\/\/sb.tf2us.com\/secure\/callback\/demo",
// 				"transFeeRate": "0.001000",
// 				"transFeeAmount": "10000.00",
// 				"totalTransFee": "10015.00",
// 				"paymentActions": {
// 						"mobilePayUrl": "https:\/\/sit-payer.paylabs.co.id\/payer-api\/v1\/callback\/forward\/MDA5LTAwOQ==\/8f3edf2d126d97b0dc793331297246d06eecfedf13d8e24cd2f6165a13cf61ce"
// 				}
// 			}
// 		}
// 	*/
// 	/* // Success inquiry VA
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sit-pay.paylabs.co.id\/payment\/v2.1\/va\/query",
// 				"data": {
// 						"requestId": "20240621161023",
// 						"merchantId": "010430",
// 						"merchantTradeNo": "1718960294",
// 						"paymentType": "MuamalatVA"
// 				},
// 				"headers": {
// 						"Content-Type": "application\/json;charset=utf-8",
// 						"X-TIMESTAMP": "2024-06-21T16:10:23.423+07:00",
// 						"X-SIGNATURE": "UN\/cngRCfqf\/HwaSnRw\/cFYyCwfL3Y5YN47cxeWnLXMrY2+J15Y+HlEceedafjYFrl8LIacBNdOI72CUiPs6ACmla4kXChCf2TbU\/vnTUy8VUQ7uijgcXWyloGWkDuQB3+IVtmGJMArw+KgukHAZEJSTJ2sbNTK419jIyXK1s\/iEGZwRqiypU\/OkURUnC3yAoGH\/WadIvG7fTmOugJnA+o1vdbcX6stTn0lR+IVl7GHpI8aCh\/d9yu1WmxQrRv09C\/Fms6fOMnBfHXIGA935OAPh\/CGSX3x19VTWeBT5UzcC+ORnjfNef83AiHBF3k492kk4HcFhij6SWbBz8DC6vQ==",
// 						"X-PARTNER-ID": "010430",
// 						"X-REQUEST-ID": "20240621161023"
// 				},
// 				"option": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"merchantId": "010430",
// 				"requestId": "20240621161023",
// 				"errCode": "0",
// 				"paymentType": "MuamalatVA",
// 				"amount": "15000.00",
// 				"merchantTradeNo": "1718960294",
// 				"createTime": "20240621155817",
// 				"platformTradeNo": "2024062143000000034",
// 				"successTime": "20240621155919",
// 				"expiredTime": "20240622155817",
// 				"status": "02",
// 				"productName": "BILLING1",
// 				"productInfo": [
// 						{
// 							"id": "1718960294",
// 							"name": "BILLING1",
// 							"price": 15000,
// 							"type": "VA",
// 							"quantity": 1
// 						}
// 				],
// 				"transFeeRate": "0.000000",
// 				"transFeeAmount": "10000.00",
// 				"totalTransFee": "10000.00",
// 				"vaCode": "9999900000000001"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }
