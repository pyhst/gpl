<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['DANA_MERCHANT_ID']
);
$init->setRequestURL($_ENV['DANA_HOST_URL']);
$init->setParams([
	'client_id' => $_ENV['DANA_CLIENT_ID'],
	'client_secret' => $_ENV['DANA_CLIENT_SECRET'],
	'private_key' => $_ENV['DANA_PRIVATE_KEY'],
	'merchant_id' => $_ENV['DANA_MERCHANT_ID'],
]);
$vendor = new \PyGPaymentLib\Vendors\Dana($init);



/**
 *
 * Get Token
 *
 */
try {
	$result = $vendor->GetToken([]);
	extract($result);
	print_r($response);
	/* // Success token
		{
			"responseCode": "2007300",
			"responseMessage": "Successful",
			"accessToken": "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3MTUzMjc0NzIsImlzcyI6ImlkLmRhbmEiLCJleHAiOjE3MTUzMjgzNzJ9.U8czL5VsyAHtcGicLakVZTT-g-690YiAXhpdV81IQjq_GKbDF0vwqeZgZMIX8zwh09ptyhD39187f2NWHpiRsYeNujD203Adj4IBNz6_0oiMtqjU9Stqs4VUp8tWpZ0ojL2pES-ou3dnlCxH9TcWPWqwCrAb7GVzOfbKHGqDZyAOdumCpu8fmRw2YYgKFst1XWJPp2otLbAy3OCTuGozo5fb-NWVx1G6_1ZHy4L08uopCVU1pKzWcnoAo9P7lZiq48cBT4rUg6gf_w11agEGI3V6GAE8xHJyrM9CpAxO8796Rsw8EJ0NNsmnaNy8yOI4aNQ-KgA8AZ0p48WLxS8JZg",
			"tokenType": "Bearer",
			"expiresIn": "900"
		}
	*/
	$content = (object) json_decode($result['response']['content']);
	$init->setParam('token', $content->accessToken);
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}



// /**
//  *
//  * Check Balance
//  *
//  */
// try {
// 	$result = $vendor->CheckBalance();
// 	extract($result);
// 	print_r($response);
// 	/* // Success get balance
// 		{
// 			"status": "000",
// 			"data": {
// 				"response": {
// 						"head": {
// 							"function": "dana.merchant.queryMerchantResource",
// 							"clientId": "2023011299157589316709",
// 							"version": "2.0",
// 							"respTime": "2024-05-10T14:50:26+07:00",
// 							"reqMsgId": "TIME1715327426"
// 						},
// 						"body": {
// 							"resultInfo": {
// 								"resultCodeId": "00000000",
// 								"resultCode": "SUCCESS",
// 								"resultMsg": "SUCCESS",
// 								"resultStatus": "S"
// 							},
// 							"merchantResourceInformations": [
// 								{
// 										"resourceType": "MERCHANT_DEPOSIT_BALANCE",
// 										"value": "{\"amount\":\"190308000\",\"currency\":\"IDR\"}"
// 								},
// 								{
// 										"resourceType": "MERCHANT_AVAILABLE_BALANCE",
// 										"value": "{\"amount\":\"0\",\"currency\":\"IDR\"}"
// 								},
// 								{
// 										"resourceType": "MERCHANT_TOTAL_BALANCE",
// 										"value": "{\"amount\":\"0\",\"currency\":\"IDR\"}"
// 								}
// 							]
// 						}
// 				},
// 				"signature": "rRB\/ZPO3qZ+w8ojt9bSjY7Lj1fs0Rp07i9jP\/aflvDV8vHrxdAKyFnM1Atj+5nb2gJXFoQ7OsU7iVclO8yMnx10i+XUJXf79LHevbIrtMzLTc0xOJW9sSPVokd5SGgQtg3VRe43Kweg7uB6bAZfyOJeIgNt6bkBDnL26FN+SvUgnj80avB9s4vNzwZ8rfszmyp8tM4Wi6+1D2eTvUBPlPUUIlEv4gdYbrGwGPaWYzGVcc2ecrAsxbBh3LHeQW\/epaSkPH+4QAWZUMPCsq9m8So6B+IlWT3qLKLW0zA87jVzpYTFM2q2wU9ijGj1iQu+6GP3ao9J4xrgpn3VqOoFimA=="
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }



