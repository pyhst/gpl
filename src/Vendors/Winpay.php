<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;
use PyGPaymentLib\Transaction;

class Winpay extends Requestor implements VendorInterface
{

	protected $form;

	/*==========================================================================================
												Start of Private functions
	==========================================================================================**/

		private function _OpenSSLEncrypt($message, $key)
		{
			$output = false;
			$encrypt_method = "AES-256-CBC";
			$secret_key = $key;
			$secret_iv = $key;
			$key = hash('sha256', $secret_key);
			$iv = substr(hash('sha256', $secret_iv), 0, 16);
			$output = openssl_encrypt($message, $encrypt_method, $key, 0, $iv);
			$output = trim(base64_encode($output));
			return $output;
		}

	/*=================================   End of Private functions   ==================================*/

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args = [])
	{
		try {
			$this->request['headers'] = [
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':' . $this->init->getSecret()),
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/token'
			);
			$this->request['data'] = [
				'api_key1' => $this->init->getMID(),
				'api_key2' => $this->init->getSecret()
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			$response = json_decode($response['content']);
			if (
				!empty($response->rc)
				&& $response->rc === '00'
			) {
				$content = $response;
				if (
					!empty($content->rc)
					&& $content->rc == '00'
				) {
					$result = $content->data;
				}
			} else {
				throw new \Exception('Token is empty', 901);
			}
		} catch (\Throwable $e) {
			// throw new \Exception($this->ThrowError($e));
			$this->ThrowErrorException($e);
		}
		return $result ?? [];
	}

	public function CreateDummyForm($args)
	{
		// Inapplicable
	}

	public function RedirectPayment(Transaction $transaction)
	{
		// Inapplicable
	}

	public function SecurePayment(Transaction $transaction)
	{
		try {
			$this->transaction = $transaction;
			//
			$this->form['order_id'] =  $this->transaction->getOrderID();
			$this->form['invoice_no'] = $this->transaction->getInvoiceNo();
			$this->form['currency'] = $this->transaction->getCurrency();
			//
			$this->form['item'] = $this->transaction->getItem();
			$this->form['amount'] = $this->transaction->getAmount();
			$this->form['description'] = $this->transaction->getDescription();
			//
			$this->form['customer_name'] = $this->transaction->getCustomerName();
			$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			//
			$arr = explode(',', $this->transaction->getPaymentMethod());
			$payment_method = strtoupper(trim( $arr[0] ?? '' ));
			$payment_channel = strtoupper(trim( $arr[1] ?? '' ));
			// Create spi_signature
			$merchant_key = $this->init->getMerchantKey();
			$spi_token =  $this->init->getMID() . $this->init->getSecret();
			$spi_merchant_transaction_reff = $this->transaction->getOrderID();
			$spi_amount = number_format(doubleval($this->form['amount']), 2, ".", "");
			$spi_signature = strtoupper(sha1(
				$spi_token . '|' .
				$merchant_key . '|' .
				$spi_merchant_transaction_reff . '|' .
				$spi_amount .
				'|0|0'
			));
			//
			$token = $this->GetToken();
			$this->form['token'] = $token->token;
			$data = [
				'cms' => "WINPAY API",
				'spi_callback' => $this->init->getCallbackURL(),
				'url_listener' => $this->init->getReturnURL(),
				'spi_currency' => $this->form['currency'],
				'spi_item' => [
						[
								'name' => $this->form['item'],
								'sku' => $this->form['invoice_no'],
								'qty' => 1,
								'unitPrice' => $this->form['amount'],
								'desc' => $this->form['description'],
							],
					],
				'spi_amount' => $this->form['amount'],
				'spi_signature' => $spi_signature,
				'spi_token' => $spi_token,
				'spi_merchant_transaction_reff' => $spi_merchant_transaction_reff,
				'spi_billingPhone' => $this->form['customer_phone'],
				'spi_billingEmail' => $this->form['customer_email'],
				'spi_billingName' => $this->form['customer_name'],
				'spi_paymentDate' =>
					date("YmdHis",
						strtotime(date('H:i:s')) + ($this->transaction->getExpireIn() * 60 * 60)
					),
			];
			// Overides & insertion
			switch ($payment_channel) {
				case 'QRISPAY':
					$data = $data + [
							'spi_qr_type' => 'dynamic',
							// Tipe QR yang digunakan, WAJIB diisi dengan tipe 'dynamic' atau 'static'.
// Ada hole dari pihak DANA, pihak Aplikasi DANA tidak bisa mengenali parameter spi_qr_fee dan spi_qr_fee_type,
// sesuai standard dari ASPI, untuk mensiasati hal ini, parameter tersebut bisa dihilangkan saja,
// ketika mengirimkan data payload ke kami, saat request QR, terima kasih
//							'spi_qr_fee_type' => 'open',
							// Tipe Fee/Tips yang digunakan, WAJIB diisi jika menggunakan tipe QR 'dynamic'.
							// Diisi dengan tipe 'open', 'fix' atau 'percent'.
							// Jika dikosongi maka dianggap no fee atau tidak menggunakan fee.
//							'spi_qr_fee' => '0',
							// Nominal dari fee tergantung dari tipenya.
							// Jika tipenya open boleh tidak digunakan atau diisi 0 saja.
							// Jika tipenya fix diisi nominal integer lebih dari 0.
							// Jika tipenya percent diisi float antara 0 sampai 100,
							// untuk pemisahan pecahan menggunakan tanda titik, misalnya : 0.5
						];
					break;
				default:
					$data = $data + [
							'get_link' => 'no',
						];
					break;
			}
			$this->form['data'] = $data;
			// Data
			$encrypted_message = $this->_OpenSSLEncrypt(
					json_encode($data),
					$token->token
				);
			// Go
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/apiv2/' . $payment_channel
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/x-www-form-urlencoded',
			];
			$this->request['data'] = [
				'orderdata' =>
					substr($encrypted_message, 0, 10) .
					$token->token .
					substr($encrypted_message, 10)
			];
			$this->request['option'] = [
				'as_json' => false,
			];
			$post = $this->DoRequest('POST',  $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (
					!empty($content->rc)
					&& $content->rc == '00'
				) {
					// Success
					/*
					// VA
					{
						"rc": "00",
						"rd": "Transaksi Anda sedang dalam proses, Segera lakukan pembayaran menggunakan Mandiri Va sejumlah IDR Rp. 889.400- sebelum jam 2021-02-10 19:52, Order ID Anda adalah 888981000126806. RAHASIA Dilarang menyebarkan ke ORANG Tdk DIKENAL   Terimakasih",
						"request_time": "2021-02-10 17:52:11.078139",
						"data": {
							"reff_id": "303700501",
							"payment_code": "888981000126806",
							"order_id": "0012954330",
							"request_key": "",
							"url_listener": "https:\/\/vogame.dev\/returnUrl",
							"payment_method": "MANDIRI VIRTUAL ACCOUNT",
							"payment_method_code": "MANDIRIVA",
							"fee_admin": 0,
							"total_amount": 889400,
							"spi_status_url": "https:\/\/secure-payment.winpay.id\/guidance\/index\/mandiriva?payid=8752f32167db210724be3beb0234ba53"
						},
						"response_time": "2021-02-10 17:52:13.485668"
					}
					// Alfamart
					{
						"rc": "00",
						"rd": "Transaksi Anda sedang dalam proses, Anda akan melakukan pembayaran menggunakan Alfamart, Silakan melakukan pembayaran sejumlah IDR 877.400-. Order ID Anda adalah 306474118. RAHASIA Dilarang menyebarkan ke ORANG Tdk DIKENAL",
						"request_time": "2021-02-25 16:22:18.633754",
						"data": {
							"reff_id": "306474118",
							"payment_code": "306474118",
							"order_id": "0014244938",
							"request_key": "",
							"url_listener": "https:\/\/a.g-dev.io\/secure\/callback\/demo",
							"payment_method": "Alfamart",
							"payment_method_code": "ALFAMART",
							"fee_admin": 0,
							"total_amount": 877400,
							"spi_status_url": "https:\/\/secure-payment.winpay.id\/guidance\/index\/alfamart?payid=14f4a4b9013b18c57acc84971a8a34ac"
						},
						"response_time": "2021-02-25 16:22:20.102106"
					}
					// QRIS
					{
						"rc": "00",
						"rd": "QR Image is successfully generated",
						"request_time": "2021-02-10 15:07:47.452935",
						"data": {
							"spi_status_url": "https:\/\/secure-payment.winpay.id\/guidance\/index\/qrispay?payid=b7e211ba6014e2954e439246feb0424b",
							"payment_method": "Pembayaran QRIS",
							"payment_method_code": "QRISPAY",
							"fee_admin": 0,
							"total_amount": 64000,
							"order_id": "0012944467",
							"spi_hash": "b7e211ba6014e2954e439246feb0424b",
							"tips": 6400,
							"nominal_mdr": 448,
							"image_qr": "https:\/\/secure-payment.winpay.id\/scqr\/get_image_qr?payid=b7e211ba6014e2954e439246feb0424b"
						},
						"response_time": "2021-02-10 15:07:48.160497"
					}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
					// $result = [
					// 		'request' => (array) $this->request,
					// 		'response' => [
					// 			'content' => json_encode($res),
					// 			'status_code' => 200,
					// 		],
					// 	];
				} else {
					// throw new \Exception($content->rd);
					throw new \Exception('Parse failed (' . ($content->rc ?? 'Unknown status') . ')', 901);
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
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function InquiryPaymentChannel()
	{
		try {
			// Go
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/toolbar'
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Authorization' => 'Basic ' . base64_encode(
					$this->init->getMID() . ':' . $this->init->getSecret()
				),
			];
			$this->request['data'] = [];
			$this->request['option'] = [
				'as_json' => false,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (
					!empty($content->rc)
					&& $content->rc == '00'
				) {
					// Success
					/*
					{
						"rc": "00",
						"rd": "Success",
						"request_time": "2021-02-25 15:32:07.235263",
						"data": {
							"token": "000f1f4cb5118390cc2ec79af671d61719c6f7a74281b16c2e70ba485dcf1750",
							"products": {
								"clickpay": [{
									"payment_code": "BCAKP",
									"payment_name": "BCA Klik Pay",
									"payment_description": "Bayar dengan BCA Klik Pay",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-bca-klikpay.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/BCAKP",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/BCAKP",
									"is_direct": false
								}, {
									"payment_code": "CIMBC",
									"payment_name": "CIMB Clicks",
									"payment_description": "Bayar dengan CIMB Clicks",
									"payment_logo": "https:\/\/secure-payment.speedcash.co.id\/img\/spi-cimb-clicks.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/CIMBC",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/CIMBC",
									"is_direct": false
								}, {
									"payment_code": "BTNONLINE",
									"payment_name": "Debit Online BTN",
									"payment_description": "Bayar dengan Debit Online BTN",
									"payment_logo": "https:\/\/secure-payment.plasamall.com\/img\/spi-btnonline.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/BTNONLINE",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/BTNONLINE",
									"is_direct": false
								}],
								"modern store": [{
									"payment_code": "ALFAMART",
									"payment_name": "Alfamart",
									"payment_description": "Bayar di gerai Alfamart",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-alfamart.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/ALFAMART",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/ALFAMART",
									"is_direct": true
								}, {
									"payment_code": "FASTPAY",
									"payment_name": "Fastpay",
									"payment_description": "Bayar di Outlet Fastpay",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-fastpay.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/FASTPAY",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/FASTPAY",
									"is_direct": true
								}],
								"bank transfer": [{
									"payment_code": "MANDIRIPC",
									"payment_name": "Mandiri Pay Code",
									"payment_description": "Bayar dengan Mandiri Payment Code",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-mandiri.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/MANDIRIPC",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/MANDIRIPC",
									"is_direct": true
								}, {
									"payment_code": "BCAPC",
									"payment_name": "ATM BCA",
									"payment_description": "Bayar di ATM BCA",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-atm-bca.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/BCAPC",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/BCAPC",
									"is_direct": true
								}],
								"virtual account": [{
									"payment_code": "BNIVA",
									"payment_name": "BNI VIRTUAL ACCOUNT",
									"payment_description": "Bayar dengan BNI VIRTUAL ACCOUNT",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-bni-va.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/BNIVA",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/BNIVA",
									"is_direct": true
								}, {
									"payment_code": "BRIVA",
									"payment_name": "BRI VIRTUAL ACCOUNT",
									"payment_description": "Bayar dengan BRI VIRTUAL ACCOUNT",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-bri-va.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/BRIVA",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/BRIVA",
									"is_direct": true
								}, {
									"payment_code": "PERMATAVA",
									"payment_name": "PERMATA VIRTUAL ACCOUNT",
									"payment_description": "Bayar dengan PERMATA VIRTUAL ACCOUNT",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-permata-va.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/PERMATAVA",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/PERMATAVA",
									"is_direct": true
								}, {
									"payment_code": "MANDIRIVA",
									"payment_name": "MANDIRI VIRTUAL ACCOUNT",
									"payment_description": "Bayar dengan MANDIRI VIRTUAL ACCOUNT",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-mandiriva.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/MANDIRIVA",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/MANDIRIVA",
									"is_direct": true
								}]
							}
						},
						"response_time": "2021-02-25 15:32:07.444127"
					}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
					// $result = [
					// 		'request' => (array) $this->request,
					// 		'response' => [
					// 				'content' => json_encode($res),
					// 				'status_code' => 200,
					// 			],
					// 	];
				} else {
					// throw new \Exception($content->rd);
					throw new \Exception('Parse failed (' . ($content->rc ?? 'Unknown status') . ')', 901);
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
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function Callback(object $request)
	{
		/*
		// Example incoming data
		// VA
		{
			"id_transaksi": "317416981",
			"no_reff": "202104122561701618197389",
			"response_code": "00",
			"id_produk": "SCPIMNDRVA",
			"method_code": "MANDIRIVA",
			"keterangan": "888981000152380 14500 Mr Dummy",
			"nominal": "14500.00",
			"biaya_layanan": "2000.00",
			"nominal_nett": "14500.00",
			"method_name": "Mandiri VA"
		}
		// QRIS
		{
		  "id_transaksi": "4929984",
		  "no_reff": "5e4ce7cf7901234569",
		  "response_code": "00",
		  "id_produk": "QRISPAY",
		  "method_code": "QRISPAY",
		  "keterangan": "Transaksi Anda berhasil",
		  "nominal": 50352.47,
		  "biaya_layanan": 252.00,
		  "tips": 0,
		  "nominal_nett": 50100.47,
		  "method_name":"QR TRANSFER",
		  "qris_data":
			{
				"brand_name":"SPEEDCASH",
				"issuer_reff":"000004929983",
				"buyer_reff":"Tester",
				"sent_time":"2020-11-16T07:58:01.852Z"
			}
		}
		*/
		try {
			SELF::Validate($request, [
					'id_transaksi',
					'no_reff',
					'response_code',
				]);
			switch ($request->response_code) {
				case '00':
					$response_message = 'Success';
					break;
				case '01':
					$response_message = 'Access denied';
					break;
				case '04':
					$response_message = 'Data not found ';
					break;
				case '05':
					$response_message = 'General error';
					break;
				case '99':
					$response_message = 'Parameter not valid';
					break;
			}
			//
			if (
				!empty($request->response_code)
				&& $request->response_code == '00'
			) {
				$res = [
					'status' => '000',
					'data' => (array) $request,
				];
				// $result = [
				// 		'request' => (array) $request,
				// 		'response' => [
				// 				'content' => json_encode($res),
				// 				'status_code' => 200,
				// 			],
				// 	];
			} else {
				// throw new \Exception('Callback check failed');
				throw new \Exception('Signature check failed', 902);
			}
		} catch (\Throwable $e) {
			// throw new \Exception($this->ThrowError($e));
			$this->ThrowErrorException($e);
		}
		// return $result ?? [];
		return SELF::JSONResult($this->request, $res, '200');
	}

	public function CallbackAlt(object $request)
	{
		// Inapplicable
	}

	public function Inquiry(object $request)
	{
		try {
			SELF::Validate($request, [
				'is_qris',
			]);
			// Go
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/transaction/' .
					($request->is_qris == 'no' ?
						'/check-wpi-transaction' :
						'/check-qris-transaction')
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':' . $this->init->getSecret()),
			];
			$this->request['data'] = [
				'order_id' => $request->order_id ?? '',
				// 'id_transaction' => $request->id_transaction ?? '',
				// 'id_transaction_inquiry' => $request->id_transaction_inquiry ?? '',
				// 'id_transaction_payment' => $request->id_transaction_payment ?? '',
			];
			$this->request['option'] = [
				'as_json' => false,
			];
			$get = $this->DoRequest('GET',  $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (
					!empty($content->rc)
					&& $content->rc == '00'
				) {
					// Success
					/*
					*/
					$res = [
							'status' => '000',
							'data' => (array) $content,
						];
					// $result = [
					// 		'request' => (array) $this->request,
					// 		'response' => [
					// 				'content' => json_encode($res),
					// 				'status_code' => 200,
					// 			],
					// 	];
				} else {
					// throw new \Exception($content->rd);
					throw new \Exception('Parse failed (' . ($content->rd ?? 'Unknown status') . ')', 901);
				}
			} else {
				// throw new \Exception($content);
				// throw new \Exception($content ?? 'Unknown error occured');
				throw new \Exception('Parse failed (' . ($content ?? 'Unknown error') . ')', 902);
			}
		} catch (\Throwable $e) {
			// throw new \Exception($this->ThrowError($e));
			$this->ThrowErrorException($e);
		}
		// return $result ?? [];
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
