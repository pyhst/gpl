<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['NUSAPAY_CLIENT_ID'],
	$_ENV['NUSAPAY_CLIENT_SECRET']
);
$init->setRequestURL($_ENV['NUSAPAY_IBANK_URL']);
$init->setParams([
	'bank' => 'cimb',
	'accnum' => '860008882900',
]);
$vendor = new \Growinc\Payment\Vendors\Nusapay($init);

// /**
//  *
//  * Get Bank List
//  *
//  */
// try {
// 	$result = $vendor->BankList();
// 	extract($result);
// 	print_r($response);
// 	/*
// {"status":"000","data":[{"name":"BANK DANAMON","code":"001"},{"name":"BANK RAKYAT INDONESIA","code":"002"},{"name":"BANK MANDIRI","code":"008"},{"name":"BANK BNI","code":"009"},{"name":"DANAMON SYARIAH","code":"011"},{"name":"BANK CENTRAL ASIA","code":"014"},{"name":"BII \/ MAYBANK","code":"016"},{"name":"PAN INDONESIA \/PANIN","code":"019"},{"name":"CIMB NIAGA","code":"022"},{"name":"BANK UOB INDONESIA","code":"023"},{"name":"BANK OCBC NISP","code":"028"},{"name":"CITIBANK","code":"031"},{"name":"BANK OF AMERICA","code":"033"},{"name":"BANK ARTHA GRAHA","code":"037"},{"name":"THE BANK OF TOKYO","code":"042"},{"name":"DBS INDONESIA","code":"046"},{"name":"RESONA PERDANIA BANK","code":"047"},{"name":"BANK MIZUHO INDONESIA","code":"048"},{"name":"STANDARD CHARTERED BANK","code":"050"},{"name":"BANK CAPITAL INDONESIA","code":"054"},{"name":"BNP PARIBAS INDONESIA","code":"057"},{"name":"BANK ANZ INDONESIA","code":"061"},{"name":"BANK OF CHINA","code":"069"},{"name":"BANK BUMI ARTA","code":"076"},{"name":"BANK EKONOMI RAHARJA","code":"087"},{"name":"BANK MAYAPADA","code":"097"},{"name":"BANK JABAR","code":"110"},{"name":"BANK DKI","code":"111"},{"name":"BPD YOGYAKARTA","code":"112"},{"name":"BPD JAWA TENGAH","code":"113"},{"name":"BANK JATIM","code":"114"},{"name":"BPD JAMBI","code":"115"},{"name":"BANK ACEH","code":"116"},{"name":"BPD ACEH SYARIAH","code":"116"},{"name":"BPD SUMATERA UTARA","code":"117"},{"name":"BPD SUMATERA BARAT","code":"118"},{"name":"BPD RIAU KEPRI","code":"119"},{"name":"BPD SUMATERA SELATAN BABEL","code":"120"},{"name":"BPD LAMPUNG","code":"121"},{"name":"BPD KALSEL","code":"122"},{"name":"BPD KALIMANTAN BARAT","code":"123"},{"name":"BPD KALIMANTAN TIMUR","code":"124"},{"name":"BPD KALTENG","code":"125"},{"name":"PT BANK SULSELBAR","code":"126"},{"name":"BPD SULAWESI UTARA","code":"127"},{"name":"BPD NTB","code":"128"},{"name":"BPD BALI","code":"129"},{"name":"BPD NTT","code":"130"},{"name":"BPD MALUKU","code":"131"},{"name":"BPD PAPUA","code":"132"},{"name":"BPD BENGKULU","code":"133"},{"name":"BPD SULAWESI TENGAH","code":"134"},{"name":"BPD SULAWESI TENGGARA","code":"135"},{"name":"BPD BANTEN","code":"137"},{"name":"BANK NUSANTARA","code":"145"},{"name":"BANK OF INDIA INDONESIA","code":"146"},{"name":"BANK MUAMALAT","code":"147"},{"name":"BANK MESTIKA DHARMA","code":"151"},{"name":"BANK METRO EXPRESS","code":"152"},{"name":"BANK SINARMAS","code":"153"},{"name":"BANK MASPION INDONESIA","code":"157"},{"name":"BANK GANESHA","code":"161"},{"name":"BANK WINDU KENTJANA","code":"162"},{"name":"BANK ICBC INDONESIA","code":"164"},{"name":"BANK HARMONI INTERNATIONAL","code":"166"},{"name":"BANK QNB INDONESIA","code":"167"},{"name":"BANK TABUNGAN NEGARA","code":"200"},{"name":"BTN SYARIAH","code":"200"},{"name":"BTPN","code":"213"},{"name":"BANK SYARIAH BRI","code":"422"},{"name":"BANK JABAR SYARIAH","code":"425"},{"name":"BANK MEGA TBK.","code":"426"},{"name":"BNI SYARIAH","code":"427"},{"name":"BANK BUKOPIN","code":"441"},{"name":"BANK SYARIAH INDONESIA","code":"451"},{"name":"BANK JASA JAKARTA","code":"472"},{"name":"BANK MNC INTERNATIONAL","code":"485"},{"name":"BANK YUDHA BAKTI","code":"490"},{"name":"BANK MITRANIAGA","code":"491"},{"name":"BANK AGRONIAGA","code":"494"},{"name":"BANK SBI INDONESIA","code":"498"},{"name":"BANK ROYAL INDONESIA","code":"501"},{"name":"NOBU NATIONAL BANK","code":"503"},{"name":"BANK MEGA SYARIAH","code":"506"},{"name":"BANK INA PERDANA","code":"513"},{"name":"BANK PRIMA MASTER","code":"520"},{"name":"BANK SYARIAH BUKOPIN","code":"521"},{"name":"BANK SAHABAT SAMPOERNA","code":"523"},{"name":"BANK KESEJAHTERAAN","code":"535"},{"name":"BANK BCA SYARIAH","code":"536"},{"name":"BANK ARTOS INDONESIA","code":"542"},{"name":"BANK BTPN SYARIAH","code":"547"},{"name":"BANK MULTI ARTA SENTOSA","code":"548"},{"name":"BANK MAYORA","code":"553"},{"name":"BANK INDEX SELINDO","code":"555"},{"name":"BANK CENTRATAMA","code":"559"},{"name":"BANK SINAR HARAPAN","code":"564"},{"name":"BANK SINAR HARAPAN","code":"564"},{"name":"BANK VICTORIA","code":"566"},{"name":"BANK HARDA INTERNASIONAL","code":"567"},{"name":"BANK AGRIS","code":"945"},{"name":"BANK CTBC INDONESIA","code":"949"},{"name":"COMMONWEALTH BANK","code":"950"},{"name":"TESTER","code":"999"}]}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }

