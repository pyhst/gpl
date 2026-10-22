<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init($_ENV['ODEO_CLIENT_ID'], $_ENV['ODEO_CLIENT_SECRET']);
$init->setPaymentURL($_ENV['ODEO_HOST_URL']);
$init->setRequestURL($_ENV['ODEO_HOST_URL']);
$init->setSigningKey($_ENV['ODEO_SIGNIN_KEY']);

$vendor = new \Growinc\Payment\Vendors\Odeo($init);



/**
 *
 * Get Token
 *
 */
try {
	$result = $vendor->GetToken();
	extract($result);
	// print_r($response);
	$init->setParams([
		'access_token' => json_decode($response['content'])->access_token,
	]);
	/* // Success
		{
			"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2NzYwOTMzNDQsImV4cCI6MTY3NjA5NTE0NCwiY2lkIjoiMkhJam5OUFcxbmpqbTNHbzBjdlVJT3VRRjE2REw0QnUiLCJ1aWQiOjE3NTQwLCJ1cGYiOjEwMCwic2NwIjoiIn0.e319d6ibHt6iuRsVmLO4Q5ycJlvIt_YlBQm8MHM7sic",
			"expires_in": 1800,
			"scope": "",
			"token_type": "Bearer"
		}
	*/
} catch (\Throwable $e) {
	$vendor->ThrowErrorException($e);
}



// /**
//  *
//  * Get Bank List
//  *
//  */
// try {
// 	$result = $vendor->BankList();
// 	extract($result);
// 	print_r($response);
// 	/* // Success
// 		{
// 			"status": "000",
// 			"data": {
// 				"banks": [{
// 					"bank_id": 1,
// 					"name": "BCA",
// 					"bank_code": "014",
// 					"swift_code": "CENAIDJA"
// 				}, {
// 					"bank_id": 2,
// 					"name": "BNI",
// 					"bank_code": "009",
// 					"swift_code": "BNINIDJA"
// 				}]
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }



// /**
//  *
//  * Get Balance
//  *
//  */
// try {
// 	$result = $vendor->CheckBalance();
// 	extract($result);
// 	print_r($response);
// 	/* // Success
// 		{
// 			"status": "000",
// 			"data": {
// 				"cash": {
// 					"amount": 865002,
// 					"currency": "IDR",
// 					"formatted_amount": "Rp865,002"
// 				},
// 				"locked_cash": {
// 					"amount": 0,
// 					"currency": "IDR",
// 					"formatted_amount": "Rp0"
// 				}
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }



// /**
//  *
//  * Bank account inquiry
//  *
//  */
// try {
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setCustomerBankAccountNumber('6044222099');
// 	$transaction->setCustomerBankCode('014');
// 	$transaction->setCustomerName('TESTER');
// 	$result = $vendor->BankAccountInquiry($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success
// 		{
// 			"status": 50000,
// 			"data": {
// 				"bank_id": "014",
// 				"account_number": "6044222099",
// 				"account_name": "Ronathan",
// 				"customer_name": "TESTER",
// 				"fee": 1000,
// 				"status": 50000,
// 				"created_at": "1678599516",
// 				"max_amount": null,
// 				"bank_account_inquiry_id": "3805"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Send disbursement
//  *
//  */
// try {
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setCustomerBankAccountNumber('6044222099');
// 	$transaction->setAmount(10500);
// 	$transaction->setCustomerBankCode('014');
// 	$transaction->setCustomerName('TESTER');
// 	$transaction->setDescription('TESTING DISBURSEMENT');
// 	$transaction->setReferenceNumber(time());
// 	$result = $vendor->OnlineTransferExec($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success disb
// 		{
// 			"status": 10000,
// 			"data": {
// 				"disbursement_id": "7240",
// 				"bank_id": "014",
// 				"bank_code": "061",
// 				"account_number": "6044222099",
// 				"customer_name": "TESTER",
// 				"amount": 10500,
// 				"fee": 5500,
// 				"mdr": 0,
// 				"description": "TESTING DISBURSEMENT",
// 				"reference_id": "1678599487",
// 				"status": 10000,
// 				"created_at": "1678599490"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Check Transfer Status
//  *
//  */
// try {
// 	$request = (object) [
// 		'disbursement_id' => '7239',
// 	];
// 	//
// 	$result = $vendor->CheckTransfer($request);
// 	extract($result);
// 	print_r($response);
// 	/* // Success
// 		{
// 			"status": 50000,
// 			"data": {
// 				"disbursement_id": "7239",
// 				"bank_id": 14,
// 				"bank_code": "061",
// 				"account_number": "6044222099",
// 				"customer_name": "TESTER",
// 				"amount": 10500,
// 				"fee": 5500,
// 				"mdr": 0,
// 				"description": "TESTING DISBURSEMENT",
// 				"reference_id": "1678599204",
// 				"status": 50000,
// 				"created_at": "1678599206"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }
