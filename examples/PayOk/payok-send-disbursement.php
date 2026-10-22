<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init($_ENV['PAYOK_MERCHANT_ID'], null);
$init->setParams([
	'PAYOK_PRIVATE_KEY' => $_ENV['PAYOK_PRIVATE_KEY'],
	'PAYOK_PUBLIC_KEY' => $_ENV['PAYOK_PUBLIC_KEY'],
]);
$init->setPaymentURL($_ENV['PAYOK_HOST_URL']);
$init->setRequestURL($_ENV['PAYOK_HOST_URL']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$init->setReturnURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \Growinc\Payment\Vendors\PayOk($init);



// /**
//  *
//  * Check Balance
//  *
//  */
// try {
// 	$result = $vendor->CheckBalance();
// 	extract($result);
// 	print_r($response);
// 	/* // Success check balance
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sit-remit-api.paylabs.co.id\/api-pay\/remit\/v1.1\/balanceQuery",
// 				"data": {
// 						"partnerReferenceNo": "20240620110729",
// 						"accountNo": "010430",
// 						"balanceTypes": "CASH"
// 				},
// 				"headers": {
// 						"Content-Type": "application\/json",
// 						"X-TIMESTAMP": "2024-06-20T11:07:29.723Z",
// 						"X-SIGNATURE": "C6sg8PiOWetqEDZtCYbw+LwJiTdhfL2jImgPjdXm31cDPpEYfnEkdZemK4kxIel2VzWn8RjlHb6X\/JfrD0JvjwUuKqRk\/HLj+j+l8NlEYHPZsRqqdX8oQLMTw8sCp+ZJUEzAbXTWnyOxWlOoiUex0XwJhi8ID\/U3ahyKeLWfRzUZoCU8O9fOqB7oqf0ChLnv\/NdnjbhEvztgsARbGSqbEyRCaKZt\/kxKWw2+jokCUXhv6a8GMuVXQpdNe3XQO7WQmy4St9ej13CW4faEwcBiJjun7CcLv4sE9QkNVekwX7IPf\/DKApGflCkLa+ugmzKhO5AU6zlp9kF2RjIecTqghA==",
// 						"X-PARTNER-ID": "010430",
// 						"X-EXTERNAL-ID": "20240620110729",
// 						"ORIGIN": "www.yourdomain.com",
// 						"CHANNEL-ID": "-"
// 				},
// 				"option": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"referenceNo": "2024062043090000024",
// 				"accountNo": "2406040000010430",
// 				"name": "Test Merchant",
// 				"partnerReferenceNo": "20240620110729",
// 				"responseMessage": "Request has been processed successfully",
// 				"accountInfos": {
// 						"amount": {
// 							"value": 0,
// 							"currency": "Rp"
// 						},
// 						"holdAmount": {
// 							"value": 0,
// 							"currency": "Rp"
// 						},
// 						"floatAmount": {
// 							"value": 0,
// 							"currency": "Rp"
// 						},
// 						"ledgerBalance": {
// 							"value": 4953500,
// 							"currency": "Rp"
// 						},
// 						"availableBalance": {
// 							"value": 4953500,
// 							"currency": "Rp"
// 						},
// 						"currentMultilateralLimit": {
// 							"value": 0,
// 							"currency": "Rp"
// 						},
// 						"status": "Available",
// 						"balanceType": "Cash",
// 						"registrationStatusCode": "registered"
// 				},
// 				"responseCode": "SUCCESS"
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
// 	$time = time();
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setReferenceNumber($time);
// 	$transaction->setAmount(10000);
// 	$transaction->setCustomerBankCode('011');
// 	$transaction->setCustomerBankAccountNumber('7700173383');
// 	$result = $vendor->BankAccountInquiry($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success bank account inquiry
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sit-remit-api.paylabs.co.id\/api-pay\/remit\/v1.1\/remitInquiry",
// 				"data": {
// 						"partnerReferenceNo": "20240620110756",
// 						"beneficiaryAccountNo": "7700173383",
// 						"beneficiaryBankCode": "011",
// 						"additionalInfo": {
// 							"remitType": "BANK",
// 							"amount": 10000
// 						}
// 				},
// 				"headers": {
// 						"Content-Type": "application\/json",
// 						"X-TIMESTAMP": "2024-06-20T11:07:56.729Z",
// 						"X-SIGNATURE": "Gi54K\/jvnt\/Q3EucWbIXuJNv8RP7jWG8mokVA\/\/5fkIOj8vrV+nBb6ESUwwbB4xd4GXKIr0MAaHkgURv+R7oJoQCFN6g4DXozvsawsbH30cbbgfvnfLAeFo\/E8S\/LiPj4as5MCrlos8sEcyQHy37S21WMZ9PM5I3qIeMOouV+Y4mXxZ1GORlbackVhVbk\/rRlUA8rV8Zted14GjZlcr+0uVkWE3\/QxL+BHL+msg8GzyDDAq9B0HL5O+h5SE1ONDBlNrUDrY91Bl3btzkug9xVm47ZdvVWSfEjF8eo4uIltWYY+v8GfmnF7WvsohQ34tae39bXJr9UlhwVSj6gz+KMg==",
// 						"X-PARTNER-ID": "010430",
// 						"X-EXTERNAL-ID": "20240620110756",
// 						"ORIGIN": "www.yourdomain.com",
// 						"CHANNEL-ID": "-"
// 				},
// 				"option": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"beneficiaryAccountNo": "7700173383",
// 				"beneficiaryAccountName": "7700173383",
// 				"referenceNo": "2024062043090000025",
// 				"beneficiaryBankName": "BANK DANAMON",
// 				"partnerReferenceNo": "20240620110756",
// 				"currency": "Rp",
// 				"beneficiaryBankCode": "011",
// 				"responseMessage": "Request has been processed successfully",
// 				"responseCode": "SUCCESS"
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
// 	$time = time();
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setReferenceNumber(date('YmdHis', $time));
// 	$transaction->setAmount(10000);
// 	$transaction->setTransactionID('2024062043090000025');
// 	$transaction->setCustomerBankAccountNumber('7700173383');
// 	$transaction->setCustomerEmail('tester@gmail.com');
// 	$transaction->setReqDateTime(date('YmdHis', $time));
// 	$transaction->setDescription('TEST-TRANSFER');
// 	$transaction->setParams([
// 		'SOURCE_ACCOUNT_NUMBER' => '2406040000010430',
// 	]);
// 	$result = $vendor->OnlineTransferExec($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success disb
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sit-remit-api.paylabs.co.id\/api-pay\/remit\/v1.1\/remitCreate",
// 				"data": {
// 						"partnerReferenceNo": "20240620180823",
// 						"feeType": "OUR",
// 						"currency": "Rp",
// 						"amount": {
// 							"value": 10000,
// 							"currency": "Rp"
// 						},
// 						"customerReference": "2024062043090000025",
// 						"beneficiaryAccountNo": "7700173383",
// 						"beneficiaryEmail": "tester@gmail.com",
// 						"transactionDate": "20240620180823",
// 						"sourceAccountNo": "2406040000010430",
// 						"remark": "TEST-TRANSFER"
// 				},
// 				"headers": {
// 						"Content-Type": "application\/json",
// 						"X-TIMESTAMP": "2024-06-20T11:08:23.699Z",
// 						"X-SIGNATURE": "MTHUqxCJrCx87VhVoarquvKEExzyley50hcgMvztZKCMb5hSJVTa0jm5B8NwpYg+HwjCny7iL5CZtz8GdATb8gGtgpSFi0ncCtFqYyLcxTd2ESX5R9ySaPvQy4xm+pzSx6HMPfv51vXOSrdJIsh5m1V44Ly7L\/DwjQVL4ZtadSVa+XorEaCSqBFBhLqxJzjVwFgJoR+exA8XchsqCXNkc8Am8GE2cxB5zJrgZuXGZjxSCN5VV8KCLHGmvt9GxktCNGg8CTjaodc3Uw4JCgLFpJBr\/JnJrZRS4jxSBE4u\/y86ceLM+NS6QgDZs97+uV3FkNaQoBdo\/DfEmMunLf0c9g==",
// 						"X-PARTNER-ID": "010430",
// 						"X-EXTERNAL-ID": "20240620110823",
// 						"ORIGIN": "www.yourdomain.com",
// 						"CHANNEL-ID": "-"
// 				},
// 				"option": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"beneficiaryAccountNo": "7700173383",
// 				"amount": {
// 						"currency": "Rp",
// 						"value": 10000
// 				},
// 				"referenceNo": "2024062043090000025",
// 				"partnerReferenceNo": "20240620180823",
// 				"beneficiaryBankCode": "011",
// 				"responseMessage": "Request has been processed successfully",
// 				"sourceAccountNo": "010430",
// 				"responseCode": "SUCCESS"
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
// 		'originalPartnerReferenceNo' => '20240620180823',
// 		// 'originalReferenceNo' => '2024062043090000025',
// 		// 'originalExternalId' => '20240620110823',
// 		// 'transactionDate' => '20240620180823',
// 		// 'amount' => 10000,
// 	];
// 	//
// 	$result = $vendor->CheckTransfer($request);
// 	extract($result);
// 	print_r($response);
// 	/* // Success check status
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sit-remit-api.paylabs.co.id\/api-pay\/remit\/v1.1\/remitQuery",
// 				"data": {
// 						"originalPartnerReferenceNo": "20240620180823",
// 						"originalReferenceNo": "2024062043090000025",
// 						"originalExternalId": "20240620110823",
// 						"serviceCode": "remit.query",
// 						"transactionDate": "20240620180823",
// 						"amount": {
// 							"value": 10000,
// 							"currency": "Rp"
// 						}
// 				},
// 				"headers": {
// 						"Content-Type": "application\/json",
// 						"X-TIMESTAMP": "2024-06-20T11:10:01.011Z",
// 						"X-SIGNATURE": "ciWeA7x5OfbnXQ7r0gZzc66hcbbLVLnfE4uN2cal6MT1MG0AAXONyU0Xk6+B33nXbKsgWITa\/7mxI7hBqr39B+YgqPHhfHvQbnnsfjmAxFmnQkRgtuUrIbI8vzwWrvRSw6MPN1vlVOlh41QN2Ly7nP2BLE1KtMWxXIA37JVa3fqCl688aBH2iVMal\/c98nGXJkWDB3avvpVj47QA7M1EVSlhd+X5Mu9zC1TPm3SNQFb7icsHi+xsRIrriXW1pzEqyHt97Qx5xEoXhGQHQ2G29DL25WYc+ayqf+FT2lhdc1jf71cpaQBrKPk2OcYUwf+SKQfsvgUBsQOhYn+kHdI5xQ==",
// 						"X-PARTNER-ID": "010430",
// 						"X-EXTERNAL-ID": "20240620111001",
// 						"ORIGIN": "www.yourdomain.com",
// 						"CHANNEL-ID": "-"
// 				},
// 				"option": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"beneficiaryAccountNo": "7700173383",
// 				"amount": {
// 						"currency": "Rp",
// 						"value": 10000
// 				},
// 				"originalReferenceNo": "2024062043090000025",
// 				"referenceNumber": "20240620110823",
// 				"originalExternalId": "20240620110823",
// 				"serviceCode": "18",
// 				"latestTransactionStatus": "SUCCESS",
// 				"transactionStatusDesc": "Successful",
// 				"originalPartnerReferenceNo": "20240620180823",
// 				"responseMessage": "Request has been processed successfully",
// 				"sourceAccountNo": "010430",
// 				"responseCode": "SUCCESS"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r($vendor::ThrowError($e));
// }