// /**
//  *
//  * Check Balance
//  *
//  */
// try {
// 	$result = $vendor->CheckBalance();
// 	extract($result);
// 	print_r($response);
// 	/*
// {"status":"000","data":{"account_number":"860008882900","balance":205469148.33}}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }

// /**
//  *
//  * Check Interbank Inquiry
//  *
//  */
// try {
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setCustomerBankName('022');
// 	$transaction->setCustomerBankAccountNumber('9639081212121314');
// 	$result = $vendor->InterbankTransferInquiry($transaction);
// 	extract($result);
// 	print_r($response);
// 	/*
// {"status":"000","data":{"dest_account_number":"9639081212121314","dest_account_name":"PAY XN","dest_bank_name":"BANK CIMB NIAGA","dest_bank_code":"220026"}}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }

/**
 *
 * In House Transfer
 *
 */
try {
	$transaction = new \Growinc\Payment\Transaction();
	$transaction->setCustomerBankName('022');
	$transaction->setCustomerBankAccountNumber('9639081212121314');
	$transaction->setAmount(1000);
	$transaction->setDescription('Test Transfer to VA');
	$transaction->setOrderID(time());
	//
	$result = $vendor->InHouseTransfer($transaction);
	extract($result);
	print_r($response);
	/* // Success
		{
			"status": "000",
			"data": {
				"trans_id": "1664552912",
				"trans_date": "20220930",
				"reference_id": "1664552912",
				"details": {
					"from_accnum": "860008882900",
					"to_accnum": "9639081212121314",
					"to_name": "9639081212121314",
					"to_bank": "CIMB",
					"to_bank_code": "022",
					"currency": "IDR",
					"amount": "1000",
					"note": "Test Transfer to VA"
				}
			}
		}
	*/
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}

// /**
//  *
//  * Check Transfer // Not applicable
//  *
//  */
// try {
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setReferenceNumber('1664552912');
// 	$transaction->setReqDateTime('20220930');
// 	$transaction->setPaymentChannel('TX');
// 	$result = $vendor->CheckTransfer($transaction);
// 	extract($result);
// 	print_r($response);
// 	/*
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }