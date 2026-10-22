<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;

use PyGPaymentLib\Helper;

class ibankPGA extends Requestor implements VendorInterface
{
	use Helper;

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

	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////
	|
	| Accept Payment
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\*/

	public function RedirectPayment(\PyGPaymentLib\Transaction $transaction)
	{
		// Inapplicable
	}

	public function SecurePayment(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$payment_method = strtolower(trim($transaction->getPaymentMethod()));
			switch ($payment_method) {
				case 'qris': /*--------------------------------------  Qris  --------------------------------------*/
					$this->request['data'] = [
						'merchant_uuid' => $this->init->getMID(),
						'service_code' => $transaction->getServiceCode(),
							'customer_id' => $transaction->getCustomerID(),
							'customer_name' => $transaction->getCustomerName(),
							'customer_email' => $transaction->getCustomerEmail(),
							'customer_phone' => $transaction->getCustomerPhone(),
						'invoice_num' => $transaction->getInvoiceNo(),
						'reference_num' => $transaction->getReferenceNumber(),
							'description' => $transaction->getDescription(),
							'amount' => (float) $transaction->getAmount(),
								'fee_fixed' => (float) $transaction->getFeeFixed(),
								'fee_percentage' => (float) $transaction->getFeePercentage(),
									'surcharge_fixed' => (float) $transaction->getSurchargeFixed(),
									'surcharge_percentage' => (float) $transaction->getSurchargePercentage(),
										'tips_fixed' => (float) $transaction->getTipsFixed(),
										'tips_percentage' => (float) $transaction->getTipsPercentage(),
										'tips_manual' => $transaction->getTipsManual(),
						'qris_type' => $transaction->getQrType(),
						'expire_in' => $transaction->getExpireIn(),
					];
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/CreateBilling'
					);
					$pattern = "POST:" . $this->init->getMID() . ":CreateBilling";
					$signature = base64_encode(hash_hmac('sha512', $pattern, $this->init->getSecret(), true));
					$this->request['headers'] = [
						'Content-Type' => 'application/json',
						'X-Signature' => $signature,
					];
					$this->request['option'] = [
						'as_json' => true,
					];
					break;
				case 'va': /*--------------------------------------  Virtual Account  --------------------------------------*/
					break;
				case 'emoney': /*--------------------------------------  Nusapay E-Money  --------------------------------------*/
					break;
				case 'cart': /*--------------------------------------  Nusapay Cart  --------------------------------------*/
					break;
				default:
					throw new \Exception('Payment method not defined/unknown', 900);
					break;
			}
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/*
				*/
				$content = (object) json_decode($content);
				$res = $content;
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function Callback(object $request)
	{
		// Inapplicable
	}

	public function CallbackAlt(object $request)
	{
		// Inapplicable
	}

