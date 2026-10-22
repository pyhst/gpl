<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;

use PyGPaymentLib\Helper;

class Yukk extends Requestor implements VendorInterface
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



	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\
	|
	| Payment
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\ ///////////////////////*/

	public function RedirectPayment(\PyGPaymentLib\Transaction $transaction)
	{
		// Not applicable
	}

	public function SecurePayment(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$payment_method = $transaction->getPaymentMethod();
			$payment_channel = $transaction->getPaymentChannel();
			switch ($payment_method) {
				case 'qris':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/partner/qris/request'
					);
					$this->request['data'] = [
						'nominal' => (float) $transaction->getAmount(),
						'order_id' => $transaction->getOrderID(),
						'additional_field' => [
							'pos_id' => substr(date('His', $transaction->getTime()), -3, 3),
							'transaction_time' => gmdate("Y-m-d\TH:i:s", $transaction->getTime()),
						],
					];
					/*
						// Success // QRIS
						{
							"time": 154,
							"status_code": 6000,
							"status_message": "Your Order Request Saved. Please make payment within 1800 second(s).",
							"result": {
								"order_id": "1679055213",
								"nominal": 10000,
								"additional_field": "{\"pos_id\":\"333\",\"transaction_time\":\"2023-03-17T12:13:33\"}",
								"timeout_in_seconds": 1800,
								"timeout_datetime": "2023-03-17T19:43:33+07:00",
								"qris_string": "00020101021226660014ID.CO.YUKK.WWW011893600817023001408602152302230000140860303UME51440014ID.CO.QRIS.WWW0215ID20232498839190303UME5204541153033605405100005802ID5918MALTI (TARUMAJAYA)6006BEKASI610517215624101077677599051016790552130712DO2YUQO7FMP063040B2E"
							}
						}
						// RC
						6000 		Success 										API Request Success
						9014 		Error Order ID Already Exist			Order ID can’t be duplicated
						9017 		Error Nominal is below Minimum 		Nominal requested by partner is below the minimum amount set by the system. Partner is advised to request nominal higher than the minimum amount.
						9021 		Error Invalid Parameter 				Parameter is not matched. To know which parameter is not matched, see field status_message.
						7999 		Error Other 								Other errors. To know what error occurred, see field status_message.
					*/
					break;
				case 'va':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/'
					);
					$this->request['data'] = [
					];
					/*
						// Success // VA
					*/
					break;
				case 'ewallet':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/'
					);
					$this->request['data'] = [
					];
					/*
						// Success // E-wallet
					*/
					break;
				default:
					throw new \Exception('Undefined payment method', 800);
					break;
			}
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'client-id' => $this->init->getMID(),
				'signature-key' => hash('sha512', json_encode($this->request['data']) . $this->init->getSecret()),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				$res = [
					'status' => ($content->status_code == '6000' ? '000' : $content->status_code),
					'message' => $content->status_message,
					'data' => $content->result,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function Callback(object $request)
	{
		/*
			// Example incoming data // QRIS
				{
					"nominal": 50000,
					"paid_by": "SHERINA AYU VIBIAGI",
					"payment_method": "Bank Multiarta Sentosa(93600548)",
					"merchant_branch": "RS HERMINA (Yogyakarta)",
					"rrn": "000039957143",
					"transaction_time": "19-Sep-2022 12:31:22"
				}
			// Example incoming data // VA
		*/
		try {
			if ($request->success == 'true') {
				$res = [
					'status' => '000',
					'data' => (array) $request->data_payment,
				];
			} else {
				throw new \Exception('Callback failed', 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, '200');
	}

	public function CallbackAlt(object $request)
	{
		// Inapplicable
	}

	public function Inquiry(object $request)
	{
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/partner/qris/inquiry'
			);
			$this->request['data'] = [
				'order_id' => $request->order_id,
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'client-id' => $this->init->getMID(),
				'signature-key' => hash('sha512', json_encode($this->request['data']) . $this->init->getSecret()),
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success inquiry
					{
						"time": 119,
						"status_code": 6000,
						"status_message": "Inquiry Success",
						"result": {
							"id": 7677599,
							"order_id": "1679055213",
							"edc_id": 16878,
							"nominal": 10000,
							"additional_field": "{\"pos_id\":\"333\",\"transaction_time\":\"2023-03-17T12:13:33\"}",
							"max_payment": 1,
							"status": "NOT_PAID",
							"total_payment": 0,
							"is_timeout": false,
							"timeout_in_seconds": 1175,
							"timeout_datetime": "2023-03-17T19:43:33+07:00"
						}
					}
				*/
				$content = (object) json_decode($content);
				$res = [
					'status' => ($content->status_code == '6000' ? '000' : $content->status_code),
					'message' => $content->status_message,
					'data' => $content->result,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
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
		// Inapplicable
	}

	public function BankAccountInquiry(\PyGPaymentLib\Transaction $transaction)
	{
		// Inapplicable
	}

	public function OnlineTransferExec(\PyGPaymentLib\Transaction $transaction)
	{
		// Inapplicable
	}

	public function CheckTransfer(object $request)
	{
		// Inapplicable
	}

}
