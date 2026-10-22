<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;

use PyGPaymentLib\Helper;

class G2Up extends Requestor implements VendorInterface
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
			$time = time();
			$timestamp = date('Y/m/d H:i:s');
			$signature = hash_hmac('sha512',
				'payment/qris-request' .
				$this->init->getSecret() .
				$timestamp
			, $this->init->getMID());
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/payment/qris-request'
			);
			$this->request['data'] = [
				'topUpAmount' => (float) $transaction->getAmount(),
				'memberNo' => $transaction->getCustomerID(),
				'referenceNo' => $transaction->getReferenceNumber(),
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
				'AppKey' => $this->init->getMID(),
				'AppSecret' => $this->init->getSecret(),
				'X-TIME' => $timestamp,
				'X-SIGNATURE' => $signature,
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
					!empty($content->status)
					&& trim(strtolower($content->status)) == true
				) {
					/* // Success // QRIS
						{
							"status": "success",
							"message": "QRIS Merchant",
							"qrcode": "00020101021226670016ID.CO.MBAYAR.WWW01189360082931683196300214E2P168319630630303UMI51460016ID.CO.MBAYAR.WWW0215ID10210651519170303UMI520407425303360540815000.005802ID5910Risang Pay6015JAKARTA SELATAN6105121506234050622479607062110330810Risang Pay6304477C"
						}
					*/
					$res = [
						'status' => '000',
						'data' => array_merge((array) $content, $this->request['data']),
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

	public function Callback(object $request)
	{
		/* // Example incoming data // VA
		*/
		/* // Success QRIS
			{
				"referenceNo":"R015",
				"memberNo":"001",
				"merchantID":1,
				"CustName":"CUSTOMER",
				"CustEmail":"customer@bbb.id",
				"phone":"085741907194",
				"amount":100,
				"status":"PAID",
				"paydate":"2024-05-09 18:08:43"
			}
		*/
		try {
			if ($request->success == 'true') {
				$res = [
					'status' => '000',
					'data' => (array) $request->data_payment,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $request ? json_encode($request) : 'Unknown status']), 902);
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
			$time = time();
			$timestamp = date('Y/m/d H:i:s');
			$signature = hash_hmac('sha512',
				'payment/qris-request' .
				$this->init->getSecret() .
				$timestamp
			, $this->init->getMID());
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/qris/inquiry'
			);
			$this->request['data'] = [
				'startDate' => $request->startDate,
				'endDate' => $request->endDate,
				'referenceNo' => $request->referenceNo,
				'memberNo' => $request->memberNo,
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
				'X-TIME' => $timestamp,
				'X-SIGNATURE' => $signature,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (!empty($content)) {
					/* // Success inquiry QRIS
						[
							{
								"referenceNo": "1716267137",
								"memberNo": "TESTER",
								"topUpAmount": 15000,
								"requestDate": "2024-05-21 11:52:20",
								"responseDate": "2024-05-21 11:52:21",
								"paymentNo": "QRP24052100001",
								"paymentDate": "2024-05-21 11:56:54",
								"paymentStatus": "00"
							}
						]
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
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
