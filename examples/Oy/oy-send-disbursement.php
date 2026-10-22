<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init($_ENV['OY_USERNAME'], $_ENV['OY_API_KEY']);
$init->setPaymentURL($_ENV['OY_HOST_URL']);
$init->setRequestURL($_ENV['OY_HOST_URL']);
$init->setCallbackURL($_ENV['OY_CALLBACK_URL']);
$init->setReturnURL($_ENV['OY_RETURN_URL']);

$vendor = new \Growinc\Payment\Vendors\Oy($init);



// /**
//  *
//  * Check Balance
//  *
//  */
// try {
// 	$result = $vendor->CheckBalance();
// 	extract($result);
// 	print_r($response);
// 	/*
// 		{
// 			"status": "000",
// 			"message": "Success",
// 			"data": {
// 				"balance": 9703595,
// 				"overdraftBalance": 0,
// 				"overbookingBalance": 0,
// 				"pendingBalance": 0,
// 				"availableBalance": 9703595,
// 				"timeStamp": "07-03-2023 06:54:30"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }





/**
 *
 * Bank account inquiry
 *
 */
try {
	$transaction = new \Growinc\Payment\Transaction();
	$transaction->setCustomerBankCode('014');
	$transaction->setCustomerBankAccountNumber('6044222099');
	$result = $vendor->BankAccountInquiry($transaction);
	extract($result);
	print_r($response);
	/*
		{
			"status": "000",
			"data": {
				"bank_code": "011",
				"account_number": "7700173383",
				"account_name": "Golden Kunze",
				"timestamp": "2023-03-06T11:23:06",
				"id": "78f84e1f-7a4a-40ff-a8a9-5501c34a3679",
				"invoice_id": "e56c328e-5cba-4352-bbe7-a27fa92a3829"
			}
		}
	*/
} catch (\Throwable $e) {
	$vendor->ThrowErrorException($e);
}



// /**
//  *
//  * Send disbursement
//  *
//  */
// try {
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setCustomerBankCode('011');
// 	$transaction->setCustomerBankAccountNumber('7700173383');
// 	$transaction->setAmount(10500);
// 	$transaction->setDescription('Test transfer');
// 	$transaction->setOrderID(time());
//		$transaction->setCustomerEmail('test@gmail.com');
// 	$result = $vendor->OnlineTransferExec($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success disb
// 		{
// 			"status": "101",
// 			"message": "Request is Processed",
// 			"data": {
// 				"amount": 10500,
// 				"timestamp": "06-03-2023 11:45:54",
// 				"recipient_bank": "011",
// 				"recipient_account": "7700173383",
// 				"trx_id": "6404bccd-c88c-47bd-ae7a-37b79fb52fd3",
// 				"partner_trx_id": "1678103153"
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
// 		'reference_id' => '1678101958',
// 		'send_callback' => 0,
// 	];
// 	//
// 	$result = $vendor->CheckTransfer($request);
// 	extract($result);
// 	print_r($response);
// 	/*
// 		{
// 			"status": "000",
// 			"message": "Success",
// 			"data": {
// 				"amount": 10500,
// 				"timestamp": "06-03-2023 11:44:11",
// 				"tx_status_description": null,
// 				"recipient_name": "Golden Kunze",
// 				"recipient_bank": "011",
// 				"recipient_account": "7700173383",
// 				"trx_id": "26de30ce-0ead-4626-8799-a179b5e1a58a",
// 				"partner_trx_id": "1678101958",
// 				"created_date": "06-03-2023 11:25:59",
// 				"last_updated_date": "06-03-2023 11:25:59"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }
