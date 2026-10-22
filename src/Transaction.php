<?php

namespace PyGPaymentLib;

class Transaction extends TransactionExtends
{

	protected $time;
	//
	protected $order_id;
	protected $invoice_no;
		protected $reference_number;
			protected $virtual_account_number;
		protected $external_id;
		protected $request_id;
	protected $amount;
		protected $fee_fixed;
		protected $fee_percentage;
		protected $surcharge_fixed;
		protected $surcharge_percentage;
			protected $tips_fixed;
			protected $tips_percentage;
			protected $tips_manual;
	protected $description;
	protected $remark;
	protected $currency;
	protected $currency_code;
	//
	protected $customer_id;
	protected $customer_name;
	protected $customer_email;
	protected $customer_phone;
	protected $customer_address;
	protected $customer_city;
	protected $country_code;
	protected $postal_code;
	protected $ip_address;
	//
	protected $customer_userid; // internet banking login
	//
	protected $customer_bank_name;
	protected $customer_bank_account_name;
	protected $customer_bank_account_number;
	protected $customer_bank_code;
	protected $customer_bank_id;
	//
	protected $payment_method;
	protected $payment_channel;
	protected $payment_type;
	protected $payment_bank;
	protected $expire_in; // In minutes, def 120
	protected $expire_at; // Precise Date/Time - for overriding --^
	//
	protected $item;
	// cc
	protected $cc_card_holder_name;
	protected $cc_card_number;
	protected $cc_card_exp_month;
	protected $cc_card_exp_year;
	protected $cc_card_cvv;
	protected $cc_token;
	// more
	protected $params;
	// disbursement
	protected $transfer_method;
	protected $disbursement_type;
	//
	protected $merchant_id;
	protected $merchant_name;
	protected $merchant_email;
	//
	protected $service_code;
	protected $qr_type;

	public function __construct()
	{
		$this->time = time();
		$this->order_id = '00' . substr($this->time, 2, strlen($this->time));
		// $this->invoice_no = 'INV' . substr($this->time, 2, strlen($this->time)); // Disabled for QRIS, because limited to 10 chars in ibankPG
		$this->description = 'Payment for ' . $this->invoice_no;
		$this->currency = 'IDR';
		$this->currency_code = '360';
		$this->expire_in = 120; // In minutes
		$this->expire_at = ''; // Precise Date/Time - for overriding --^
	}

	public function setTime(string $time): void
	{
		$this->time = $time;
	}

	public function getTime(): ?string
	{
		return $this->time;
	}

	//

	public function setParams(array $params): void
	{
		$this->params = $params;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function setParam(string $key, string $value): void
	{
		$this->params[$key] = $value;
	}

	public function getParam(string $key): ?string
	{
		if (!isset($this->params[$key])) {
			return false;
		}
		return $this->params[$key];
	}

	//

	public function setOrderID(string $order_id): void
	{
		$this->order_id = $order_id;
	}

	public function getOrderID(): ?string
	{
		return $this->order_id;
	}

	public function setInvoiceNo(string $invoice_no): void
	{
		$this->invoice_no = $invoice_no;
	}

	public function getInvoiceNo(): ?string
	{
		return $this->invoice_no;
	}

			public function setReferenceNumber(string $reference_number): void
			{
				$this->reference_number = $reference_number;
			}

			public function getReferenceNumber(): ?string
			{
				return $this->reference_number;
			}

		public function setVirtualAccountNumber(string $virtual_account_number): void
		{
			$this->virtual_account_number = $virtual_account_number;
		}

		public function getVirtualAccountNumber(): ?string
		{
			return $this->virtual_account_number;
		}

			public function setExternalID(string $external_id): void
			{
				$this->external_id = $external_id;
			}

			public function getExternalID(): ?string
			{
				return $this->external_id;
			}

			public function setRequestID(string $request_id): void
			{
				$this->request_id = $request_id;
			}

			public function getRequestID(): ?string
			{
				return $this->request_id;
			}

	public function setAmount(int $amount): void
	{
		$this->amount = $amount;
	}

	public function getAmount(): ?int
	{
		return $this->amount;
	}

						public function setFeeFixed(int $fee_fixed): void
						{
							$this->fee_fixed = $fee_fixed;
						}

						public function getFeeFixed(): ?int
						{
							return $this->fee_fixed;
						}

						public function setFeePercentage(float $fee_percentage): void
						{
							$this->fee_percentage = $fee_percentage;
						}

						public function getFeePercentage(): ?float
						{
							return $this->fee_percentage;
						}

							public function setSurchargeFixed(int $surcharge_fixed): void
							{
								$this->surcharge_fixed = $surcharge_fixed;
							}

							public function getSurchargeFixed(): ?int
							{
								return $this->surcharge_fixed;
							}

							public function setSurchargePercentage(float $surcharge_percentage): void
							{
								$this->surcharge_percentage = $surcharge_percentage;
							}

							public function getSurchargePercentage(): ?float
							{
								return $this->surcharge_percentage;
							}

								public function setTipsFixed(int $tips_fixed): void
								{
									$this->tips_fixed = $tips_fixed;
								}

								public function getTipsFixed(): ?int
								{
									return $this->tips_fixed;
								}

								public function setTipsPercentage(float $tips_percentage): void
								{
									$this->tips_percentage = $tips_percentage;
								}

								public function getTipsPercentage(): ?float
								{
									return $this->tips_percentage;
								}

								public function setTipsManual(int $tips_manual): void
								{
									$this->tips_manual = $tips_manual;
								}

								public function getTipsManual(): ?int
								{
									return $this->tips_manual;
								}


	public function setDescription(string $description): void
	{
		$this->description = $description;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

		public function setRemark(string $remark): void
		{
			$this->remark = $remark;
		}

		public function getRemark(): ?string
		{
			return $this->remark;
		}

	public function setCurrency(string $currency): void
	{
		$this->currency = $currency;
	}

	public function getCurrency(): ?string
	{
		return $this->currency;
	}

	public function setCurrencyCode(string $currency_code): void
	{
		$this->currency_code = $currency_code;
	}

	public function getCurrencyCode(): ?string
	{
		return $this->currency_code;
	}

	//

		public function setCustomerID(string $customer_id): void
		{
			$this->customer_id = $customer_id;
		}

		public function getCustomerID(): ?string
		{
			return $this->customer_id;
		}

	public function setCustomerName(string $customer_name): void
	{
		$this->customer_name = $customer_name;
	}

	public function getCustomerName(): ?string
	{
		return $this->customer_name;
	}

	public function setCustomerEmail(string $customer_email): void
	{
		$this->customer_email = $customer_email;
	}

	public function getCustomerEmail(): ?string
	{
		return $this->customer_email;
	}

	public function setCustomerPhone($customer_phone): void
	{
		$this->customer_phone = $customer_phone;
	}

	public function getCustomerPhone(): ?string
	{
		return $this->customer_phone;
	}

	public function setCustomerAddress($customer_address): void
	{
		$this->customer_address = $customer_address;
	}

	public function getCustomerAddress(): ?string
	{
		return $this->customer_address;
	}

	public function setCustomerCity($customer_city): void
	{
		$this->customer_city = $customer_city;
	}

	public function getCustomerCity(): ?string
	{
		return $this->customer_city;
	}

	public function setCountrycode($country_code): void
	{
		$this->country_code = $country_code;
	}

	public function getCountryCode(): ?string
	{
		return $this->country_code;
	}

	public function setPostalCode($postal_code): void
	{
		$this->postal_code = $postal_code;
	}

	public function getPostalCode(): ?string
	{
		return $this->postal_code;
	}

					public function setIPAddress($ip_address): void
					{
						$this->ip_address = $ip_address;
					}

					public function getIPAddress(): ?string
					{
						return $this->ip_address;
					}

			public function setCustomerBankName($customer_bank_name): void
			{
				$this->customer_bank_name = $customer_bank_name;
			}

			public function getCustomerBankName(): ?string
			{
				return $this->customer_bank_name;
			}

			public function setCustomerBankAccountName($customer_bank_account_name): void
			{
				$this->customer_bank_account_name = $customer_bank_account_name;
			}

			public function getCustomerBankAccountName(): ?string
			{
				return $this->customer_bank_account_name;
			}

			public function setCustomerBankAccountNumber($customer_bank_account_number): void
			{
				$this->customer_bank_account_number = $customer_bank_account_number;
			}

			public function getCustomerBankAccountNumber(): ?string
			{
				return $this->customer_bank_account_number;
			}

				public function setCustomerBankCode($customer_bank_code): void
				{
					$this->customer_bank_code = $customer_bank_code;
				}

				public function getCustomerBankCode(): ?string
				{
					return $this->customer_bank_code;
				}

				public function setCustomerBankId($customer_bank_id): void
				{
					$this->customer_bank_id = $customer_bank_id;
				}

				public function getCustomerBankId(): ?string
				{
					return $this->customer_bank_id;
				}

	//

	public function setPaymentMethod($payment_method): void
	{
		$this->payment_method = $payment_method;
	}

	public function getPaymentMethod(): ?string
	{
		return $this->payment_method;
	}

		public function setPaymentChannel($payment_channel): void
		{
			$this->payment_channel = $payment_channel;
		}

		public function getPaymentChannel(): ?string
		{
			return $this->payment_channel;
		}

	public function setPaymentType($payment_type): void
	{
		$this->payment_type = $payment_type;
	}

	public function getPaymentType(): ?string
	{
		return $this->payment_type;
	}

		public function setPaymentBank($payment_bank): void
		{
			$this->payment_bank = $payment_bank;
		}

		public function getPaymentBank(): ?string
		{
			return $this->payment_bank;
		}

	public function setExpireIn($expire_in): void // Hours
	{
		$this->expire_in = $expire_in;
	}

	public function getExpireIn(): ?string
	{
		return $this->expire_in;
	}

			public function setExpireAt($expire_at): void // Precise Date/Time - for overriding --^
			{
				$this->expire_at = $expire_at;
			}

			public function getExpireAt(): ?string
			{
				return $this->expire_at;
			}

	//

	// public function setItem(&$item_detail): void
	public function setItem($item): void
	{
		$this->item = $item;
	}

	// public function getItem(): ?array
	public function getItem()
	{
		return $this->item;
	}

	// bca_klikbca

	public function setCustomerUserID(string $customer_userid): void
	{
		$this->customer_userid = $customer_userid;
	}

	public function getCustomerUserID(): ?string
	{
		return $this->customer_userid;
	}

	// credit card start

	public function setCardHolderName(string $cc_card_holder_name): void
	{
		$this->cc_card_holder_name = $cc_card_holder_name;
	}

	public function getCardHolderName(): ?string
	{
		return $this->cc_card_holder_name;
	}

	public function setCardNumber(string $cc_card_number): void
	{
		$this->cc_card_number = $cc_card_number;
	}

	public function getCardNumber(): ?string
	{
		return $this->cc_card_number;
	}

	public function setCardExpMonth(string $cc_card_exp_month): void
	{
		$this->cc_card_exp_month = $cc_card_exp_month;
	}

	public function getCardExpMonth(): ?string
	{
		return $this->cc_card_exp_month;
	}

	public function setCardExpYear(string $cc_card_exp_year): void
	{
		$this->cc_card_exp_year = $cc_card_exp_year;
	}

	public function getCardExpYear(): ?string
	{
		return $this->cc_card_exp_year;
	}

	public function setCardCVV(string $cc_card_cvv): void
	{
		$this->cc_card_cvv = $cc_card_cvv;
	}

	public function getCardCVV(): ?string
	{
		return $this->cc_card_cvv;
	}

	public function setCardToken(string $cc_token): void
	{
		$this->cc_token = $cc_token;
	}

	public function getCardToken(): ?string
	{
		return $this->cc_token;
	}

	// disbursement

		public function setTransferMethod($transfer_method): void
		{
			$this->transfer_method = $transfer_method;
		}

		public function getTransferMethod(): ?string
		{
			return $this->transfer_method;
		}

		public function setDisbursementType($disbursement_type): void
		{
			$this->disbursement_type = $disbursement_type;
		}

		public function getDisbursementType()
		{
			return $this->disbursement_type;
		}

	// merchant

		public function setMerchantID($merchant_id): void
		{
			$this->merchant_id = $merchant_id;
		}

		public function getMerchantID(): ?string
		{
			return $this->merchant_id;
		}

		public function setMerchantName($merchant_name): void
		{
			$this->merchant_name = $merchant_name;
		}

		public function getMerchantName(): ?string
		{
			return $this->merchant_name;
		}

			public function setMerchantEmail($merchant_email): void
			{
				$this->merchant_email = $merchant_email;
			}

			public function getMerchantEmail(): ?string
			{
				return $this->merchant_email;
			}

// Qris

		public function setServiceCode($service_code): void
		{
			$this->service_code = $service_code;
		}

		public function getServiceCode(): ?string
		{
			return $this->service_code;
		}

		public function setQrType($qr_type): void
		{
			$this->qr_type = $qr_type;
		}

		public function getQrType(): ?string
		{
			return $this->qr_type;
		}

}
