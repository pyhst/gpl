<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['PAZEMO_API_KEY'],
	''
);
$init->setPaymentURL($_ENV['PAZEMO_HOST_URL']);
$init->setRequestURL($_ENV['PAZEMO_HOST_URL']);
$vendor = new \Growinc\Payment\Vendors\Pazemo($init);

/**
 *
 * Payment
 *
 */
try {
	$transaction = new \Growinc\Payment\Transaction();
	$transaction->setInvoiceNo('INV-' . time());
	$transaction->setCustomerName('testing');
	$transaction->setCustomerEmail('testing@gmail.com');
	$transaction->setCustomerPhone('088812131415');
	$transaction->setItem('Product');
	$transaction->setAmount(rand(5000,10000) * 10);
	$transaction->setDescription('Product' . rand(1, 100));
	$transaction->setParams([
		'account_id' => $_ENV['PAZEMO_IDR_ACCOUNT_ID'],
	]);
	//
	// transfer
	// va
	// qris
	//
	// $transaction->setPaymentMethod('va,bni');
	// $transaction->setPaymentMethod('va,bca');
	$transaction->setPaymentMethod('qris,qris');
	// $transaction->setPaymentMethod('transfer,bca');
	//
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($response);
	// Success transfer BCA
	/*
		{
			"status": "000",
			"data": {
				"id": "65139a43-4aed-4c47-b5b3-85ca6b407d8e",
				"createdTime": "2022-06-16T08:03:52.912Z",
				"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
				"type": "transfer",
				"status": "pending",
				"amount": 71030,
				"feeAmount": 0,
				"bankId": "bca",
				"addressName": "IDNP Sikoma Games",
				"transactionId": "03394e03-5e31-4e03-8e96-1e816dfabd96",
				"requestId": "0055366632"
			}
		}
	*/
	// Success VA BNI
	/*
		{
			"status": "000",
			"data": {
				"id": "295292e5-629d-406a-8d2f-fd80cc5b86da",
				"createdTime": "2022-06-16T08:04:32.621Z",
				"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
				"type": "va",
				"status": "pending",
				"amount": 67030,
				"feeAmount": 1500,
				"bankId": "bni",
				"address": "9881802922061603",
				"addressName": "IDNP Sikoma Games",
				"transactionId": "69170c57-7768-4938-bf51-6db01028f878",
				"requestId": "0055366672"
			}
		}
	*/
	// Success QRIS
	/*
		{
			"status": "000",
			"data": {
				"id": "79195182-3d52-4298-81fd-e5a48574732a",
				"createdTime": "2022-06-26T17:08:18.829Z",
				"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
				"type": "qris",
				"status": "pending",
				"amount": 75820,
				"feeAmount": 0,
				"bankId": "",
				"addressName": "IDNP Sikoma Games",
				"transactionId": "ae7e4bde-fd27-4c4c-aeac-290ce8065486",
				"requestId": "0056263299"
			}
		}
	*/
	$content = (object) json_decode($response['content']);
	$result = $vendor->CheckDeposit((object) [
		'account_id' => $_ENV['PAZEMO_IDR_ACCOUNT_ID'],
		'trx_id' => $content->data->id,
		'qr' => 0,
	]);
	extract($result);
	print_r($response);
	// Success QRIS detail
	/*
		{
			"status": "000",
			"data": "iVBORw0KGgoAAAANSUhEUgAAAfQAAAH0AQAAAADjreInAAAEpElEQVR42u2dPZLiWgyFRRE49BJYCkuDpbEUluCQwIWGe6UjyfR0TfCCF+g46KKxP0hUuvo5EqL\/6dqEPPn\/lX+KXdfPP+f55nPVfb7zXF\/nt+h9vJCTjmf0NZ65O0Oe\/OfpcX0eEhlPj2s87ddb5Pa46PgguQ2DG7fuV7tFnvywLRlGZvZnTw9ru6nZ38AU9rct+6l8GXnywc\/r83rwYmY3Hdnt4e4Kn0ie\/E9+HIQnO\/\/yoXFLH7IWR6ZKnnzh\/fz7\/NmNtw\/6BEnAZIVHEznrL+cn+Z484u8abf\/rxd\/id\/It+ZLML7sfhK\/qtj5PX6ZpjqNxW\/Qtv9QPyHfkw7YitrYkP8Im479yvFEjOJEnP8xOkaShfmTZ2rS2GZGLzBcz\/z9bIGWFAPLtebekYWTgxZ1UxN8Iu8UKSdMQl4y\/yHfmPW2bJmVXlI2mR5uOLF+o15jC\/5FvzXuRGmWjiJ\/snXPcmt8hUSywU5M8+Q3259jVPZrW0y7Pv1kIGO9E\/ES+M\/+U0tLwQ053N7KsaE9DRPxtt3D+ke\/M65ZBUmRru+X\/EXZnaG63\/GgkT149SbvqV9sV3VZ7R4DN0Nw7+uTJuyealxcZUS0q3foFt0JINA2RfHs+JGUXj40ibFJUGz1bS0O0+Bv1b\/KteWt7oOwYsdEsBBj2uHiNEmXHcGTk2\/NWSYzY2toebn\/eP7viRByX6z8i\/yffnfeUXjztn9XG0J\/FLTl8R83\/yffmLTaKaNv1rxk2hf8yj+Y5XrFf8p35NeUcyZvaFYYofjSOGGsWAqr9kW\/Na7TNrKWKsmPqX2ciZzUCHJbeYyNPHvXHIptGRcAD8Rui7SFbRLAu3\/VL8q35or9H2QhGds+Jjl\/6r+T78tHSeELbIavpFzN\/c\/9l+o837E\/Ik\/eSYsTWtUiU0nw5+Dg\/\/0I\/Tb43f47+h+TYmDU5vDWC+qNYsWBKY4\/6a\/JNeQ1th5mUuv\/KIEnTRg9CWCVP\/mfa\/5BSrY5CknqPDRWB0E+Tb88rJPWuDYpuq2HZWkNEXuMn8t358FaoLdraBXFry2kNeLSpSMv5RfLN+UjprWwdsmlxtSsa+UWjX\/Sv5JvzJf9\/+yKYTOSk6u+97WGtWfLkY341JWX6QJPVNwql\/qPsD9KDfpZ8X96HNA677V6uCJknorf240Q86aH\/Qb45L0sd8on4yaLtw0aq6KjNZZ2xP4Z8b97nD2PaJxtpWvZPSRlkvcu3fo18V15jbBXVRsVD4b+ea5a2fSKaPPmyfypmM2z+p3ZkQ23mt+p3kG\/Pu0hRytiqb4QJIdFB9pEbYVI\/S74vH2vrVWORmbj+QyAkklxEdSgkkSevusdKesX8at0\/ZZ9YF9kLQnPy5HGlb9qO82P6kJK\/YVhR96\/9reRb8lUtVPVnX9VGQdt+rwchefKxf3zDbvFchKcx9uwatRDrf9Uvyffls6WKQUSJbfW+Uchn7PFDCHX+hzx5dMsikAqRR83osmyEiIo8+cPvr7hvwpLxqpaWwyKGzN\/Id+fz91ewyGMJRSMmgvSn\/mMnT\/7w+ytad0OXRXixmhyD9MjxyJPn7w+T78v\/Aan+Gjyk+9DZAAAAAElFTkSuQmCC"
		}
	*/
	// Success VA detail
	/*
		{
			"status": "000",
			"data": {
				"id": "4d4e8ebb-e07c-4b2c-8743-dea8c4af7577",
				"createdTime": "2022-06-26T17:25:29.846Z",
				"accountId": "186992eb-dc10-475a-ae72-fc442a0a15ff",
				"type": "va",
				"status": "pending",
				"amount": "73510.00000000",
				"feeAmount": "0.00000000",
				"bankId": "bca",
				"address": "900156360282",
				"addressName": "IDNP Sikoma Games",
				"transactionId": "df021af7-eb15-43c8-8fec-caefd7a0280a",
				"requestId": "0056264330"
			}
		}
	*/
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}
