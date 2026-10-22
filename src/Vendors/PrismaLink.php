<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;

class PrismaLink extends Requestor implements VendorInterface
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
			$this->form['payment_method'] = $this->transaction->getPaymentMethod();
			$this->form['expired_in'] = $this->transaction->getExpireIn();
			$this->form['expired_at'] = $this->transaction->getExpireAt();
			//
			switch (strtolower($this->form['payment_method'])) {
				/*------------------------------v Begin of QRIS v---------- */
				case 'qris':
					// Go
					$this->request['form'] = $this->form;
					$this->request['time'] = $this->transaction->getTime();
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/payment/integration/transaction/api/submit-trx'
					);
					$this->request['data'] = [
						'transmission_date_time' => date('Y-m-d H:i:s.v O', $this->transaction->getTime()), // 'transmission_date_time' => date('Y-m-d\TH:i:s.vP', $this->transaction->getTime()),
						'transaction_currency' => $this->transaction->getCurrency(),
						'merchant_key_id' => $this->init->getMID(),
						'merchant_id' => $this->init->getMerchantKey(),
						'merchant_ref_no' => $this->transaction->getOrderID(),
						'backend_callback_url' => $this->init->getBackendURL(),
						'frontend_callback_url' => $this->init->getResponseUrl(),
						'user_id' => $this->transaction->getCustomerUserID(),
						'user_phone_number' => $this->transaction->getCustomerPhone(),
						'user_name' => $this->transaction->getCustomerName(),
						'user_email' => $this->transaction->getCustomerEmail(),
						'user_device_id' => $this->transaction->getCustomerUserID(),
						'user_ip_address' => $this->transaction->getIPAddress(),
						'product_details' => json_encode([[
							'item_code' => '01',
							'item_title' => $this->transaction->getItem(),
							'quantity' => '1',
							'total' => (float) $this->transaction->getAmount(),
							'currency' => 'IDR',
						]]),
						'shipping_details' => json_encode([[
							'address' => $this->transaction->getCustomerAddress(),
							'telephoneNumber' => $this->transaction->getCustomerPhone(),
							'handphoneNumber' => $this->transaction->getCustomerPhone(),
						]]),
						'invoice_number' => $this->transaction->getInvoiceNo(),
						'remarks' => $this->transaction->getDescription(),
						'transaction_amount' => (float) $this->transaction->getAmount(),
						'transaction_date_time' => date('Y-m-d H:i:s.v O', $this->transaction->getTime()), // 'transaction_date_time' => date('Y-m-d\TH:i:s.vP', $this->transaction->getTime()),
						'payment_method' => 'QR',
						'other_bills' => '',
						'reserved_va_id' => '',
						'va_name' => 'TEST',
						'terminal_id' => '',
						'integration_type' => '02',
						'action_id' => '00',
						'bank_id' => '',
						'validity' =>
							!empty($this->transaction->getExpireAt())
							? date('Y-m-d H:i:s.v O', $this->transaction->getExpireAt())
							: date('Y-m-d H:i:s.v O', $this->transaction->getTime() + ($this->transaction->getExpireIn() * 3600))
						,
						'external_id' => '',
					];
					$this->request['headers'] = [
						'Content-Type' => 'application/json',
						'mac' => hash_hmac('sha256', json_encode($this->request['data']), $this->init->getSecret(), false),
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
						$content = (object) json_decode($content);
						if (
							!empty($content->response_code)
							&&
								(
									$content->response_code == 'PL000'
								)
						) {
							/* // Success
							{
								"response_description": "SUCCESS",
								"payment_page_url": null,
								"response_code": "PL000",
								"transaction_status": "PNDNG",
								"va_number_list": null,
								"debitin_session_token": null,
								"creditcard_form_url": null,
								"qris_data": "00020101021226670016COM.NOBUBANK.WWW01189360050300000488240214070500000312780303UMI51440014ID.CO.QRIS.WWW0215ID20200814000960303UMI5204549953033605405868605502015802ID5914CLOTHING STORE60032606105158116239011405310000018062061000539799530703A0163047CEE",
								"plink_ref_no": "31052232805946",
								"creditcard_session_token": null,
								"debitin_form_url": null,
								"response_message": "SUCCESS",
								"initial_data_customer": null,
								"validity": null,
								"timestamp": "2022-05-31 13:52:33.174 +0700"
							}
							*/
							$res = [
								'status' => '000',
								'data' => (array) $content,
							];
						} else {
							if (
								!empty($content->response_code)
								&& !empty($content->response_description)
							) {
								throw new \Exception('Parse failed (' . ($content->response_code) . ') ' . $content->response_description, 901);
							} else {
								throw new \Exception('Parse failed (' . ($content->status ?? 'Unknown status') . ')', 901);
							}
						}
					} else {
						throw new \Exception('Parse failed (' . ($content ?? 'Unknown error') . ')', 902);
					}
					break;
					/*------------------------------^ End of QRIS ^---------- */
				default:
					// Go
					$this->request['form'] = $this->form;
					$this->request['time'] = $this->transaction->getTime();
					$this->request['url'] = '';
					$this->request['data'] = $this->request['form'];
					$this->request['headers'] = [];
					$this->request['option'] = [];
					//
					$res = [
						'status' => '000',
						'data' => $this->request['data'],
					];
					break;
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, 200);
	}

	public function Callback(object $request)
	{
		// Nothing here ----- function handled by own
	}

	public function CallbackAlt(object $request)
	{
		// Inapplicable
	}

	public function Inquiry(object $request)
	{
		try {
			SELF::Validate($request, [
				'merchant_ref_no',
				'plink_ref_no',
			]);
			// Go
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/payment/integration/transaction/api/inquiry-transaction'
			);
			$this->request['data'] = [
				'merchant_id' => $this->init->getMerchantKey(),
				'merchant_key_id' => $this->init->getMID(),
				'transmission_date_time' => date('Y-m-d H:i:s.v O', time()),
				//
				'merchant_ref_no' => $request->merchant_ref_no,
				'plink_ref_no' => $request->plink_ref_no,
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'mac' => hash_hmac('sha256', json_encode($this->request['data']), $this->init->getSecret(), false),
			];
			$this->request['option'] = [
				'to_json' => false,
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
					!empty($content->statusMessage)
					&& $content->merchantOrderId == $request->order_id
				) {
					// Success
					/*
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception('Parse failed (' . ($content->statusMessage ?? 'Unknown status') . ')', 901);
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
