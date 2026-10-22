<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(
	$_ENV['DANA_MERCHANT_ID']
);
$init->setRequestURL($_ENV['DANA_HOST_URL']);
$init->setParams([
	'client_id' => $_ENV['DANA_CLIENT_ID'],
	'client_secret' => $_ENV['DANA_CLIENT_SECRET'],
	'private_key' => $_ENV['DANA_PRIVATE_KEY'],
]);
$vendor = new \Growinc\Payment\Vendors\Dana($init);


/**
 *
 * Get Oauth Token
 *
 */
try {
	$result = $vendor->SNAP_GetOauthToken([
		'customer_phone' => '081212121314',
	]);
	extract($result);
	print_r($response);
	/*
{"responseCode":"2007300","responseMessage":"Successful","accessToken":"eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE2NjUwODAyMjcsImlzcyI6ImlkLmRhbmEiLCJleHAiOjE2NjUwODA4Mjd9.iPcnyAKTTo9Yoh44vcUxYz-GXbCFxbZAOxllPoeQwFhbSptz0qSEMnpa9WNmFo8ZwDJieW2gQAjaKLG2fkZIxLykw8oRgY11UBYIwxsoFVmjrcd2gLNmLVdbbtxqtgeiZhr4POn5JOYCYaMmq0sf7w8RHTAkpTjy_xmOhIk73HKt59VQ1HcILH9tC9TU8RwXhT2sOSIWC3kt898BRjJ9rmd20Kd7nhDgMXsvwHWnCRPZep8e0Q73hoFSPKuTaBFWQ1quH_b13uIlb1L6YM_PSQgav8hcGJTr1cLmEqEeLOK-itYD0j1WhUWZP736t3_RlA4mS45IlxOL6ZwFdPHRrA","tokenType":"Bearer","expiresIn":"600"}
	*/
	$content = (object) json_decode($result['response']['content']);
	$init->setParams([
		'client_id' => $_ENV['DANA_CLIENT_ID'],
		'client_secret' => $_ENV['DANA_CLIENT_SECRET'],
		'private_key' => $_ENV['DANA_PRIVATE_KEY'],
		'token' => $content->accessToken,
	]);
} catch (\Throwable $e) {
	print_r($vendor::ThrowError($e));
}