// /**
//  *
//  * Get Bank List // Not applicable
//  *
//  */
// try {
// 	$result = $vendor->BankList();
// 	extract($result);
// 	print_r($response);
// 	/*
// 	{"status":"000","data":[{"name":"PT BANK CENTRAL ASIA TBK.","code":"014","limit":25000000},{"name":"PT BANK RAKYAT INDONESIA (PERSERO), TBK.","code":"002","limit":25000000},{"name":"PT BANK TABUNGAN NEGARA (PERSERO)","code":"200","limit":0},{"name":"PT. BANK MANDIRI (PERSERO), TBK.","code":"008","limit":25000000},{"name":"PT BANK ANTARDAERAH","code":"088","limit":0},{"name":"PT BANK ARTHA GRAHA INTERNASIONAL, TBK.","code":"037","limit":0},{"name":"PT BANK BNI","code":"009","limit":0},{"name":"PT BANK BUKOPIN, TBK","code":"441","limit":0},{"name":"PT BANK BUMI ARTA, TBK","code":"076","limit":0},{"name":"PT BANK CIMB NIAGA, TBK","code":"022","limit":0},{"name":"PT BANK DANAMON INDONESIA TBK","code":"011","limit":0},{"name":"PT BANK EKONOMI RAHARJA, TBK","code":"087","limit":0},{"name":"PT BANK GANESHA","code":"161","limit":0},{"name":"PT BANK HANA","code":"484","limit":0},{"name":"PT BANK HIMPUNAN SAUDARA 1906, TBK","code":"212","limit":0},{"name":"PT BANK ICB BUMIPUTERA TBK","code":"485","limit":0},{"name":"PT BANK ICBC INDONESIA","code":"164","limit":0},{"name":"PT BANK INDEX SELINDO","code":"555","limit":0},{"name":"PT BANK INTERNASIONAL INDONESIA TBK","code":"016","limit":0},{"name":"PT BANK MASPION INDONESIA","code":"157","limit":0},{"name":"PT BANK MAYAPADA INTERNATIONAL TBK","code":"097","limit":0},{"name":"PT BANK MEGA SYARIAH","code":"506","limit":0},{"name":"PT BANK MEGA, TBK","code":"426","limit":0},{"name":"PT BANK MESTIKA DHARMA","code":"151","limit":0},{"name":"PT BANK METRO EXPRESS","code":"152","limit":0},{"name":"PT BANK MUAMALAT INDONESIA","code":"147","limit":0},{"name":"PT BANK MUTIARA, TBK","code":"095","limit":0},{"name":"PT BANK NUSANTARA PARAHYANGAN,TBK","code":"145","limit":0},{"name":"PT BANK OCBC NISP, TBK","code":"028","limit":0},{"name":"PT BANK OF INDIA INDONESIA, TBK","code":"146","limit":0},{"name":"PT BANK PERMATA TBK","code":"013","limit":25000000},{"name":"PT BANK RAKYAT INDONESIA AGRONIAGA, TBK.","code":"494","limit":0},{"name":"PT BANK SBI INDONESIA","code":"498","limit":0},{"name":"PT BANK SINARMAS, TBK","code":"153","limit":0},{"name":"PT BANK SYARIAH MANDIRI","code":"451","limit":0},{"name":"PT BANK UOB INDONESIA (DAHULU UOB BUANA)","code":"023","limit":0},{"name":"PT PAN INDONESIA BANK, TBK","code":"019","limit":0},{"name":"PT QNB BANK KESAWAN TBK","code":"167","limit":0},{"name":"PT ANGLOMAS INTERNASIONAL BANK","code":"531","limit":0},{"name":"PT BANK ANDARA","code":"466","limit":0},{"name":"PT BANK ARTOS INDONESIA","code":"542","limit":0},{"name":"PT BANK BCA SYARIAH","code":"536","limit":25000000},{"name":"PT BANK BISNIS INTERNASIONAL","code":"567","limit":0},{"name":"PT BANK BRISYARIAH","code":"422","limit":0},{"name":"PT BANK DINAR INDONESIA","code":"526","limit":0},{"name":"PT BANK FAMA INTERNASIONAL","code":"562","limit":0},{"name":"PT BANK HARDA INTERNASIONAL","code":"567","limit":0},{"name":"PT BANK INA PERDANA","code":"513","limit":25000000},{"name":"PT BANK JABAR BANTEN SYARIAH","code":"425","limit":0},{"name":"PT BANK JASA JAKARTA","code":"472","limit":0},{"name":"PT BANK KESEJAHTERAAN EKONOMI","code":"535","limit":0},{"name":"PT BANK MAYORA","code":"553","limit":0},{"name":"PT BANK MITRANIAGA","code":"491","limit":0},{"name":"PT BANK MULTIARTA SENTOSA","code":"548","limit":0},{"name":"PT BANK NATIONALNOBU","code":"503","limit":0},{"name":"PT BANK PANIN SYARIAH","code":"517","limit":0},{"name":"PT BANK PUNDI INDONESIA, TBK","code":"558","limit":0},{"name":"PT BANK ROYAL INDONESIA","code":"501","limit":0},{"name":"PT BANK SAHABAT PURBA DANARTA","code":"547","limit":0},{"name":"PT BANK SAHABAT SAMPOERNA","code":"523","limit":0},{"name":"PT BANK SINAR HARAPAN BALI","code":"564","limit":0},{"name":"PT BANK SYARIAH BUKOPIN","code":"774","limit":0},{"name":"PT BANK TABUNGAN PENSIUNAN NASIONAL, TBK","code":"213","limit":0},{"name":"PT BANK VICTORIA INTERNATIONAL, TBK","code":"566","limit":0},{"name":"PT BANK VICTORIA SYARIAH","code":"405","limit":0},{"name":"PT BANK YUDHA BHAKTI","code":"490","limit":0},{"name":"PT CENTRATAMA NASIONAL BANK","code":"559","limit":0},{"name":"PT PRIMA MASTER BANK","code":"520","limit":0},{"name":"BPD SULAWESI TENGGARA","code":"135","limit":0},{"name":"BPD YOGYAKARTA","code":"112","limit":0},{"name":"BPD KALIMANTAN TIMUR","code":"124","limit":0},{"name":"PT BANK DKI","code":"111","limit":0},{"name":"PT BANK ACEH","code":"116","limit":0},{"name":"PT BANK KALIMANTAN TENGAH","code":"125","limit":0},{"name":"PT BPD JAMBI","code":"115","limit":0},{"name":"PT BPD SULAWESI SELATAN DAN SULAWESI BARAT","code":"126","limit":0},{"name":"PT BPD LAMPUNG","code":"121","limit":0},{"name":"PT BPD RIAU KEPRI","code":"119","limit":0},{"name":"PT BPD SUMATERA BARAT","code":"118","limit":0},{"name":"PT. BPD JAWA BARAT DAN BANTEN, TBK","code":"110","limit":0},{"name":"PT. BPD MALUKU","code":"131","limit":0},{"name":"PT. BPD BENGKULU","code":"133","limit":0},{"name":"PT. BPD JAWA TENGAH","code":"113","limit":0},{"name":"PT. BPD JAWA TIMUR","code":"114","limit":0},{"name":"PT. BPD KALIMANTAN BARAT","code":"123","limit":0},{"name":"PT. BPD NUSA TENGGARA BARAT","code":"128","limit":0},{"name":"PT. BPD NUSA TENGGARA TIMUR","code":"130","limit":0},{"name":"PT. BPD SULAWESI TENGAH","code":"134","limit":0},{"name":"PT. BPD SULAWESI UTARA","code":"127","limit":0},{"name":"PT. BPD BALI","code":"129","limit":0},{"name":"PT. BPD KALIMANTAN SELATAN","code":"122","limit":0},{"name":"PT. BPD PAPUA","code":"132","limit":0},{"name":"PT. BPD SUMATERA SELATAN DAN BANGKA BELITUNG","code":"120","limit":0},{"name":"PT. BPD SUMATERA UTARA","code":"117","limit":0},{"name":"PT BANK COMMONWEALTH","code":"950","limit":0},{"name":"PT BANK AGRIS","code":"945","limit":0},{"name":"PT BANK ANZ INDONESIA","code":"061","limit":0},{"name":"PT BANK BNP PARIBAS INDONESIA","code":"057","limit":0},{"name":"PT BANK CAPITAL INDONESIA, TBK","code":"054","limit":0},{"name":"PT BANK DBS INDONESIA","code":"046","limit":0},{"name":"PT BANK KEB INDONESIA","code":"484","limit":0},{"name":"PT BANK MAYBANK SYARIAH INDONESIA","code":"777","limit":0},{"name":"PT BANK MIZUHO INDONESIA","code":"048","limit":0},{"name":"PT BANK RABOBANK INTERNATIONAL INDONESIA","code":"089","limit":0},{"name":"PT BANK RESONA PERDANIA","code":"047","limit":0},{"name":"PT BANK WINDU KENTJANA INTERNATIONAL, TBK","code":"036","limit":0},{"name":"PT BANK WOORI INDONESIA","code":"212","limit":0},{"name":"PT. BANK CHINATRUST INDONESIA","code":"949","limit":0},{"name":"PT. BANK SUMITOMO MITSUI INDONESIA","code":"045","limit":0},{"name":"BANK OF AMERICA, N.A","code":"033","limit":0},{"name":"BANK OF CHINA LIMITED","code":"069","limit":0},{"name":"CITIBANK N.A.","code":"031","limit":0},{"name":"DEUTSCHE BANK AG.","code":"067","limit":0},{"name":"JP. MORGAN CHASE BANK, N.A.","code":"032","limit":0},{"name":"STANDARD CHARTERED BANK","code":"050","limit":0},{"name":"THE BANGKOK BANK COMP. LTD","code":"040","limit":0},{"name":"THE BANK OF TOKYO MITSUBISHI UFJ LTD","code":"042","limit":0},{"name":"THE HONGKONG & SHANGHAI BANKING CORP","code":"041","limit":0},{"name":"THE ROYAL BANK OF SCOTLAND N.V.","code":"052","limit":0},{"name":"LINK AJA","code":"911","limit":55000000}]}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }



