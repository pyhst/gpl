<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init($_ENV['NUTECH_MERCHANT_ID'], $_ENV['NUTECH_PRIVATE_KEY']);
$init->setMerchantKey($_ENV['NUTECH_ALGO_SALT']);
$init->setPaymentURL($_ENV['NUTECH_HOST_URL']);
$init->setRequestURL($_ENV['NUTECH_HOST_URL']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$init->setReturnURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \Growinc\Payment\Vendors\Nutech($init);



// /**
//  *
//  * Create billing
//  *
//  */
// try {
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setAmount(10000);
// 	$transaction->setCustomerName('Testing');
// 	$transaction->setCustomerPhone('081212121314');
// 	$transaction->setOrderID(time());
// 	//
// 	$transaction->setPaymentMethod('va');
// 	$transaction->setPaymentChannel('PERMATA');
// 	// $transaction->setPaymentMethod('qris');
// 	// $transaction->setPaymentChannel('QR_CODE');
// 	//
// 	$result = $vendor->SecurePayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success QRIS
// 		{
// 			"status": 0,
// 			"message": "SUCCESS",
// 			"data": {
// 				"merTransactionId": "1692610365",
// 				"code": 0,
// 				"msg": "SUCCESS",
// 				"va": null,
// 				"paymentUrl": null,
// 				"qrisImage": "data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAA6AAAAOgAQAAAAALviYcAAALfElEQVR42u2dTW4jTQ5EKXjhZR1BR9HRpKPpKDqCl14YnSNVkkGm3FPSAB9mKgdPi4ahn3zVGyJJBoPW\/gcvAwoUKFCgQIECBQoUKFCgQIECBQoUKFCgQIECBQoUKFCgQIECBQoUKNB\/Hnqx\/mqXU\/vqf9z\/+Xx8yeo79vHn\/qvr8euz\/Xm8c2zfj4\/a9fHR\/cvndrv\/dbgfeLot3\/cv99cHUKBAZ4WeHv\/2c77Wk1f6es7V37mfvKw\/s8d3Hiev7\/yYn\/OzvnOMI2+2Ptjj9Q0UKNB5obcebR7Qz\/s5zloj0s0iIi3xhPfvHPrzrF82f9T+Zf9j8f+FLUCBAp0feo8ki8cWWy8e64GPk\/PnK9QPXK80\/d7i15Xlew1WLb8DFCjQ\/wto+4rQ1DwZOX71jOR+pXkceA9Epif0ZMWO5drTLzD\/UUQCChTobqHKZSwqFf5Of4whEJ0yPdFHPZe55C3FiyHvJVBAgQLdKzSroL16+U\/98WbpFShQoDuF1tA0RpK1ZNFTmEehQ2lO\/8ijzXjbWa80j5vMx5tNW6BAge4YqipovE7lw2+rr\/PaK\/FGqnKZLHR01m3tnvyiAwUKdCpodD3W2LJqKkIm8eiDrHmK1ze8afIZbdO4kywBjY9uVYkBFCjQSaHNg4xqnp3+o4rHemBNfEJ30ZutLq4QPcUVtSoCFCjQ+aDeNvWLxx\/rgSiSml7hfBwoEeZaKZWmwhWbRW5x6o1UPwcoUKDzQq0Is7tewiseV1tji\/IUDzvrO1HWuEaz1VnqsEjBBRQo0CmhIYHwmuf6ZYUdh0pp2aJ2EQ+W0HPrAoxeF42o9dmAAgU6KTQGuFx97WHHbynHElvMqjrrJISruNU0ade8ySz\/NoECChTo3qF9qFNV0LicmM75E7JM73r4d\/wgsyLUjFzGfCJsIwwCBQp091BpKq49T5FMoo5vHB3q50i83TuqP11TsUSIW8Xbqc0AChTolNAc8\/SKR2shsLT+83I5yRpIlkx\/i7fNx0X9ZKBAgU4KjfluzYBLXmWmJum37iT+hFEeUc1TAc2VV6+E4kCBAt01VP3T1FVeTQ0RkyhLThHdmcqOOQw++kusD9\/qRBhQoEBnhIZwwie+VeiwQETT5ENyC+m6A6pcJj0oLMuqQIECnRLamgeQUbNdTSQkw+7d0ngnvKpCuum2EmeNkG\/0T4ECBbp\/qJcsqtY6hJreInHbOldVRQrzlbZ1Kd0skgw5UwEFCnROaFPRMuLP9VjmxM2ew9ePBsGseOH2j+og2PkKFCjQiaHRJM0xz4v+MA2CFX2mizD9UYcqaM9lWqQ5r5u2QIEC3S+0eti2oo44pKoqKx4mlYWfXGbEYjSsuavt5hQJUKBAdw+thpOa2xrtIHxYw2pSY6W1qqg1znpsbtIBChTo3qF93tNrFxr0Hozxy2xXDHREj7X4WvucuJKjFw4zQIECnQBqVgYxckZDUu215pmTHaUq0nMZlUxzFHRbsw0UKNAZoF6rvKT9VNN9Q2FHjlK+W+eUTdI6J27pOOHnAAUKdFqoxfhG7NzMjmrdrePTnVqpk+73bWitaq3nC0M8oECB7hs67ter9rYpuh5iVOisloxjH3X6o7jov7SfAgoU6M6hkXqUGOW6C6mve+1CRnYnWeV7jIpeia495UCgQIHOCa1V0EFgeWotLjBfUQPpWqyLHyh5Vfrhq9CxpbwCChToDFA\/R173rQx5lbJGaC\/b4HSt\/ukpvelqUgMUKNB5oa7Z1r6t6Hqcy6xHaXa4QUTOksfJar8WmRZQoEBnhbarmqRamXerXjGxtjv12MV6oms4W1jlV5\/bp0cFChTobFDNidsxzOXq3FZfyPs8o6FcpoQdjXjcvNXS2uvhdKBAge4Y2lod665++Br9VoXT34mo9dcdfH6lOW9GJKBAge4ceqk9jqZ5Tyvq61Rsnn\/PiJ1uNsyJa+2OizmBAgU6M\/RDV5Fwuo4RcjO1TQ+x2vsQDjPRNrXBm+7JrxIoUKCzQm2JhKX0TwfJpYoh9XISI11Vi2XSZ3bP\/M3Ff0CBAt01tGkQQ1prO5ZoE+0P360zhJ3aTwnFpp4wR0WAAgU6JTQ2Z8XabldfH9pvjykZ1VaE2bM33Vv9U6BAge4cGhMZfhWxTGrSku7s8Ud21ul1X247gwAjMiCgQIHOC5U3XR8FLY610mwPzreqiuQl58kPP9RZWwkUUKBAdw+V07XljoxPVTM0teHfWbRtZ3gwL31ECvNyoAMoUKB7h0b7Q24Sz93ScVevolbNd\/QdLef60d4uoECBzgrVl1WXKEt7cwOXDXnKRR62KaW4PXlVvSUUBwoU6J6hXX19Kmu721O0MfncxjbPCE1NHlM3jZBrPd+WIR5QoED3DS1elG6MH8sySvfE7afSPUYl03ie9Jeolteba7aAAgW6b6hXM+o1Y5BXWXpHZP\/07C763XzG3bDjCdVhAQoU6MTQJmd7FT+Lr7XSHDlYSnKpssaiVEiTpG\/44QMFCnTv0JBAyMHynPcNy6nw7\/CrTKV3LtXSnLgVH6oNV1ugQIFOAL2mG20bpFNpP3XJ3Z25zbOY3o+eloO9LVCgQGeF1tjiP1f8yVtKWkb8XrLj5ZH27Dp1se0RM6BAge4aWnbwaVlGbuGs6\/nC+\/rDCx1pev8TSoyf8szV7A4oUKDzQYdNed1+6iR722DFz85ynQqH\/HOdEx\/oPvYFFCjQSaE5J\/68VEs7MrzQ8TQTGpKMmsvIBt8bqcvL4XSgQIHuFuq1Cw1r9BxEy\/hi+WbTTGg+Yaz1bBoDKd50L7aCAgUKdOdQjWud47M17PjFI8Y8Y05cSsu0wT9mCtPnv0S3I1CgQOeFaitWmND6xLfyFA1r\/MkRchlXqiFSh0Nj18a2UBwoUKD7hqYMWyULXWCkxjzd0hhfgWiwuyxbv1fWn7eG04ECBbpz6EdNaqqKe2k5CqqIdPj1K3sWar4TkYACBbpraDZJrcx7DtsuOkt9kIg28fOliLcP5QnfatoCBQp0t1B3isgl3X+5t\/xa4lkWgvsYyDmVV2\/1T4ECBbp36FU\/13SnQtQxy6GaCY3dOkvL8Y1Yz\/fh42Phfb3RPwUKFOjOoSpixIFFcmnFIGIoh\/Yv1zW+rtl+knc2oECBzg2N1OPTG6Ax8R3LN6+W6\/kkwZLrVHhVLbl2J38FFCjQaaFKPSS59EC0Nju0jlM91joIJuvap10bFj9vQIECnReaAsvv2vUYa57xUfjXheV1\/BFXmsGiaiMiAQUKdO9QF092f1otubDqYZt0t6NxC9wyLuqDIYdgvdzBBxQo0P1Di1eMFNr2tGYrDK5TmN2KJd1gWtV6XXTLmw4oUKATQE9FV6lKRfjTVqeIMJHIMc\/iMTUMgrWy\/hsoUKCzQn2j96ipiNjyFQlLTnKpf\/odccxHQXNhX\/hjmwEFCnRaaPNSp6ylpLPyPw7Z9fDnCSWG79taMv4MO\/jW5AgoUKCzQotBhGQSuWHz60llkb5zrXzUitVMNdi3DW86oECB7hw60MP0fix9PO3vjstJirf\/Mifetl1tgQIFunvoxYYeR1wz3PTe9djpHqPFWzK3rPlOulK8sJ8CChTo\/qF+XSlj3Wvb9OAqi9jWHdOdpUVSPyq3lJwIOw4hDyhQoJNBI7ZEaMo1vq2VrMQOqeEs3ZOY9dDk+Ds7+IACBToTVHNb4VaXGqp8sKM2cIU3nXRWtgypUB36AAoU6MzQlEnUhbxaqpWm9\/EYTzGqlYV9w74toECBzgktuUxkJdk0iXnPyGVkR+Pi7XWovE+O33zEo8Xg+fXYgAIFOi90qIIOhc1uB+E2MmFbp5ao5FVLLOeSy2V0VG8bci+gQIHuHPrfewEFChQoUKBAgQIFChQoUKBAgQIFChQoUKBAgQIFChQoUKBAgQIFChTo1NB\/AWKErDxkePefAAAAAElFTkSuQmCC",
// 				"sign": "wpycvM9s13dWko1hVL1v\/G0ReLIrkssIPrJNrWW3BGXZADfXvqdK5g=="
// 			}
// 		}
// 	*/
// 	/* // Success PERMATA
// 		{
// 			"status": 0,
// 			"message": "SUCCESS",
// 			"data": {
// 				"merTransactionId": "1692610424",
// 				"code": 0,
// 				"msg": "SUCCESS",
// 				"va": "729361816181",
// 				"paymentUrl": null,
// 				"qrisImage": null,
// 				"sign": "FgsRWvdw12mSV+kSXFds6pCHjFf3sKDR6ugN4Ax6s8kEPVR+dkWMcQ=="
// 			}
// 		}
// 	 */
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
// 		// 'transactionId' => '1692610424',
// 		'merTransactionId' => '1692610424',
// 	]);
// 	extract($result);
// 	print_r($response);
// 	/* // Success PERMATA
// 		{
// 			"status": 0,
// 			"message": "SUCCESS",
// 			"data": {
// 				"merTransactionId": "1692610424",
// 				"amount": 10000,
// 				"bankCode": "PERMATA",
// 				"va": "729361816181",
// 				"mobile": "081212121314",
// 				"name": "Testing",
// 				"status": 0,
// 				"createTime": "2023-08-21T09:33:48.000+00:00",
// 				"updateTime": "2023-08-21T09:33:47.000+00:00",
// 				"code": 0,
// 				"msg": "SUCCESS",
// 				"sign": "YGH7noxxhPAgUh6DVyGJjxOtWZniLuCLFw735b0xJN6RygCEIcBhGQ=="
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }
