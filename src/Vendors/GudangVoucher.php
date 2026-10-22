<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

use Growinc\Payment\Helper;
class GudangVoucher extends Requestor implements VendorInterface
{
	use Helper;

	protected $form;

	public function Index()
	{
		// Inapplicable
	}

	public function getPrivateKey()
	{
		$private_key = $this->init->getParam('gv_private_key');
		if (!empty($private_key)) {
			return
				"-----BEGIN RSA PRIVATE KEY-----\n" .
				chunk_split($private_key, 64, "\n") .
				"-----END RSA PRIVATE KEY-----\n";
		}
		if (!$cert_store = file_get_contents($_ENV['DANA_PRIVATE_KEY_FILE'])) {
			return "Error: Unable to read the cert file\n";
		}
		return $cert_store;
	}

	public function GetToken($args)
	{
		try {
			$time = time();
			$timestamp = date('Y-m-d\TH:i:sP', $time);
			//
			$this->request['data'] = [
				'grantType' => 'client_credentials',
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/v1.0/access-token/b2b'
			);
			//
			$payload = $this->init->getMID() . '|' . $timestamp;
			$signature = '';
			$gv_private_key =
				"-----BEGIN RSA PRIVATE KEY-----\n" .
				chunk_split($this->init->getParams()['gv_private_key'], 64, "\n") .
				"-----END RSA PRIVATE KEY-----\n";
			if (!openssl_sign($payload, $signature, $gv_private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $timestamp,
				'X-CLIENT-KEY' => $this->init->getMID(),
				'X-SIGNATURE' => base64_encode($signature),
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
					!empty($content->accessToken)
				) {
					// Success
					/*
						{
							"responseCode": "2007300",
							"responseMessage": "Successful",
							"accessToken": "a03462f2e46d48dd4d71d005c0d4d23e",
							"tokenType": "Bearer",
							"expiresIn": 900
						}
					*/
					$res = $content;
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

	public function CreateDummyForm($args)
	{
		// Inapplicable
	}

	public function RedirectPayment(\Growinc\Payment\Transaction $transaction)
	{
		// $this->transaction = $transaction;
		// //
		// $payment_url = $this->init->getPaymentURL() . '?'
		// 	. 'merchantid=' . $this->init->getMID()
		// 	. '&amount=' . (float) $this->transaction->getAmount()
		// 	. '&product=' . $this->transaction->getItem()
		// 	. '&custom=' . $this->transaction->getOrderID()
		// 	. '&email=' . $this->transaction->getCustomerEmail()
		// 	. '&signature=' . md5( $this->init->getMID() . $this->transaction->getAmount() . $this->init->getSecret() . $this->transaction->getOrderID() )
		// 	. '&custom_redirect=' . $this->init->getCallbackURL();
		// header('location: ' . SELF::CleanURL( $payment_url ));
	}

	public function SecurePayment(\Growinc\Payment\Transaction $transaction)
	{
		// try {
		// 	$this->transaction = $transaction;
		// 	//
		// 	$this->form['order_id'] = $this->transaction->getOrderID();
		// 	$this->form['invoice_no'] = $this->transaction->getInvoiceNo();
		// 	$this->form['currency'] = $this->transaction->getCurrency();
		// 	//
		// 	$this->form['item'] = $this->transaction->getItem();
		// 	$this->form['amount'] = (float) $this->transaction->getAmount();
		// 	// $this->form['description'] = $this->transaction->getDescription();
		// 	//
		// 	// $this->form['customer_name'] = $this->transaction->getCustomerName();
		// 	$this->form['customer_email'] = $this->transaction->getCustomerEmail();
		// 	// $this->form['customer_phone'] = $this->transaction->getCustomerPhone();
		// 	// $this->form['customer_address'] = $this->transaction->getCustomerAddress();
		// 	// $this->form['country_code'] = $this->transaction->getCountryCode();
		// 	//
		// 	$arr = explode(',', $this->transaction->getPaymentMethod());
		// 	$payment_method = strtolower(trim( $arr[0] ?? '' ));
		// 	$payment_channel = strtolower(trim( $arr[1] ?? '' ));
		// 	//
		// 	$payment_url = $this->init->getPaymentURL() . '?'
		// 		. 'merchantid=' . $this->init->getMID()
		// 		. '&amount=' . (float) $this->form['amount']
		// 		. '&product=' . $this->form['item']
		// 		. '&custom=' . $this->form['order_id']
		// 		. '&email=' . $this->form['customer_email']
		// 		. '&signature=' . md5(
		// 					$this->init->getMID() .
		// 					$this->form['amount'] .
		// 					$this->init->getSecret() .
		// 					$this->form['order_id']
		// 				)
		// 		. '&custom_redirect=' . urlencode($this->init->getCallbackURL());
		// 	// Go
		// 	$this->request['form'] = $this->form;
		// 	$this->request['time'] = $this->transaction->getTime();
		// 	$this->request['url'] = SELF::CleanURL( $payment_url );
		// 	$this->request['data'] = [];
		// 	$this->request['headers'] = [];
		// 	$this->request['option'] = [
		// 			'as_json' => false,
		// 		];
		// 	$post = $this->DoRequest('POST', $this->request);
		// 	$response = (array) $post['response'];
		// 	extract($response);
		// 	if (!empty($status_code) && $status_code === 200) {
		// 		if (!empty($content)) {
		// 			// HTML Dom
		// 			$doc = new \DOMDocument();
		// 			libxml_use_internal_errors(true);
		// 			$doc->loadHTML($content);
		// 			libxml_clear_errors();
		// 			$xpath = new \DOMXpath($doc);

		// 			// Find error
		// 			$alert_message = '';
		// 			$alerts = $xpath->query('//div[@class="col-md-12"]/div/span[@class="text-muted"]');
		// 			foreach($alerts as $alert) {
		// 				$alert_message = trim(strip_tags($alert->textContent));
		// 				throw new \Exception('Vendor alert: ' . $alert_message, 1);
		// 				break; // Get first only
		// 			}

		// 			// First segment
		// 			$arr_p = [];
		// 			$ps = $xpath->query('//p[@class="mb-1"]');
		// 			foreach($ps as $p) {
		// 				$arr_p[] = $p->textContent;
		// 			}
		// 			if (count($arr_p)) {
		// 				$order_id = trim(explode(':', $arr_p[0])[1]);
		// 				$order_amount = str_replace('.', '',
		// 						trim(explode('Rp.',
		// 							$arr_p[1]
		// 						)[1])
		// 					);
		// 				$order_expired_at = trim(explode('sebelum', $arr_p[2])[1]);
		// 			}
		// 			// Second segment // Get GV payment URL
		// 			$as = $xpath->query('//div[@id="headingQR"]/h4/a/@href');
		// 			foreach($as as $a) {
      // 				$gv_payment_url = $a->value;
		// 				break; // Only first attempt
		// 			}
		// 			// Third segment // Get GV VA numbers
		// 			$arr_bank = [];
		// 			$tables = $xpath->query('//table[@class="table mb-0"]');
		// 			for ($i=0; $i<count($tables); $i++) {
		// 				$bank_name = trim(
		// 					$xpath->query('./tr[1]/td[1]', $tables[$i])->item(0)->textContent
		// 				);
		// 				$bank_name = str_replace('Bank ', '', $bank_name);
		// 				$bank_name = str_replace(' ', '_', $bank_name);
		// 				$bank_name = preg_replace('/[^A-Za-z0-9\-_]/', '', $bank_name); // Removes special chars.
		// 				$bank_name = strtolower($bank_name);
		// 				if ($payment_method == 'bank_transfer') {
		// 					if ($bank_name == $payment_channel) {
		// 						$bank_fee = trim(
		// 							preg_replace("/[^0-9]/", "",
		// 								$xpath->query('./tr[1]/td[2]', $tables[$i])->item(0)->textContent
		// 							)
		// 						);
		// 						$bank_pay_code = trim(
		// 							preg_replace("/[^0-9]/", "",
		// 								$xpath->query('./tr[2]/td[1]', $tables[$i])->item(0)->textContent
		// 							)
		// 						);
		// 						$bank_amount = trim(
		// 							preg_replace("/[^0-9]/", "",
		// 									$xpath->query('./tr[2]/td[2]', $tables[$i])->item(0)->textContent
		// 							)
		// 						);
		// 					}
		// 				}
		// 				// if (empty($payment_method) && empty($payment_channel)) {
		// 					$arr_bank[$i]['bank_code'] = $bank_name;
		// 					$arr_bank[$i]['fee'] = (float) trim(
		// 						preg_replace("/[^0-9]/", "",
		// 							$xpath->query('./tr[1]/td[2]', $tables[$i])->item(0)->textContent
		// 						)
		// 					);
		// 					$arr_bank[$i]['amount'] = (float) trim(
		// 						preg_replace("/[^0-9]/", "",
		// 								$xpath->query('./tr[2]/td[2]', $tables[$i])->item(0)->textContent
		// 						)
		// 					);
		// 					$arr_bank[$i]['pay_code'] = trim(
		// 						preg_replace("/[^0-9]/", "",
		// 							$xpath->query('./tr[2]/td[1]', $tables[$i])->item(0)->textContent
		// 						)
		// 					);
		// 				// }
		// 			}

		// 			// Get Alfamart
		// 			$arr_alfa = [];
		// 			$divs = $xpath->query('//div[@id="collapseAlfa"]');
		// 			for ($i=0; $i<count($divs); $i++) {
		// 				$alfa_pay_code = trim(
		// 					preg_replace("/[^0-9]/", "",
		// 						$xpath->query('//p[@class="mb-0"]/b', $divs[$i])->item(0)->textContent
		// 					)
		// 				);
		// 				$alfa_fee = trim(
		// 					preg_replace("/[^0-9]/", "",
		// 						$xpath->query('//div[@class="panel-body"]//div[@class="card-body"]//div[@class="row justify-content-between"]//div[@class="col-auto col-md-12"]//small[@class="text-muted"]', $divs[$i])->item(1)->textContent
		// 					)
		// 				);
		// 				$alfa_amount = trim(
		// 					preg_replace("/[^0-9]/", "",
		// 						$xpath->query('//p[@class="mb-0"]/b', $divs[$i])->item(1)->textContent
		// 					)
		// 				);
		// 				$arr_alfa = [
		// 						'cstore_code' => 'alfamart',
		// 						'fee' => (float) $alfa_fee,
		// 						'amount' => (float) $alfa_amount,
		// 						'pay_code' => $alfa_pay_code,
		// 					];
		// 				break; // Get first only
		// 			}

		// 			// QRIS navigation // Get QRIS URL path
		// 			if ($payment_method == 'qris'
		// 				|| $payment_channel == 'qris'
		// 				|| (empty($payment_method) && empty($payment_channel))
		// 			) {
		// 				$qris_payment_url = $payment_url . '&QRIS=1';
		// 				// Go
		// 				$this->request['form'] = $this->form;
		// 				$this->request['time'] = $this->transaction->getTime();
		// 				$this->request['url'] = SELF::CleanURL( $qris_payment_url );
		// 				$this->request['data'] = [];
		// 				$this->request['headers'] = [];
		// 				$this->request['option'] = [
		// 						'as_json' => false,
		// 					];
		// 				$post = $this->DoRequest('POST', $this->request);
		// 				$response = (array) $post['response'];
		// 				extract($response);
		// 				if (!empty($status_code) && $status_code === 200) {
		// 					if (!empty($content)) {
		// 						// HTML Dom
		// 						$doc = new \DOMDocument();
		// 						libxml_use_internal_errors(true);
		// 						$doc->loadHTML($content);
		// 						libxml_clear_errors();
		// 						$xpath = new \DOMXpath($doc);
		// 						// First segment
		// 						$qris_url = '';
		// 						$imgs = $xpath->query('//img[@width="75%"]/@src');
		// 						foreach($imgs as $img) {
		// 							$qris_url = $img->textContent;
		// 						}
		// 					}
		// 				}
		// 			}

		// 			// Return
		// 			if (empty($payment_method) && empty($payment_channel)) {
		// 				// Show all options
		// 				// Success
		// 				/* All option shown
		// 				{
		// 					"status": "000",
		// 					"data": {
		// 						"order_id": "0013619064",
		// 						"amount": 10000,
		// 						"bank_pay_codes": [{
		// 							"bank_code": "bca",
		// 							"fee": 4400,
		// 							"amount": 14400,
		// 							"pay_code": "7700610100480083"
		// 						}, {
		// 							"bank_code": "permata",
		// 							"fee": 1500,
		// 							"amount": 11500,
		// 							"pay_code": "8992010100480083"
		// 						}, {
		// 							"bank_code": "bni",
		// 							"fee": 2000,
		// 							"amount": 12000,
		// 							"pay_code": "8558010100480083"
		// 						}, {
		// 							"bank_code": "cimb_niaga",
		// 							"fee": 1000,
		// 							"amount": 11000,
		// 							"pay_code": "3049010100480083"
		// 						}, {
		// 							"bank_code": "atm_bersama",
		// 							"fee": 1500,
		// 							"amount": 11500,
		// 							"pay_code": "500501010100480083"
		// 						}],
		// 						"cstore_pay_codes": {
		// 							"cstore_code": "alfamart",
		// 							"fee": 2500,
		// 							"amount": 12500,
		// 							"pay_code": "00480083"
		// 						},
		// 						"qris_url": "https:\/\/www.gudangvoucher.com\/merchant\/cetak.php?type=3&number=MDAwMjAxMDEwMjEyMjY3MzAwMjFDT00uR1VEQU5HVk9VQ0hFUi5XV1cwMTE4OTM2MDA5MTYzMDAyNDc3NTkwMDIxNUdWMjIwMDAwMjQ3NzU5MDAzMDNVQkU1MTQ1MDAxNUlELk9SLkdQTlFSLldXVzAyMTVJRDIwMjEwNjI5ODYwNDEwMzAzVUJFNTIwNDU5NDU1MzAzMzYwNTQwNTEwMDAwNTgwMklENTkwNFZQQVk2MDE1SkFLQVJUQSBTRUxBVEFONjEwNTEyMjQwNjIzMzAxMDgwMDQ4MDA4MzA1MTcyMTAyMTgxMDMxMDMzaUxaVDYzMDRERDA1",
		// 						"gv_wallet_payment_url": "https:\/\/www.gudangvoucher.com\/payment.php?merchantid=878&amount=10000&product=Apple&custom=0013619064&email=lorem@ipsum.com&custom_redirect=https:\/\/a.g-dev.io\/secure\/callback\/demo",
		// 						"payment_url": "https:\/\/www.gudangvoucher.com\/pg\/v3\/payment.php?merchantid=878&amount=10000&product=Apple&custom=0013619064&email=lorem@ipsum.com&signature=7d9b584ee8e5498f9df9b03f38eda9ad&custom_redirect=https%3A%2F%2Fa.g-dev.io%2Fsecure%2Fcallback%2Fdemo",
		// 						"expired_at": "2021-02-18 14:31:03"
		// 					}
		// 				}
		// 				*/
		// 				$res = [
		// 						'status' => '000',
		// 						'data' => (array) [
		// 								'order_id' => $order_id,
		// 								'amount' => (float) $order_amount,
		// 								//
		// 								'bank_pay_codes' => $arr_bank,
		// 								'cstore_pay_codes' => $arr_alfa,
		// 								//
		// 								'qris_url' => $qris_url ?? '',
		// 								'gv_wallet_payment_url' => $gv_payment_url ?? '',
		// 								'payment_url' => $payment_url ?? '',
		// 								'expired_at' => date('Y-m-d H:i:s', strtotime($order_expired_at)),
		// 							],
		// 					];
		// 			} else {
		// 				// Only filtered channel
		// 				if ($payment_method == 'qris' || $payment_channel == 'qris') {
		// 					// Success
		// 					/*  QRIS example
		// 					{
		// 						"status": "000",
		// 						"data": {
		// 							"order_id": "0013619238",
		// 							"qris_url": "https:\/\/www.gudangvoucher.com\/merchant\/cetak.php?type=3&number=MDAwMjAxMDEwMjEyMjY3MzAwMjFDT00uR1VEQU5HVk9VQ0hFUi5XV1cwMTE4OTM2MDA5MTYzMDAyNDc3NTkwMDIxNUdWMjIwMDAwMjQ3NzU5MDAzMDNVQkU1MTQ1MDAxNUlELk9SLkdQTlFSLldXVzAyMTVJRDIwMjEwNjI5ODYwNDEwMzAzVUJFNTIwNDU5NDU1MzAzMzYwNTQwNTEwMDAwNTgwMklENTkwNFZQQVk2MDE1SkFLQVJUQSBTRUxBVEFONjEwNTEyMjQwNjIzMzAxMDgwMDQ4MDEwMDA1MTcyMTAyMTgxMDMzNTdlV3RNZDYzMDQwRTlC",
		// 							"amount": 10000,
		// 							"payment_url": "https:\/\/www.gudangvoucher.com\/pg\/v3\/payment.php?merchantid=878&amount=10000&product=Apple&custom=0013619238&email=lorem@ipsum.com&signature=70cfd8b65d60dac322f4e99900f3ca9e&custom_redirect=https%3A%2F%2Fa.g-dev.io%2Fsecure%2Fcallback%2Fdemo",
		// 							"expired_at": "2021-02-18 14:33:57"
		// 						}
		// 					}
		// 					*/
		// 					$res = [
		// 							'status' => '000',
		// 							'data' => (array) [
		// 									'order_id' => $order_id,
		// 									//
		// 									'qris_url' => $qris_url,
		// 									'amount' => (float) $order_amount,
		// 									//
		// 									'payment_url' => $payment_url,
		// 									'expired_at' => date('Y-m-d H:i:s', strtotime($order_expired_at)),
		// 								],
		// 						];
		// 				} elseif ($payment_method == 'cstore' && $payment_channel == 'alfamart') {
		// 					// Success
		// 					/*  Alfamart example
		// 					{
		// 						"status": "000",
		// 						"data": {
		// 							"order_id": "0013619293",
		// 							"cstore": "alfamart",
		// 							"fee": 2500,
		// 							"amount": 12500,
		// 							"pay_code": "00480105",
		// 							"payment_url": "https:\/\/www.gudangvoucher.com\/pg\/v3\/payment.php?merchantid=878&amount=10000&product=Apple&custom=0013619293&email=lorem@ipsum.com&signature=6ddc1ecaf8cf93afc07649107b12d3d2&custom_redirect=https%3A%2F%2Fa.g-dev.io%2Fsecure%2Fcallback%2Fdemo",
		// 							"expired_at": "2021-02-18 14:34:52"
		// 						}
		// 					}
		// 					*/
		// 					$res = [
		// 							'status' => '000',
		// 							'data' => (array) [
		// 									'order_id' => $order_id,
		// 									//
		// 									'cstore' => $payment_channel,
		// 									'fee' => (float) $arr_alfa['fee'],
		// 									'amount' => (float) $arr_alfa['amount'],
		// 									'pay_code' => $arr_alfa['pay_code'],
		// 									//
		// 									'payment_url' => $payment_url,
		// 									'expired_at' => date('Y-m-d H:i:s', strtotime($order_expired_at)),
		// 								],
		// 						];
		// 				} else {
		// 					// Success
		// 					/*  VA example
		// 					{
		// 						"status": "000",
		// 						"data": {
		// 							"order_id": "0013619123",
		// 							"bank_code": "cimb_niaga",
		// 							"fee": 1000,
		// 							"amount": 11000,
		// 							"pay_code": "3049010100480089",
		// 							"payment_url": "https:\/\/www.gudangvoucher.com\/pg\/v3\/payment.php?merchantid=878&amount=10000&product=Apple&custom=0013619123&email=lorem@ipsum.com&signature=d2bf279d8fffce30a365d9e64aa35203&custom_redirect=https%3A%2F%2Fa.g-dev.io%2Fsecure%2Fcallback%2Fdemo",
		// 							"expired_at": "2021-02-18 14:32:02"
		// 						}
		// 					}
		// 					*/
		// 					$res = [
		// 							'status' => '000',
		// 							'data' => (array) [
		// 									'order_id' => $order_id,
		// 									//
		// 									'bank_code' => $payment_channel,
		// 									'fee' => (float) $bank_fee,
		// 									'amount' => (float) $bank_amount,
		// 									'pay_code' => $bank_pay_code,
		// 									//
		// 									'payment_url' => $payment_url,
		// 									'expired_at' => date('Y-m-d H:i:s', strtotime($order_expired_at)),
		// 								],
		// 						];
		// 				}
		// 			}
		// 		} else {
		// 			throw new \Exception('Parse failed, data is empty', 901);
		// 		}
		// 	} else {
		// 		throw new \Exception('Parse failed (' . ($content ?? 'Unknown error') . ')', 902);
		// 	}
		// } catch (\Throwable $e) {
		// 	// throw new \Exception($this->ThrowError($e));
		// 	$this->ThrowErrorException($e);
		// }
		// // return $result ?? [];
		// return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function Callback(object $request)
	{
		// // Example incoming data callback payment
		// /*
		// {
		// 	"data": "878<\/merchant_id>VPAY<\/merchant>87820210208154546<\/reference>0012773633<\/voucher_code>Apple<\/purpose>0012773633<\/custom>SUCCESS<\/status>YES<\/development><\/trans_doc>"
		// }
		// */
		// try {
		// 	if (isset($request->data) && !empty($request->data)) {
		// 		$xml = new \SimpleXMLElement(
		// 				preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $request->data)
		// 			);
		// 		if (count($xml->xpath('//trans_doc'))) {
		// 			$content = $xml->xpath('//trans_doc')[0];
		// 			$amount = $content->xpath('//amount')[0];
		// 			$amount = [
		// 					'currency' => (string) (isset($amount['currency']) ? $amount['currency'] : 'IDR'),
		// 					'nominal' => (float) (isset($amount['nominal']) ? $amount['nominal'] : 0),
		// 				];
		// 			$content = (array) $content;
		// 			$content = array_merge((array) $content, $amount);
		// 			// XML content
		// 			/*
		// 			SimpleXMLElement Object
		// 			(
		// 				[merchant_id] => 878
		// 				[merchant] => VPAY
		// 				[reference] => 87820210208154546
		// 				[voucher_code] => 0012773633
		// 				[amount] => SimpleXMLElement Object
		// 					(
		// 							[@attributes] => Array
		// 								(
		// 									[currency] => IDR
		// 									[nominal] => 560000
		// 								)

		// 					)

		// 				[purpose] => Apple
		// 				[custom] => 0012773633
		// 				[status] => SUCCESS
		// 				[development] => YES
		// 			)
		// 			*/
		// 			$res = [
		// 					'status' => '000',
		// 					'data' => $content,
		// 				];
		// 			$result = [
		// 					'request' => (array) $request,
		// 					'response' => [
		// 							'content' => json_encode($res),
		// 							'status_code' => 200,
		// 						],
		// 				];
		// 		} else {
		// 			throw new \Exception('Callback XML is empty');
		// 		}
		// 	} else {
		// 		throw new \Exception('Callback is empty');
		// 	}
		// } catch (\Throwable $e) {
		// 	throw new \Exception($this->ThrowError($e));
		// }
		// return $result ?? [];
	}

	public function CallbackAlt(object $request)
	{
		// Inapplicable
	}

	public function Inquiry(object $request)
	{
		// try {
		// 	SELF::Validate($request, [
		// 			'order_id'
		// 		]);
		// 	//
		// 	$request_url = $this->init->getRequestURL()
		// 		. '?'
		// 		. 'merchantid=' . $this->init->getMID()
		// 		. '&custom=' . $request->order_id
		// 		. '&signature=' . md5(
		// 			$this->init->getSecret() .
		// 			$this->init->getMID() .
		// 			$request->order_id
		// 		);
		// 	// Go
		// 	$this->request['form']['request_url'] = $request_url;
		// 	$this->request['form']['signature'] = [
		// 		$this->init->getSecret(),
		// 		$this->init->getMID(),
		// 		$request->order_id
		// 	];
		// 	$this->request['time'] = time();
		// 	$this->request['url'] = SELF::CleanURL( $request_url );
		// 	$this->request['data'] = [
		// 		'merchantid' => $this->init->getMID(),
		// 		'custom' => $request->order_id,
		// 		'signature' => md5(
		// 			$this->init->getSecret() .
		// 			$this->init->getMID() .
		// 			$request->order_id
		// 		),
		// 	];
		// 	$this->request['headers'] = [];
		// 	$this->request['option'] = [
		// 		'as_json' => false,
		// 	];
		// 	$post = $this->DoRequest('POST', $this->request);
		// 	$response = (array) $post['response'];
		// 	extract($response);
		// 	if (!empty($status_code) && $status_code === 200) {
		// 		$content = (object) json_decode($content);
		// 		if (
		// 			!isset($content->error_code)
		// 			&& !isset($content->message)
		// 		) {
		// 			// Success
		// 			/*
		// 			*/
		// 			$res = [
		// 					'status' => '000',
		// 					'data' => (array) $content,
		// 				];
		// 			// $result = [
		// 			// 		'request' => (array) $request,
		// 			// 		'response' => [
		// 			// 				'content' => json_encode($res),
		// 			// 				'status_code' => 200,
		// 			// 			],
		// 			// 	];
		// 		} else {
		// 			// throw new \Exception($content->message);
		// 			throw new \Exception('Parse failed (' . ($content->message ?? 'Unknown status') . ')', 901);
		// 		}
		// 	} else {
		// 		// throw new \Exception($content);
		// 		throw new \Exception('Parse failed (' . ($content ?? 'Unknown error') . ')', 902);
		// 	}
		// } catch (\Throwable $e) {
		// 	// throw new \Exception($this->ThrowError($e));
		// 	$this->ThrowErrorException($e);
		// }
		// // return $result ?? [];
		// return SELF::JSONResult($this->request, $res, $status_code);
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



	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\
	|
	| Disbursement
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\ ///////////////////////*/

	public function BankList()
	{
		// Inapplicable
	}

	public function CheckBalance()
	{
		try {
			$time = time();
			$timestamp = date('Y-m-d\TH:i:sP', $time);
			//
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/v1.0/balance-inquiry'
			);
			$this->request['data'] = [
				'partnerReferenceNo' => 'CKB' . date('YmdHis'),
				'accountNo' => $this->init->getParams()['gv_connect_key'],
				'additionalInfo' => [
					'gv_connect_key' => $this->init->getParams()['gv_connect_key'],
					'merchantId' => '1167',
				],
			];
			//
			$gv_private_key = $this->getPrivateKey();
			if (is_array($this->request['data'])) {
				$RequestBody = json_encode($this->request['data']);
				$hash = hash('sha256', $RequestBody);
			} else {
				$RequestBody = "";
				$hash = hash('sha256', $RequestBody);
			}
			$hash1 = strtolower($hash);
			$stringToSign = "POST:/api/v1.0/balance-inquiry:$hash1:$timestamp";
			openssl_sign($stringToSign, $signature, $gv_private_key, "RSA-SHA256");
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
				// 'Authorization-Customer' => 'Bearer ' . $this->init->getParams()['token'],
				'X-TIMESTAMP' => $timestamp,
				// 'X-CLIENT-KEY' => $this->init->getMID(),
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->init->getMID(),
				'X-EXTERNAL-ID' => date('YmdHis', $time),
				'CHANNEL-ID' => '95051',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == "successful"
				) {
					/* // Success
						{
							"responseCode": "2001100",
							"responseMessage": "Successful",
							"partnerReferenceNo": "CKB20240320155217",
							"referenceNo": "BAI2403200133690200043RQCA23LX",
							"name": "test_1710488615",
							"accountInfos": [
								{
										"balanceType": "Cash",
										"availableBalance": {
											"value": "10000000.00",
											"currency": "IDR"
										},
										"registrationStatusCode": "0001",
										"status": "0001"
								}
							],
							"additionalInfo": {
								"topup_code": {
										"va-bca": "7700-6870-0452-3196",
										"va-bni": "8558-1212-0452-3196",
										"va-permata": "8992-1212-0452-3196",
										"va-niaga": "304-9452-3196",
										"va-atmbersama": "50-0501-1212-0452-3196"
								}
							}
						}
					*/
					$res = [
						'status' => '000',
						'request' => $this->request,
						'data' => [
							'balance_type' => $content->accountInfos[0]->balanceType,
							'active_balance' => (float) $content->accountInfos[0]->availableBalance->value,
							'pending_balance' => (float) $content->accountInfos[0]->availableBalance->value,
							'total_balance' => (float) $content->accountInfos[0]->availableBalance->value,
							'currency' => $content->accountInfos[0]->availableBalance->currency,
							'topup_code' => $content->additionalInfo->topup_code,
						],
					];
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function BankAccountInquiry(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$time = time();
			$timestamp = date('Y-m-d\TH:i:sP', $time);
			//
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/v1.0/emoney/bank-account-inquiry'
			);
			$this->request['data'] = [
				'partnerReferenceNo' => 'BAI' . date('YmdHis', $transaction->getTime()),
				'beneficiaryAccountNumber' => $transaction->getCustomerBankAccountNumber(),
				'customerNumber' => $transaction->getCustomerBankAccountNumber(),
				'amount' => [
					'value' => (string) $transaction->getAmount() . '.00',
					'currency' => $transaction->getCurrency(),
				],
				'additionalInfo' => [
					'merchantId' => $this->init->getParams()['gv_merchant_id'],
					'gv_connect_key' => $this->init->getParams()['gv_connect_key'],
					'beneficiaryBankCode' => $transaction->getCustomerBankCode(),
				],
			];
			//
			// $payload = $this->init->getMID() . '|' . $timestamp;
			// $signature = '';
			$gv_private_key =
				"-----BEGIN RSA PRIVATE KEY-----\n" .
				chunk_split($this->init->getParams()['gv_private_key'], 64, "\n") .
				"-----END RSA PRIVATE KEY-----\n";
			// if (!openssl_sign($payload, $signature, $gv_private_key, OPENSSL_ALGO_SHA256)) {
			// 	throw new \Exception('Failed to getToken', 800);
			// }
			//
			if (is_array($this->request['data'])) {
				$RequestBody = json_encode($this->request['data']);
				$hash = hash('sha256', $RequestBody);
			} else {
				$RequestBody = "";
				$hash = hash('sha256', $RequestBody);
			}
			$hash1 = strtolower($hash);
			$stringToSign = "POST:/api/v1.0/emoney/bank-account-inquiry:$hash1:$timestamp";
			openssl_sign($stringToSign, $signature, $gv_private_key, "RSA-SHA256");
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
				// 'Authorization-Customer' => 'Bearer ' . $this->init->getParams()['token'],
				'X-TIMESTAMP' => $timestamp,
				// 'X-CLIENT-KEY' => $this->init->getMID(),
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->init->getMID(),
				'X-EXTERNAL-ID' => date('YmdHis', $time),
				'CHANNEL-ID' => '95051',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == "successful"
				) {
					/* // Success
						{
							"responseCode": "2004200",
							"responseMessage": "Successful",
							"partnerReferenceNo": "BAI20240322171946",
							"referenceNo": "BAI240322013820020001VTF1QYDQG",
							"beneficiaryAccountNumber": "5787811122",
							"beneficiaryAccountName": "BPK JONOMADE MADEMADEMADEMADE IMAMADE",
							"beneficiaryBankCode": "009",
							"beneficiaryBankName": "BANK BNI",
							"amount": {
								"value": "15000.00",
								"currency": "IDR"
							}
						}
					*/
					$res = [
						'status' => '000',
						'request' => $this->request,
						'data' => [
							'beneficiary_bank_account_number' => $content->beneficiaryAccountNumber,
							'beneficiary_bank_account_name' => $content->beneficiaryAccountName,
							'beneficiary_bank_code' => $content->beneficiaryBankCode,
							'beneficiary_bank_name' => $content->beneficiaryBankName,
							'amount' => (float) $content->amount->value,
							'currency' => $content->amount->currency,
						],
					];
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function OnlineTransferExec(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$time = time();
			$timestamp = date('Y-m-d\TH:i:sP', $time);
			//
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/v1.0/emoney/transfer-bank'
			);
			$this->request['data'] = [
				'partnerReferenceNo' => 'OTE' . date('YmdHis', $transaction->getTime()),
				'beneficiaryAccountNumber' => $transaction->getCustomerBankAccountNumber(),
				'beneficiaryBankCode' => $transaction->getCustomerBankCode(),
				'customerNumber' => $transaction->getCustomerBankAccountNumber(),
				'amount' => [
					'value' => (string) $transaction->getAmount() . '.00',
					'currency' => $transaction->getCurrency(),
				],
				'additionalInfo' => [
					'merchantId' => $this->init->getParams()['gv_merchant_id'],
					'gv_connect_key' => $this->init->getParams()['gv_connect_key'],

				],
			];
			//
			// $payload = $this->init->getMID() . '|' . $timestamp;
			// $signature = '';
			$gv_private_key =
				"-----BEGIN RSA PRIVATE KEY-----\n" .
				chunk_split($this->init->getParams()['gv_private_key'], 64, "\n") .
				"-----END RSA PRIVATE KEY-----\n";
			// if (!openssl_sign($payload, $signature, $gv_private_key, OPENSSL_ALGO_SHA256)) {
			// 	throw new \Exception('Failed to getToken', 800);
			// }
			//
			if (is_array($this->request['data'])) {
				$RequestBody = json_encode($this->request['data']);
				$hash = hash('sha256', $RequestBody);
			} else {
				$RequestBody = "";
				$hash = hash('sha256', $RequestBody);
			}
			$hash1 = strtolower($hash);
			$stringToSign = "POST:/api/v1.0/emoney/transfer-bank:$hash1:$timestamp";
			openssl_sign($stringToSign, $signature, $gv_private_key, "RSA-SHA256");
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
				// 'Authorization-Customer' => 'Bearer ' . $this->init->getParams()['token'],
				'X-TIMESTAMP' => $timestamp,
				// 'X-CLIENT-KEY' => $this->init->getMID(),
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->init->getMID(),
				'X-EXTERNAL-ID' => date('YmdHis', $time),
				'CHANNEL-ID' => '95051',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == "successful"
				) {
					/* // Success disb
						{
							"responseCode": "2004300",
							"responseMessage": "Successful",
							"partnerReferenceNo": "OTE20240508154807",
							"referenceNo": "BAP24050801725402000HBPPW4HDRL",
							"transactionDate": "2024-05-08T15:48:09+07:00",
							"referenceNumber": "OTE20240508154807",
							"additionalInfo": {
								"sourceAccountName": "test_1710488615",
								"namaRek": "BPK JONOMADE MADEMADEMADEMADE IMAMADE",
								"nomorRek": "5787811122",
								"namaBank": "BANK BNI",
								"nominal": 10000,
								"transferfee": 500,
								"finalamount": 10500,
								"remark1": null,
								"desc": "Transaksi Berhasil"
							}
						}
					*/
					$res = [
						'status' => '000',
						'request' => $this->request,
						'data' => [
							'beneficiary_bank_account_number' => $content->additionalInfo->nomorRek,
							'beneficiary_bank_account_name' => $content->additionalInfo->namaRek,
							'beneficiary_bank_code' => $transaction->getCustomerBankCode(),
							'beneficiary_bank_name' => $content->additionalInfo->namaBank,
							'amount' => (float) $content->additionalInfo->nominal,
							'currency' => $transaction->getCurrency(),
						],
					];
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function OnlineTransferCheckTransfer(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$time = time();
			$timestamp = date('Y-m-d\TH:i:sP', $time);
			//
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/api/v1.0/transfer/status'
			);
			$this->request['data'] = [
				'originalPartnerReferenceNo' => $transaction->getReferenceNumber(),
				'originalExternalId' => $transaction->getExternalID(),
				'serviceCode' => $transaction->getServiceCode(),
				'transactionDate' => $transaction->getReqDateTime(),
				'additionalInfo' => [
					'merchantId' => $this->init->getParams()['gv_merchant_id'],
					'gv_connect_key' => $this->init->getParams()['gv_connect_key'],
				],
			];
			//
			$gv_private_key = $this->getPrivateKey();
			if (is_array($this->request['data'])) {
				$RequestBody = json_encode($this->request['data']);
				$hash = hash('sha256', $RequestBody);
			} else {
				$RequestBody = "";
				$hash = hash('sha256', $RequestBody);
			}
			$hash1 = strtolower($hash);
			$stringToSign = "POST:/api/v1.0/transfer/status:$hash1:$timestamp";
			openssl_sign($stringToSign, $signature, $gv_private_key, "RSA-SHA256");
			//
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $this->init->getParams()['token'],
				// 'Authorization-Customer' => 'Bearer ' . $this->init->getParams()['token'],
				'X-TIMESTAMP' => $timestamp,
				// 'X-CLIENT-KEY' => $this->init->getMID(),
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->init->getMID(),
				'X-EXTERNAL-ID' => date('YmdHis', $time),
				'CHANNEL-ID' => '95051',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == "successful"
				) {
					/* // Success inquiry
						{
							"responseCode": "2003600",
							"responseMessage": "Successful",
							"originalReferenceNo": "BAP24050801942802000W3Q5KVZ0X4",
							"originalPartnerReferenceNo": "OTE20240508155830",
							"serviceCode": "43",
							"transactionDate": "2024-05-08T15:58:32+07:00",
							"amount": {
								"value": "10000.00",
								"currency": "IDR"
							},
							"beneficiaryAccountNo": "5787811122",
							"beneficiaryBankCode": "009",
							"referenceNumber": "BAP24050801942802000W3Q5KVZ0X4",
							"sourceAccountNo": "test_1710488615",
							"latestTransactionStatus": "00",
							"transactionStatusDesc": "Success"
						}
					*/
					$res = [
						'status' => '000',
						'request' => $this->request,
						'data' => [
							'original_reference_no' => $content->originalReferenceNo,
							'original_partner_reference_no' => $content->originalPartnerReferenceNo,
							'service_code' => $content->serviceCode,
							'transaction_datetime' => $content->transactionDate,
							'beneficiary_bank_account_number' => $content->beneficiaryAccountNo,
							'beneficiary_bank_code' => $content->beneficiaryBankCode,
							'amount' => (float) $content->amount->value,
							'currency' => $content->amount->currency,
						],
					];
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}
}
