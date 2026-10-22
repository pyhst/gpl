<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class Mcu extends Requestor implements VendorInterface
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

	public function RedirectPayment(\Growinc\Payment\Transaction $transaction)
	{
		// Inapplicable
	}

	public function SecurePayment(\Growinc\Payment\Transaction $transaction)
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
			$this->form['payment_channel'] = $this->transaction->getPaymentMethod();
			$this->form['params'] = $this->transaction->getParams();
			// Go
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/' . $this->form['params']['type'] . '/request'
			);
			$this->request['data'] = [
				'agent_code' => $this->init->getMID(),
				'agent_division' => $this->init->getSecret(),
				'customer_name' => $this->form['customer_name'],
				'customer_email' => $this->form['customer_email'],
				'customer_phone' => $this->form['customer_phone'],
					'payment_method_code' => $this->form['payment_channel'],
						'bank_accnum' => $this->form['params']['bank_accnum'],
						'bank_accname' => $this->form['params']['bank_accname'],
							'customer_bank_name' => $this->form['params']['customer_bank_name'],
							'customer_bank_accnum' => $this->form['params']['customer_bank_accnum'],
							'customer_bank_accname' => $this->form['params']['customer_bank_accname'],
								'amount' => $this->form['amount'],
								'invoice_no' => $this->form['invoice_no'],
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
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
					!empty($content->status) && $content->status == 1
					&& !empty($content->message)
				) {
					$res = [
						'status' => '000',
						'data' => (array) $content->message,
					];
				} else {
					// throw new \Exception('Message is empty', 901);
					throw new \Exception('Parse failed (' . ($content->message ?? 'Unknown status') . ')', 901);
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

	public function Callback(object $request)
	{
		// Inapplicable
	}

	public function CallbackAlt(object $request)
	{
		// Inapplicable
	}

	public function InquiryPaymentChannel()
	{
		try {
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/account'
			);
			$this->request['data'] = [
				'agent_code' => $this->init->getMID(),
				'agent_division' => $this->init->getSecret(),
				'bank_name' => '',
				'is_active' => '1',
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
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
					!empty($content->status) && $content->status == 1
					&& !empty($content->message)
				) {
					$res = [
						'status' => '000',
						'data' => (array) $content->message,
					];
				} else {
					// throw new \Exception('Message is empty', 901);
					throw new \Exception('Parse failed (' . ($content->message ?? 'Unknown status') . ')', 901);
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

	public function Inquiry(object $request)
	{
		try {
			if (empty($request)) {
				throw new \Exception('Request is empty', 900);
			}
			// Validation
			SELF::Validate($request, [
				'id',
				'type',
			]);
			// Go
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/' . $request->type . '/status'
			);
				$this->request['data'] = [
				'agent_code' => $this->init->getMID(),
				'agent_division' => $this->init->getSecret(),
				'transaction_id' => $request->id,
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
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
					!empty($content->status) && $content->status == 1
					&& !empty($content->message)
				) {
					$res = [
						'status' => '000',
						'data' => (array) $content->message,
					];
				} else {
					// throw new \Exception('Message is empty', 901);
					throw new \Exception('Parse failed (' . ($content->message ?? 'Unknown status') . ')', 901);
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

	public function InquiryAll(object $request)
	{
		try {
			if (empty($request)) {
				throw new \Exception('Request is empty', 900);
			}
			// Validation
			SELF::Validate($request, [
				'type',
			]);
			// Go
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/' . $request->type . '/pendinglist'
			);
				$this->request['data'] = [
				'agent_code' => $this->init->getMID(),
				'agent_division' => $this->init->getSecret(),
			];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
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
					!empty($content->status) && $content->status == 1
					&& !empty($content->message)
				) {
					$res = [
						'status' => '000',
						'data' => (array) $content->message,
					];
				} else {
					// throw new \Exception('Message is empty', 901);
					throw new \Exception('Parse failed (' . ($content->message ?? 'Unknown status') . ')', 901);
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
