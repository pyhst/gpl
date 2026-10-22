<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

use Growinc\Payment\Helper;

class MawarPay extends Requestor implements VendorInterface
{
	use Helper;

	protected $form;
	protected $access_token;

	//

	public function RetrievePrivateKey()
	{
		$private_key = $this->init->getPrivateKey();
		if (!empty($private_key)) {
			return
				"-----BEGIN PRIVATE KEY-----\n" .
				chunk_split($private_key, 64, "\n") .
				"-----END PRIVATE KEY-----\n";
		}
		if (!$cert_store = file_get_contents($this->init->getPrivateKey())) {
			return "Error: Unable to read the cert file\n";
		}
		return $cert_store;
	}

	public function VerifySignature($string_to_sign, $signature, $public_key = null)
	{
		if (!$public_key) {
			$public_key = $this->init->getPublicKey();
			if (!empty($public_key)) {
				$public_key =
					"-----BEGIN PUBLIC KEY-----\n" .
					chunk_split($public_key, 64, "\n") .
					"-----END PUBLIC KEY-----\n";
			}
		}
		$verified = openssl_verify($string_to_sign, base64_decode($signature), $public_key, OPENSSL_ALGO_SHA256);
		return $verified;
	}

	//

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args = null)
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

	public function RedirectPayment(\Growinc\Payment\Transaction $transaction)
	{
		// Not applicable
	}

	public function SecurePayment(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$date = new \DateTime('now', new \DateTimeZone('UTC'));
			//
			$url = '/api-pay/payment/createApi';
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				$url
			);
			$this->request['data'] = [
				'mchId' => $this->init->getMID(),
				'tradeType' => 'payment.create',
				'version' => 'V1.0',
				'requestTime' => $date->format('Y-m-d\TH:i:s.v\Z'),
				'outTradeNo' => $transaction->getReferenceNumber(),
				'amount' => (string) $transaction->getAmount() . '.00',
				'channel' => $transaction->getPaymentChannel(),
				'payer' => $transaction->getCustomerName(),
				'mobilePhone' => $transaction->getCustomerPhone(),
				'email' => $transaction->getCustomerEmail(),
				'goodsInfo' => $transaction->getDescription(),
				'expiredTime' => (60 * $transaction->getExpireIn()),
				'notifyUrl' => $this->init->getCallbackURL(),
				'callbackUrl' => $this->init->getCallbackURL(),
			];
			//
			$payload = json_encode($this->request['data']);
			$string_to_sign = implode('&', [
				$payload,
				$url,
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$this->request['headers'] = [
				'Content-Type' => 'application/json;charset=utf-8',
				'Sign' => base64_encode($signature),
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
					(isset($content->returnCode) && $content->returnCode == 'SUCCESS')
					&& !empty($content->outChannelNo)
				) {
					/* // Success QRIS
						{
							"returnCode": "SUCCESS",
							"mchId": "410022",
							"outTradeNo": "1792310941",
							"outChannelNo": "2026101802200001413",
							"amount": 15000,
							"payContentType": "04",
							"payContent": "{\"qrisUrl\":\"https://id-payer.smmslink.com/payer/en-qris-index-v2.html?a=S3h6TXdCU3BQR04xQ2lUcC9CWkdLYy9hZk9tTktWNEpMRFc4K2VKVzlObz0=\",\"qrCode\":\"00020101021226670016COM.NOBUBANK.WWW01189360050300000850300214260909000007860303UBE51440014ID.CO.QRIS.WWW0303UBE0215ID2026590064898520465335303360540815000.005802ID5914Novexa payment6013JAKARTA TIMUR61051314062720108MB8T9WL6061920261018054007920280703A010804POSP991800020001082026101863043C62\"}",
							"transTime": "20261018150902",
							"expiredTime": "20261019150902"
						}
					*/
					/* // Success VA
					*/
					/* // Success EWALLET
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
			$date = new \DateTime('now', new \DateTimeZone('UTC'));
			//
			$url = '/api-pay/payment/query';
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				$url
			);
			$this->request['data'] = [
				'mchId' => $this->init->getMID(),
				'tradeType' => 'payment.query',
				'version' => 'V1.0',
				'requestTime' => $date->format('Y-m-d\TH:i:s.v\Z'),
				'outTradeNo' => $request->outTradeNo,
				'queryType' => '1',
			];
			//
			$payload = json_encode($this->request['data']);
			$string_to_sign = implode('&', [
				$payload,
				$url,
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$this->request['headers'] = [
				'Content-Type' => 'application/json;charset=utf-8',
				'Sign' => base64_encode($signature),
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
					(isset($content->returnCode) && $content->returnCode == 'SUCCESS')
					&& !empty($content->outChannelNo)
				) {
					/* // Success inquiry QRIS
						{
							"returnCode": "SUCCESS",
							"outTradeNo": "1792311038",
							"outChannelNo": "2026101802200001417",
							"transTime": "20261018151039",
							"expiredTime": "20261019151040",
							"payContentType": "04",
							"payContent": "{\"qrisUrl\":\"https://id-payer.smmslink.com/payer/en-qris-index-v2.html?a=S3h6TXdCU3BQR04xQ2lUcC9CWkdLUW92amxKeVBhdE9nVk9RUzVlZmphdz0=\",\"qrCode\":\"00020101021226670016COM.NOBUBANK.WWW01189360050300000850300214260909000007850303UBE51440014ID.CO.QRIS.WWW0303UBE0215ID2026590064880520465335303360540815000.005802ID5917Swiftplus payment6013JAKARTA TIMUR61051314062720108MB8TADP3061920261018054007935020703A010804POSP991800020001082026101863049486\"}",
							"amount": 15000,
							"transFeeRate": 0.008,
							"transFee": 0,
							"totalTransFee": 120,
							"mchId": "410022",
							"status": "01",
							"payChannelType": "QRIS"
						}
					*/
					/* // Success inquiry VA
					*/
					/* // Success inquiry EWALLET
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
		try {
			$date = new \DateTime('now', new \DateTimeZone('UTC'));
			//
			$url = '/api-pay/remit/balance/query';
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				$url
			);
			$this->request['data'] = [
				'mchId' => $this->init->getMID(),
				'tradeType' => 'remit.balance.query',
				'version' => 'V1.0',
				'requestTime' => $date->format('Y-m-d\TH:i:s.v\Z'),
			];
			//
			$payload = json_encode($this->request['data']);
			$string_to_sign = implode('&', [
				$payload,
				$url,
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$this->request['headers'] = [
				'Content-Type' => 'application/json;charset=utf-8',
				'Sign' => base64_encode($signature),
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
					(isset($content->returnCode) && $content->returnCode == 'SUCCESS')
					&& isset($content->balanceAmt)
				) {
					/* // Success check balance
						{
							"returnCode": "SUCCESS",
							"mchId": "410022",
							"withdrawnAmt": 0,
							"balanceAmt": 0,
							"rechargingAmt": 0,
							"totalBalance": 0
						}
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

	public function BankAccountInquiry(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$date = new \DateTime('now', new \DateTimeZone('UTC'));
			//
			$url = '/api-pay/remit/account/inquiry';
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				$url
			);
			$this->request['data'] = [
				'mchId' => $this->init->getMID(),
				'tradeType' => 'remit.inquiry',
				'version' => 'V1.0',
				'requestTime' => $date->format('Y-m-d\TH:i:s.v\Z'),
				'outTradeNo' => $transaction->getReferenceNumber(),
				'amount' => (string) $transaction->getAmount() . '.00',
				'bankCode' => $transaction->getCustomerBankCode(),
				'bankId' => $transaction->getCustomerBankId(),
				'bankName' => $transaction->getCustomerBankName(),
				'bankCardNo' => $transaction->getCustomerBankAccountNumber(),
				'bankCardHolder' => $transaction->getCustomerBankAccountName(),
				'city' => $transaction->getCustomerCity(),
				'email' => $transaction->getCustomerEmail(),
			];
			//
			$payload = json_encode($this->request['data']);
			$string_to_sign = implode('&', [
				$payload,
				$url,
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$this->request['headers'] = [
				'Content-Type' => 'application/json;charset=utf-8',
				'Sign' => base64_encode($signature),
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
					(isset($content->returnCode) && $content->returnCode == 'SUCCESS')
					&& !empty($content->outTradeNo)
				) {
					/* // Success bank account inquiry
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

	public function OnlineTransferExec(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$date = new \DateTime('now', new \DateTimeZone('UTC'));
			//
			$url = '/api-pay/remit/order/create';
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				$url
			);
			$this->request['data'] = [
				'mchId' => $this->init->getMID(),
				'tradeType' => 'remit.create',
				'version' => 'V1.0',
				'requestTime' => $date->format('Y-m-d\TH:i:s.v\Z'),
				'outTradeNo' => $transaction->getReferenceNumber(),
				'idToken' => $transaction->getOrderID(),
				'amount' => (string) $transaction->getAmount() . '.00',
				'bankCode' => $transaction->getCustomerBankCode(),
				'bankId' => $transaction->getCustomerBankId(),
				'bankName' => $transaction->getCustomerBankName(),
				'bankCardNo' => $transaction->getCustomerBankAccountNumber(),
				'bankCardHolder' => $transaction->getCustomerBankAccountName(),
				'firstName' => $transaction->getCustomerName(),
				'lastName' => $transaction->getCustomerName(),
				'city' => $transaction->getCustomerCity(),
				'phone' => $transaction->getCustomerPhone(),
				'email' => $transaction->getCustomerEmail(),
				'summary' => $transaction->getDescription(),
				'notifyUrl' => $this->init->getCallbackURL(),
			];
			//
			$payload = json_encode($this->request['data']);
			$string_to_sign = implode('&', [
				$payload,
				$url,
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$this->request['headers'] = [
				'Content-Type' => 'application/json;charset=utf-8',
				'Sign' => base64_encode($signature),
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
					(isset($content->returnCode) && $content->returnCode == 'SUCCESS')
					&& !empty($content->outTradeNo)
				) {
					/* // Success disb
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

	public function OnlineTransferCheckTransfer(object $request)
	{
		try {
			$date = new \DateTime('now', new \DateTimeZone('UTC'));
			//
			$url = '/api-pay/remit/order/query';
			$this->request['url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				$url
			);
			$this->request['data'] = [
				'mchId' => $this->init->getMID(),
				'tradeType' => 'remit.query',
				'version' => 'V1.0',
				'requestTime' => $date->format('Y-m-d\TH:i:s.v\Z'),
				'outTradeNo' => $request->outTradeNo,
			];
			//
			$payload = json_encode($this->request['data']);
			$string_to_sign = implode('&', [
				$payload,
				$url,
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$this->request['headers'] = [
				'Content-Type' => 'application/json;charset=utf-8',
				'Sign' => base64_encode($signature),
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
					(isset($content->returnCode) && $content->returnCode == 'SUCCESS')
					&& !empty($content->outChannelNo)
				) {
					/* // Success check status
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

}
