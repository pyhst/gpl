<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

use Growinc\Payment\Helper;

class IBBv2 extends Requestor implements VendorInterface
{
	use Helper;

	protected $form;
	protected $access_token;
	protected $max_retry = 3;

	//

	public function SetAccessToken($access_token)
	{
		$this->access_token = $access_token;
	}

	public function GetAccessToken() // : string
	{
		return $this->access_token;
	}

	public function GetTokenFile($assoc_file = 'token') : string
	{
		return __DIR__ . '/_temp_' . $assoc_file . '_' .
			(new \ReflectionClass($this))->getShortName() . '_' .
			$this->init->getMID() . '_' .
			'.txt';
	}

	public function GetTokenFileContent($assoc_file = 'token')
	{
		if (file_exists($this->GetTokenFile($assoc_file))) {
			return file_get_contents($this->GetTokenFile($assoc_file));
		}
	}

	public function CheckToken()
	{
		// Get temp token
		if (!empty($this->GetTokenFileContent()) && SELF::is_JSON($this->GetTokenFileContent())) {
			$token = json_decode($this->GetTokenFileContent(), true);
			$this->SetAccessToken($token['data']['access_token']);
		} else {
			// Get token
			if (empty($this->GetAccessToken())) {
				// Request new
				$this->SetAccessToken($this->GetToken());
			}
		}
	}

	//

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args = null)
	{
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/UserLogin'
			);
			$this->request['data'] = [
				'email' => $this->init->getMID(),
				'password' => $this->init->getSecret(),
			];
			//
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
				// Parse data
				$content = (object) json_decode($content);
				if (
					!empty($content->data->access_token)
				) {
					/* // Success
						{
							"rc": "00",
							"rm": "Success",
							"message": "Success",
							"data": {
								"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJodHRwOi8vMTAuNDUuNDYuMjMyOjk1MDMvYXBpL3YxL2F1dGgvbG9naW4iLCJpYXQiOjE3NjQ0OTYyNzQsImV4cCI6MTc2NDQ5OTg3NCwibmJmIjoxNzY0NDk2Mjc0LCJqdGkiOiJ2dnp1QXU5OGJPQmJUUmo5Iiwic3ViIjoiMDE5YTcyNzQtYTJlZi03MjI3LTllNzgtNGQ2ZmI4ZGMyMzM4IiwicHJ2IjoiZjZiNzE1NDlkYjhjMmM0MmI3NTgyN2FhNDRmMDJiN2VlNTI5ZDI0ZCIsInVzZXJfaWQiOiIwMTlhNzI3NC1hMmVmLTcyMjctOWU3OC00ZDZmYjhkYzIzMzgiLCJjbGllbnRfaWQiOiIwMTlhNzI3NC1hMmY0LTcwM2UtOWQyNi1iZDM4MzFhNGI4OTMiLCJzY29wZSI6bnVsbH0.K-ll3vdhm0FOsUY5l-2CvhUEjA9EeBEb9qd44GhdAL2nAGM56Rm1Hydn0zXlGWNKZdT_y1d7rMN5hBMUjwc2mg",
								"token_type": "Bearer",
								"refresh_token": "ef047426768811be3b97fd5a667b562e08e9abd1f2bf427cb9fe19143921f906",
								"device_name": "GuzzleHttp\/6.5.5 curl\/8.4.0 PHP\/8.1.25"
							}
						}
					*/
					file_put_contents($this->GetTokenFile(), json_encode($content));
					$this->SetAccessToken($content->data->access_token);
					//
					$res = (string) $content->data->access_token;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
			throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 902);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return $res ?? [];
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

	public function RedirectPayment(\Growinc\Payment\Transaction $transaction)
	{
		// Not applicable
	}

	public function SecurePayment(\Growinc\Payment\Transaction $transaction)
	{
		try {
			// $this->CheckToken();
			$this->GetToken();
			$token_retry = 0;
			retry:
			//
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/CreateBilling'
			);
			$this->request['data'] = [
				'merchant_uuid' => $this->init->getMerchantKey(),
				'service_code' => $transaction->getParam('SERVICE_CODE') ?? 'ACCEPTANCE',
				'channel_code' => $transaction->getPaymentChannel(),
				'amount' => (int) $transaction->getAmount(),
				'reference_num' => $transaction->getReferenceNumber(),
				'name' => $transaction->getRemark(),
				'expire_in' => $transaction->getExpireIn(),
				'customer_id' => $transaction->getCustomerID() ?? '0000',
				'description' => $transaction->getDescription(),
				'customer_name' => $transaction->getCustomerName() ?? 'CUSTOMER',
				'customer_phone' => $transaction->getCustomerPhone() ?? '0888',
				'customer_email' => $transaction->getCustomerEmail() ?? 'email@customer.com',
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . $this->GetAccessToken(),
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
					!empty($content->rc)
					&& $content->rc == '00'
					&& !empty($content->data->billing_id)
				) {
					/* // Success // QRIS
						{
							"rc": "00",
							"rm": "SUCCESS",
							"message": "Success",
							"data": {
								"billing_id": "019ad434-56eb-7295-b9ce-3eb2d5bb5ed7",
								"reference_num": "QRIS1764496792",
								"billing_num": null,
								"name": "QRIS1764496792",
								"description": "Payment for ",
								"qr_string": "00020101021226730021COM.GUDANGVOUCHER.WWW011893600916300452416802151202240826000010303UBE51450015ID.OR.GPNQR.WWW02151202240826000010303UBE52045812530336054031005802ID5904KIVO6009TANGERANG6105155106267011025300459510307KMB000105202511305951105063yWv207141659512025113063044C55",
								"qr_url": "https:\/\/api-ibbv2.topglobal.my.id\/transaction\/qr\/2025\/11\/30\/QRIS1764496792.png",
								"deeplink_url": null,
								"net_amount": 100,
								"fee_total": 2,
								"fee_waived": 0,
								"fee_charged_to": "MERC",
								"amount": 100,
								"status": 0,
								"status_descr": "PENDING",
								"expire_in": "120",
								"expiring_at": "2025-11-30T10:59:51.000000Z",
								"created_at": "2025-11-30T09:59:51.000000Z",
								"customer_id": "0000",
								"customer_name": "CUSTOMER",
								"customer_phone": "0888",
								"customer_email": "email@customer.com"
							}
						}
					*/
					$res = $content;
				} elseif (
					!empty($content->rm) && stripos($content->rm, 'UNAUTHORIZED') !== false
				) {
					/* // Failed token
						{
							"rc": "99",
							"rm": "UNAUTHORIZED",
							"message": "Invalid token format."
						}
					*/
					$token_retry++;
					if ($token_retry > $this->max_retry) throw new \Exception('Max token retry reached', 800);
					// Request new
					$this->SetAccessToken($this->GetToken());
					goto retry;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed #1', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				if (!empty($content->rm) && stripos($content->rm, 'UNAUTHORIZED') !== false) {
					/* // Failed token
						{
							"rc": "99",
							"rm": "UNAUTHORIZED",
							"message": "Invalid token format."
						}
					*/
					$token_retry++;
					if ($token_retry > $this->max_retry) throw new \Exception('Max token retry reached', 800);
					// Request new
					$this->SetAccessToken($this->GetToken());
					goto retry;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed #2', $content ?? 'Unknown error']), 902);
				}
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? ['status'=>400,'data'=>$content??null], $status_code ?? 400);
	}

	public function Callback(object $request)
	{
		/* // Example incoming data // VA
		*/
		/* // Success QRIS
		*/
		try {
			if ($request->success == 'true') {
				$res = [
					'status' => '000',
					'data' => (array) $request->data,
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
			// $this->CheckToken();
			$this->GetToken();
			$token_retry = 0;
			retry:
			//
			SELF::Validate($request, [
				'reference_num',
			]);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/CheckBilling'
			);
			$this->request['data'] = [
				'merchant_uuid' => $this->init->getMerchantKey(),
				'service_code' => $request->service_code ?? 'ACCEPTANCE',
				'reference_num' => $request->reference_num,
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . $this->GetAccessToken(),
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
					!empty($content->rc)
					&& $content->rc == '00'
					&& !empty($content->data->billing_id)
				) {
					/* // Success inquiry QRIS
						{
							"rc": "00",
							"rm": "SUCCESS",
							"message": "Success",
							"data": {
								"billing_id": "019ad438-ce58-7000-8659-d893489f0313",
								"reference_num": "QRIS1764497085",
								"amount": 100,
								"status": "0",
								"status_descr": "PENDING",
								"expired_at": null,
								"created_at": "2025-11-30T10:04:43.000000Z",
								"paid_at": null,
								"settled_at": null
							}
						}
					*/
					$res = $content;
				} elseif (
					!empty($content->rm) && stripos($content->rm, 'UNAUTHORIZED') !== false
				) {
					/* // Failed token
						{
							"rc": "99",
							"rm": "UNAUTHORIZED",
							"message": "Invalid token format."
						}
					*/
					$token_retry++;
					if ($token_retry > $this->max_retry) throw new \Exception('Max token retry reached', 800);
					// Request new
					$this->SetAccessToken($this->GetToken());
					goto retry;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed #1', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				if (!empty($content->rm) && stripos($content->rm, 'UNAUTHORIZED') !== false) {
					/* // Failed token
						{
							"rc": "99",
							"rm": "UNAUTHORIZED",
							"message": "Invalid token format."
						}
					*/
					$token_retry++;
					if ($token_retry > $this->max_retry) throw new \Exception('Max token retry reached', 800);
					// Request new
					$this->SetAccessToken($this->GetToken());
					goto retry;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed #2', $content ?? 'Unknown error']), 902);
				}
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

	public function CheckBalance(object $request)
	{
		try {
			// $this->CheckToken();
			$this->GetToken();
			$token_retry = 0;
			retry:
			//
			SELF::Validate($request, [
				//
			]);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/CheckMerchantBalance'
			);
			$this->request['data'] = [
				'merchant_uuid' => $this->init->getMerchantKey(),
				'service_code' => $request->service_code ?? 'DISBURSEMENT',
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . $this->GetAccessToken(),
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
					!empty($content->rc)
					&& $content->rc == '00'
					&& !empty($content->data->balances)
				) {
					/* // Success check balance
						{
							"rc": "00",
							"rm": "Success",
							"message": "Success",
							"data": {
								"merchant_uuid": "019a7277-4006-7100-a149-abab4f3ed558",
								"balances": {
										"pending_balance": 0,
										"effective_balance": 0,
										"disbursement_balance": 9950
								}
							}
						}
					*/
					$res = $content;
				} elseif (
					!empty($content->rm) && stripos($content->rm, 'UNAUTHORIZED') !== false
				) {
					/* // Failed token
						{
							"rc": "99",
							"rm": "UNAUTHORIZED",
							"message": "Invalid token format."
						}
					*/
					$token_retry++;
					if ($token_retry > $this->max_retry) throw new \Exception('Max token retry reached', 800);
					// Request new
					$this->SetAccessToken($this->GetToken());
					goto retry;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed #1', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				if (!empty($content->rm) && stripos($content->rm, 'UNAUTHORIZED') !== false) {
					/* // Failed token
						{
							"rc": "99",
							"rm": "UNAUTHORIZED",
							"message": "Invalid token format."
						}
					*/
					$token_retry++;
					if ($token_retry > $this->max_retry) throw new \Exception('Max token retry reached', 800);
					// Request new
					$this->SetAccessToken($this->GetToken());
					goto retry;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed #2', $content ?? 'Unknown error']), 902);
				}
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function BankAccountInquiry(\Growinc\Payment\Transaction $transaction)
	{
		try {
			// $this->CheckToken();
			$this->GetToken();
			$token_retry = 0;
			retry:
			//
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/InquiryBankAccount'
			);
			$this->request['data'] = [
				'merchant_uuid' => $this->init->getMerchantKey(),
				'service_code' => $transaction->getParam('SERVICE_CODE') ?? 'DISBURSEMENT',
				'beneficiary_bank_code' => $transaction->getCustomerBankCode(),
				'beneficiary_bank_account_number' => $transaction->getCustomerBankAccountNumber(),
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . $this->GetAccessToken(),
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
					!empty($content->rc)
					&& $content->rc == '00'
					&& !empty($content->data->beneficiary_bank_account_name)
				) {
					/* // Success bank account inquiry
						{
							"rc": "00",
							"rm": "Success",
							"message": "Success",
							"data": {
								"beneficiary_bank_code": "014",
								"beneficiary_bank_account_number": "8760673566",
								"beneficiary_bank_account_name": "Test Account"
							}
						}
					*/
					$res = $content;
				} elseif (
					!empty($content->rm) && stripos($content->rm, 'UNAUTHORIZED') !== false
				) {
					/* // Failed token
						{
							"rc": "99",
							"rm": "UNAUTHORIZED",
							"message": "Invalid token format."
						}
					*/
					$token_retry++;
					if ($token_retry > $this->max_retry) throw new \Exception('Max token retry reached', 800);
					// Request new
					$this->SetAccessToken($this->GetToken());
					goto retry;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed #1', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				if (!empty($content->rm) && stripos($content->rm, 'UNAUTHORIZED') !== false) {
					/* // Failed token
						{
							"rc": "99",
							"rm": "UNAUTHORIZED",
							"message": "Invalid token format."
						}
					*/
					$token_retry++;
					if ($token_retry > $this->max_retry) throw new \Exception('Max token retry reached', 800);
					// Request new
					$this->SetAccessToken($this->GetToken());
					goto retry;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed #2', $content ?? 'Unknown error']), 902);
				}
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function OnlineTransferExec(\Growinc\Payment\Transaction $transaction)
	{
		try {
			// $this->CheckToken();
			$this->GetToken();
			$token_retry = 0;
			retry:
			//
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/CreateDisbursement'
			);
			$this->request['data'] = [
				'merchant_uuid' => $this->init->getMerchantKey(),
				'service_code' => $transaction->getParam('SERVICE_CODE') ?? 'DISBURSEMENT',
				'reference_num' => $transaction->getReferenceNumber(),
				'name' => $transaction->getRemark(),
				'description' => $transaction->getDescription(),
				'customer_id' => $transaction->getCustomerID() ?? '0000',
				'customer_name' => $transaction->getCustomerName() ?? 'CUSTOMER',
				'customer_phone' => $transaction->getCustomerPhone() ?? '0888',
				'customer_email' => $transaction->getCustomerEmail() ?? 'email@customer.com',
				'beneficiary_bank_code' => $transaction->getCustomerBankCode(),
				'beneficiary_bank_account_number' => $transaction->getCustomerBankAccountNumber(),
				'amount' => (int) $transaction->getAmount(),
				'transfer_method' => $transaction->getTransferMethod(),
				'allow_failover' => $transaction->getParam('ALLOW_FAILOVER') ?? false,
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . $this->GetAccessToken(),
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
					!empty($content->rc)
					&& $content->rc == '00'
					&& !empty($content->data->disbursement_id)
				) {
					/* // Success disb
						{
							"rc": "00",
							"rm": "Success",
							"message": "Success",
							"data": {
								"disbursement_id": "0198c07b-9ff2-73c8-a81a-f44f61baefef",
								"reference_num": "DISB1755575983",
								"name": "DISBURSEMENT 1755575983",
								"description": "SENDING DISBURSEMENT 1755575983",
								"net_amount": 10000,
								"fee_total": 4500,
								"fee_waived": 0,
								"fee_charged_to": "CUST",
								"amount": 10000,
								"status": 0,
								"expire_in": null,
								"expiring_at": "2025-08-19T04:00:44.000000Z",
								"created_at": "2025-08-19T03:59:44.000000Z",
								"customer_id": "1234567890",
								"customer_name": "PG",
								"customer_phone": "081212121314",
								"customer_email": "tester@gmail.com",
								"beneficiary_bank_code": "014",
								"beneficiary_bank_account_number": "8760673566",
								"beneficiary_bank_account_name": ""
							}
						}
					*/
					$res = $content;
				} elseif (
					!empty($content->rm) && stripos($content->rm, 'UNAUTHORIZED') !== false
				) {
					/* // Failed token
						{
							"rc": "99",
							"rm": "UNAUTHORIZED",
							"message": "Invalid token format."
						}
					*/
					$token_retry++;
					if ($token_retry > $this->max_retry) throw new \Exception('Max token retry reached', 800);
					// Request new
					$this->SetAccessToken($this->GetToken());
					goto retry;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed #1', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				if (!empty($content->rm) && stripos($content->rm, 'UNAUTHORIZED') !== false) {
					/* // Failed token
						{
							"rc": "99",
							"rm": "UNAUTHORIZED",
							"message": "Invalid token format."
						}
					*/
					$token_retry++;
					if ($token_retry > $this->max_retry) throw new \Exception('Max token retry reached', 800);
					// Request new
					$this->SetAccessToken($this->GetToken());
					goto retry;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed #2', $content ?? 'Unknown error']), 902);
				}
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

	public function OnlineTransferCheckTransfer(object $request)
	{
		try {
			// $this->CheckToken();
			$this->GetToken();
			$token_retry = 0;
			retry:
			//
			SELF::Validate($request, [
				'reference_num',
			]);
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/CheckDisbursement'
			);
			$this->request['data'] = [
				'merchant_uuid' => $this->init->getMerchantKey(),
				'service_code' => $request->service_code ?? 'DISBURSEMENT',
				'reference_num' => $request->reference_num,
			];
			$this->request['headers'] = [
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . $this->GetAccessToken(),
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
					!empty($content->rc)
					&& $content->rc == '00'
					&& !empty($content->data->disbursement_uuid)
				) {
					/* // Success check transfer
						{
							"rc": "00",
							"rm": "SUCCESS",
							"message": "Success",
							"data": {
								"disbursement_uuid": "0199cd5a-9bfe-7139-9c36-9430be6803b8",
								"reference_num": "DISB1760086891",
								"transfer_method": "BIFAST",
								"amount": 20000,
								"beneficiary_bank_code": "008",
								"beneficiary_bank_account_number": "8760673566",
								"beneficiary_bank_account_name": "DAVID MARTINEZ",
								"status": 6,
								"status_descr": "SETTLED",
								"created_at": "2025-10-10T09:01:31.000000Z",
								"sent_at": "2025-10-10T09:01:58.000000Z"
							}
						}
					*/
					$res = $content;
				} elseif (
					!empty($content->rm) && stripos($content->rm, 'UNAUTHORIZED') !== false
				) {
					/* // Failed token
						{
							"rc": "99",
							"rm": "UNAUTHORIZED",
							"message": "Invalid token format."
						}
					*/
					$token_retry++;
					if ($token_retry > $this->max_retry) throw new \Exception('Max token retry reached', 800);
					// Request new
					$this->SetAccessToken($this->GetToken());
					goto retry;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed #1', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				if (!empty($content->rm) && stripos($content->rm, 'UNAUTHORIZED') !== false) {
					/* // Failed token
						{
							"rc": "99",
							"rm": "UNAUTHORIZED",
							"message": "Invalid token format."
						}
					*/
					$token_retry++;
					if ($token_retry > $this->max_retry) throw new \Exception('Max token retry reached', 800);
					// Request new
					$this->SetAccessToken($this->GetToken());
					goto retry;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed #2', $content ?? 'Unknown error']), 902);
				}
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
	}

}