	public function Inquiry(object $request)
	{
		try {
			SELF::Validate($request, [
				// 'merchant_uuid',
				// 'service_code',
				'payment_method',
			]);
			switch ($request->payment_method) {
				case 'qris': /*--------------------------------------  Qris  --------------------------------------*/
					$this->request['data'] = [
						'merchant_uuid' => $request->merchant_uuid ?? $this->init->getMID(),
						'service_code' => $request->service_code ?? $this->init->getParams()['service_code'],
							'invoice_num' => $request->invoice_num,
							'reference_num' => $request->reference_num,
							'with_payments' => $request->with_payments,
					];
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						'/CheckBilling'
					);
					$pattern = "POST:" . $this->init->getMID() . ":CheckBilling";
					$signature = base64_encode(hash_hmac('sha512', $pattern, $this->init->getSecret(), true));
					$this->request['headers'] = [
						'Content-Type' => 'application/json',
						'X-Signature' => $signature,
					];
					$this->request['option'] = [
						'as_json' => true,
					];
					$post = $this->DoRequest('POST', $this->request);
					break;
				case 'va': /*--------------------------------------  Virtual Account  --------------------------------------*/
					break;
				case 'emoney': /*--------------------------------------  Nusapay E-Money  --------------------------------------*/
					break;
				case 'cart': /*--------------------------------------  Nusapay Cart  --------------------------------------*/
					break;
				default:
					throw new \Exception('Payment method not defined/unknown', 900);
					break;
			}
			//
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/*
				*/
				$content = (object) json_decode($content);
				$res = $content;
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function Cancel(object $request)
	{
		try {
			SELF::Validate($request, [
				// 'merchant_uuid',
				// 'service_code',
				'payment_method',
			]);
			switch ($request->payment_method) {
				case 'qris': /*--------------------------------------  Qris  --------------------------------------*/
					$this->request['data'] = [
						'merchant_uuid' => $request->merchant_uuid ?? $this->init->getMID(),
						'service_code' => $request->service_code ?? $this->init->getParams()['service_code'],
							'invoice_num' => $request->invoice_num,
							'reference_num' => $request->reference_num,
					];
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						'/CancelBilling'
					);
					$pattern = "POST:" . $this->init->getMID() . ":CancelBilling";
					$signature = base64_encode(hash_hmac('sha512', $pattern, $this->init->getSecret(), true));
					$this->request['headers'] = [
						'Content-Type' => 'application/json',
						'X-Signature' => $signature,
					];
					$this->request['option'] = [
						'as_json' => true,
					];
					$post = $this->DoRequest('POST', $this->request);
					break;
				case 'va': /*--------------------------------------  Virtual Account  --------------------------------------*/
					break;
				case 'emoney': /*--------------------------------------  Nusapay E-Money  --------------------------------------*/
					break;
				case 'cart': /*--------------------------------------  Nusapay Cart  --------------------------------------*/
					break;
				default:
					throw new \Exception('Payment method not defined/unknown', 900);
					break;
			}
			//
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/*
				*/
				$content = (object) json_decode($content);
				$res = $content;
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function Settle(object $request)
	{
		// Inapplicable
	}

	public function Refund(object $request)
	{
		try {
			SELF::Validate($request, [
				// 'merchant_uuid',
				// 'service_code',
					'payment_method',
						'amount',
						'invoice_num',
						'customer_id',
						// 'customer_name',
			]);
			switch ($request->payment_method) {
				case 'qris': /*--------------------------------------  Qris  --------------------------------------*/
					$this->request['data'] = [
						'merchant_uuid' => $request->merchant_uuid ?? $this->init->getMID(),
						'service_code' => $request->service_code ?? $this->init->getParams()['service_code'],
							'customer_id' => $request->customer_id,
							'customer_name' => $request->customer_name,
							'customer_phone' => $request->customer_phone,
							'customer_email' => $request->customer_email,
								'invoice_num' => $request->invoice_num,
								'rrn' => $request->rrn, // Switching's RRN number
									'reference_num' => $request->reference_num ?? '', // Client's refund reference num
										'amount' => (float) $request->amount,
										// 'fee_fixed' => (float) $request->fee_fixed,
										// 'fee_percentage' => (float) $request->fee_percentage,
					];
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						'/MakeRefund'
					);
					$pattern = "POST:" . $this->init->getMID() . ":MakeRefund";
					$signature = base64_encode(hash_hmac('sha512', $pattern, $this->init->getSecret(), true));
					$this->request['headers'] = [
						'Content-Type' => 'application/json',
						'X-Signature' => $signature,
					];
					$this->request['option'] = [
						'as_json' => true,
					];
					$post = $this->DoRequest('POST', $this->request);
					break;
				case 'va': /*--------------------------------------  Virtual Account  --------------------------------------*/
					break;
				case 'emoney': /*--------------------------------------  Nusapay E-Money  --------------------------------------*/
					break;
				case 'cart': /*--------------------------------------  Nusapay Cart  --------------------------------------*/
					break;
				default:
					throw new \Exception('Payment method not defined/unknown', 900);
					break;
			}
			//
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/* Sample success
				{
					"status": "000",
					"message": "Refund success",
					"data": {
						"merchant_uuid": "15c1c9ee-98db-3fb7-85ed-85ece14894cd",
						"service_code": "NSIQRIS",
						"transaction_uuid": "3d402519-92b9-301a-87fb-4dc5c5ba6325",
						"reference_num": 1671163445,
						"invoice_num": "R12160000003",
						"description": "",
						"currency": "360",
						"net_amount": 200000,
						"fee_amount": 0,
						"surcharge_amount": 0,
						"amount": 200000,
						"customer_id": "1234567890",
						"customer_name": "CUSTOMERAAA",
						"customer_phone": "081212121314",
						"customer_email": "customeraaa@gmail.com",
						"status": "7",
						"status_descr": "REFUNDED",
						"refunded_at": "2022-12-16 11:04:06"
					},
					"debug": {
						"execution_time_ms": 493.22
					}
				}
				*/
				$content = (object) json_decode($content);
				$res = $content;
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function RefundStatus(object $request)
	{
		// Inapplicable
	}

	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\
	|
	| Instruction API
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\ ///////////////////////*/

	public function CheckPayment(object $request)
	{
		try {
			SELF::Validate($request, [
				// 'merchant_uuid',
				// 'service_code',
				// 'invoice_num',
			]);
			switch ($request->payment_method) {
				case 'qris': /*--------------------------------------  Qris  --------------------------------------*/
					$this->request['data'] = [
						'merchant_uuid' => $request->merchant_uuid ?? $this->init->getMID(),
						'service_code' => $request->service_code ?? $this->init->getParams()['service_code'],
							'date' => $request->date ?? '',
							'invoice_num' => $request->invoice_num ?? '',
							'reference_num' => $request->reference_num ?? '',
					];
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						'/CheckPayment'
					);
					$pattern = "POST:" . $this->init->getMID() . ":CheckPayment";
					$signature = base64_encode(hash_hmac('sha512', $pattern, $this->init->getSecret(), true));
					$this->request['headers'] = [
						'Content-Type' => 'application/json',
						'X-Signature' => $signature,
					];
					$this->request['option'] = [
						'as_json' => true,
					];
					$post = $this->DoRequest('POST', $this->request);
					break;
				case 'va': /*--------------------------------------  Virtual Account  --------------------------------------*/
					break;
				case 'emoney': /*--------------------------------------  Nusapay E-Money  --------------------------------------*/
					break;
				case 'cart': /*--------------------------------------  Nusapay Cart  --------------------------------------*/
					break;
				default:
					throw new \Exception('Payment method not defined/unknown', 900);
					break;
			}
			//
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/*
				*/
				$content = (object) json_decode($content);
				$res = $content;
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function CheckRefund(object $request)
	{
		try {
			SELF::Validate($request, [
				// 'merchant_uuid',
				// 'service_code',
				// 'invoice_num',
			]);
			switch ($request->payment_method) {
				case 'qris': /*--------------------------------------  Qris  --------------------------------------*/
					$this->request['data'] = [
						'merchant_uuid' => $request->merchant_uuid ?? $this->init->getMID(),
						'service_code' => $request->service_code ?? $this->init->getParams()['service_code'],
							'date' => $request->date ?? '',
							'invoice_num' => $request->invoice_num ?? '',
							'reference_num' => $request->reference_num ?? '',
					];
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						'/CheckRefund'
					);
					$pattern = "POST:" . $this->init->getMID() . ":CheckRefund";
					$signature = base64_encode(hash_hmac('sha512', $pattern, $this->init->getSecret(), true));
					$this->request['headers'] = [
						'Content-Type' => 'application/json',
						'X-Signature' => $signature,
					];
					$this->request['option'] = [
						'as_json' => true,
					];
					$post = $this->DoRequest('POST', $this->request);
					break;
				case 'va': /*--------------------------------------  Virtual Account  --------------------------------------*/
					break;
				case 'emoney': /*--------------------------------------  Nusapay E-Money  --------------------------------------*/
					break;
				case 'cart': /*--------------------------------------  Nusapay Cart  --------------------------------------*/
					break;
				default:
					throw new \Exception('Payment method not defined/unknown', 900);
					break;
			}
			//
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/*
				*/
				$content = (object) json_decode($content);
				$res = $content;
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function MakeStatusCheck(object $request)
	{
		try {
			SELF::Validate($request, [
				// 'merchant_uuid',
				// 'service_code',
					'payment_method',
						// 'amount',
						'reference_num',
						'invoice_num',
						// 'customer_id',
						// 'customer_name',
			]);
			switch ($request->payment_method) {
				case 'qris': /*--------------------------------------  Qris  --------------------------------------*/
					$this->request['data'] = [
						'merchant_uuid' => $request->merchant_uuid ?? $this->init->getMID(),
						'service_code' => $request->service_code ?? $this->init->getParams()['service_code'],
							'customer_id' => $request->customer_id ?? null,
							'customer_name' => $request->customer_name ?? null,
							'customer_phone' => $request->customer_phone ?? null,
							'customer_email' => $request->customer_email ?? null,
								'amount' => $request->amount ?? 0,
								'invoice_num' => $request->invoice_num,
								'rrn' => $request->reference_num,
					];
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						'/MakeStatusCheck'
					);
					$pattern = "POST:" . $this->init->getMID() . ":MakeStatusCheck";
					$signature = base64_encode(hash_hmac('sha512', $pattern, $this->init->getSecret(), true));
					$this->request['headers'] = [
						'Content-Type' => 'application/json',
						'X-Signature' => $signature,
					];
					$this->request['option'] = [
						'as_json' => true,
					];
					$post = $this->DoRequest('POST', $this->request);
					break;
				case 'va': /*--------------------------------------  Virtual Account  --------------------------------------*/
					break;
				case 'emoney': /*--------------------------------------  Nusapay E-Money  --------------------------------------*/
					break;
				case 'cart': /*--------------------------------------  Nusapay Cart  --------------------------------------*/
					break;
				default:
					throw new \Exception('Payment method not defined/unknown', 900);
					break;
			}
			//
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/*
				*/
				$content = (object) json_decode($content);
				$res = $content;
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function MakeInquiry(object $request)
	{
		try {
			SELF::Validate($request, [
				// 'merchant_uuid',
				// 'service_code',
				'qr_string',
			]);
			$this->request['data'] = [
				'merchant_uuid' => $request->merchant_uuid ?? $this->init->getMID(),
				'service_code' => $request->service_code ?? $this->init->getParams()['service_code'],
				'qr_string' => $request->qr_string,
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/MakeInquiry'
			);
			$pattern = "POST:" . $this->init->getMID() . ":MakeInquiry";
			$signature = base64_encode(hash_hmac('sha512', $pattern, $this->init->getSecret(), true));
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-Signature' => $signature,
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/*
				*/
				$content = (object) json_decode($content);
				$res = $content;
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function MakePayment(object $request)
	{
		try {
			SELF::Validate($request, [
				// 'merchant_uuid',
				// 'service_code',
				// 'amount',
				// 'fee_fixed',
				// 'fee_percentage',
					'customer_id',
					'customer_name',
					'customer_phone',
					// 'customer_email',
						// 'invoice_num',
						// 'qr_string',
							'reference_num',
			]);
			$this->request['data'] = [
				'merchant_uuid' => $request->merchant_uuid ?? $this->init->getMID(),
				'service_code' => $request->service_code ?? $this->init->getParams()['service_code'],
					'customer_id' => $request->customer_id,
					'customer_name' => $request->customer_name,
					'customer_phone' => $request->customer_phone,
					'customer_email' => $request->customer_email,
						'amount' => $request->amount ?? 0,
						'fee_fixed' => $request->fee_fixed ?? 0,
						'fee_percentage' => $request->fee_percentage ?? 0,
							'surcharge_fixed' => $request->surcharge_fixed ?? 0,
							'surcharge_percentage' => $request->surcharge_percentage ?? 0,
								'tips_fixed' => $request->tips_fixed ?? 0,
								'tips_percentage' => $request->tips_percentage ?? 0,
									//
									// Find billing
									'invoice_num' => $request->invoice_num ?? '',
									// OR use QR
									'qr_string' => $request->qr_string ?? '',
									//
									// RRN
									'reference_num' => $request->reference_num ?? '', // Client's payment reference num
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/MakePayment'
			);
			$pattern = "POST:" . $this->init->getMID() . ":MakePayment";
			$signature = base64_encode(hash_hmac('sha512', $pattern, $this->init->getSecret(), true));
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'X-Signature' => $signature,
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			//
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/*
				*/
				$content = (object) json_decode($content);
				$res = $content;
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

}
