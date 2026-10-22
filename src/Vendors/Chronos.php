<?php

namespace PyGPaymentLib\Vendors;

use PyGPaymentLib\Requestor;
use PyGPaymentLib\Vendors\VendorInterface;

use PyGPaymentLib\Helper;

class Chronos extends Requestor implements VendorInterface
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
			$payment_method = trim(strtolower($transaction->getPaymentMethod()));
			$payment_channel = trim(strtolower($transaction->getPaymentChannel()));
			switch ($payment_method) {
				case 'va':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/virtual-account'
					);
					if ($transaction->getRemark()) {
						$viewName = $transaction->getRemark();
					} else {
						$viewName = $transaction->getCustomerName();
					}
					$this->request['data'] = [
						'bankCode' => strval($payment_channel),
						'singleUse' => true,
						'type' => 'ClosedAmount',
						'reference' => $transaction->getReferenceNumber(),
						'amount' => (int) $transaction->getAmount(),
						'expiryMinutes' => (int) $transaction->getExpireIn(), // minutes
						'viewName' => $viewName,
						'additionalInfo' => [
							'callback' => $this->init->getCallbackURL(),
						],
					];
					break;
				case 'qris':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/qris'
					);
					$this->request['data'] = [
						'reference' => $transaction->getReferenceNumber(),
						'amount' => (int) $transaction->getAmount(),
						'expiryMinutes' => (int) $transaction->getExpireIn(), // minutes
						'viewName' => $transaction->getCustomerName(),
						'additionalInfo' => [
							'callback' => $this->init->getCallbackURL(),
						],
					];
					break;
				case 'ewallet':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/e-wallet'
					);
					$this->request['data'] = [
						'reference' => $transaction->getReferenceNumber(),
						'phoneNumber' => $transaction->getCustomerPhone(),
						'channel' => $payment_channel,
						'amount' => (int) $transaction->getAmount(),
						'expiryMinutes' => (int) $transaction->getExpireIn(), // minutes
						'viewName' => $transaction->getCustomerName(),
						'additionalInfo' => [
							'callback' => $this->init->getCallbackURL(),
							'successRedirectUrl' => $this->init->getReturnURL(),
						],
					];
					break;
				case 'cstore':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/retail'
					);
					$this->request['data'] = [
						'reference' => $transaction->getReferenceNumber(),
						'phoneNumber' => $transaction->getCustomerPhone(),
						'channel' => $payment_channel,
						'amount' => (int) $transaction->getAmount(),
						'expiryMinutes' => (int) $transaction->getExpireIn(), // minutes
						'viewName' => $transaction->getCustomerName(),
						'additionalInfo' => [
							'callback' => $this->init->getCallbackURL(),
						],
					];
					break;
				case 'cc':
					$this->request['url'] = SELF::CleanURL(
						$this->init->getPaymentURL() .
						'/credit-card'
					);
					$this->request['data'] = [
						'reference' => $transaction->getReferenceNumber(),
						'phoneNumber' => $transaction->getCustomerPhone(),
						'amount' => (int) $transaction->getAmount(),
						'expiryMinutes' => (int) $transaction->getExpireIn(), // minutes
						'viewName' => $transaction->getCustomerName(),
						'additionalInfo' => [
							'callback' => $this->init->getCallbackURL(),
						],
					];
					break;
				default:
					throw new \Exception('Undefined payment method', 800);
					break;
			}
			//
			$body_data = $this->request['data'];
			$json_encoded = json_encode($body_data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR );
			$string_to_hash = $this->init->getMerchantKey() . $json_encoded;
			$merchant_token = $this->init->getMerchantToken();
			$hash_hmac_sha512 = hash_hmac('sha512', $string_to_hash, $merchant_token);
			// $this->request['_debug_sign'] = [
			// 	'body_data' => $body_data,
			// 	'json_encoded' => $json_encoded,
			// 	'string_to_hash' => $string_to_hash,
			// 	'merchant_token' => $merchant_token,
			// 	'hash_hmac_sha512' => $hash_hmac_sha512,
			// ];
			//
			$this->request['headers'] = [
				'On-Key' => $this->init->getMerchantKey(),
				'On-Token' => $this->init->getMerchantToken(),
				'On-Signature' => $hash_hmac_sha512,
				'Accept' => 'application/json',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == "success"
				) {
					/*
						// Success // VA
							{
								"responseCode": 200,
								"responseMessage": "success",
								"responseData": {
									"id": "1a8f06d3-0799-4cb8-a514-fd734d7377e3",
									"merchantRef": "1695013923",
									"status": "pending",
									"feePayer": "customer",
									"amount": 10000,
									"fee": 5000,
									"totalAmount": 15000,
									"expiredDate": "2023-09-18T13:12:03+07:00",
									"additionalInfo": {
										"callback": "https:\/\/google.com"
									},
									"virtualAccount": {
										"bankCode": "014",
										"vaNumber": "915581558693",
										"viewName": "Guntur Brahmaputra"
									}
								}
							}
						// Success // QRIS
							{
								"responseCode": 200,
								"responseMessage": "success",
								"responseData": {
									"id": "b9847301-c5d2-42f0-9928-715427417a10",
									"merchantRef": "1695014203",
									"status": "pending",
									"feePayer": "customer",
									"amount": 10000,
									"fee": 100,
									"totalAmount": 10100,
									"expiredDate": "2023-09-18T12:26:44+07:00",
									"additionalInfo": {
										"callback": "https:\/\/google.com"
									},
									"qris": {
										"content": "Harum odio sit explicabo et dolorum. Et voluptas optio quo. Consectetur quia quae sapiente accusamus.",
										"image": "data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAAZcAAAGXCAYAAAB\/Zh0NAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAJ3ElEQVR4nO3dS3LsyBEAQbaM978ytNJKlKlmXgwzreB+AID4NMNqkajP8zxfAFD61\/QfAMB9xAWAnLgAkBMXAHLiAkBOXADIiQsAOXEBICcuAOTEBYCcuACQExcAcuICQE5cAMiJCwA5cQEgJy4A5L6rA30+H1ta\/uB5nk95vNP7XJ93u6n7cst7v\/09nTre25TvgZULADlxASAnLgDkxAWAnLgAkBMXAHLiAkBOXADIiQsAuWxC\/9Qtk+P1hO\/UxHB93rdNSE9d79Tv6G2T8v5f\/X1WLgDkxAWAnLgAkBMXAHLiAkBOXADIiQsAOXEBICcuAOR+fUL\/1NTk7tREbj3RfMtE+Knte8BPqa+jvt5bJvnf9v\/qhJULADlxASAnLgDkxAWAnLgAkBMXAHLiAkBOXADIiQsAubUT+m9zy172278MMDV5P3UdU8c7NfVlAP55Vi4A5MQFgJy4AJATFwBy4gJATlwAyIkLADlxASAnLgDkTOgvsX0S3eT9n513u+33Zer58vdZuQCQExcAcuICQE5cAMiJCwA5cQEgJy4A5MQFgJy4AJBbO6H\/tknb7RPI9ST\/9on\/qfNO3ZftXzjY\/v9g+983wcoFgJy4AJATFwBy4gJATlwAyIkLADlxASAnLgDkxAWA3K9P6N+y5\/jb1JPUjvc7x9vuli8I8N+sXADIiQsAOXEBICcuAOTEBYCcuACQExcAcuICQE5cAMh9nscAKv\/f9gnp7XuY15Pe2+\/z1GT79vfgTaxcAMiJCwA5cQEgJy4A5MQFgJy4AJATFwBy4gJATlwAyH1XB7plr+ntE8i1W\/Yc3z4BX593+xcTTt3ypYFbjleycgEgJy4A5MQFgJy4AJATFwBy4gJATlwAyIkLADlxASCXTehvn1A9tXniddLbJqlPbd8rfvvfd8rv8mebr9fKBYCcuACQExcAcuICQE5cAMiJCwA5cQEgJy4A5MQFgNzneZoB3lsmpKcmn7dPXJ+65TpObX9fpmx\/vlP3b\/v\/yfK8Vi4A5MQFgJy4AJATFwBy4gJATlwAyIkLADlxASAnLgDkvqf\/gP9lao\/1U9u\/NFCrr7d+blPPY+o9nXpfbnkPpt6rN\/3fsHIBICcuAOTEBYCcuACQExcAcuICQE5cAMiJCwA5cQEgt3ZCf\/te7G+b8N08CfxXbP9Sw\/YJ8+3vff18b3nv6+dxwsoFgJy4AJATFwBy4gJATlwAyIkLADlxASAnLgDkxAWA3Od5rhhAzWyffK5tnwg\/NbXHem37RP3234fz7mHlAkBOXADIiQsAOXEBICcuAOTEBYCcuACQExcAcuICQO57+g\/4U1N7otcTtNsnpGtv25t8am\/37V9MODV1HW\/7HZXXa+UCQE5cAMiJCwA5cQEgJy4A5MQFgJy4AJATFwBy4gJA7tcn9G\/Zm\/yWifrtf9\/2Cena1O9j+17x2\/9vbP9ixwQrFwBy4gJATlwAyIkLADlxASAnLgDkxAWAnLgAkBMXAHKf51k74JmqJ2g371399bV7cvfra\/8Esi8D\/Jmpyfbt7\/125Xtv5QJATlwAyIkLADlxASAnLgDkxAWAnLgAkBMXAHLiAkBu7YT+9gn42vY9zE9N3edb7t+pqQn4Kduf79ve+xNWLgDkxAWAnLgAkBMXAHLiAkBOXADIiQsAOXEBICcuAOS+f\/uEt0zabp9s3zy5+1d4X\/7M9kn+7eetbb\/e8rxWLgDkxAWAnLgAkBMXAHLiAkBOXADIiQsAOXEBICcuAOR+fUJ\/+0T4qanJ7O3H83x\/53i1W57b9udR3+fNz83KBYCcuACQExcAcuICQE5cAMiJCwA5cQEgJy4A5MQFgFw2ob99sviWPbhPTU3ubr8v9QT39kl+78HPtk\/yn9r8vli5AJATFwBy4gJATlwAyIkLADlxASAnLgDkxAWAnLgAkPs8TzNY+raJYRP\/fH3tf26nPN+fvW2Sv2TlAkBOXADIiQsAOXEBICcuAOTEBYCcuACQExcAcuICQC6b0D8+4fKJ5qlJWxPSP3vbfZ760sX2SfTt1ztl8322cgEgJy4A5MQFgJy4AJATFwBy4gJATlwAyIkLADlxASD3XR3oTXtD\/xPnnZqQnrp\/27\/UcGrqOrY\/39ot78ubWLkAkBMXAHLiAkBOXADIiQsAOXEBICcuAOTEBYCcuACQyyb0T5kYvtv2LzVMTbZPfdHhVH3\/3uaW975k5QJATlwAyIkLADlxASAnLgDkxAWAnLgAkBMXAHLiAkDu8zzN4OYte3rby\/5ntzw3fjb1BYFbfke1G67XygWAnLgAkBMXAHLiAkBOXADIiQsAOXEBICcuAOTEBYDc9\/Qf8FumJoa3u2Uy+9TURPj2PdanTN2XGybgv77mfh8nrFwAyIkLADlxASAnLgDkxAWAnLgAkBMXAHLiAkBOXADIvWZCv5603T6ZPTWBvH3yvj7eLV9qqG2\/f9u\/1DD15YySlQsAOXEBICcuAOTEBYCcuACQExcAcuICQE5cAMiJCwC5tRP62ye4t++xvn1Cesr2LxecetuXH05tvy\/18U7v88TzsHIBICcuAOTEBYCcuACQExcAcuICQE5cAMiJCwA5cQEg93meVw1or3XLxP+U7ffv1PYvSdRueU+3X8fEe2DlAkBOXADIiQsAOXEBICcuAOTEBYCcuACQExcAcuICQO67OtDb9mI\/tX2yffsk8Pb3auo6pu5z\/b7ccv+mbP59WLkAkBMXAHLiAkBOXADIiQsAOXEBICcuAOTEBYCcuACQyyb0T22feD21fc\/smsn7n91yX7a\/f7Wp633TfbZyASAnLgDkxAWAnLgAkBMXAHLiAkBOXADIiQsAOXEBIPfrE\/qn3japbE\/v37H9ywr18bY\/j9ot13vDdVi5AJATFwBy4gJATlwAyIkLADlxASAnLgDkxAWAnLgAkFs7oc\/P6sndqS8DTO09P7WX\/fYvMGz\/MsDUc6ttfw9KVi4A5MQFgJy4AJATFwBy4gJATlwAyIkLADlxASAnLgDkTOi\/3PZJ4O0T16e2f+GgPu8U9\/nPlNdh5QJATlwAyIkLADlxASAnLgDkxAWAnLgAkBMXAHLiAkBu7YT+9snxKdsngW\/ZA35KPRFeH2\/q\/bvlPT21\/Xd+wsoFgJy4AJATFwBy4gJATlwAyIkLADlxASAnLgDkxAWA3Od5msHN7XtrT6knqevz1m55D7ZPcJ962\/N42573tfK+WLkAkBMXAHLiAkBOXADIiQsAOXEBICcuAOTEBYCcuACQyyb0AeA\/rFwAyIkLADlxASAnLgDkxAWAnLgAkBMXAHLiAkBOXADIiQsAOXEBICcuAOTEBYCcuACQExcAcuICQE5cAMiJCwC5fwNpvLltwvhgHAAAAABJRU5ErkJggg==",
										"url": ""
									}
								}
							}
						// Success // E-wallet OVO
							{
								"responseCode": 200,
								"responseMessage": "success",
								"responseData": {
									"id": "480be06f-98d9-45d3-b5bb-33f01799e34d",
									"merchantRef": "1695014379",
									"status": "pending",
									"feePayer": "customer",
									"amount": 10000,
									"fee": 300,
									"totalAmount": 10300,
									"expiredDate": "2023-09-18T12:29:40+07:00",
									"additionalInfo": {
										"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo"
									},
									"eWallet": {
										"viewName": "Testing",
										"channel": "ovo",
										"url": "http:\/\/schmitt.com\/"
									}
								}
							}
						// Success // CStore Alfamart
							{
								"responseCode": 200,
								"responseMessage": "success",
								"responseData": {
									"id": "3ebddaa6-4076-4455-8cd7-4e4017f6a616",
									"merchantRef": "1695040749",
									"status": "pending",
									"feePayer": "customer",
									"amount": 10000,
									"fee": 4000,
									"totalAmount": 14000,
									"expiredDate": "2023-09-18T19:49:10+07:00",
									"additionalInfo": {
										"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo"
									},
									"retail": {
										"viewName": "Testing",
										"channel": "alfamart",
										"paymentCode": "vV0H6FU5b0PsrvTvNVer"
									}
								}
							}
						// Success // Credit Card
							{
								"responseCode": 200,
								"responseMessage": "success",
								"responseData": {
									"id": "69c961b0-7ba5-4368-989c-2738f88c0b1f",
									"merchantRef": "1695014567",
									"status": "pending",
									"feePayer": "customer",
									"amount": 10000,
									"fee": 0,
									"totalAmount": 10000,
									"expiredDate": "2023-09-18T12:32:48+07:00",
									"additionalInfo": {
										"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo"
									},
									"creditCard": {
										"url": "http:\/\/carter.info\/recusandae-rem-optio-dolor-quae-deleniti-ipsum-voluptas"
									}
								}
							}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content->responseData,
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
			// Go
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() . '/check/' . $request->id . '?resendCallback=' . $request->resend_callback
			);
			$this->request['headers'] = [
				'On-Key' => $this->init->getMerchantKey(),
				'On-Token' => $this->init->getMerchantToken(),
				'On-Signature' => hash_hmac('sha512', $this->init->getMerchantKey(), $this->init->getMerchantToken()),
				'Accept' => 'application/json',
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == "success"
				) {
					/* // Success inquiry QRIS
						{
							"responseCode": 200,
							"responseMessage": "success",
							"responseData": {
								"id": "eaf82ca6-f5e3-4b8a-930d-8c23b4890612",
								"merchantRef": "1695014617",
								"status": "pending",
								"feePayer": "customer",
								"amount": 10000,
								"fee": 100,
								"totalAmount": 10100,
								"expiredDate": "2023-09-18T12:33:38+07:00",
								"additionalInfo": {
									"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo"
								},
								"qris": {
									"content": "Nesciunt sint cumque fugit omnis totam. Asperiores blanditiis magni aliquid magni facilis quidem beatae. Voluptas magnam dolorem et ut et ipsa.",
									"image": "data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAAe8AAAHvCAYAAAB9iVfNAAAACXBIWXMAAA7EAAAOxAGVKw4bAAANyUlEQVR4nO3cQXLjSBYFweGY7n9lzHJqIWtDtRLKHwn3A5AEmGQYNu9zXdd\/AICO\/+7+AADA3xFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCI+XrqhT+fz\/XUa5dd1\/VZ+Xq77vPq61jt7n3Z9X3sun+rP98pr7faruu965T7PN2Tv3NP3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABDz2MLaXdOXuu5avTBkGepn77vr9d52nqdf767zN\/33set7m35e7pqwKOfJGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGK2L6zdNX1xbNf7Tl9UWv35dt2XCYtKE+26L7uW8Vafl+m\/313e9n\/\/b3jyBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgJjMwtrb7FoI27U0ddfbXm+66UuAu77f6ct9lt36PHkDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjIW1oVYvG+1aSpq+wLXa2xbWLKfNej3ew5M3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxGQW1qYva612yqLS3e\/tlOWqUxblpi+OuS9ne9v1\/huevAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBm+8LaKUtiu5yyTDZ9uWr6OX3bUtcp5+WU17tr+u+oxJM3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxHyuy+ANf2\/1UtL0palddi2ivW2Ba9d1nPK+\/D5P3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABDz9dQLT1+uumv6Utf0paTVn++U6717Xqb\/jnYter1teW6XXYuGp7zekzx5A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQMznuvYOOE1fkFq9pON6f\/a+01\/vrtXn4JTFsem\/j9Wmn6u7JiyO\/aYJS2yevAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASDmsYW16UtYb1sicv9mOeX7WO1ty3jTve383TXhf8iTNwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMQ8trC22vQFn11LRBOWfn7T9GWyXYtep5yD6ct905cj75p+rqb\/zu968v558gaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoCYxxbWdi1XTV9iu8tC2M9eb5dd9++uU87VatPv8\/Sls7umn4MST94AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQ87X7A5yyuHPKdaz2tkWv1YtZ0+\/f9MW76efqlEW+6XYtPT55\/zx5A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQMxjC2ulpZoV7zt92Wj6ktNd069j17mffk7f9vu4a\/pC3V3Tr+OUHv3JkzcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEfK7rmWGc6YtKpyxN3TV9Cct9+dn7Tlh8WmH6\/8Zdu7636Utn05V+l568ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIOZr9wdYbdfC0CkLYatfb\/p9OeV9d32\/u0y\/jumLd7uW3abfl+nn\/k+evAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBm+8La6sWdXYtju1iQ+t7q73f6uZq+tDf9vNx1yjLZ6v+N6feltJx2lydvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiNm+sLba25Z5TrmO6Z9vtenXu\/rzTf9dTl+Ae9t5mf59TPh8nrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEg5nNdo4d7Xmv1ItWu951+HbvsWmiavnQ23fQFuF2\/t7tOOS8W1gCAvybeABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQ87X7A6x2yoLPatOXl+465fudfh27FsJW23WfVy+iWcb73tuWGf\/kyRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBijltYW70wdMoy1PQlol3fx9ve967pn2+66efqrunnb7VSPzx5A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQMxjC2urF2h2Le7cNWFxZ4Xp17FrQcr5+5np\/we73nf6ubrrlEW5uyb8j3vyBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgJjHFtZWm76ENWFxZ6LVy0unnINdC2G7TL8vpyzA3TX98003YdHQkzcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEfK5ryxDV\/z\/A8EWlU+xaaJr+vs7fz0xf1pqwhPVPpv+OTrFrae9JnrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEg5rGFtV1LWKcsep2y+DT99VY7ZbnqlO\/jrlOu422mLyQ+eV48eQNAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0DM1+4PsMuu5a+7di11rV4Emr50Nn1Rafo5nf6+q1\/P9\/E773v39ab\/3p7kyRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBiPtf1zJDX25aIpr\/eaqcsk931tu9j9fveNf17O+XcTz8vp\/yOnlxi8+QNADHiDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMV+7P8Bd05ecdi0CrfbkItAKFp+8b8Hb7t8p71v6H\/fkDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMeINADGf62oMyuxa1tqltPTzm3xvv2P18tz011tt+jk9xfTFxSfPgSdvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiPl66oWnLxtN\/3zT33f6ktiuBaRd93n6uZp+XnZdx9sWwqaf5+mf70+evAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASDmsYW1XaYv5OxaVNpl13LV9Pdd7W0LZqt\/57uW005ZMJt+\/k7kyRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBiPtf1zDDO9AWk6ctGpyyOvc30xbu7pv\/eVjvl9+Y+v4cnbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIjJLKyd4pT7sus6di3ZvW2hbvr9m\/59nPL7veuU651+HX\/y5A0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAxjy2sTfe2Jafpy1\/Tl42mL4ntMn2p621LgHedck6n\/+8+yZM3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxHzt\/gDTl7\/u2rWUNH1BarXpi093WaT63tvO1fQltrtO+R+\/a8L\/kCdvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiHlsYW36YtFqq9\/3lIWwU76P1aZ\/vrt2ndNd\/xsTlrVWvK\/z973SffHkDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMeINADGf68oMyrzK9MW21UtT05erVpt+vaecv118bz8z\/Tom\/H49eQNAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0DM11MvPH0BaZdTFsJWm35fpp\/nXYtPqxf0Vr\/vKZy\/33m9Cctpd3nyBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgJjHFtbumrBUs8KuBanV77vr9VZf765zZXHse7uWsFbbdU6n379d52\/68tyTPHkDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAzPaFtbumLyqttmtJ7JSls1OWl952HdMXx6af+9VOOX+r\/9cmnANP3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABCTWVjje6uXfqa\/3q5lrV1LdqutXszadf+mL39NWOCa+L6rv7dTFiH\/DU\/eABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AEGNh7SWmL4ntWnZbbfri2PRlrelLdqec07umL8C9+ffmyRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBiMgtru5Z+plu9RLRrQWr6602\/jl2mL4Tddcr3u\/r7eNs5vXsdE67XkzcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEbF9YO2WhaboJi0C\/ade5mr7Udcrvbdf1Tv9+V5t+HbsW6ib8n3ryBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgJjPdR0xuAQAr+HJGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBi\/gdUJWI+Mape9AAAAABJRU5ErkJggg==",
									"url": ""
								}
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content->responseData,
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
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/balance'
			);
			$this->request['headers'] = [
				'On-Key' => $this->init->getMerchantKey(),
				'On-Token' => $this->init->getMerchantToken(),
				'On-Signature' => hash_hmac('sha512', $this->init->getMerchantKey(), $this->init->getMerchantToken()),
				'Accept' => 'application/json',
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == "success"
				) {
					/* // Success
						{
							"responseCode": 200,
							"responseMessage": "success",
							"responseData": {
								"active": 0,
								"pending": 0,
								"total": 0
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => [
							'active_balance' => $content->responseData->active,
							'pending_balance' => $content->responseData->pending,
							'total_balance' => $content->responseData->total,
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

	public function CheckBalanceAlt()
	{
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/aggregators/balance'
			);
			$this->request['headers'] = [
				'On-Key' => $this->init->getMerchantKey(),
				'On-Token' => $this->init->getMerchantToken(),
				'On-Signature' => hash_hmac('sha512', $this->init->getMerchantKey(), $this->init->getMerchantToken()),
				'Accept' => 'application/json',
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($content) && SELF::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == "success"
				) {
					/* // Success
						{
							"responseCode": 200,
							"responseMessage": "success",
							"responseData": [
								{
										"name": "Nobu (Disbursement)",
										"account": "SPN - Nobu BI Fast Disbursement Group 1",
										"id": 1021,
										"balance": 0
								},
								{
										"name": "OY! Indonesia",
										"account": "PT KTT - taratara Disbursement Group 1",
										"id": 1030,
										"balance": 166661181201
								}
							]
						}
					*/
					$res = [
						'status' => '000',
						'data' => $content->responseData,
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

	public function BankAccountInquiry(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/account-inquiry'
			);
			$this->request['data'] = [
				'bankCode' => $transaction->getCustomerBankCode(),
				'accountNumber' => $transaction->getCustomerBankAccountNumber(),
				'reference' => $transaction->getOrderID(),
				'additionalInfo' => [
					'callback' => $this->init->getCallbackURL(),
				],
			];
			//
			$body_data = $this->request['data'];
			$json_encoded = json_encode($body_data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR );
			$string_to_hash = $this->init->getMerchantKey() . $json_encoded;
			$merchant_token = $this->init->getMerchantToken();
			$hash_hmac_sha512 = hash_hmac('sha512', $string_to_hash, $merchant_token);
			//
			$this->request['headers'] = [
				'On-Key' => $this->init->getMerchantKey(),
				'On-Token' => $this->init->getMerchantToken(),
				'On-Signature' => $hash_hmac_sha512,
				'Accept' => 'application/json',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == "success"
				) {
					/* // Success
						{
							"responseCode": 200,
							"responseMessage": "success",
							"responseData": {
								"id": "49bfde57-8819-44f1-8730-857d1c8d5b4f",
								"status": "pending",
								"accountNumber": "7700173383",
								"accountName": "Kaelyn Morissette",
								"bankCode": "011",
								"additionalInfo": {
									"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo"
								}
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => $content->responseData,
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

	public function OnlineTransferExec(\PyGPaymentLib\Transaction $transaction)
	{
		try {
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/disburse'
				// '/v2/disburse'
			);
			//
			$signature = hash_hmac('sha512',
				$this->init->getMerchantKey() . $transaction->getOrderID(),
				$this->init->getMerchantToken()
			);
			//
			$this->request['data'] = [
				'bankCode' => $transaction->getCustomerBankCode(),
				'recipientAccount' => $transaction->getCustomerBankAccountNumber(),
				'reference' => $transaction->getOrderID(),
				'amount' => (int) $transaction->getAmount(),
				'additionalInfo' => [
					'callback' => $this->init->getCallbackURL(),
				],
			];
			//
			$body_data = $this->request['data'];
			$json_encoded = json_encode($body_data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR );
			$string_to_hash = $this->init->getMerchantKey() . $json_encoded;
			$merchant_token = $this->init->getMerchantToken();
			$hash_hmac_sha512 = hash_hmac('sha512', $string_to_hash, $merchant_token);
			//
			$this->request['headers'] = [
				'On-Key' => $this->init->getMerchantKey(),
				'On-Token' => $this->init->getMerchantToken(),
				'On-Signature' => $hash_hmac_sha512,
				'Accept' => 'application/json',
			];
			$this->request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == "success"
				) {
					/* // Success disb
						{
							"responseCode": 200,
							"responseMessage": "success",
							"responseData": {
								"id": "c1ddc1cc-7652-4bb7-bff8-7409aee76211",
								"merchantRef": "1695033936",
								"status": "pending",
								"feePayer": "customer",
								"amount": 10500,
								"fee": 3000,
								"totalAmount": 7500,
								"expiredDate": "2023-09-18T18:45:38+07:00",
								"additionalInfo": {
									"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo"
								},
								"disbursement": {
									"bankCode": "011",
									"recipientAccount": "7700173383",
									"recipientName": null
								}
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content->responseData,
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

	public function CheckTransfer(object $request)
	{
		try {
			// Go
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() . '/check/' . $request->id . '?resendCallback=' . $request->resend_callback
			);
			$this->request['headers'] = [
				'On-Key' => $this->init->getMerchantKey(),
				'On-Token' => $this->init->getMerchantToken(),
				'On-Signature' => hash_hmac('sha512', $this->init->getMerchantKey(), $this->init->getMerchantToken()),
				'Accept' => 'application/json',
			];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($content) && Helper::is_JSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == "success"
				) {
					/* // Success check disb
						{
							"responseCode": 200,
							"responseMessage": "success",
							"responseData": {
								"id": "c1ddc1cc-7652-4bb7-bff8-7409aee76211",
								"merchantRef": "1695033936",
								"status": "pending",
								"feePayer": "customer",
								"amount": 10500,
								"fee": 3000,
								"totalAmount": 7500,
								"expiredDate": "2023-09-18T18:45:38+07:00",
								"additionalInfo": {
									"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo"
								},
								"disbursement": {
									"bankCode": "011",
									"recipientAccount": "7700173383",
									"recipientName": null
								}
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content->responseData,
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
