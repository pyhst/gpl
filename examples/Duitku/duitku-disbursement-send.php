<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['DUITKU_DISBURSEMENT_MERCHANT_ID'],
	$_ENV['DUITKU_DISBURSEMENT_API_KEY']
);
$init->setRequestURL($_ENV['DUITKU_HOST_URL']);
$init->setParams([
	'email' => $_ENV['DUITKU_DISBURSEMENT_EMAIL'],
]);
$vendor = new \Growinc\Payment\Vendors\Duitku($init);

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
// 	{"status":"000","data":[{"name":"PT BANK CENTRAL ASIA TBK.","code":"014","limit":25000000},{"name":"PT BANK RAKYAT INDONESIA (PERSERO), TBK.","code":"002","limit":25000000},{"name":"PT BANK TABUNGAN NEGARA (PERSERO)","code":"200","limit":0},{"name":"PT. BANK MANDIRI (PERSERO), TBK.","code":"008","limit":25000000},{"name":"PT BANK ANTARDAERAH","code":"088","limit":0},{"name":"PT BANK ARTHA GRAHA INTERNASIONAL, TBK.","code":"037","limit":0},{"name":"PT BANK BNI","code":"009","limit":0},{"name":"PT BANK BUKOPIN, TBK","code":"441","limit":0},{"name":"PT BANK BUMI ARTA, TBK","code":"076","limit":0},{"name":"PT BANK CIMB NIAGA, TBK","code":"022","limit":0},{"name":"PT BANK DANAMON INDONESIA TBK","code":"011","limit":0},{"name":"PT BANK EKONOMI RAHARJA, TBK","code":"087","limit":0},{"name":"PT BANK GANESHA","code":"161","limit":0},{"name":"PT BANK HANA","code":"484","limit":0},{"name":"PT BANK HIMPUNAN SAUDARA 1906, TBK","code":"212","limit":0},{"name":"PT BANK ICB BUMIPUTERA TBK","code":"485","limit":0},{"name":"PT BANK ICBC INDONESIA","code":"164","limit":0},{"name":"PT BANK INDEX SELINDO","code":"555","limit":0},{"name":"PT BANK INTERNASIONAL INDONESIA TBK","code":"016","limit":0},{"name":"PT BANK MASPION INDONESIA","code":"157","limit":0},{"name":"PT BANK MAYAPADA INTERNATIONAL TBK","code":"097","limit":0},{"name":"PT BANK MEGA SYARIAH","code":"506","limit":0},{"name":"PT BANK MEGA, TBK","code":"426","limit":0},{"name":"PT BANK MESTIKA DHARMA","code":"151","limit":0},{"name":"PT BANK METRO EXPRESS","code":"152","limit":0},{"name":"PT BANK MUAMALAT INDONESIA","code":"147","limit":0},{"name":"PT BANK MUTIARA, TBK","code":"095","limit":0},{"name":"PT BANK NUSANTARA PARAHYANGAN,TBK","code":"145","limit":0},{"name":"PT BANK OCBC NISP, TBK","code":"028","limit":0},{"name":"PT BANK OF INDIA INDONESIA, TBK","code":"146","limit":0},{"name":"PT BANK PERMATA TBK","code":"013","limit":25000000},{"name":"PT BANK RAKYAT INDONESIA AGRONIAGA, TBK.","code":"494","limit":0},{"name":"PT BANK SBI INDONESIA","code":"498","limit":0},{"name":"PT BANK SINARMAS, TBK","code":"153","limit":0},{"name":"PT BANK SYARIAH MANDIRI","code":"451","limit":0},{"name":"PT BANK UOB INDONESIA (DAHULU UOB BUANA)","code":"023","limit":0},{"name":"PT PAN INDONESIA BANK, TBK","code":"019","limit":0},{"name":"PT QNB BANK KESAWAN TBK","code":"167","limit":0},{"name":"PT ANGLOMAS INTERNASIONAL BANK","code":"531","limit":0},{"name":"PT BANK ANDARA","code":"466","limit":0},{"name":"PT BANK ARTOS INDONESIA","code":"542","limit":0},{"name":"PT BANK BCA SYARIAH","code":"536","limit":25000000},{"name":"PT BANK BISNIS INTERNASIONAL","code":"567","limit":0},{"name":"PT BANK BRISYARIAH","code":"422","limit":0},{"name":"PT BANK DINAR INDONESIA","code":"526","limit":0},{"name":"PT BANK FAMA INTERNASIONAL","code":"562","limit":0},{"name":"PT BANK HARDA INTERNASIONAL","code":"567","limit":0},{"name":"PT BANK INA PERDANA","code":"513","limit":25000000},{"name":"PT BANK JABAR BANTEN SYARIAH","code":"425","limit":0},{"name":"PT BANK JASA JAKARTA","code":"472","limit":0},{"name":"PT BANK KESEJAHTERAAN EKONOMI","code":"535","limit":0},{"name":"PT BANK MAYORA","code":"553","limit":0},{"name":"PT BANK MITRANIAGA","code":"491","limit":0},{"name":"PT BANK MULTIARTA SENTOSA","code":"548","limit":0},{"name":"PT BANK NATIONALNOBU","code":"503","limit":0},{"name":"PT BANK PANIN SYARIAH","code":"517","limit":0},{"name":"PT BANK PUNDI INDONESIA, TBK","code":"558","limit":0},{"name":"PT BANK ROYAL INDONESIA","code":"501","limit":0},{"name":"PT BANK SAHABAT PURBA DANARTA","code":"547","limit":0},{"name":"PT BANK SAHABAT SAMPOERNA","code":"523","limit":0},{"name":"PT BANK SINAR HARAPAN BALI","code":"564","limit":0},{"name":"PT BANK SYARIAH BUKOPIN","code":"774","limit":0},{"name":"PT BANK TABUNGAN PENSIUNAN NASIONAL, TBK","code":"213","limit":0},{"name":"PT BANK VICTORIA INTERNATIONAL, TBK","code":"566","limit":0},{"name":"PT BANK VICTORIA SYARIAH","code":"405","limit":0},{"name":"PT BANK YUDHA BHAKTI","code":"490","limit":0},{"name":"PT CENTRATAMA NASIONAL BANK","code":"559","limit":0},{"name":"PT PRIMA MASTER BANK","code":"520","limit":0},{"name":"BPD SULAWESI TENGGARA","code":"135","limit":0},{"name":"BPD YOGYAKARTA","code":"112","limit":0},{"name":"BPD KALIMANTAN TIMUR","code":"124","limit":0},{"name":"PT BANK DKI","code":"111","limit":0},{"name":"PT BANK ACEH","code":"116","limit":0},{"name":"PT BANK KALIMANTAN TENGAH","code":"125","limit":0},{"name":"PT BPD JAMBI","code":"115","limit":0},{"name":"PT BPD SULAWESI SELATAN DAN SULAWESI BARAT","code":"126","limit":0},{"name":"PT BPD LAMPUNG","code":"121","limit":0},{"name":"PT BPD RIAU KEPRI","code":"119","limit":0},{"name":"PT BPD SUMATERA BARAT","code":"118","limit":0},{"name":"PT. BPD JAWA BARAT DAN BANTEN, TBK","code":"110","limit":0},{"name":"PT. BPD MALUKU","code":"131","limit":0},{"name":"PT. BPD BENGKULU","code":"133","limit":0},{"name":"PT. BPD JAWA TENGAH","code":"113","limit":0},{"name":"PT. BPD JAWA TIMUR","code":"114","limit":0},{"name":"PT. BPD KALIMANTAN BARAT","code":"123","limit":0},{"name":"PT. BPD NUSA TENGGARA BARAT","code":"128","limit":0},{"name":"PT. BPD NUSA TENGGARA TIMUR","code":"130","limit":0},{"name":"PT. BPD SULAWESI TENGAH","code":"134","limit":0},{"name":"PT. BPD SULAWESI UTARA","code":"127","limit":0},{"name":"PT. BPD BALI","code":"129","limit":0},{"name":"PT. BPD KALIMANTAN SELATAN","code":"122","limit":0},{"name":"PT. BPD PAPUA","code":"132","limit":0},{"name":"PT. BPD SUMATERA SELATAN DAN BANGKA BELITUNG","code":"120","limit":0},{"name":"PT. BPD SUMATERA UTARA","code":"117","limit":0},{"name":"PT BANK COMMONWEALTH","code":"950","limit":0},{"name":"PT BANK AGRIS","code":"945","limit":0},{"name":"PT BANK ANZ INDONESIA","code":"061","limit":0},{"name":"PT BANK BNP PARIBAS INDONESIA","code":"057","limit":0},{"name":"PT BANK CAPITAL INDONESIA, TBK","code":"054","limit":0},{"name":"PT BANK DBS INDONESIA","code":"046","limit":0},{"name":"PT BANK KEB INDONESIA","code":"484","limit":0},{"name":"PT BANK MAYBANK SYARIAH INDONESIA","code":"777","limit":0},{"name":"PT BANK MIZUHO INDONESIA","code":"048","limit":0},{"name":"PT BANK RABOBANK INTERNATIONAL INDONESIA","code":"089","limit":0},{"name":"PT BANK RESONA PERDANIA","code":"047","limit":0},{"name":"PT BANK WINDU KENTJANA INTERNATIONAL, TBK","code":"036","limit":0},{"name":"PT BANK WOORI INDONESIA","code":"212","limit":0},{"name":"PT. BANK CHINATRUST INDONESIA","code":"949","limit":0},{"name":"PT. BANK SUMITOMO MITSUI INDONESIA","code":"045","limit":0},{"name":"BANK OF AMERICA, N.A","code":"033","limit":0},{"name":"BANK OF CHINA LIMITED","code":"069","limit":0},{"name":"CITIBANK N.A.","code":"031","limit":0},{"name":"DEUTSCHE BANK AG.","code":"067","limit":0},{"name":"JP. MORGAN CHASE BANK, N.A.","code":"032","limit":0},{"name":"STANDARD CHARTERED BANK","code":"050","limit":0},{"name":"THE BANGKOK BANK COMP. LTD","code":"040","limit":0},{"name":"THE BANK OF TOKYO MITSUBISHI UFJ LTD","code":"042","limit":0},{"name":"THE HONGKONG & SHANGHAI BANKING CORP","code":"041","limit":0},{"name":"THE ROYAL BANK OF SCOTLAND N.V.","code":"052","limit":0},{"name":"LINK AJA","code":"911","limit":55000000}]}
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
// 	{"status":"000","data":{"user_id":26054,"user_email":"dev@vogame.co.id","total_balance":10000000,"effective_balance":10000000}}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }

/**
 *
 * Online Transfer Inquiry
 *
 */
try {
	$transaction = new \Growinc\Payment\Transaction();
	$transaction->setCustomerBankName('014');
	// $transaction->setCustomerBankAccountNumber('6044222099');
	// $transaction->setCustomerBankAccountNumber('8760673566'); // Duitku for test only
	$transaction->setCustomerBankAccountNumber('');
	$transaction->setAmount(10000);
	$transaction->setDescription('Test Transfer');
		$transaction->setMerchantID('001');
		$transaction->setMerchantName('PG');
	$transaction->setOrderID(time());
	//
	$result = $vendor->OnlineTransferInquiry($transaction);
	extract($result);
	print_r($response);
	/*
		{
			"status": "000",
			"data": {
				"email": "dev@vogame.co.id",
				"bankCode": "014",
				"bankAccount": "8760673566",
				"amountTransfer": 10000,
				"accountName": "Test Account",
				"custRefNumber": "000000088999",
				"disburseId": 111286,
				"responseCode": "00",
				"responseDesc": "Success"
			}
		}
	*/

	/**
	*
	* Online Transfer Execute
	*
	*/
	// try {
	// 	$content = (object) json_decode($response['content']);
	// 	//
	// 	$transaction = new \Growinc\Payment\Transaction();
	// 	$transaction->setTransactionID($content->data->disburseId);
	// 	$transaction->setCustomerBankName($content->data->bankCode);
	// 	$transaction->setCustomerBankAccountNumber($content->data->bankAccount);
	// 	$transaction->setAmount($content->data->amountTransfer);
	// 	$transaction->setCustomerBankAccountName($content->data->accountName);
	// 	$transaction->setOrderID(time());
	// 	$transaction->setDescription('Test Online Transfer');
	// 	//
	// 	$result = $vendor->OnlineTransferExec($transaction);
	// 	extract($result);
	// 	print_r($response);
	// 	/*
	// 	{"status":"000","data":{"email":"dev@vogame.co.id","bankCode":"014","bankAccount":"8760673566","amountTransfer":10000,"accountName":"Test Account","custRefNumber":"000000089002","responseCode":"00","responseDesc":"Success"}}
	// 	*/
	// } catch (\Throwable $e) {
	// 	print_r($vendor::ThrowError($e));
	// }

} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}

// /**
//  *
//  * Kliring Inquiry
//  *
//  */
// try {
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setCustomerBankName('014');
// 	// $transaction->setCustomerBankAccountNumber('6044222099');
// 	$transaction->setCustomerBankAccountNumber('8760673466'); // Duitku for test only
// 	$transaction->setAmount(100000000);
// 	$transaction->setTransferMethod('RTGS');
// 	$transaction->setDescription('Test Transfer');
// 		$transaction->setMerchantID('001');
// 		$transaction->setMerchantName('VoGame');
// 	//
// 	$result = $vendor->ClearingInquiry($transaction);
// 	extract($result);
// 	print_r($result);
// 	/*
// 	{
// 		"status": "000",
// 		"data": {
// 			"email": "dev@vogame.co.id",
// 			"bankCode": "014",
// 			"bankAccount": "8760673466",
// 			"amountTransfer": 150000000,
// 			"accountName": "Test Account",
// 			"custRefNumber": "000000089000",
// 			"disburseId": 111287,
// 			"type": "RTGS",
// 			"responseCode": "00",
// 			"responseDesc": "Approved or completed successfully"
// 		}
// 	}
// 	*/

// 	/**
// 	*
// 	* Kliring Execute
// 	*
// 	*/
// 	try {
// 		$content = (object) json_decode($response['content']);
// 		//
// 		$transaction = new \Growinc\Payment\Transaction();
// 		$transaction->setTransactionID($content->data->disburseId);
// 		$transaction->setCustomerBankName($content->data->bankCode);
// 		$transaction->setCustomerBankAccountNumber($content->data->bankAccount);
// 		$transaction->setAmount($content->data->amountTransfer);
// 		$transaction->setCustomerBankAccountName($content->data->accountName);
// 		$transaction->setOrderID($content->data->custRefNumber);
// 		$transaction->setDescription('Test Kliring Transfer');
// 		$transaction->setTransferMethod($content->data->type);
// 		//
// 		$result = $vendor->ClearingExec($transaction);
// 		extract($result);
// 		print_r($result);
// 		/*
// 		{
// 			"status": "000",
// 			"data": {
// 				"email": "dev@vogame.co.id",
// 				"bankCode": "014",
// 				"bankAccount": "8760673466",
// 				"amountTransfer": 150000000,
// 				"accountName": "Test Account",
// 				"custRefNumber": "000000089054",
// 				"type": "RTGS",
// 				"responseCode": "-510",
// 				"responseDesc": "Wallet Anda tidak mencukupi"
// 			}
// 		}
// 		*/
// 	} catch (\Throwable $e) {
// 		print_r($vendor::ThrowError($e));
// 	}

// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }

// /**
//  *
//  * Check Transfer
//  *
//  */
// try {
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setTransactionID('111345');
// 	//
// 	$result = $vendor->CheckTransfer($transaction);
// 	extract($result);
// 	print_r($response);
// 	/*
// 	{
// 		"status": "000",
// 		"data": {
// 			"bankCode": "014",
// 			"bankAccount": "8760673566",
// 			"amountTransfer": 10000,
// 			"accountName": "Test Account",
// 			"custRefNumber": "000000089056",
// 			"responseCode": "00",
// 			"responseDesc": "Success"
// 		}
// 	}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }