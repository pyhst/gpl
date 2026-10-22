<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;

class Xendit extends Requestor implements VendorInterface
{

	protected $form;

	/*==========================================================================================
												Start of Private
	==========================================================================================**/

		public function _CreateCardToken($args)
		{
			// try {
			// 	$this->request['headers'] = [
			// 		'Content-Type' => 'application/json',
			// 		'Accept' => 'application/json',
			// 	];
			// 	$this->request['url'] = $args['token_url'];
			// 	$this->request['data'] = [
			// 			'amount' => $args['amount'],
			// 			'card_number' => $args['card_number'],
			// 			'card_exp_month' => $args['card_exp_month'],
			// 			'card_exp_year' => $args['card_exp_year'],
			// 			'card_cvn' => $args['card_cvn'],
			// 			'is_multiple_use' => $args['is_multiple_use'],
			// 			'should_authenticate' => $args['should_authenticate'],
			// 		];
			// 	$get = $this->DoRequest('GET', $this->request);
			// 	$response = (array) $get['response'];
			// 	extract($response);
			// 	if (!empty($status_code) && $status_code === 200) {
			// 		$content = (object) json_decode($content);
			// 		if (
			// 			!empty($content->status_code)
			// 			&& $content->status_code == 200
			// 		) {
			// 			$result = $content->token_id;
			// 		}
			// 	}
			// } catch (\Throwable $e) {
			// 	throw new \Exception($this->ThrowError($e));
			// }
			// return $result ?? [];
		}

	/*=================================   End of Private   ==================================*/

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args)
	{
		// Inapplicable
	}

	public function CreateDummyForm($args)
	{
		// Inapplicable
	}



	/**
	 *
	 * Disbursement
	 *
	 */

	public function BankList()
	{
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/available_disbursements_banks'
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':'),
				'Content-Length' => strlen(json_encode([])),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('GET', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 200
			) {
				/*
					[{
						"name": "Bank Central Asia (BCA)",
						"code": "BCA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Mandiri",
						"code": "MANDIRI",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank CIMB Niaga",
						"code": "CIMB",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Danamon",
						"code": "DANAMON",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Permata",
						"code": "PERMATA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Panin",
						"code": "PANIN",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Anglomas International Bank",
						"code": "ANGLOMAS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bangkok Bank",
						"code": "BANGKOK",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Agris",
						"code": "AGRIS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Sinarmas",
						"code": "SINARMAS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank BRI Agroniaga",
						"code": "AGRONIAGA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Andara",
						"code": "ANDARA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank ANZ Indonesia",
						"code": "ANZ",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Artha Graha International",
						"code": "ARTHA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Artos Indonesia",
						"code": "ARTOS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Bisnis Internasional",
						"code": "BISNIS_INTERNASIONAL",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank BJB",
						"code": "BJB",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank BJB Syariah",
						"code": "BJB_SYR",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank BNI Syariah",
						"code": "BNI_SYR",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank BNP Paribas",
						"code": "BNP_PARIBAS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Bukopin",
						"code": "BUKOPIN",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Bumi Arta",
						"code": "BUMI_ARTA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Capital Indonesia",
						"code": "CAPITAL",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Central Asia (BCA) Syariah",
						"code": "BCA_SYR",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Chinatrust Indonesia",
						"code": "CHINATRUST",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank CIMB Niaga UUS",
						"code": "CIMB_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Commonwealth",
						"code": "COMMONWEALTH",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Danamon UUS",
						"code": "DANAMON_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank DBS Indonesia",
						"code": "DBS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Dinar Indonesia",
						"code": "DINAR_INDONESIA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank DKI",
						"code": "DKI",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank DKI UUS",
						"code": "DKI_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Fama International",
						"code": "FAMA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Ganesha",
						"code": "GANESHA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Hana",
						"code": "HANA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Harda Internasional",
						"code": "HARDA_INTERNASIONAL",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Himpunan Saudara 1906",
						"code": "HIMPUNAN_SAUDARA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank ICBC Indonesia",
						"code": "ICBC",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Ina Perdania",
						"code": "INA_PERDANA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Index Selindo",
						"code": "INDEX_SELINDO",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Jasa Jakarta",
						"code": "JASA_JAKARTA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Kesejahteraan Ekonomi",
						"code": "KESEJAHTERAAN_EKONOMI",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Maspion Indonesia",
						"code": "MASPION",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Mayapada International",
						"code": "MAYAPADA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Maybank Syariah Indonesia",
						"code": "MAYBANK_SYR",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Mayora",
						"code": "MAYORA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Mega",
						"code": "MEGA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Mizuho Indonesia",
						"code": "MIZUHO",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank MNC Internasional",
						"code": "MNC_INTERNASIONAL",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Muamalat Indonesia",
						"code": "MUAMALAT",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Multi Arta Sentosa",
						"code": "MULTI_ARTA_SENTOSA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Nationalnobu",
						"code": "NATIONALNOBU",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Nusantara Parahyangan",
						"code": "NUSANTARA_PARAHYANGAN",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank OCBC NISP",
						"code": "OCBC",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank OCBC NISP UUS",
						"code": "OCBC_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank of America Merill-Lynch",
						"code": "BAML",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank of China (BOC)",
						"code": "BOC",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank of India Indonesia",
						"code": "INDIA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank of Tokyo Mitsubishi UFJ",
						"code": "TOKYO",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Panin Syariah",
						"code": "PANIN_SYR",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Permata UUS",
						"code": "PERMATA_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Rabobank International Indonesia",
						"code": "RABOBANK",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Resona Perdania",
						"code": "RESONA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Royal Indonesia",
						"code": "ROYAL",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Sahabat Sampoerna",
						"code": "SAHABAT_SAMPOERNA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank SBI Indonesia",
						"code": "SBI_INDONESIA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Sumitomo Mitsui Indonesia",
						"code": "MITSUI",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Syariah BRI",
						"code": "BRI_SYR",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Syariah Bukopin",
						"code": "BUKOPIN_SYR",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Syariah Mandiri",
						"code": "MANDIRI_SYR",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Syariah Mega",
						"code": "MEGA_SYR",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Tabungan Negara (BTN)",
						"code": "BTN",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Tabungan Negara (BTN) UUS",
						"code": "BTN_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Tabungan Pensiunan Nasional",
						"code": "TABUNGAN_PENSIUNAN_NASIONAL",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank UOB Indonesia",
						"code": "UOB",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Victoria Internasional",
						"code": "VICTORIA_INTERNASIONAL",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Victoria Syariah",
						"code": "VICTORIA_SYR",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Woori Indonesia",
						"code": "WOORI",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Yudha Bhakti",
						"code": "YUDHA_BHAKTI",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Aceh",
						"code": "ACEH",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Aceh UUS",
						"code": "ACEH_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Bali",
						"code": "BALI",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Bengkulu",
						"code": "BENGKULU",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Daerah Istimewa Yogyakarta (DIY)",
						"code": "DAERAH_ISTIMEWA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Daerah Istimewa Yogyakarta (DIY) UUS",
						"code": "DAERAH_ISTIMEWA_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Jambi",
						"code": "JAMBI",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Jambi UUS",
						"code": "JAMBI_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Jawa Tengah",
						"code": "JAWA_TENGAH",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Jawa Tengah UUS",
						"code": "JAWA_TENGAH_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Jawa Timur",
						"code": "JAWA_TIMUR",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Jawa Timur UUS",
						"code": "JAWA_TIMUR_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Kalimantan Barat",
						"code": "KALIMANTAN_BARAT",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Kalimantan Barat UUS",
						"code": "KALIMANTAN_BARAT_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Kalimantan Selatan",
						"code": "KALIMANTAN_SELATAN",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Kalimantan Selatan UUS",
						"code": "KALIMANTAN_SELATAN_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Kalimantan Tengah",
						"code": "KALIMANTAN_TENGAH",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Kalimantan Timur",
						"code": "KALIMANTAN_TIMUR",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Kalimantan Timur UUS",
						"code": "KALIMANTAN_TIMUR_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Lampung",
						"code": "LAMPUNG",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Maluku",
						"code": "MALUKU",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Nusa Tenggara Barat",
						"code": "NUSA_TENGGARA_BARAT",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Nusa Tenggara Barat UUS",
						"code": "NUSA_TENGGARA_BARAT_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Nusa Tenggara Timur",
						"code": "NUSA_TENGGARA_TIMUR",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Papua",
						"code": "PAPUA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Riau Dan Kepri",
						"code": "RIAU_DAN_KEPRI",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Riau Dan Kepri UUS",
						"code": "RIAU_DAN_KEPRI_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Sulawesi Tengah",
						"code": "SULAWESI",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Sulawesi Tenggara",
						"code": "SULAWESI_TENGGARA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Sulselbar",
						"code": "SULSELBAR",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Sulselbar UUS",
						"code": "SULSELBAR_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Sulut",
						"code": "SULUT",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Sumatera Barat",
						"code": "SUMATERA_BARAT",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Sumatera Barat UUS",
						"code": "SUMATERA_BARAT_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Sumsel Dan Babel",
						"code": "SUMSEL_DAN_BABEL",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Sumsel Dan Babel UUS",
						"code": "SUMSEL_DAN_BABEL_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Sumut",
						"code": "SUMUT",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Sumut UUS",
						"code": "SUMUT_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Centratama Nasional Bank",
						"code": "CENTRATAMA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Citibank",
						"code": "CITIBANK",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Deutsche Bank",
						"code": "DEUTSCHE",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "HSBC Indonesia (formerly Bank Ekonomi Raharja)",
						"code": "HSBC",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Hongkong and Shanghai Bank Corporation (HSBC) UUS",
						"code": "HSBC_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "JP Morgan Chase Bank",
						"code": "JPMORGAN",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Prima Master Bank",
						"code": "PRIMA_MASTER",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Royal Bank of Scotland (RBS)",
						"code": "RBS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Standard Charted Bank",
						"code": "STANDARD_CHARTERED",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Mitra Niaga",
						"code": "MITRA_NIAGA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Arta Niaga Kencana",
						"code": "ARTA_NIAGA_KENCANA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Rakyat Indonesia (BRI)",
						"code": "BRI",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Negara Indonesia (BNI)",
						"code": "BNI",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Maybank",
						"code": "MAYBANK",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Shinhan Indonesia (formerly Bank Metro Express)",
						"code": "SHINHAN",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Mandiri Taspen Pos (formerly Bank Sinar Harapan Bali)",
						"code": "MANDIRI_TASPEN",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Indonesia Eximbank (formerly Bank Ekspor Indonesia)",
						"code": "EXIMBANK",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank QNB Indonesia (formerly Bank QNB Kesawan)",
						"code": "QNB_INDONESIA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BPD Banten (formerly Bank Pundi Indonesia)",
						"code": "BANTEN",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank JTrust Indonesia (formerly Bank Mutiara)",
						"code": "JTRUST",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "BTPN Syariah (formerly BTPN UUS and Bank Sahabat Purba Danarta)",
						"code": "BTPN_SYARIAH",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "China Construction Bank Indonesia (formerly Bank Antar Daerah and Bank Windu Kentjana International)",
						"code": "CCB",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Sinarmas UUS",
						"code": "SINARMAS_UUS",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Amar Indonesia (formerly Anglomas International Bank)",
						"code": "AMAR",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Woori Saudara Indonesia 1906 (formerly Bank Himpunan Saudara and Bank Woori Indonesia)",
						"code": "WOORI_SAUDARA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Oke Indonesia (formerly Bank Andara)",
						"code": "OKE",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "GoPay",
						"code": "GOPAY",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "OVO",
						"code": "OVO",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Mandiri E-Cash",
						"code": "MANDIRI_ECASH",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "DANA",
						"code": "DANA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "LinkAja",
						"code": "LINKAJA",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "ShopeePay",
						"code": "SHOPEEPAY",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Syariah Indonesia (BSI)",
						"code": "BSI",
						"can_disburse": true,
						"can_name_validate": true
					}, {
						"name": "Bank Mestika Dharma",
						"code": "MESTIKA_DHARMA",
						"can_disburse": true,
						"can_name_validate": true
					}]
				*/
				// Parse data
				$content = (object) json_decode($content);
				$res = [
					'status' => '000',
					'data' => array_map(function($item) {
						return [
							'id' => strtolower($item->code),
							'type' => 'bank',
							'name' => $item->name,
							'code' => strtoupper($item->code),
						];
					}, (array) $content),
				];
			} else {
				throw new \Exception('Status code is not 200', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function AccountInquiry(\PyGPaymentLib\Transaction $transaction)
	{
		// Inapplicable
	}

	public function CheckBalance(object $request)
	{
		try {
			SELF::Validate($request, [
				'account_type'
			]);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/balance?account_type=' . $request->account_type
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':'),
				'Content-Length' => strlen(json_encode([])),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 200
			) {
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content)
				) {
					/* // Success
						{
							"balance": 30059396403
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception('Data is empty', 901);
				}
			} else {
				throw new \Exception('Status code is not 200', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function DoTransfer(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->request['data'] = [
				'external_id' => $transaction->getInvoiceNo(),
				'bank_code' => $transaction->getCustomerBankName(),
				'account_holder_name' => $transaction->getCustomerBankAccountName(),
				'account_number' => $transaction->getCustomerBankAccountNumber(),
				'description' => $transaction->getDescription(),
				'amount' => $transaction->getAmount(),
				'email_to' => [],
				'email_cc' => [],
				'email_bcc' => [],
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/disbursements'
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':'),
				'Content-Length' => strlen(json_encode([])),
				'X-IDEMPOTENCY-KEY' => base64_encode($transaction->getInvoiceNo()),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 200
			) {
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content->status)
					&& $content->status == 'PENDING'
				) {
					/*
						{
							"status": "PENDING",
							"user_id": "5fc89b828c3f5a408eef8f78",
							"external_id": "TT20220602122424",
							"amount": 10000,
							"bank_code": "BCA",
							"account_holder_name": "MAJU SELARAS KOMPUTER PT",
							"disbursement_description": "Test Transfer",
							"id": "6298498a5b5ee9aabcfd2f4b"
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception('Data is empty', 901);
				}
			} else {
				throw new \Exception('Status code is not 200', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function CheckTransfer(object $request)
	{
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/disbursements/' . $request->id
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':'),
				'Content-Length' => strlen(json_encode([])),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 200
			) {
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content)
				) {
					/* // Success
						{
							"status": "COMPLETED",
							"user_id": "5fc89b828c3f5a408eef8f78",
							"external_id": "TT20220602122522",
							"amount": 10000,
							"bank_code": "BCA",
							"account_holder_name": "MAJU SELARAS KOMPUTER PT",
							"disbursement_description": "Test Transfer",
							"is_instant": true,
							"id": "629849c5d3dcca6caacdb8b7"
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception('Data is empty', 901);
				}
			} else {
				throw new \Exception('Status code is not 200', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}



	/**
	 *
	 * Accept Payment
	 *
	 */

	public function RedirectPayment(\PyGPaymentLib\Transaction $transaction)
	{
		// Inapplicable
	}

	public function SecurePayment(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->transaction = $transaction;
			//
			$this->form['order_id'] = $this->transaction->getOrderID();
			$this->form['invoice_no'] = $this->transaction->getInvoiceNo();
			$this->form['currency'] = $this->transaction->getCurrency();
			//
			$this->form['item'] = $this->transaction->getItem();
			$this->form['amount'] = (float) $this->transaction->getAmount();
			$this->form['description'] = $this->transaction->getDescription();
			//
			$this->form['customer_name'] = $this->transaction->getCustomerName();
			$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			// $this->form['customer_address'] = $this->transaction->getCustomerAddress();
			// $this->form['country_code'] = $this->transaction->getCountryCode();
			// //
			// $this->form['billing_address'] = [
			// 		'first_name' => $this->form['customer_name'],
			// 		'last_name' => '',
			// 		'email' => $this->form['customer_email'],
			// 		'phone' => $this->form['customer_phone'],
			// 		'address' => ' ',
			// 		'city' => ' ',
			// 		'postal_code' => ' ',
			// 		'country_code' => $this->form['country_code'],
			// 	];
			// $this->form['shipping_address'] = [
			// 		'first_name' => $this->form['customer_name'],
			// 		'last_name' => '',
			// 		'email' => $this->form['customer_email'],
			// 		'phone' => $this->form['customer_phone'],
			// 		'address' => ' ',
			// 		'city' => ' ',
			// 		'postal_code' => ' ',
			// 		'country_code' => $this->form['country_code'],
			// 	];
			// $this->form['customer_details'] = [
			// 		'first_name' => $this->form['customer_name'],
			// 		'last_name' => '',
			// 		'email' => $this->form['customer_email'],
			// 		'phone' => $this->form['customer_phone'],
			// 		'billing_address' => $this->form['billing_address'],
			// 		'shipping_address' => $this->form['shipping_address'],
			// 	];
			//
			$arr = explode(',', $this->transaction->getPaymentMethod());
			$payment_method = strtolower(trim( $arr[0] ?? '' ));
			$payment_channel = strtolower(trim( $arr[1] ?? '' ));
			//
			// $expire_date = gmdate("Y-m-d\TH:i:s\Z",  $this->transaction->getTime() + (3600 * ( 7 + $this->transaction->getExpireIn()))) . '+07:00';
			$expire_date = gmdate("Y-m-d\TH:i:s",  $this->transaction->getTime() + (3600 * ( 7 + $this->transaction->getExpireIn()))) . '+07:00';
			//
			switch ($payment_method) {
				case 'bank_transfer':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() . '/callback_virtual_accounts'
					);
					$this->request['data'] = [
							'external_id' => $this->form['order_id'],
							'bank_code' => strtoupper($payment_channel),
							'name' => $this->form['customer_name'],
							'is_closed' => true, // When set to true, the virtual account will be closed and will only accept the amount specified in expected_amount
							'expected_amount' => $this->form['amount'],
							// 'expiration_date' => gmdate("Y-m-d\TH:i:s\Z", strtotime("now") + ($this->transaction->getExpireIn() * 60)),
							'expiration_date' => $expire_date,
							'is_single_use' => 'true',
							// 'description' => '', // This field is only supported for BRI
						];
					break;
				case 'credit_card':
					throw new \Exception("Currently Inapplicable", 1);
					break;
				case 'ewallet':
					// updated 5/14/2021 according to Xendit updated API docs
					/*
					$this->request['url'] = $this->init->getPaymentURL() . '/ewallets';
					$this->request['data'] = [
							'external_id' => $this->form['order_id'],
							'amount' => $this->form['amount'],
							'phone' => $this->form['customer_phone'],
							'ewallet_type' => strtoupper($payment_channel),
						];
					switch (strtoupper($payment_channel)) {
						case 'OVO':
							// Nothing as modification here
							break;
						case 'DANA':
							$this->request['data'] = array_merge($this->request['data'], [
									'callback_url' => $this->init->getCallbackURL(),
									'redirect_url' => $this->init->getReturnURL(),
								]);
							break;
						case 'LINKAJA':
							$this->request['data'] = array_merge($this->request['data'], [
									// 'items' => $this->transaction->getItem(),
									'items' => [
											[
													'id' => '1',
													'name' => $this->form['item'],
													'price' => $this->form['amount'],
													'quantity' => 1,
												],
										],
									'callback_url' => $this->init->getCallbackURL(),
									'redirect_url' => $this->init->getReturnURL(),
								]);
							break;
					}
					*/
					$this->request['url'] = SELF::CleanURL(
							$this->init->getPaymentURL() .
							'/ewallets/charges'
					);
					$this->request['data'] = [
							'reference_id' => $this->form['order_id'],
							'currency' => $this->form['currency'] ?? 'IDR',
							'amount' => (float) $this->form['amount'],
							'checkout_method' => 'ONE_TIME_PAYMENT',
							// 'basket' => [
							// 	'reference_id' => $this->form['order_id'],
							// 	'name' => $this->form['item'] ?? 'PRODUCT',
							// 	'category' => 'DIGITAL',
							// 	'currency' => $this->form['currency'] ?? 'IDR',
							// 	'price' => $this->form['amount'],
							// 	'quantity' => 1,
							// 	'type' => 'PRODUCT',
							// 	// 'url' => '',
							// 	// 'description' => '',
							// 	// 'sub_category' => '',
							// 	// 'metadata' => null,
							// ],
							// 'payment_method_id' => '',
							// 'customer_id' => '',
							// 'metadata' => [
							// 	'branch_area' => 'Jakarta',
							// 	'branch_city' => 'Jakarta',
							// ],
						];
					switch (strtoupper($payment_channel)) {
						case 'OVO':
							$this->request['data'] = array_merge($this->request['data'], [
									'channel_code' => 'ID_OVO',
									'channel_properties' => [
											'mobile_number' => $this->form['customer_phone'],
										],
								]);
							break;
						case 'DANA':
							$this->request['data'] = array_merge($this->request['data'], [
									'channel_code' => 'ID_DANA',
									'channel_properties' => [
											'success_redirect_url' => $this->init->getReturnURL(),
										],
								]);
							break;
						case 'LINKAJA':
							$this->request['data'] = array_merge($this->request['data'], [
									'channel_code' => 'ID_LINKAJA',
									'channel_properties' => [
											'success_redirect_url' => $this->init->getReturnURL(),
										],
								]);
							break;
						case 'SHOPEEPAY':
							$this->request['data'] = array_merge($this->request['data'], [
									'channel_code' => 'ID_SHOPEEPAY',
									'channel_properties' => [
											'success_redirect_url' => $this->init->getReturnURL(),
										],
								]);
							break;
					}
					break;
				case 'qris':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() . '/qr_codes'
					);
					$this->request['data'] = [
							'external_id' => $this->form['order_id'],
							'type' => 'DYNAMIC', // DYNAMIC QR code contains the payment value upon scanning and can be paid multiple times
							// 'type' => 'STATIC', // STATIC QR code requires end user to input the payment value and can be paid multiple times
							'callback_url' => $this->init->getCallbackURL(),
							'amount' => $this->form['amount'],
						];
					break;
				case 'cstore':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() . '/fixed_payment_code'
					);
					$this->request['data'] = [
							'external_id' => $this->form['order_id'],
							'retail_outlet_name' => strtoupper($payment_channel),
							'name' => $this->form['customer_name'],
							'expected_amount' => $this->form['amount'],
							// 'payment_code' => '',
							// 'expiration_date' => gmdate("Y-m-d\TH:i:s\Z", strtotime("now") + ($this->transaction->getExpireIn() * 60)),
							'expiration_date' => $expire_date,
							'is_single_use' => 'true',
						];
					break;
			}

			if (!trim($this->request['url'])) {
				throw new \Exception("URL is empty", 1);
			}

			/*
			// $this->form['payment_type'] = $this->transaction->getPaymentType();
			// $this->form['payment_method'] = strtoupper($this->transaction->getPaymentMethod());
			$this->form['payment_url'] = $this->init->getPaymentURL() . '/callback_virtual_accounts';
			$this->form['expiry_period'] = $this->transaction->getExpireIn(); // minutes

			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = $this->form['payment_url'];

			// Returns ISO8601 in proper format
			$expiration_date = gmdate("Y-m-d\TH:i:s\Z", strtotime("now") + ($this->form['expiry_period'] * 60));

			$this->request['data'] = [
					'external_id' => $this->form['order_id'],
					'bank_code' => strtoupper($payment_channel),
					'name' => $this->form['customer_name'],
					"is_closed" => true,
					"expiration_date" => $expiration_date,
					"expected_amount" => $this->form['amount']
				];
			*/

			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			// $this->request['url'] = preg_replace('#/+#','/', $this->request['url']);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':'),
				'Content-Length' => strlen(json_encode($this->request['data'])),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			// print_r($this->request);
			$post = $this->DoRequest('POST', $this->request);
			// print_r($post);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) &&
				(
					$status_code === 200
					||
					$status_code === 202
				)
			) {
				$content = (object) json_decode($content);
				// return print_r($content);
				if (
					!empty($content->status)
					&&
						(
							$content->status == 'PENDING'
							|| $content->status == 'ACTIVE'
							|| $content->status == 'REQUEST_RECEIVED'
						)
				) {
					// Success
					/*
					{
						"is_closed": true,
						"status": "PENDING",
						"currency": "IDR",
						"owner_id": "5f706881fefc961e3f708f02",
						"external_id": "VA_fixed-1604335070",
						"bank_code": "BCA",
						"merchant_code": "10766",
						"name": "Lorem Ipsum",
						"account_number": "107669999610501",
						"expected_amount": 10000,
						"expiration_date": "2020-11-08T17:00:00.000Z",
						"is_single_use": false,
						"id": "5fa035de49715e400fc114f9"
					}
					*/
					// Pending
					/*
					{
						"id": "qr_a5ddd405-5ffb-4a63-a6f8-ca51c620bea1",
						"external_id": "0008177386",
						"amount": null,
						"qr_string": "00020101021126660014ID.LINKAJA.WWW011893600911002411480002152004230411480010303UME51450015ID.OR.GPNQR.WWW02150000000000000000303UME520454995802ID5920Placeholder merchant6007Jakarta610612345662190715fhBNl5rSWmgMfwV53033606304F35B",
						"callback_url": "https://bankdev/oanwef4851ashrb/pg/dk/redapi_result",
						"type": "STATIC",
						"status": "ACTIVE",
						"created": "2020-12-17T03:56:29.685Z",
						"updated": "2020-12-17T03:56:29.685Z"
					}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					// throw new \Exception($content->status, 901);
					throw new \Exception('Parse failed (' . ($content->status ?? 'Unknown status') . ')', 901);
				}
			} else {
				// throw new \Exception($content, 902);
				throw new \Exception('Parse failed (' . ($content ?? 'Unknown error') . ')', 902);
			}
		} catch (\Throwable $e) {
			// return SELF::JSONError($e, 400);
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function Callback(object $request)
	{
		// Example incoming data callback payment
		/*
		header x-callback-token
		{
			"amount": 50000,
			"callback_virtual_account_id": "5f8e658568ed5f402a1faadb",
			"payment_id": "5f8e658f8d65ab385241f17a",
			"external_id": "demo-va-callback",
			"account_number": "9999624535",
			"merchant_code": "88608",
			"bank_code": "MANDIRI",
			"transaction_timestamp": "2020-10-20T04:20:31.000Z",
			"currency": "IDR",
			"created": "2020-10-20T04:20:31.937Z",
			"updated": "2020-10-20T04:20:33.074Z",
			"id": "5f8e658fc5710a7ae00f75db",
			"owner_id": "5c2323c67d6d305ac433ba20"
		}
		*/
		try {
			$callbackToken = $_SERVER['HTTP_X_CALLBACK_TOKEN'];
			if (strcmp($callbackToken, $this->init->getSecret()) === 0) {
				$res = [
					'status' => '000',
					'data' => (array) $request,
				];
				// $result = [
				// 		'request' => (array) $request,
				// 		'response' => [
				// 				'content' => json_encode($res),
				// 				'status_code' => 200,
				// 				// 'order_id' => $content['data']['external_id'],
				// 				// 'transaction_id' => $content['data']['payment_id'],
				// 				// 'status' => '',
				// 				// 'transaction_time' => $content['data']['transaction_timestamp'],
				// 				// 'amount' => $content['data']['amount'],
				// 				// 'bank_code' => $content['data']['bank_code'],
				// 				// 'va_number' => $content['data']['account_number'],
				// 			],
				// 	];
			} else {
				throw new \Exception('Signature check failed', 902);
			}
		} catch (\Throwable $e) {
			// throw new \Exception($this->ThrowError($e));
			$this->ThrowErrorException($e);
		}
		// return $result ?? [];
		return SELF::JSONResult($request, $res, 200);
	}

	public function CallbackAlt(object $request)
	{
		// Example incoming data notification create VA
		/*
		{
			"id": "57fb4e076fa3fa296b7f5a97",
			"owner_id": "5824128aa6f9f9b648be9d76",
			"external_id": "va-1487156410",
			"merchant_code": "88608",
			"account_number": "886081000123456",
			"bank_code": "MANDIRI",
			"name": "John Doe",
			"is_closed": false,
			"is_single_use": false,
			"status": "ACTIVE",
			"expiration_date": "2048-02-15T11:01:52.722Z",
			"updated": "2016-10-10T08:15:03.404Z",
			"created": "2016-10-10T08:15:03.404Z"
		}
		*/
		try {
			$res = [
				'status' => '000',
				'data' => (array) $request,
			];
			// $result = [
			// 		'request' => (array) $request,
			// 		'response' => [
			// 				'content' => json_encode($res),
			// 				'status_code' => 200,
			// 				// 'id' => $content['data']['id'],
			// 				// 'order_id' => $content['data']['external_id'],
			// 				// 'transaction_id' => $content['data']['owner_id'],
			// 				// 'bank_code' => $content['data']['bank_code'],
			// 				// 'va_number' => $content['data']['account_number'],
			// 				// 'status' => $content['data']['status'],
			// 				// 'expired_date' => $content['data']['expiration_date'],
			// 			],
			// 	];
		} catch (\Throwable $e) {
			// throw new \Exception($this->ThrowError($e));
			$this->ThrowErrorException($e);
		}
		// return $result ?? [];
		return SELF::JSONResult($request, $res, 200);
	}

	public function Inquiry(object $request)
	{
		try {
			SELF::Validate($request, [
				'id',
				'type_of'
			]);
			// Go
			$this->request['time'] = time();
			switch ($request->type_of) {
				case 'qris':
					$path = '/qr_codes/';
					break;
				case 'va':
					$path = '/callback_virtual_accounts/';
					break;
				case 'ewallet':
					$path = '/ewallets/charges/';
					break;
			}
			$this->request['url'] = SELF::CleanURL(
					$this->init->getRequestURL() .
					$path . $request->id
			);
			$this->request['data'] = [];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':')
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (
					!isset($content->error_code)
					&& !isset($content->message)
				) {
					// Success
					/*
					{
						"status": "000",
						"data": {
							"is_closed": true,
							"status": "ACTIVE",
							"currency": "IDR",
							"owner_id": "5f706881fefc961e3f708f02",
							"external_id": "0008115320",
							"bank_code": "BCA",
							"merchant_code": "10766",
							"name": "LOREM IPSUM",
							"account_number": "107669999020779",
							"expected_amount": 100000,
							"expiration_date": "2020-12-16T12:22:00.000Z",
							"is_single_use": false,
							"id": "5fd9e479ed81dd402014403c"
						}
					}
					*/
					$res = [
							'status' => '000',
							'data' => (array) $content,
						];
					// $result = [
					// 		'request' => (array) $request,
					// 		'response' => [
					// 				'content' => json_encode($res),
					// 				'status_code' => 200,
					// 			],
					// 	];
				} else {
					// throw new \Exception($content->message);
					throw new \Exception('Parse failed (' . ($content->message ?? 'Unknown status') . ')', 901);
				}
			} else {
				// throw new \Exception($content);
				throw new \Exception('Parse failed (' . ($content ?? 'Unknown error') . ')', 902);
			}
		} catch (\Throwable $e) {
			// throw new \Exception($this->ThrowError($e));
			$this->ThrowErrorException($e);
		}
		// return $result ?? [];
		return SELF::JSONResult($request, $res, $status_code);
	}

	// public function InquiryPayment(object $request)
	// {
	// 	try {
	// 		SELF::Validate($request, [
	// 				'payment_id',
	// 			]);
	// 		// Go
	// 		$this->request['time'] = time();
	// 		$this->request['url'] = SELF::CleanURL(
	// 			$this->init->getRequestURL() . '/callback_virtual_account_payments/payment_id=' . $request->payment_id
	// 		);
	// 		$this->request['url'] = preg_replace('#/+#','/', $this->request['url']);
	// 		$this->request['data'] = [];
	// 		$this->request['headers'] = [
	// 			'Content-Type' => 'application/json',
	// 			'Accept' => 'application/json',
	// 			'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':')
	// 		];
	// 		$get = $this->DoRequest('GET', $this->request);
	// 		$response = (array) $get['response'];
	// 		extract($response);
	// 		if (!empty($status_code) && $status_code === 200) {
	// 			$content = (object) json_decode($content);
	// 			if (
	// 				!isset($content->error_code)
	// 				&& !isset($content->message)
	// 			) {
	// 				// Success
	// 				/*
	// 				{
	// 					"status": "000",
	// 					"data": {
	// 						"payment_id": "5f9fb9758d65ab3c1141f230",
	// 						"callback_virtual_account_id": "5f9fb82dbcbf722b71041f3f",
	// 						"external_id": "VA_fixed-1604302892",
	// 						"account_number": "9999000002",
	// 						"bank_code": "MANDIRI",
	// 						"amount": 50000,
	// 						"transaction_timestamp": "2020-11-02T07:47:01.000Z",
	// 						"merchant_code": "88608",
	// 						"currency": "IDR",
	// 						"id": "5f9fb9758940c131b3d7b96d"
	// 					}
	// 				}
	// 				*/
	// 				$res = [
	// 						'status' => '000',
	// 						'data' => (array) $content,
	// 					];
	// 				$result = [
	// 						'request' => (array) $request,
	// 						'response' => [
	// 								'content' => json_encode($res),
	// 								'status_code' => 200,
	// 								// 'va_number' => $content['data']['account_number'],
	// 								// 'bank_code' => $content['data']['bank_code'],
	// 								// 'amount' => $content['data']['amount'],
	// 								// 'transaction_id' => $content['data']['payment_id'], // vendor transaction_id
	// 								// 'order_id' => $content['data']['external_id'], // PGA order_id
	// 								// 'transaction_status' => '',
	// 							],
	// 					];
	// 			} else {
	// 				throw new \Exception($content->message);
	// 			}
	// 		} else {
	// 			throw new \Exception($content);
	// 		}
	// 	} catch (\Throwable $e) {
	// 		throw new \Exception($this->ThrowError($e));
	// 	}
	// 	return $result ?? [];
	// }

	public function Cancel(object $request)
	{
		// Inapplicable
	}

	public function Settle(object $request)
	{
		// Inapplicable
	}

	public function Refund(object $request)
	{
		// Inapplicable
	}

	public function RefundStatus(object $request)
	{
		// Inapplicable
	}

	public function EncryptCardData(\PyGPaymentLib\Transaction $transaction)
	{
		if (isset($_POST['submit']) && !empty($_POST['submit'])) {
			echo $_POST['encryptedCardInfo'];
		} else {
			$secret = $this->init->getSecret();
			$card_number = $transaction->getCardNumber();
			$card_exp_month = $transaction->getCardExpMonth();
			$card_exp_year = $transaction->getCardExpYear();
			$card_cvn = (string) $transaction->getCardCVV();
			$amount = (float) 100; // $transaction->getAmount(); // Minimum amount just to verify card
			$callback_url = $this->init->getCallbackURL();
			$html = '<!DOCTYPE HTML>
<html>
<head>
	<title>Secure Payment</title>
	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="https://js.xendit.co/v1/xendit.min.js"></script>
	<script type="text/javascript">
		$(function () {
			var fileEnv = "production";
			if (fileEnv !== "production") {
				Xendit._useIntegrationURL(true);
			}

			Xendit.setPublishableKey("' . $secret . '");
			var tokenData = getTokenData();
			Xendit.card.createToken(tokenData, xenditResponseHandler);

			function getTokenData () {
				Xendit.card.validateCardNumber("' . $card_number . '");
				Xendit.card.validateExpiry("' . $card_exp_month . '", "' . $card_exp_year . '");
				Xendit.card.validateCvn("' . $card_cvn . '");
				return {
					amount: $("#form").find("#amount").val(),
					card_number: $("#form").find("#card-number").val(),
					card_exp_month: $("#form").find("#card-exp-month").val(),
					card_exp_year: $("#form").find("#card-exp-year").val(),
					card_cvn: $("#form").find("#card-cvn").val(),
					currency: $("#form").find("#currency").val(),
					billing_details: $("#form").find("#should-send-billing-details").prop("checked") ? getBillingDetails() : undefined,
					customer: $("#form").find("#should-send-customer-details").prop("checked") ? getCustomerDetails() : undefined,
					is_multiple_use: $("#form").find("#bundle-authentication").prop("checked") ? true : false,
					should_authenticate: $("#form").find("#skip-authentication").prop("checked") ? false : true,
					on_behalf_of: $("#form").find("#on-behalf-of").val(),
				};
			}
			function xenditResponseHandler (err, creditCardToken) {
				if (err) {
					return displayError(err);
				}
				if (creditCardToken.status === "VERIFIED") {
					$("#form").append($("<input type=\"hidden\" name=\"tokenData\" />").val(JSON.stringify(creditCardToken)));
					$("#submit").click();
				} else if (creditCardToken.status === "IN_REVIEW") {
					window.open(creditCardToken.payer_authentication_url, "inline-frame");
					$("#three-ds-container").show();
				} else if (creditCardToken.status === "FAILED") {
				}
				displaySuccess(creditCardToken);
			}
			function displayError (err) {
				$("#result").text(JSON.stringify(err, null, 4));
			}
			function displaySuccess (creditCardToken) {
				$("#result").text(JSON.stringify(creditCardToken, null, 4));
			}
		});
	</script>
</head>
<body>
	<form action="' . $callback_url . '" method="post" name="form" id="form">
		cardnumber: <input type="text" id="card-number" maxlength="16" placeholder="Credit Card Number" value="' . $card_number . '" /><br/> <!--name="card-number"-->
		month: <input type="text" id="card-exp-month" maxlength="2" placeholder="MM" value="' . $card_exp_month . '" /><br/> <!--name="card-exp-month"-->
		year: <input type="text" id="card-exp-year" maxlength="4" placeholder="YYYY" value="' . $card_exp_year . '" /><br/> <!--name="card-exp-year"-->
		cvv: <input type="password" id="card-cvn" maxlength="3" autocomplete="off" placeholder="CVV2/CVC2" value="' . $card_cvn . '" /><br/> <!--name="card-cvn"-->
		amount: <input type="text" id="amount" placeholder="Amount" value="' . $amount . '" /><br/> <!--name="amount"-->
		currency: <input type="text" id="currency" value="IDR" /><br/> <!--name="currency"-->
		<hr>
		Billing Details <input type="text" id="billing-details" /> <!--name="billing-details"-->
		<input type="checkbox" id="should-send-billing-details" /> Send billing details? <!--name="should-send-billing-details"--> <br />
		Customer Details <input type="text" id="customer-details" /> <!--name="customer-details"-->
		<input type="checkbox" id="should-send-customer-details" /> Send customer details? <!--name="should-send-customer-details"-->
		<hr>
		<input type="checkbox" checked="checked" id="bundle-authentication" /> Is multiple-use<br /> <!--name="bundle-authentication"-->
		<input type="checkbox" checked="checked" id="skip-authentication" /> Skip authentication<br /> <!--name="skip-authentication"-->
		On behalf of <input type="text" id="on-behalf-of" placeholder="" /> <!--name="on-behalf-of"-->
		<hr>
		<input type="submit" id="submit" value="Submit" /> <!--name="submit"-->
	</form>
	<div id="result">
	</div>
	<div id="three-ds-container" style="display: none;">
		<iframe height="450" width="550" name="inline-frame" id="inline-frame"> </iframe>
	</div>
</body>
</html>';
			echo $html;
		}
	}

}
