<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;

class Xfers extends Requestor implements VendorInterface
{

	protected $form;

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
				'/api/v4/banks'
			);
			$this->request['headers'] = [
				'Accept' => 'application/json',
				'Content-Type' => 'application/vnd.api+json',
				'Authorization' => 'Basic ' . base64_encode( $this->init->getMID() . ':' . $this->init->getSecret() ),
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
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content->data)
				) {
					/*
						{
							"data": [{
								"id": "bca",
								"type": "bank",
								"attributes": {
									"name": "Bank Central Asia",
									"shortCode": "BCA"
								}
							}, {
								"id": "mandiri",
								"type": "bank",
								"attributes": {
									"name": "Bank Mandiri",
									"shortCode": "MANDIRI"
								}
							}, {
								"id": "bni",
								"type": "bank",
								"attributes": {
									"name": "Bank Negara Indonesia",
									"shortCode": "BNI"
								}
							}, {
								"id": "permata",
								"type": "bank",
								"attributes": {
									"name": "Bank Permata",
									"shortCode": "PERMATA"
								}
							}, {
								"id": "bri",
								"type": "bank",
								"attributes": {
									"name": "Bank Rakyat Indonesia",
									"shortCode": "BRI"
								}
							}, {
								"id": "cimb_niaga",
								"type": "bank",
								"attributes": {
									"name": "CIMB Niaga Bank",
									"shortCode": "CIMB_NIAGA"
								}
							}, {
								"id": "danamon",
								"type": "bank",
								"attributes": {
									"name": "Bank Danamon",
									"shortCode": "DANAMON"
								}
							}, {
								"id": "panin",
								"type": "bank",
								"attributes": {
									"name": "Bank Panin",
									"shortCode": "PANIN"
								}
							}, {
								"id": "maybank",
								"type": "bank",
								"attributes": {
									"name": "Bank Maybank",
									"shortCode": "BII"
								}
							}, {
								"id": "anglomas",
								"type": "bank",
								"attributes": {
									"name": "Anglomas International Bank",
									"shortCode": "ANGLOMAS"
								}
							}, {
								"id": "bangkok",
								"type": "bank",
								"attributes": {
									"name": "Bangkok Bank",
									"shortCode": "BANGKOK"
								}
							}, {
								"id": "ibk",
								"type": "bank",
								"attributes": {
									"name": "Bank IBK Indonesia",
									"shortCode": "IBK"
								}
							}, {
								"id": "sinarmas",
								"type": "bank",
								"attributes": {
									"name": "Sinarmas",
									"shortCode": "SINARMAS"
								}
							}, {
								"id": "bri_agro",
								"type": "bank",
								"attributes": {
									"name": "Bank BRI Agro",
									"shortCode": "BRI_AGRO"
								}
							}, {
								"id": "andara",
								"type": "bank",
								"attributes": {
									"name": "Bank Andara",
									"shortCode": "ANDARA"
								}
							}, {
								"id": "antar_daerah",
								"type": "bank",
								"attributes": {
									"name": "Bank CCB Indonesia",
									"shortCode": "ANTAR_DAERAH"
								}
							}, {
								"id": "anz",
								"type": "bank",
								"attributes": {
									"name": "Bank ANZ Indonesia",
									"shortCode": "ANZ"
								}
							}, {
								"id": "artha",
								"type": "bank",
								"attributes": {
									"name": "Bank Artha Graha International",
									"shortCode": "ARTHA"
								}
							}, {
								"id": "artos",
								"type": "bank",
								"attributes": {
									"name": "Bank Artos Indonesia",
									"shortCode": "ARTOS"
								}
							}, {
								"id": "bisnis",
								"type": "bank",
								"attributes": {
									"name": "Bank Bisnis Internasional",
									"shortCode": "BISNIS"
								}
							}, {
								"id": "bjb",
								"type": "bank",
								"attributes": {
									"name": "Bank BJB",
									"shortCode": "BJB"
								}
							}, {
								"id": "bnp",
								"type": "bank",
								"attributes": {
									"name": "Bank BNP Paribas",
									"shortCode": "BNP"
								}
							}, {
								"id": "bukopin",
								"type": "bank",
								"attributes": {
									"name": "Bank Bukopin",
									"shortCode": "BUKOPIN"
								}
							}, {
								"id": "bumi_arta",
								"type": "bank",
								"attributes": {
									"name": "Bank Bumi Arta",
									"shortCode": "BUMI_ARTA"
								}
							}, {
								"id": "capital",
								"type": "bank",
								"attributes": {
									"name": "Bank Capital Indonesia",
									"shortCode": "CAPITAL"
								}
							}, {
								"id": "bca_syr",
								"type": "bank",
								"attributes": {
									"name": "Bank Central Asia (BCA) Syariah",
									"shortCode": "BCA_SYR"
								}
							}, {
								"id": "chinatrust",
								"type": "bank",
								"attributes": {
									"name": "Bank Chinatrust Indonesia",
									"shortCode": "CHINATRUST"
								}
							}, {
								"id": "cimb_uus",
								"type": "bank",
								"attributes": {
									"name": "Bank CIMB Niaga UUS",
									"shortCode": "CIMB_UUS"
								}
							}, {
								"id": "commonwealth",
								"type": "bank",
								"attributes": {
									"name": "Bank Commonwealth",
									"shortCode": "COMMONWEALTH"
								}
							}, {
								"id": "danamon_uus",
								"type": "bank",
								"attributes": {
									"name": "Bank Danamon UUS",
									"shortCode": "DANAMON_UUS"
								}
							}, {
								"id": "dbs",
								"type": "bank",
								"attributes": {
									"name": "Bank DBS Indonesia",
									"shortCode": "DBS"
								}
							}, {
								"id": "dinar_indonesia",
								"type": "bank",
								"attributes": {
									"name": "Bank Dinar Indonesia",
									"shortCode": "DINAR_INDONESIA"
								}
							}, {
								"id": "oke",
								"type": "bank",
								"attributes": {
									"name": "Bank OKE",
									"shortCode": "OKE"
								}
							}, {
								"id": "dki",
								"type": "bank",
								"attributes": {
									"name": "Bank DKI",
									"shortCode": "DKI"
								}
							}, {
								"id": "dki_uus",
								"type": "bank",
								"attributes": {
									"name": "Bank DKI UUS",
									"shortCode": "DKI_UUS"
								}
							}, {
								"id": "fama",
								"type": "bank",
								"attributes": {
									"name": "Bank Fama International",
									"shortCode": "FAMA"
								}
							}, {
								"id": "ganesha",
								"type": "bank",
								"attributes": {
									"name": "Bank Ganesha",
									"shortCode": "GANESHA"
								}
							}, {
								"id": "hana",
								"type": "bank",
								"attributes": {
									"name": "Bank Hana",
									"shortCode": "HANA"
								}
							}, {
								"id": "harda_internasional",
								"type": "bank",
								"attributes": {
									"name": "Bank Harda Internasional",
									"shortCode": "HARDA_INTERNASIONAL"
								}
							}, {
								"id": "woori_saudara",
								"type": "bank",
								"attributes": {
									"name": "Bank Woori Saudara",
									"shortCode": "WOORI_SAUDARA"
								}
							}, {
								"id": "icbc",
								"type": "bank",
								"attributes": {
									"name": "Bank ICBC Indonesia",
									"shortCode": "ICBC"
								}
							}, {
								"id": "ina_perdana",
								"type": "bank",
								"attributes": {
									"name": "Bank Ina Perdana",
									"shortCode": "INA_PERDANA"
								}
							}, {
								"id": "index_selindo",
								"type": "bank",
								"attributes": {
									"name": "Bank Index Selindo",
									"shortCode": "INDEX_SELINDO"
								}
							}, {
								"id": "jasa_jakarta",
								"type": "bank",
								"attributes": {
									"name": "Bank Jasa Jakarta",
									"shortCode": "JASA_JAKARTA"
								}
							}, {
								"id": "kesejahteraan_ekonomi",
								"type": "bank",
								"attributes": {
									"name": "Bank Kesejahteraan Ekonomi",
									"shortCode": "KESEJAHTERAAN_EKONOMI"
								}
							}, {
								"id": "maspion",
								"type": "bank",
								"attributes": {
									"name": "Bank Maspion Indonesia",
									"shortCode": "MASPION"
								}
							}, {
								"id": "mayapada",
								"type": "bank",
								"attributes": {
									"name": "Bank Mayapada International",
									"shortCode": "MAYAPADA"
								}
							}, {
								"id": "maybank_syr",
								"type": "bank",
								"attributes": {
									"name": "Bank Maybank Syariah Indonesia",
									"shortCode": "MAYBANK_SYR"
								}
							}, {
								"id": "mayora",
								"type": "bank",
								"attributes": {
									"name": "Bank Mayora",
									"shortCode": "MAYORA"
								}
							}, {
								"id": "mega",
								"type": "bank",
								"attributes": {
									"name": "Bank Mega",
									"shortCode": "MEGA"
								}
							}, {
								"id": "mestika_dharma",
								"type": "bank",
								"attributes": {
									"name": "Bank Mestika Dharma",
									"shortCode": "MESTIKA_DHARMA"
								}
							}, {
								"id": "shinhan",
								"type": "bank",
								"attributes": {
									"name": "Bank Shinhan Indonesia",
									"shortCode": "SHINHAN"
								}
							}, {
								"id": "mizuho",
								"type": "bank",
								"attributes": {
									"name": "Bank Mizuho Indonesia",
									"shortCode": "MIZUHO"
								}
							}, {
								"id": "mnc_internasional",
								"type": "bank",
								"attributes": {
									"name": "Bank MNC Internasional",
									"shortCode": "MNC_INTERNASIONAL"
								}
							}, {
								"id": "muamalat",
								"type": "bank",
								"attributes": {
									"name": "Bank Muamalat Indonesia",
									"shortCode": "MUAMALAT"
								}
							}, {
								"id": "multi_arta_sentosa",
								"type": "bank",
								"attributes": {
									"name": "Bank Multi Arta Sentosa",
									"shortCode": "MULTI_ARTA_SENTOSA"
								}
							}, {
								"id": "j_trust",
								"type": "bank",
								"attributes": {
									"name": "Bank J Trust Indonesia",
									"shortCode": "J_TRUST"
								}
							}, {
								"id": "nationalnobu",
								"type": "bank",
								"attributes": {
									"name": "Bank Nationalnobu",
									"shortCode": "NATIONALNOBU"
								}
							}, {
								"id": "nusantara_parahyangan",
								"type": "bank",
								"attributes": {
									"name": "Bank Nusantara Parahyangan",
									"shortCode": "NUSANTARA_PARAHYANGAN"
								}
							}, {
								"id": "ocbc",
								"type": "bank",
								"attributes": {
									"name": "Bank OCBC NISP",
									"shortCode": "OCBC"
								}
							}, {
								"id": "ocbc_uus",
								"type": "bank",
								"attributes": {
									"name": "Bank OCBC NISP UUS",
									"shortCode": "OCBC_UUS"
								}
							}, {
								"id": "baml",
								"type": "bank",
								"attributes": {
									"name": "Bank of America Merill-Lynch",
									"shortCode": "BAML"
								}
							}, {
								"id": "boc",
								"type": "bank",
								"attributes": {
									"name": "Bank of China (BOC)",
									"shortCode": "BOC"
								}
							}, {
								"id": "india",
								"type": "bank",
								"attributes": {
									"name": "Bank of India Indonesia",
									"shortCode": "INDIA"
								}
							}, {
								"id": "tokyo",
								"type": "bank",
								"attributes": {
									"name": "Bank of Tokyo Mitsubishi UFJ",
									"shortCode": "TOKYO"
								}
							}, {
								"id": "panin_syr",
								"type": "bank",
								"attributes": {
									"name": "Bank Panin Syariah",
									"shortCode": "PANIN_SYR"
								}
							}, {
								"id": "permata_uus",
								"type": "bank",
								"attributes": {
									"name": "Bank Permata UUS",
									"shortCode": "PERMATA_UUS"
								}
							}, {
								"id": "qnb",
								"type": "bank",
								"attributes": {
									"name": "Bank QNB Indonesia",
									"shortCode": "QNB"
								}
							}, {
								"id": "rabobank",
								"type": "bank",
								"attributes": {
									"name": "Bank Rabobank International Indonesia",
									"shortCode": "RABOBANK"
								}
							}, {
								"id": "resona",
								"type": "bank",
								"attributes": {
									"name": "Bank Resona Perdania",
									"shortCode": "RESONA"
								}
							}, {
								"id": "royal",
								"type": "bank",
								"attributes": {
									"name": "Bank Royal Indonesia",
									"shortCode": "ROYAL"
								}
							}, {
								"id": "btpn_syr",
								"type": "bank",
								"attributes": {
									"name": "Bank BTPN Syariah",
									"shortCode": "BTPN_SYR"
								}
							}, {
								"id": "sahabat_sampoerna",
								"type": "bank",
								"attributes": {
									"name": "Bank Sahabat Sampoerna",
									"shortCode": "SAHABAT_SAMPOERNA"
								}
							}, {
								"id": "sbi_indonesia",
								"type": "bank",
								"attributes": {
									"name": "Bank SBI Indonesia",
									"shortCode": "SBI_INDONESIA"
								}
							}, {
								"id": "mandiri_taspen",
								"type": "bank",
								"attributes": {
									"name": "Bank Mandiri Taspen",
									"shortCode": "MANDIRI_TASPEN"
								}
							}, {
								"id": "mitsui",
								"type": "bank",
								"attributes": {
									"name": "Bank Sumitomo Mitsui Indonesia",
									"shortCode": "MITSUI"
								}
							}, {
								"id": "bukopin_syr",
								"type": "bank",
								"attributes": {
									"name": "Bank Syariah Bukopin",
									"shortCode": "BUKOPIN_SYR"
								}
							}, {
								"id": "mandiri_syr",
								"type": "bank",
								"attributes": {
									"name": "Bank Syariah Mandiri",
									"shortCode": "MANDIRI_SYR"
								}
							}, {
								"id": "mega_syr",
								"type": "bank",
								"attributes": {
									"name": "Bank Syariah Mega",
									"shortCode": "MEGA_SYR"
								}
							}, {
								"id": "btn",
								"type": "bank",
								"attributes": {
									"name": "Bank Tabungan Negara (BTN)",
									"shortCode": "BTN"
								}
							}, {
								"id": "btn_uus",
								"type": "bank",
								"attributes": {
									"name": "Bank Tabungan Negara (BTN) UUS",
									"shortCode": "BTN_UUS"
								}
							}, {
								"id": "tabungan_pensiunan_nasional",
								"type": "bank",
								"attributes": {
									"name": "Bank Tabungan Pensiunan Nasional",
									"shortCode": "TABUNGAN_PENSIUNAN_NASIONAL"
								}
							}, {
								"id": "tabungan_pensiunan_nasional_uus",
								"type": "bank",
								"attributes": {
									"name": "Bank Tabungan Pensiunan Nasional UUS",
									"shortCode": "TABUNGAN_PENSIUNAN_NASIONAL_UUS"
								}
							}, {
								"id": "uob",
								"type": "bank",
								"attributes": {
									"name": "Bank UOB Indonesia",
									"shortCode": "UOB"
								}
							}, {
								"id": "victoria_internasional",
								"type": "bank",
								"attributes": {
									"name": "Bank Victoria Internasional",
									"shortCode": "VICTORIA_INTERNASIONAL"
								}
							}, {
								"id": "victoria_syr",
								"type": "bank",
								"attributes": {
									"name": "Bank Victoria Syariah",
									"shortCode": "VICTORIA_SYR"
								}
							}, {
								"id": "ccb",
								"type": "bank",
								"attributes": {
									"name": "Bank CCB Indonesia",
									"shortCode": "CCB"
								}
							}, {
								"id": "woori",
								"type": "bank",
								"attributes": {
									"name": "Bank Woori Indonesia",
									"shortCode": "WOORI"
								}
							}, {
								"id": "yudha_bhakti",
								"type": "bank",
								"attributes": {
									"name": "Bank Yudha Bhakti",
									"shortCode": "YUDHA_BHAKTI"
								}
							}, {
								"id": "aceh",
								"type": "bank",
								"attributes": {
									"name": "BPD Aceh",
									"shortCode": "ACEH"
								}
							}, {
								"id": "aceh_uus",
								"type": "bank",
								"attributes": {
									"name": "BPD Aceh UUS",
									"shortCode": "ACEH_UUS"
								}
							}, {
								"id": "banten",
								"type": "bank",
								"attributes": {
									"name": "BPD Banten",
									"shortCode": "BANTEN"
								}
							}, {
								"id": "bali",
								"type": "bank",
								"attributes": {
									"name": "BPD Bali",
									"shortCode": "BALI"
								}
							}, {
								"id": "bengkulu",
								"type": "bank",
								"attributes": {
									"name": "BPD Bengkulu",
									"shortCode": "BENGKULU"
								}
							}, {
								"id": "bpd_diy",
								"type": "bank",
								"attributes": {
									"name": "Bank Pembangunan Daerah (BPD DIY)",
									"shortCode": "BPD_DIY"
								}
							}, {
								"id": "bpd_diy_syr",
								"type": "bank",
								"attributes": {
									"name": "Bank Pembangunan Daerah (BPD DIY) Syariah",
									"shortCode": "BPD_DIY_SYR"
								}
							}, {
								"id": "jambi",
								"type": "bank",
								"attributes": {
									"name": "BPD Jambi",
									"shortCode": "JAMBI"
								}
							}, {
								"id": "jambi_uus",
								"type": "bank",
								"attributes": {
									"name": "BPD Jambi UUS",
									"shortCode": "JAMBI_UUS"
								}
							}, {
								"id": "jawa_tengah",
								"type": "bank",
								"attributes": {
									"name": "BPD Jawa Tengah",
									"shortCode": "JAWA_TENGAH"
								}
							}, {
								"id": "jawa_tengah_uus",
								"type": "bank",
								"attributes": {
									"name": "BPD Jawa Tengah UUS",
									"shortCode": "JAWA_TENGAH_UUS"
								}
							}, {
								"id": "jawa_timur",
								"type": "bank",
								"attributes": {
									"name": "BPD Jawa Timur",
									"shortCode": "JAWA_TIMUR"
								}
							}, {
								"id": "jawa_timur_uus",
								"type": "bank",
								"attributes": {
									"name": "BPD Jawa Timur UUS",
									"shortCode": "JAWA_TIMUR_UUS"
								}
							}, {
								"id": "kalimantan_barat",
								"type": "bank",
								"attributes": {
									"name": "BPD Kalimantan Barat",
									"shortCode": "KALIMANTAN_BARAT"
								}
							}, {
								"id": "kalimantan_barat_uus",
								"type": "bank",
								"attributes": {
									"name": "BPD Kalimantan Barat UUS",
									"shortCode": "KALIMANTAN_BARAT_UUS"
								}
							}, {
								"id": "kalimantan_selatan",
								"type": "bank",
								"attributes": {
									"name": "BPD Kalimantan Selatan",
									"shortCode": "KALIMANTAN_SELATAN"
								}
							}, {
								"id": "kalimantan_selatan_uus",
								"type": "bank",
								"attributes": {
									"name": "BPD Kalimantan Selatan UUS",
									"shortCode": "KALIMANTAN_SELATAN_UUS"
								}
							}, {
								"id": "kalimantan_tengah",
								"type": "bank",
								"attributes": {
									"name": "BPD Kalimantan Tengah",
									"shortCode": "KALIMANTAN_TENGAH"
								}
							}, {
								"id": "kalimantan_timur",
								"type": "bank",
								"attributes": {
									"name": "BPD Kalimantan Timur",
									"shortCode": "KALIMANTAN_TIMUR"
								}
							}, {
								"id": "kalimantan_timur_uus",
								"type": "bank",
								"attributes": {
									"name": "BPD Kalimantan Timur UUS",
									"shortCode": "KALIMANTAN_TIMUR_UUS"
								}
							}, {
								"id": "lampung",
								"type": "bank",
								"attributes": {
									"name": "BPD Lampung",
									"shortCode": "LAMPUNG"
								}
							}, {
								"id": "maluku",
								"type": "bank",
								"attributes": {
									"name": "BPD Maluku",
									"shortCode": "MALUKU"
								}
							}, {
								"id": "nusa_tenggara_barat",
								"type": "bank",
								"attributes": {
									"name": "BPD Nusa Tenggara Barat",
									"shortCode": "NUSA_TENGGARA_BARAT"
								}
							}, {
								"id": "nusa_tenggara_barat_uus",
								"type": "bank",
								"attributes": {
									"name": "BPD Nusa Tenggara Barat UUS",
									"shortCode": "NUSA_TENGGARA_BARAT_UUS"
								}
							}, {
								"id": "nusa_tenggara_timur",
								"type": "bank",
								"attributes": {
									"name": "BPD Nusa Tenggara Timur",
									"shortCode": "NUSA_TENGGARA_TIMUR"
								}
							}, {
								"id": "papua",
								"type": "bank",
								"attributes": {
									"name": "BPD Papua",
									"shortCode": "PAPUA"
								}
							}, {
								"id": "riau_dan_kepri",
								"type": "bank",
								"attributes": {
									"name": "BPD Riau Dan Kepri",
									"shortCode": "RIAU_DAN_KEPRI"
								}
							}, {
								"id": "riau_dan_kepri_uus",
								"type": "bank",
								"attributes": {
									"name": "BPD Riau Dan Kepri UUS",
									"shortCode": "RIAU_DAN_KEPRI_UUS"
								}
							}, {
								"id": "sulawesi",
								"type": "bank",
								"attributes": {
									"name": "BPD Sulawesi Tengah",
									"shortCode": "SULAWESI"
								}
							}, {
								"id": "sulawesi_tenggara",
								"type": "bank",
								"attributes": {
									"name": "BPD Sulawesi Tenggara",
									"shortCode": "SULAWESI_TENGGARA"
								}
							}, {
								"id": "sulselbar",
								"type": "bank",
								"attributes": {
									"name": "BPD Sulselbar",
									"shortCode": "SULSELBAR"
								}
							}, {
								"id": "sulselbar_uus",
								"type": "bank",
								"attributes": {
									"name": "BPD Sulselbar UUS",
									"shortCode": "SULSELBAR_UUS"
								}
							}, {
								"id": "sulutgo",
								"type": "bank",
								"attributes": {
									"name": "Bank Sulutgo",
									"shortCode": "SULUTGO"
								}
							}, {
								"id": "sumatera_barat",
								"type": "bank",
								"attributes": {
									"name": "BPD Sumatera Barat",
									"shortCode": "SUMATERA_BARAT"
								}
							}, {
								"id": "sumatera_barat_uus",
								"type": "bank",
								"attributes": {
									"name": "BPD Sumatera Barat UUS",
									"shortCode": "SUMATERA_BARAT_UUS"
								}
							}, {
								"id": "sumsel_dan_babel",
								"type": "bank",
								"attributes": {
									"name": "BPD Sumsel Dan Babel",
									"shortCode": "SUMSEL_DAN_BABEL"
								}
							}, {
								"id": "sumsel_dan_babel_uus",
								"type": "bank",
								"attributes": {
									"name": "BPD Sumsel Dan Babel UUS",
									"shortCode": "SUMSEL_DAN_BABEL_UUS"
								}
							}, {
								"id": "sumut",
								"type": "bank",
								"attributes": {
									"name": "BPD Sumut",
									"shortCode": "SUMUT"
								}
							}, {
								"id": "sumut_uus",
								"type": "bank",
								"attributes": {
									"name": "BPD Sumut UUS",
									"shortCode": "SUMUT_UUS"
								}
							}, {
								"id": "centratama",
								"type": "bank",
								"attributes": {
									"name": "Centratama Nasional Bank",
									"shortCode": "CENTRATAMA"
								}
							}, {
								"id": "citibank",
								"type": "bank",
								"attributes": {
									"name": "Citibank",
									"shortCode": "CITIBANK"
								}
							}, {
								"id": "deutsche",
								"type": "bank",
								"attributes": {
									"name": "Deutsche Bank",
									"shortCode": "DEUTSCHE"
								}
							}, {
								"id": "hsbc",
								"type": "bank",
								"attributes": {
									"name": "Hongkong and Shanghai Bank Corporation (HSBC)",
									"shortCode": "HSBC"
								}
							}, {
								"id": "hsbc_uus",
								"type": "bank",
								"attributes": {
									"name": "Hongkong and Shanghai Bank Corporation (HSBC) UUS",
									"shortCode": "HSBC_UUS"
								}
							}, {
								"id": "jpmorgan",
								"type": "bank",
								"attributes": {
									"name": "JP Morgan Chase Bank",
									"shortCode": "JPMORGAN"
								}
							}, {
								"id": "prima_master",
								"type": "bank",
								"attributes": {
									"name": "Prima Master Bank",
									"shortCode": "PRIMA_MASTER"
								}
							}, {
								"id": "standard_chartered",
								"type": "bank",
								"attributes": {
									"name": "Standard Charted Bank",
									"shortCode": "STANDARD_CHARTERED"
								}
							}, {
								"id": "mitra_niaga",
								"type": "bank",
								"attributes": {
									"name": "Bank Mitra Niaga",
									"shortCode": "MITRA_NIAGA"
								}
							}, {
								"id": "ekspor_indonesia",
								"type": "bank",
								"attributes": {
									"name": "Bank Ekspor Indonesia",
									"shortCode": "EKSPOR_INDONESIA"
								}
							}, {
								"id": "arta_niaga_kencana",
								"type": "bank",
								"attributes": {
									"name": "Bank Arta Niaga Kencana",
									"shortCode": "ARTA_NIAGA_KENCANA"
								}
							}, {
								"id": "bjb_syr",
								"type": "bank",
								"attributes": {
									"name": "Bank BJB Syariah",
									"shortCode": "BJB_SYR"
								}
							}, {
								"id": "bpr_ks",
								"type": "bank",
								"attributes": {
									"name": "BPR Karyajatnika Sadaya",
									"shortCode": "BPR_KS"
								}
							}, {
								"id": "sobatku",
								"type": "bank",
								"attributes": {
									"name": "KSP SAHABAT MITRA SEJATI",
									"shortCode": "SOBATKU"
								}
							}, {
								"id": "danagung_abadi",
								"type": "bank",
								"attributes": {
									"name": "BPR Danagung Abadi",
									"shortCode": "DANAGUNG_ABADI"
								}
							}, {
								"id": "danagung_bakti",
								"type": "bank",
								"attributes": {
									"name": "BPR Danagung Bakti",
									"shortCode": "DANAGUNG_BAKTI"
								}
							}, {
								"id": "danagung_ramulti",
								"type": "bank",
								"attributes": {
									"name": "BPR Danagung Ramulti",
									"shortCode": "DANAGUNG_RAMULTI"
								}
							}, {
								"id": "bsi",
								"type": "bank",
								"attributes": {
									"name": "Bank Syariah Indonesia",
									"shortCode": "BSI"
								}
							}, {
								"id": "bii",
								"type": "bank",
								"attributes": {
									"name": "Bank International Indonesia",
									"shortCode": "BII"
								}
							}, {
								"id": "agris",
								"type": "bank",
								"attributes": {
									"name": "Bank Agris",
									"shortCode": "AGRIS"
								}
							}, {
								"id": "agroniaga",
								"type": "bank",
								"attributes": {
									"name": "Bank Agroniaga",
									"shortCode": "AGRONIAGA"
								}
							}, {
								"id": "himpunan_saudara",
								"type": "bank",
								"attributes": {
									"name": "Bank Himpunan Saudara 1906",
									"shortCode": "HIMPUNAN_SAUDARA"
								}
							}, {
								"id": "metro_express",
								"type": "bank",
								"attributes": {
									"name": "Bank Metro Express",
									"shortCode": "METRO_EXPRESS"
								}
							}, {
								"id": "mutiara",
								"type": "bank",
								"attributes": {
									"name": "Bank Mutiara",
									"shortCode": "MUTIARA"
								}
							}, {
								"id": "qnb_kesawan",
								"type": "bank",
								"attributes": {
									"name": "Bank QNB Kesawan",
									"shortCode": "QNB_KESAWAN"
								}
							}, {
								"id": "sahabat_purba_danarta",
								"type": "bank",
								"attributes": {
									"name": "Bank Sahabat Purba Danarta",
									"shortCode": "SAHABAT_PURBA_DANARTA"
								}
							}, {
								"id": "sinar_harapan_bali",
								"type": "bank",
								"attributes": {
									"name": "Bank Sinar Harapan Bali",
									"shortCode": "SINAR_HARAPAN_BALI"
								}
							}, {
								"id": "windu",
								"type": "bank",
								"attributes": {
									"name": "Bank Windu Kentjana Int",
									"shortCode": "WINDU"
								}
							}, {
								"id": "sulut",
								"type": "bank",
								"attributes": {
									"name": "BPD Sulut",
									"shortCode": "SULUT"
								}
							}, {
								"id": "bni_uus",
								"type": "bank",
								"attributes": {
									"name": "Bank Negara Indonesia 1946 (UUS)",
									"shortCode": "BNI_UUS"
								}
							}, {
								"id": "bii_uus",
								"type": "bank",
								"attributes": {
									"name": "Bank International Indonesia (UUS)",
									"shortCode": "BII_UUS"
								}
							}, {
								"id": "bjb_uus",
								"type": "bank",
								"attributes": {
									"name": "Bank BJB (UUS)",
									"shortCode": "BJB_UUS"
								}
							}, {
								"id": "pundi_indonesia",
								"type": "bank",
								"attributes": {
									"name": "Bank Eksekutif Internasional",
									"shortCode": "PUNDI_INDONESIA"
								}
							}, {
								"id": "abn_amro",
								"type": "bank",
								"attributes": {
									"name": "Bank ABN Amro",
									"shortCode": "ABN_AMRO"
								}
							}, {
								"id": "kebd",
								"type": "bank",
								"attributes": {
									"name": "Korea Exchange Bank Danamon",
									"shortCode": "KEBD"
								}
							}, {
								"id": "barclays",
								"type": "bank",
								"attributes": {
									"name": "Bank Barclays Indonesia",
									"shortCode": "BARCLAYS"
								}
							}, {
								"id": "ocbc_indonesia",
								"type": "bank",
								"attributes": {
									"name": "Bank OCBC - Indonesia",
									"shortCode": "OCBC_INDONESIA"
								}
							}]
						}
					*/
					$res = [
						'status' => '000',
						'data' => array_map(function($item) {
							return [
								'id' => $item->id,
								'type' => $item->type,
								'name' => $item->attributes->name,
								'code' => strtoupper($item->attributes->shortCode),
							];
						}, $content->data),
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

	public function CheckBalance()
	{
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/v4/overviews/balance_overview'
			);
			$this->request['headers'] = [
				'Accept' => 'application/json',
				'Content-Type' => 'application/vnd.api+json',
				'Authorization' => 'Basic ' . base64_encode( $this->init->getMID() . ':' . $this->init->getSecret() ),
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
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content->data)
				) {
					/*
					{
						"data": {
							"attributes": {
								"totalBalance": "5045825.0",
								"availableBalance": "4943825.0",
								"pendingBalance": "102000.0"
							}
						}
					}
					*/
					$res = [
						'status' => '000',
						'data' => [
							'total_balance' => $content->data->attributes->totalBalance,
							'available_balance' => $content->data->attributes->availableBalance,
							'pending_balance' => $content->data->attributes->pendingBalance,
						]
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

	public function AccountInquiry(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->request['data'] = [
				'data' => [
					'attributes' => [
						'accountNo' => $transaction->getCustomerBankAccountNumber(),
						'bankShortCode' => $transaction->getCustomerBankName(),
					],
				],
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/v4/validation_services/bank_account_validation'
			);
			$this->request['headers'] = [
				'Accept' => 'application/json',
				'Content-Type' => 'application/vnd.api+json',
				'Authorization' => 'Basic ' . base64_encode( $this->init->getMID() . ':' . $this->init->getSecret() ),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 201 // Created
			) {
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content->data)
				) {
					/*
					{
						"data": {
							"attributes": {
								"accountName": "PROD ONLY",
								"accountNo": "6044222099",
								"bankShortCode": "BCA"
							}
						}
					}
					*/
					$res = [
						'status' => '000',
						'data' => [
							'customer_bank_name' => $content->data->attributes->bankShortCode,
							'customer_bank_account_name' => $content->data->attributes->accountName,
							'customer_bank_account_number' => $content->data->attributes->accountNo,
						]
					];
				} else {
					throw new \Exception('Data is empty', 901);
				}
			} else {
				throw new \Exception('Status code is not 201', 902);
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
				'data' => [
					'attributes' => [
						'amount' => $transaction->getAmount(),
						'referenceId' => $transaction->getInvoiceNo(),
						'description' => $transaction->getDescription(),
						'disbursementMethod' => [
							'type' => 'bank_transfer',
							'bankShortCode' => $transaction->getCustomerBankName(),
							'bankAccountNo' => $transaction->getCustomerBankAccountNumber(),
							'bankAccountHolderName' => $transaction->getCustomerBankName(),
						],
					],
				],
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/v4/disbursements'
			);
			$this->request['headers'] = [
				'Accept' => 'application/json',
				'Content-Type' => 'application/vnd.api+json',
				'Authorization' => 'Basic ' . base64_encode( $this->init->getMID() . ':' . $this->init->getSecret() ),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 201 // Created
			) {
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content->data)
					&& $content->data->attributes->status == 'processing'
				) {
					/*
					{
						"data": {
							"id": "contract_ebf607aa60cb48baba5c62169595e202",
							"type": "disbursement",
							"attributes": {
								"referenceId": "TT20220520153840",
								"description": "Test Transfer",
								"amount": "10000.0",
								"status": "processing",
								"createdAt": "2022-05-20T15:38:41+07:00",
								"fees": "3885.0",
								"disbursementMethod": {
									"type": "bank_transfer",
									"bankAccountNo": "6044222099",
									"bankShortCode": "BCA",
									"bankName": "Bank Central Asia",
									"bankAccountHolderName": "BCA",
									"serverBankAccountHolderName": "PROD ONLY",
									"failureReason": ""
								}
							}
						}
					}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content->data,
					];
				} else {
					throw new \Exception('Data is empty', 901);
				}
			} else {
				throw new \Exception('Status code is not 201', 902);
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
				'/api/v4/disbursements/' . $request->id
			);
			$this->request['headers'] = [
				'Accept' => 'application/json',
				'Content-Type' => 'application/vnd.api+json',
				'Authorization' => 'Basic ' . base64_encode( $this->init->getMID() . ':' . $this->init->getSecret() ),
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
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content->data)
				) {
					/* // Success
					{
						"data": {
							"id": "contract_0574f4df86d3422fba33f58588f47194",
							"type": "disbursement",
							"attributes": {
								"referenceId": "TT20220601224051",
								"description": "Test Transfer",
								"amount": "10000.0",
								"status": "completed",
								"createdAt": "2022-06-01T22:40:51+07:00",
								"fees": "3330.0",
								"disbursementMethod": {
									"type": "bank_transfer",
									"bankAccountNo": "6044222099",
									"bankShortCode": "BCA",
									"bankName": "Bank Central Asia",
									"bankAccountHolderName": "BCA",
									"serverBankAccountHolderName": "MAJU SELARAS KOMPUTER PT",
									"failureReason": "",
									"bankReferenceNumber": "08419480"
								}
							}
						}
					}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content->data,
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
			$this->form['amount'] = (int) $this->transaction->getAmount();
			$this->form['description'] = $this->transaction->getDescription();
			//
			$this->form['customer_name'] = $this->transaction->getCustomerName();
			$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			$this->form['customer_address'] = $this->transaction->getCustomerAddress();
			$this->form['country_code'] = $this->transaction->getCountryCode();
			//
			$arr = explode(',', $this->transaction->getPaymentMethod());
			$payment_method = strtolower(trim( $arr[0] ?? '' ));
			$payment_channel = strtolower(trim( $arr[1] ?? '' ));
			//
			// Default 2 hours
			// $this->form['expired_at'] = gmdate("Y-m-d\TH:i:s",  $this->transaction->getTime() + (3600 * ( 7 + $this->transaction->getExpireIn()))) . '+07:00';
			$ISO8601_date = new \DateTime();
			$ISO8601_date->setTimestamp(time() + (
				$this->transaction->getExpireIn() * 60 * 60
			));
			$this->form['expired_at'] = $ISO8601_date->format('c');
			$this->form['expired_in'] = $this->transaction->getExpireIn();
			//
			switch ($payment_method) {
				case 'va':
					$this->form['payment_method'] = 'virtual_bank_account';
					$this->form['payment_method_options'] = [
						'bankShortCode' => $payment_channel,
						'displayName' => $this->form['description'],
						// 'suffixNo' => '7890', // Causes duplicate
					];
					break;
				case 'qris':
					$this->form['payment_method'] = 'qris';
					$this->form['payment_method_options'] = [
						'displayName' => $this->form['description'],
					];
					break;
				case 'cstore':
					$this->form['payment_method'] = 'retail_outlet';
					$this->form['payment_method_options'] = [
						'retailOutletName' => $payment_channel,
					];
					break;
				case 'ewallet':
					$this->form['payment_method'] = 'e-wallet';
					$this->form['payment_method_options'] = [
						'providerCode' => 'SHOPEEPAY',
						'afterSettlementReturnUrl' => $this->init->getReturnURL(),
					];
					// Shopee only allows 1 hours max
					// $this->form['expired_at'] = gmdate("Y-m-d\TH:i:s",  $this->transaction->getTime() + (3600 * ( 7 + 0.8))) . '+07:00';
					$ISO8601_date->setTimestamp(time() + (
						1 * 60 * 60
					));
					$this->form['expired_at'] = $ISO8601_date->format('c');
					$this->form['expired_in'] = 1;
					break;
				default:
					throw new \Exception('Payment method not defined/unknown', 900);
					break;
			}
			// Go
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/api/v4/payments'
			);
			$this->request['data'] = [
				'data' => [
					'attributes' => [
						'paymentMethodType' => $this->form['payment_method'],
						'amount' => $this->form['amount'],
						'referenceId' => $this->form['order_id'],
						'expiredAt' => $this->form['expired_at'],
						'description' => $this->form['description'],
						'paymentMethodOptions' => $this->form['payment_method_options'],
					],
				],
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/vnd.api+json',
				'Authorization' => 'Basic ' . base64_encode( $this->init->getMID() . ':' . $this->init->getSecret() ),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (
				!empty($status_code)
				&& $status_code === 201 // Created
			) {
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content->data->attributes)
					&& $content->data->attributes->status == 'pending'
				) {
					/* // Success VA BCA
					{
						"data": {
							"id": "contract_6380dc30bc034fabadc02091696df9ac",
							"type": "payment",
							"attributes": {
								"status": "pending",
								"amount": "755200.0",
								"createdAt": "2021-07-16T15:53:06+07:00",
								"description": "Voucher #40",
								"expiredAt": "2021-07-16T10:53:06",
								"referenceId": "0026425586",
								"fees": "3630.0",
								"paymentMethod": {
									"id": "va_373aae4ecde3d4a4f3bd338dc5e53f63",
									"type": "virtual_bank_account",
									"referenceId": "0026425586",
									"instructions": {
										"bankShortCode": "BCA",
										"accountNo": "1304430200007890",
										"displayName": "Voucher #40"
									}
								}
							}
						}
					}
					*/
					/* // Success ALFAMART
					{
						"data": {
							"id": "contract_a02fe9a330714c869ab89ce324bf94f3",
							"type": "payment",
							"attributes": {
								"status": "pending",
								"amount": "671500.0",
								"createdAt": "2021-07-16T15:53:42+07:00",
								"description": "Voucher #72",
								"expiredAt": "2021-07-16T10:53:42",
								"referenceId": "0026425622",
								"fees": "4400.0",
								"paymentMethod": {
									"id": "4a9d0f053edccc3b8e7c08a45111c2fa",
									"type": "retail_outlet",
									"referenceId": "0026425622",
									"instructions": {
										"retailOutletName": "ALFAMART",
										"paymentCode": "9845811",
										"displayName": null
									}
								}
							}
						}
					}
					*/
					/* // Success QRIS
					{
						"data": {
							"id": "contract_d7f0528b95494eefaccf87c457cf7332",
							"type": "payment",
							"attributes": {
								"status": "pending",
								"amount": "889200.0",
								"createdAt": "2021-07-16T15:51:13+07:00",
								"description": "Voucher #25",
								"expiredAt": "2021-07-16T10:51:13",
								"referenceId": "0026425473",
								"fees": "6846.84",
								"paymentMethod": {
									"id": "qr_a8472c01a18bd4e19b83d8",
									"type": "qris",
									"referenceId": "0026425473",
									"instructions": {
										"imageUrl": "https://sandbox-payment.winpay.id/scqr/get_image_qr?payid=60f932b88de74240bb700726b73549f9",
										"displayName": "Voucher #25"
									}
								}
							}
						}
					}
					*/
					/* // Success SHOPEE
					{
						"data": {
							"id": "contract_b638505bc2b949c78fde84ae40c7cbe3",
							"type": "payment",
							"attributes": {
								"status": "pending",
								"amount": "835300.0",
								"createdAt": "2021-07-16T16:32:20+07:00",
								"description": "Voucher #50",
								"expiredAt": "2021-07-16T17:32:20+07:00",
								"referenceId": "0026427940",
								"fees": "13782.45",
								"paymentMethod": {
									"id": "shopeepay_81251f322f39890cd32abaea500cab48792f0212",
									"type": "e-wallet",
									"referenceId": "0026427940",
									"settlement": {
										"httpUrl": "https://wsa.uat.wallet.airpay.co.id/universal-link/wallet/pay?deep_and_deferred=1\u0026token=dFhkbmR1bTBIamhW0VqULhun6fTSt7JXAGHglmMMEcUvLhbq8jdHG8WDhCNp-ZEkqgZVDJ1Mm7Z7_AEJCvNzYw1hH0vL36Z_IDM0HWxp",
										"afterSettlementUrl": "https://a.g-dev.io/secure/callback/demo"
									}
								}
							}
						}
					}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content->data,
					];
				} else {
					// throw new \Exception('Data->Attributes->Status is not pending', 901);
					throw new \Exception('Parse failed (' . ($content->data->attributes->status ?? 'Unknown status') . ')', 901);
				}
			} else {
				// throw new \Exception('Status code is not 201', 902);
				throw new \Exception('Parse failed (' . ($content ?? 'Unknown error') . ')', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function Callback(object $request)
	{
		/* // Sample incoming
		{
			"data": {
				"id": "contract_ea9c4b10df49446eafd4fb3f25a713bd",
				"type": "payment",
				"attributes": {
					"status": "paid",
					"amount": "100000.0",
					"createdAt": "2020-10-19T09:12:48+07:00",
					"description": null,
					"expiredAt": "2020-10-20T02:12:48Z",
					"referenceId": "ORDER_0001",
					"fees": "4400.0",
					"paymentMethod": {
						"id": "871767762ccded4a92461ac0d3376c1e",
						"type": "retail_outlet",
						"referenceId": "ORDER_0001",
						"instructions": {
							"retailOutletName": "ALFAMART",
							"paymentCode": "8887322"
						}
					}
				}
			}
		}
		*/
		try {
			SELF::Validate($request, [
				'id',
				'type',
				'attributes',
			]);
			if (
				$request->attributes->status == 'paid'
			) {
				$res = [
					'status' => '000',
					'data' => (array) $request,
				];
			} else {
				// throw new \Exception('Callback status is not paid', 901);
				throw new \Exception('Callback status is not paid', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($request, $res, 200);
	}

	public function CallbackAlt(object $request)
	{
		// Inapplicable
	}

	public function Inquiry(object $request)
	{
		try {
			if (empty($request)) {
				throw new \Exception('Request is empty', 900);
			}
			// Validation
			SELF::Validate($request, [
				'id',
			]);
			// Go
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/v4/payments/' . $request->id
			);
			$this->request['data'] = [];
			$this->request['headers'] = [
				'Content-Type' => 'application/vnd.api+json',
				'Authorization' => 'Basic ' . base64_encode( $this->init->getMID() . ':' . $this->init->getSecret() ),
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
				$content = (object) json_decode($content);
				if (
					!empty($content->data->attributes)
				) {
					// Success
					/*
					{
						"data": {
							"id": "contract_a047916c30a54e94a9b4b00f1d8ac022",
							"type": "payment",
							"attributes": {
								"status": "pending",
								"amount": "85650.0",
								"createdAt": "2021-07-16T16:46:01+07:00",
								"description": "Voucher #41",
								"expiredAt": "2021-07-16T18:46:01+07:00",
								"referenceId": "0026428761",
								"fees": "3630.0",
								"paymentMethod": {
									"id": "va_c31715a0b94a093634a02d4a7e558d12",
									"type": "virtual_bank_account",
									"referenceId": "0026428761",
									"instructions": {
										"bankShortCode": "BCA",
										"accountNo": "1304410000000441",
										"displayName": "Voucher #41"
									}
								}
							}
						}
					}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content->data,
					];
				} else {
					// throw new \Exception('Data->Attributes is empty', 901);
					throw new \Exception('Parse failed (' . ($content->data->attributes->status ?? 'Unknown status') . ')', 901);
				}
			} else {
				// throw new \Exception('Status code is not 200', 902);
				throw new \Exception('Parse failed (' . ($content ?? 'Unknown error') . ')', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function PaymentMethodList(object $request)
	{
		try {
			if (empty($request)) {
				throw new \Exception('Request is empty', 900);
			}
			// Go
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'api/v4/payment_methods/' . $request->type . '/' . $request->id
			);
			$this->request['data'] = [];
			$this->request['headers'] = [
				'Content-Type' => 'application/vnd.api+json',
				'Authorization' => 'Basic ' . base64_encode( $this->init->getMID() . ':' . $this->init->getSecret() ),
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
				$content = (object) json_decode($content);
				if (
					!empty($content->data->attributes)
				) {
					// Success
					/*
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content->data,
					];
				} else {
					throw new \Exception('Parse failed (' . ($content->data->attributes->status ?? 'Unknown status') . ')', 901);
				}
			} else {
				throw new \Exception('Parse failed (' . ($content ?? 'Unknown error') . ')', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

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

}
