<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;

use PyGPaymentLib\Helper;
class Odeo extends Requestor implements VendorInterface
{
	use Helper;

	protected $form;

	protected $body_json;
	protected $body_hashed;
	protected $body_hashed_encoded;
	protected $string_to_sign;
	protected $signature;


	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args = null)
	{
		try {
			$this->request['time'] = time();
			$this->request['data'] = [
				'client_id' => $this->init->getMID(),
				'client_secret' => $this->init->getSecret(),
				'grant_type' => 'client_credentials',
			];
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/oauth2/token'
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			//
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (!empty($content->access_token)) {
					/* // Success
						{
							"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2NzYwOTMzMDIsImV4cCI6MTY3NjA5NTEwMiwiY2lkIjoiMkhJam5OUFcxbmpqbTNHbzBjdlVJT3VRRjE2REw0QnUiLCJ1aWQiOjE3NTQwLCJ1cGYiOjEwMCwic2NwIjoiIn0.Es8F_VY8entgkfHFyIq3T-gc_C2EpA4s9cEfQjKXsxc",
							"expires_in": 1800,
							"scope": "",
							"token_type": "Bearer"
						}
					*/
					$res = $content;
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed, token is empty', $content ?? 'Unknown error']), 901);
				}
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
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

	public function Signature($method = 'POST', $data = '', $path = '', $query = '')
	{
		$body_json = $data;
		$body_json = empty($body_json) ? '' : json_encode($body_json);
		$body_hashed = hash('sha256', $body_json, true);
		$body_hashed_encoded = base64_encode($body_hashed);
		$string_to_sign = implode(':', [
			$method,
			$path,
			$query,
			$this->init->getParams()['access_token'],
			$this->request['time'],
			$body_hashed_encoded,
		]);
		$signature = base64_encode(
			hash_hmac('sha256', $string_to_sign, $this->init->getSigningKey(), true)
		);
		//
		$this->body_json = $body_json;
		$this->body_hashed = $body_hashed;
		$this->body_hashed_encoded = $body_hashed_encoded;
		$this->string_to_sign = $string_to_sign;
		$this->signature = $signature;
		return $signature;
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
			$this->request['time'] = $transaction->getTime();
			$payment_method = $transaction->getPaymentMethod();
			$payment_channel = $transaction->getPaymentChannel();
			//
			switch ($payment_method) {
				case 'qris':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/pg/v1/payment/qris/create'
					);
					$this->request['data'] = [
						'amount' => (float) $transaction->getAmount(),
						'validity_time' => $transaction->getExpireIn() * 60, // minutes * 60 = secs
						'merchant_reference_no' => $transaction->getReferenceNumber(),
					];
					$this->request['headers'] = [
						'Authorization' => 'Bearer ' . $this->init->getParams()['access_token'],
						'Content-Type' => 'application/json',
						'X-Odeo-Timestamp' => $this->request['time'],
						'X-Odeo-Signature' => $this->Signature('POST', $this->request['data'], '/pg/v1/payment/qris/create'),
					];
					$this->request['option'] = [
						'as_json' => 1,
					];
					$post = $this->DoRequest('POST', $this->request);
					$response = (array) $post['response'];
					extract($response);
					//
					if (!empty($content) && Helper::is_JSON($content)) {
						/* // Success // QRIS
							{
								"transaction_id": 377,
								"transaction_no": "2023021563ECF97AB0FA7",
								"merchant_reference_no": "1676474745",
								"amount": 10000,
								"fee": 1150,
								"qris_data": "00020101021226670016COM.NOBUBANK.WWW01189360050300000488820214030800000313890303UMI51440014ID.CO.QRIS.WWW0215ID20200814001600303UMI5204549953033605405100005802ID5907PT ODEO6015Banten - Kota T610515811625801140215000009414006212023021563ECF97AB0FA70703A010804POSP6304EDC9",
								"qris_image": "iVBORw0KGgoAAAANSUhEUgAAAUUAAAFFCAIAAAD0FmgKAAAABnRSTlMA\/wD\/AP83WBt9AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAKlUlEQVR4nO3d225lOY4FwPRg\/v+Xa943EppWi6TkVRGvPvvik14QkqCon3\/++ecPEOF\/br8AUEaeIYc8Qw55hhzyDDnkGXLIM+SQZ8ghz5BDniGHPEMOeYYc8gw55BlyyDPkkGfIIc+QQ54hhzxDjv89ufjn56fqPdY+Q87Wz92aiLb1K6xf42QS2\/pWW7\/v1lttPXfrVuuX3HqNwg+vFd7qxMkfkvUZcsgz5JBnyCHPkOOoHvZROJr\/pAL0SIlrrbAQNeZXvNXaVqFx61YnCitt1mfIIc+QQ54hhzxDjsp62Edha876zlvFpJPi2frDWzW8RwpgWw86+Z4L71zY8Vao7699i\/UZcsgz5JBnyCHPkKOxHjamcLPhlq0HFe55PCnq\/Iqi3UnFa+ybfJP1GXLIM+SQZ8ghz5AjoR720TeMqrCbav0at+48Vh\/qK2Hmlbi2WJ8hhzxDDnmGHPIMORrrYWP9Q1sFocLNd1tvVdhrNXacQOGXs\/UaJ1tct64t\/BN9ZJqa9RlyyDPkkGfIIc+Qo7Ie9mZ30b\/tpx99Jb1Cv\/G7erMRzfoMOeQZcsgz5JBnyPHzSF\/LmLGupvWHt\/RNROubanbr2\/i3\/T1\/WJ8hhzxDDnmGHPIMOY76wwqrGus7nxSE+pqN3uyXutWld2KsxDX2F7t1q8Lf1\/oMOeQZcsgz5JBnyFG5X\/KkM+mk1HSrXlI4i\/+kpnWrWPjR987rBxUOOSusS92qWVqfIYc8Qw55hhzyDDmO6mGP7OPbUljj6bu2b+rVLWOddienbZ6UA2\/tHv2wPkMOeYYc8gw55BlyPLpf8qR\/qFDhrPa+kk9hFefkuW9uU+3bmTh2bukW6zPkkGfIIc+QQ54hR+V+ybWTYtLJgx7Rt43x5Pc9KXE9spty653Xb1XYtXZr8pz1GXLIM+SQZ8ghz5CjcX7YSQnkY6sS80gZ46Qwc3LnwtFrfTW89YMKJ7EVVun6Srb6w4C\/kGfIIc+QQ54hR2N\/2El3UWFxZeut+jqT+gozYwPlC\/vSCmuW6zv3VZ7GHrTF+gw55BlyyDPkkGfIMTdPf+vavtnlfbf6Fecebuk7TmDsvIS1vrllJ291wvoMOeQZcsgz5JBnyPEzdrTf2Lz1vs6ztbHR7evnbt2qb+jXlsITAvoGla2vXRv7Jq3PkEOeIYc8Qw55hhxH9bCxExXH\/MZq2dojmysLB5VtKWxxe6RZcM36DDnkGXLIM+SQZ8jxyjz9kwd9jO1NGytxjTXePfLFrp9beGDA+kFbPz0ZJmeePvAX8gw55BlyyDPkqOwPu3Wrsc1o6+eO3bmwLtW3mfTkZIKPW5tn16+xRX8YsE2eIYc8Qw55hhyV\/WGFZzX2lV7GyidbTjqTbumreG1du9Z3MunWtWM7i63PkEOeIYc8Qw55hhyN+yW3fnqrEPVReK7lrVMvx+qOWz8t9CvO8Sys0W6xPkMOeYYc8gw55BlyzPWHrT\/cd6uPwrLN2P7QsbFehSW9W2ctFPZpFf4bjf0LWp8hhzxDDnmGHPIMOSrrYR9bM8THeq3W+mZErT88drDjI\/O01h7ptPsorPD1fXXWZ8ghz5BDniGHPEOOufMl+7rH1voqQIV74sa+jcJrP\/q2uPYVwMb+UcY2k1qfIYc8Qw55hhzyDDmO6mFj5x5+PFJr2fpwYbXspFFp64vtG0X2K2aAre9cyDx94C\/kGXLIM+SQZ8gx1x\/2Mdbz1LeR7aRq1edWVXLr2rW+2mHfDtBbDWEf1mfIIc+QQ54hhzxDjsZ5+h9jk5z6FM5bf+TAgL7KU9\/0uPVLFhr7JgtZnyGHPEMOeYYc8gw5juphfaWIwvpBYdVqrPVq69rCSszJr3+rI6qwtLb2SAfYmvUZcsgz5JBnyCHPkONXzg87sVXU6WtUKixTrf3GxrutD\/edPrn+8JaxmWfWZ8ghz5BDniGHPEOOn76B432zmtZuNe6MzYgf0zcDrO+Uz5O3KtR32uaa9RlyyDPkkGfIIc+QY26\/5NZGtjdrWmO77U4alU6+55MGuHVr3dZLbn247+9q7Fr9YcBfyDPkkGfIIc+Qo3G\/ZF\/16OTOfcO3TtzqANtyq23rVgtj4a22Thc4YX2GHPIMOeQZcsgz5Kg8X\/JE3wSprXMPx2aArT0ymG2sxHXrtM2Tf+6+5+oPA\/78kWdIIs+QQ54hR+V+ycLa0lpfw9DYg7YG95+MfX\/kpM71rQoV\/r5932RfvdP6DDnkGXLIM+SQZ8hR2R9WeNLf+s6F+yW3FJ4vub725LjJNzeT9k1E23pQX\/nz1sCwD+sz5JBnyCHPkEOeIce18yVPblXYl1bYenXyoID+sLFvY32rj1tFu61rC1mfIYc8Qw55hhzyDDkq62HfW08dKPlINWV97YlbzUZj3VTrO986QfJWHc78MODPH3mGJPIMOeQZcjTWw75Pmqo9rN069\/CR6mDhtbem7a8\/vH7QI\/1h9ksC\/z95hhzyDDnkGXLM9Yd9FJ4wONaotHXt2tg5j3236rtz4cbMMY\/sprQ+Qw55hhzyDDnkGXI0ni9ZOPd8rKlrbCbWWuFQ+L4Gqb66VN9Qt74vp\/BEBPslgT9\/5BmSyDPkkGfIcW2\/5IeGsP\/cI5PJHtkhuGVsa+2t\/j\/rM+SQZ8ghz5BDniHH3PmSv2J+2G8s2hU2Oa0fdHKrLW8OVzsx9lzrM+SQZ8ghz5BDniHH0X7Jj5P\/5d86uvHWdsK+bZtbth40dtrmyYdvnT45Vv1dsz5DDnmGHPIMOeQZclTWw7Y80i524qSmdfKSt84eWDsZJre+1dZPPx7Zpqo\/DNgmz5BDniGHPEOOo\/2SfecA9g3QL2zNeaTU1Pegj8JhY4XdVOuXHOv\/G9umumZ9hhzyDDnkGXLIM+SY6w\/bKhj0bYJb69vItn7QWH1oy9ZbjZ2oWHgK5NaDCg\/9PHmrNesz5JBnyCHPkEOeIUdlPayvYLD14X\/5yPhbpyL2vUZhGbKvLPfIJlbrM+SQZ8ghz5BDniFH5fmShW6Nud+6tm9\/aN9evC2\/4rmPTAhbG0uZ9RlyyDPkkGfIIc+Q49o8\/bW+SsxY61XhbrstY8c+ngxmG3vuyWuMnQGgPwz4C3mGHPIMOeQZchzVw26d5HhrsvmteetjJxuO1dLWzx2bAXby3L7i6AnrM+SQZ8ghz5BDniFHZX\/YIzvX+rrH1lWNkxH5hUc3frw5Euzkw+tr1x7pDuzbPml9hhzyDDnkGXLIM+Ro3C\/5yDGIY899RN8Ys8JWs8L9koVFypOXfIT1GXLIM+SQZ8ghz5Dj0flhayeFmcJ6yVjD0JtFu7FxYrdOU9i6VeFPT1ifIYc8Qw55hhzyDDl+ZT3sxFhx5aTIsXXnR4ZvbekrQz5y5oH9ksApeYYc8gw55BlyNNbDxrYT9o2bvzVPa+tBhScb9h0YsNWlV6iwT6vvT6WwWmZ9hhzyDDnkGXLIM+SorIfdmrc0Vpc68eZ2wsIzIj\/Gpottvcatf\/2T0xS2WJ8hhzxDDnmGHPIMOX7eHAoP\/Besz5BDniGHPEMOeYYc8gw55BlyyDPkkGfIIc+QQ54hhzxDDnmGHPIMOeQZcsgz5JBnyCHPkEOeIYc8Q47\/AykmQgSJTYKsAAAAAElFTkSuQmCC",
								"status": 10000
							}
						*/
						/* // Status list
							10000	QRIS created.
							50000	Completed payment.
							80000	Suspect payment inquiry.
							90000	Failed payment inquiry.
							90001	Cancelled payment inquiry.
							90002	Expired payment inquiry.
							90003	Refunded payment inquiry.
						*/
						$content = (object) json_decode($content);
						$res = [
							// 'status' => (!empty($content->status) && $content->status == '10000') ? '000' : $content->status,
							'status' => $content->status,
							'data' => (array) $content,
						];
					} else {
						throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
					}
					break;
				case 'va':
					// Go
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
				default:
					throw new \Exception('Payment method not defined/unknown', 900);
					break;
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
			SELF::Validate($request, [
				'payment_method',
			]);
			$this->request['time'] = time();
			switch ($request->payment_method) {
				case 'qris':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getRequestURL() .
						'/pg/v1/qris/payment/find'
					);
					$this->request['data'] = [
						'merchant_reference_no' => $request->reference_num,
					];
					$this->request['headers'] = [
						'Authorization' => 'Bearer ' . $this->init->getParams()['access_token'],
						'Accept' => 'application/json',
						'X-Odeo-Timestamp' => $this->request['time'],
						'X-Odeo-Signature' => $this->Signature('GET', '', '/pg/v1/qris/payment/find', "merchant_reference_no=$request->reference_num"),
					];
					$this->request['option'] = [
						'as_json' => 0,
					];
					//
					$get = $this->DoRequest('GET', $this->request);
					$response = (array) $get['response'];
					extract($response);
					//
					if (!empty($content) && Helper::is_JSON($content)) {
						/* // Success
							{
								"amount": 10000,
								"fee": 1150,
								"transaction_id": 378,
								"transaction_no": "2023021563ECFE4CC9275",
								"merchant_reference_no": "1676475979",
								"status": 10000
							}
						*/
						/* // Status list
							10000	QRIS created.
							50000	Completed payment.
							80000	Suspect payment inquiry.
							90000	Failed payment inquiry.
							90001	Cancelled payment inquiry.
							90002	Expired payment inquiry.
							90003	Refunded payment inquiry.
						*/
						$content = (object) json_decode($content);
						$res = [
							// 'status' => (!empty($content->status) && $content->status == '10000') ? '000' : $content->status,
							'status' => $content->status,
							'data' => (array) $content,
						];
					} else {
						throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
					}
					break;
				default:
					throw new \Exception('Payment method not defined/unknown', 900);
					break;
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
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

	public function TestPayment(object $request)
	{
		try {
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/pg/v1/payment/qris/test-callback'
			);
			$this->request['data'] = [
				'transaction_id' => $request->transaction_id,
			];
			$this->request['headers'] = [
				'Authorization' => 'Bearer ' . $this->init->getParams()['access_token'],
				'Content-Type' => 'application/json',
				'X-Odeo-Timestamp' => $this->request['time'],
				'X-Odeo-Signature' => $this->Signature('POST', $this->request['data'], '/pg/v1/payment/qris/test-callback'),
			];
			$this->request['option'] = [
				'as_json' => 1,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			//
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success
				*/
				$content = (object) json_decode($content);
				$res = [
					// 'status' => (!empty($content->status) && $content->status == '10000') ? '000' : $content->status,
					'status' => $content->status,
					'data' => (array) $content,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}



	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\
	|
	| Disbursement
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\ ///////////////////////*/

	public function BankList()
	{
		try {
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/dg/v1/banks'
			);
			$this->request['data'] = [];
			$this->request['headers'] = [
				'Authorization' => 'Bearer ' . $this->init->getParams()['access_token'],
				'Accept' => 'application/json',
				'X-Odeo-Timestamp' => $this->request['time'],
				'X-Odeo-Signature' => $this->Signature('GET', '', '/dg/v1/banks'),
			];
			$this->request['option'] = [
				'as_json' => 0,
			];
			//
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			//
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success
					{
						"banks": [{
							"bank_id": 1,
							"name": "BCA",
							"bank_code": "014",
							"swift_code": "CENAIDJA"
						}, {
							"bank_id": 2,
							"name": "BNI",
							"bank_code": "009",
							"swift_code": "BNINIDJA"
						}, {
							"bank_id": 3,
							"name": "BRI",
							"bank_code": "002",
							"swift_code": "BRINIDJA"
						}]
					}
				*/
				$content = (object) json_decode($content);
				$res = [
					'status' =>'000',
					'data' => (array) $content,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function CheckBalance()
	{
		try {
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/cash/me/balance'
			);
			$this->request['data'] = [];
			$this->request['headers'] = [
				'Authorization' => 'Bearer ' . $this->init->getParams()['access_token'],
				'Accept' => 'application/json',
				'X-Odeo-Timestamp' => $this->request['time'],
				'X-Odeo-Signature' => $this->Signature('GET', '', '/cash/me/balance'),
			];
			$this->request['option'] = [
				'as_json' => 0,
			];
			//
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			//
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success
					{
						"cash": {
							"amount": 865002,
							"currency": "IDR",
							"formatted_amount": "Rp865,002"
						},
						"locked_cash": {
							"amount": 0,
							"currency": "IDR",
							"formatted_amount": "Rp0"
						}
					}
				*/
				$content = (object) json_decode($content);
				$res = [
					'status' =>'000',
					'data' => (array) $content,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function BankAccountInquiry(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/dg/v1/bank-account-inquiry'
			);
			$this->request['data'] = [
				'account_number' => $transaction->getCustomerBankAccountNumber(),
				'bank_id' => $transaction->getCustomerBankCode(),
				'customer_name' => $transaction->getCustomerName(),
				'with_validation' => false,
			];
			$this->request['headers'] = [
				'Authorization' => 'Bearer ' . $this->init->getParams()['access_token'],
				'Content-Type' => 'application/json',
				'X-Odeo-Timestamp' => $this->request['time'],
				'X-Odeo-Signature' => $this->Signature('POST', $this->request['data'], '/dg/v1/bank-account-inquiry'),
			];
			$this->request['option'] = [
				'as_json' => 1,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			//
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success
					{
						"bank_id": "014",
						"account_number": "6044222099",
						"account_name": "Ronathan",
						"customer_name": "TESTER",
						"fee": 1000,
						"status": 50000,
						"created_at": "1676547770",
						"max_amount": null,
						"bank_account_inquiry_id": "3799"
					}
				*/
				/* // Status list
					50000	Completed inquiry.
					90000	Failed inquiry.
					90001	Wrong bank account number.
					90003	Closed bank account.
					90004	Inquiry rejected by the vendor bank.
					90005	Inquiry vendor bank is down.
				*/
				$content = (object) json_decode($content);
				$res = [
					// 'status' => (!empty($content->status) && $content->status == '50000') ? '000' : $content->status,
					'status' => $content->status,
					'data' => (array) $content,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function OnlineTransferExec(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/dg/v1/disbursements'
			);
			$this->request['data'] = [
				'account_number' => $transaction->getCustomerBankAccountNumber(),
				'amount' => (float) $transaction->getAmount(),
				'bank_id' => $transaction->getCustomerBankCode(),
				'customer_name' => $transaction->getCustomerName(),
				'description' => $transaction->getDescription(),
				'reference_id' => $transaction->getReferenceNumber(),
				'with_validation' => false,
			];
			$this->request['headers'] = [
				'Authorization' => 'Bearer ' . $this->init->getParams()['access_token'],
				'Content-Type' => 'application/json',
				'X-Odeo-Timestamp' => $this->request['time'],
				'X-Odeo-Signature' => $this->Signature('POST', $this->request['data'], '/dg/v1/disbursements'),
			];
			$this->request['option'] = [
				'as_json' => 1,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			//
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success
					{
						"disbursement_id": "7185",
						"bank_id": "014",
						"bank_code": "061",
						"account_number": "6044222099",
						"customer_name": "TESTER",
						"amount": 10500,
						"fee": 5500,
						"mdr": 0,
						"description": "TESTING DISBURSEMENT",
						"reference_id": "1676555844",
						"status": 10000,
						"created_at": "1676555845"
					}
				*/
				/* // Status list
					10000	Pending disburse inquiry.
					10002	Pending disburse inquiry due to bank reconciliation.
					30000	Disbursement is on progress.
					50000	Completed disbursement.
					80000	Suspect disburse inquiry.
					90000	Failed disburse inquiry.
					90001	Wrong bank account number.
					90003	Closed bank account.
					90004	Inquiry rejected by the vendor bank.
					90005	Inquiry vendor bank is down.
				*/
				$content = (object) json_decode($content);
				$res = [
					// 'status' => (!empty($content->status) && $content->status == '50000') ? '000' : $content->status,
					'status' => $content->status,
					'data' => (array) $content,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

	public function CheckTransfer(object $request)
	{
		try {
			$this->request['time'] = time();
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/dg/v1/disbursements/' . $request->disbursement_id
			);
			$this->request['data'] = [];
			$this->request['headers'] = [
				'Authorization' => 'Bearer ' . $this->init->getParams()['access_token'],
				'Accept' => 'application/json',
				'X-Odeo-Timestamp' => $this->request['time'],
				'X-Odeo-Signature' => $this->Signature('GET', '', '/dg/v1/disbursements/' . $request->disbursement_id),
			];
			$this->request['option'] = [
				'as_json' => 0,
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			//
			if (!empty($content) && Helper::is_JSON($content)) {
				/* // Success
					{
						"disbursement_id": "7189",
						"bank_id": 14,
						"bank_code": "061",
						"account_number": "6044222099",
						"customer_name": "TESTER",
						"amount": 10500,
						"fee": 5500,
						"mdr": 0,
						"description": "TESTING DISBURSEMENT",
						"reference_id": "1676555917",
						"status": 50000,
						"created_at": "1676555919"
					}
				*/
				/* // Status list
					10000	Pending disburse inquiry.
					10002	Pending disburse inquiry due to bank reconciliation.
					30000	Disbursement is on progress.
					50000	Completed disbursement.
					80000	Suspect disburse inquiry.
					90000	Failed disburse inquiry.
					90001	Wrong bank account number.
					90003	Closed bank account.
					90004	Inquiry rejected by the vendor bank.
					90005	Inquiry vendor bank is down.
				*/
				$content = (object) json_decode($content);
				$res = [
					// 'status' => (!empty($content->status) && $content->status == '50000') ? '000' : $content->status,
					'status' => $content->status,
					'data' => (array) $content,
				];
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 901);
			}
		} catch (\Throwable $e) {
			$this->ThrowErrorException($e);
		}
		return SELF::JSONResult($this->request, $res ?? [], $status_code ?? 400);
	}

}