// /**
//  *
//  * Bank Transfer - Inquiry Bank Account
//  *
//  */
// try {
// 	$transaction = new \PyGPaymentLib\Transaction();
// 	$transaction->setCustomerBankCode('009');
// 	$transaction->setCustomerBankAccountNumber('5787811122');
// 	$transaction->setAmount(10000);
// 	$transaction->setOrderID('1715327472');
// 	// $transaction->setCurrency('USD');
// 	//
// 	$result = $vendor->BankTransferInquiry($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success inquiry
// 		{
// 			"status": "000",
// 			"data": {
// 				"responseCode": "2004200",
// 				"responseMessage": "Success",
// 				"partnerReferenceNo": "1715327472",
// 				"beneficiaryAccountNumber": "******1122",
// 				"beneficiaryAccountName": "John Walker",
// 				"beneficiaryBankCode": "009",
// 				"beneficiaryBankShortName": "BNI",
// 				"beneficiaryBankName": "BNI BANK NEGARA INDONESIA",
// 				"amount": {
// 						"value": "10000.00",
// 						"currency": "IDR"
// 				},
// 				"additionalInfo": {
// 						"feeAmount": {
// 							"value": "0.00",
// 							"currency": "IDR"
// 						},
// 						"maxAmount": {
// 							"value": "250000000.00",
// 							"currency": "IDR"
// 						}
// 				}
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }



// /**
//  *
//  * Bank Transfer Exec
//  *
//  */
// try {
// 	$time = time();
// 	$transaction = new \PyGPaymentLib\Transaction();
// 	$transaction->setCustomerBankCode('009');
// 	$transaction->setCustomerBankAccountNumber('5787811122');
// 	$transaction->setAmount(10000);
// 	$transaction->setOrderID($time);
// 	$transaction->setCurrency('IDR');
// 	//
// 	$result = $vendor->BankTransferExec($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success transfer
// 		{
// 			"status": "000",
// 			"data": {
// 				"responseCode": "2024300",
// 				"responseMessage": "Request In Progress",
// 				"referenceNo": "2024051010121482010100166272700384221",
// 				"referenceNumber": "2024051010121482010100166272700384221",
// 				"transactionDate": "2024-05-10T14:51:12+07:00",
// 				"partnerReferenceNo": "1715327472",
// 				"additionalInfo": {
// 						"resultMsg": "PAYMENT_IN_PROCESS"
// 				}
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }



/**
 *
 * Bank Transfer Inquiry Transfer Status
 *
 */
try {
	$transaction = new \PyGPaymentLib\Transaction();
	$transaction->setCustomerBankCode('009');
	$transaction->setCustomerBankAccountNumber('5787811122');
	$transaction->setAmount(10000);
	$transaction->setReferenceNumber('2024051010121482010100166272700384221');
	$transaction->setOrderID('2024051010121482010100166272700384221');
	$transaction->setTransactionRemark('03');
	//
	$result = $vendor->BankTransferInquiryStatus($transaction);
	extract($result);
	print_r($response);
	/* // Success inquiry
		{
			"status": "000",
			"data": {
				"responseCode": "2004200",
				"responseMessage": "Success",
				"partnerReferenceNo": "1715327472",
				"beneficiaryAccountNumber": "******1122",
				"beneficiaryAccountName": "John Walker",
				"beneficiaryBankCode": "009",
				"beneficiaryBankShortName": "BNI",
				"beneficiaryBankName": "BNI BANK NEGARA INDONESIA",
				"amount": {
						"value": "10000.00",
						"currency": "IDR"
				},
				"additionalInfo": {
						"feeAmount": {
							"value": "0.00",
							"currency": "IDR"
						},
						"maxAmount": {
							"value": "250000000.00",
							"currency": "IDR"
						}
				}
			}
		}
	*/
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}



// /**
//  *
//  * DANA E-money Transfer Inquiry
//  *
//  */
// try {
// 	$transaction = new \PyGPaymentLib\Transaction();
// 	$transaction->setCustomerPhone('628127416027');
// 	$transaction->setAmount(15000);
// 	$transaction->setOrderID(time());
// 	//
// 	$result = $vendor->EmoneyTransferInquiry($transaction);
// 	extract($result);
// 	print_r($response);
// 	/*
// {"status":"000","data":{"responseCode":"2003700","responseMessage":"Successful","referenceNo":"31990842c9464ca09d192c02fba9238c","partnerReferenceNo":"1668489754","customerNumber":"628127416027","customerName":"mm","feeType":"Service fee","amount":{"value":"15000.00","currency":"IDR"},"feeAmount":{"value":"1620.00","currency":"IDR"},"customerMonthlyInLimit":"20000000","minAmount":{"value":"0.00","currency":"IDR"},"maxAmount":{"value":"1296700.00","currency":"IDR"}}}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }



// /**
//  *
//  * DANA E-money Transfer Exec
//  *
//  */
// try {
// 	$transaction = new \PyGPaymentLib\Transaction();
// 	$transaction->setCustomerPhone('628127416027');
// 	$transaction->setAmount(15000);
// 	$transaction->setOrderID(time());
// 	//
// 	$result = $vendor->EmoneyTransferExec($transaction);
// 	extract($result);
// 	print_r($response);
// 	/*
// {"status":"000","data":{"responseCode":"2003800","responseMessage":"Successful","referenceNo":"2022111510121481030100166737700698583","partnerReferenceNo":"1668489782","sessionId":"","customerNumber":"628127416027","customerName":"mm","amount":{"value":"15000.00","currency":"IDR"},"additionalInfo":{}}}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }



// /**
//  *
//  * DANA E-money Transfer Check Status
//  *
//  */
// try {
// 	$content = (object) json_decode($result['response']['content']);
// 	//
// 	$transaction = new \PyGPaymentLib\Transaction();
// 	// $transaction->setTransactionID($content->data->referenceNo);
// 	// $transaction->setReferenceNumber($content->data->referenceNumber);
// 	$transaction->setTransactionID('1668489782');
// 	$transaction->setReferenceNumber('2022111510121481030100166737700698583');
// 	//
// 	$result = $vendor->EmoneyTransferExecCheckStatus($transaction);
// 	extract($result);
// 	print_r($response);
// 	/*
// {"status":"000","data":{"responseCode":"2003900","responseMessage":"Successful","originalPartnerReferenceNo":"1668489782","originalReferenceNo":"2022111510121481030100166737700698583","serviceCode":"00","amount":{"value":"15000.00","currency":"IDR"},"latestTransactionStatus":"00","transactionStatusDesc":"Success","additionalInfo":{}}}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }