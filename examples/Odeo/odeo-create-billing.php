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
	/*
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
//  * Create billing
//  *
//  */
// try {
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setAmount(10000);
// 	$transaction->setPaymentMethod('qris');
// 	$transaction->setPaymentChannel('');
// 	$transaction->setReferenceNumber($transaction->getTime());
// 	/* // Success
// 		{
// 			"status": 10000,
// 			"data": {
// 				"transaction_id": 420,
// 				"transaction_no": "20230312640D657B62493",
// 				"merchant_reference_no": "1678599545",
// 				"amount": 10000,
// 				"fee": 1150,
// 				"qris_data": "00020101021226670016COM.NOBUBANK.WWW01189360050300000488820214030800000313890303UMI51440014ID.CO.QRIS.WWW0215ID20200814001600303UMI5204549953033605405100005802ID5907PT ODEO6015Banten - Kota T6105158116258011403120000105486062120230312640D657B624930703A010804POSP63042727",
// 				"qris_image": "iVBORw0KGgoAAAANSUhEUgAAAUUAAAFFCAIAAAD0FmgKAAAABnRSTlMA\/wD\/AP83WBt9AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAKsklEQVR4nO3d0Y4juw0FwN0g\/\/\/LN++NgQJFJKU5qXpdu7vt8YGwBEX9\/eeff\/4AEf51+wGAMvIMOeQZcsgz5JBnyCHPkEOeIYc8Qw55hhzyDDnkGXLIM+SQZ8ghz5BDniGHPEMOeYYc8gw55Bly\/PvkzX\/\/\/q16jrXPkLP1fbcmom19hPVjnExiW19q6\/NuPdXWfbcutX7IrccofPFa4aVOnPyQrM+QQ54hhzxDDnmGHEf1sI\/C0fxjFa++EtdaYSFqzK94qrWtQuPWpU4UVtqsz5BDniGHPEMOeYYclfWwj8LWnK0rfy61LoFsPeT6xYVPNVZq2rrR1jP3Nd4VdrwV6vu1b7E+Qw55hhzyDDnkGXI01sMeUdhqtn5vYbWsr6jzK4p2JxWvsW\/yTdZnyCHPkEOeIYc8Q46Eeti6fFI4jKqwm2r9GLeuPFYfKpyXtr7y\/xvrM+SQZ8ghz5BDniFHYz1srH\/opJhU+JAnnUl920X7LnXrOIGTTru+FrdHpqlZnyGHPEMOeYYc8gw5Kuthb3YX\/b\/968dJA9ytDZK\/4rt6sxHN+gw55BlyyDPkkGfI8feRvpYxY11N6xdvKdxO+NE3XK1vcNcjRxG8yfoMOeQZcsgz5JBnyHHUH1ZY1Vhf+aQg1Nds9Ga\/1CNden0nV54Y+8VuXarw81qfIYc8Qw55hhzyDDkq90uedCYVVlNOnEyfLxzr3zdef6wceHIK5NrYLP6+s0f7apbWZ8ghz5BDniGHPEOOo3pYwD6+EycNQydzvNY3evPL6eu0KzxA86Rt6+S9hV+79RlyyDPkkGfIIc+Qo3K\/ZGF5rHDc1NrYrPa+JrbCKs76Ric1nkf60samx401on1YnyGHPEMOeYYc8gw5jubpj82IX7\/3o\/BS6yvfmhHf9wHXTpqr1vo67R75MYxN+bc+Qw55hhzyDDnkGXJUzg87Udio1DebqnBG\/Pq+J1cu7EzqG0V28lRjH\/DkxVuX0h8G\/ECeIYc8Qw55hhxz8\/S3alqFxZX1QxZugRybIFV45ZPvqu+sxrW+GWBbHmkI+7A+Qw55hhzyDDnkGXIc7Zf8XmtqVP36vrfmS431aY091VrfjPi+6XG3vuex2XLWZ8ghz5BDniGHPEOOuXpY34yo9aX6RnN9jI1uX99361K3hm+tbzTWaTc2mG3sm7Q+Qw55hhzyDDnkGXI0ztO\/dcTeid9YLVu71bXWN6hsS99PtO\/jq4cBf\/7IMySRZ8ghz5CjcX7Y2HGEhfvptoyVuPomom0dGHDrez75Ygu7uPrOPLBfEviBPEMOeYYc8gw5Gs+XPPlffl+\/VOFT9dVptmpahVWcNwdonZxNOdbE9kivpPUZcsgz5JBnyCHPkKOyHlZYEelrF+s7UPJEYU1rzFjFq6+EWfgYfVfeYn2GHPIMOeQZcsgz5JjbL3lrVtMjpbWT7rG+WVyFc97HJsC9eY7nR+EPaYv1GXLIM+SQZ8ghz5CjsT9s61\/XLy6cAtV3DGLfpcbGehWW9MY6orbuWziJbf3erX8t\/AtanyGHPEMOeYYc8gw5XpkfNtZrtfUYWy++1QHWNzFrfd8+j3TafRRW+Pq+Sesz5JBnyCHPkEOeIcffvianvo17J8YKUVuPsXby5fRN+Xpzo+KWsfMDCv8Ka9ZnyCHPkEOeIYc8Q46j\/rC+mtatOU99Lz6ZIFVYTen7rvpa6x4prfWVbM3TB34gz5BDniGHPEOOuXn6H30nKo5tCTypWvV5ZJxYYd\/h1r+uX9w3x+tWi9uH9RlyyDPkkGfIIc+Qo3F+2MdvHGT1UVgf6qvTFH6TfdsJ+04ILfTIN7nF+gw55BlyyDPkkGfIcVQP6ytU3Oq2WVdixjaEbr23sBJz8vFvdUSNldYe6QBbsz5DDnmGHPIMOeQZclTOD\/sonAJ\/YmwLZN8+vl8xuP\/ErSMjx8bcj808sz5DDnmGHPIMOeQZchydL\/m91tSsprWxIsfH2Iz4MX0zwPpO+Tx5qkKFRbst1mfIIc+QQ54hhzxDjrn9klsb2QoLBoW7C8d2253U8E6+55MGuHVr3dZDbr2473c19l79YcAP5BlyyDPkkGfIUblfsnDaVl9dqm\/41olbHWBbbrVt3erhK7zU1ukCJ6zPkEOeIYc8Qw55hhxz50uu9U2QWlfpTiptJzPA1voOWBwrB47tl1wrvNTYffWHAX\/+yDMkkWfIIc+Qo3K\/ZGFtaa2vvWasA2xrcP\/JRLTCFxfW\/37FyaR9W3r76p3WZ8ghz5BDniGHPEOOo3n6t7a5bd2ocL7UWJ\/WrRnxfQdZ9v0Ft1785g\/JfkngB\/IMOeQZcsgz5Jg7X7JvA93WU20Zm1s21h\/WNzHrpHh2q4h18uL1ez\/6+g4\/rM+QQ54hhzxDDnmGHJX1sL0bXzph8OPNOtzHrWajvhpP4R\/l1gmShVd2viTwA3mGHPIMOeQZcjTWwx7ZA\/jxZv3v1rmHJ++9NW1\/\/eL1jcY2da7fa78k8N\/JM+SQZ8ghz5Bjrj+scBhVocJdfltulake6Yg6udQjG2DXV\/4Y201pfYYc8gw55BlyyDPkqJyn39cBdmuDZN9Dro0NKtu6762NqH036jtNYWzL54f1GXLIM+SQZ8ghz5Djlf6wsQFLfXvi1sbGem15ZGzbibGaVuFjFL73w\/oMOeQZcsgz5JBnyPHvkzef\/D++b0bU1n23LvVm0e6kIHTrVMRHpm2NXWr9EeyXBH4gz5BDniGHPEOOo3pYYd3i1rT9R7YTnlzqxNaNtr6rk49w6++79d43m+esz5BDniGHPEMOeYYc1\/rD+goGfdPYtx5jfd+Txxg7BXJLYQvUVllu7ZFtqmN\/Besz5JBnyCHPkEOeIUfl\/LCxUtPJffvmvN8qNfXd6GNs2NitUw3Gtqmu6Q8D\/vyRZ0giz5BDniFH4\/mSj5Qitm60NlZL66sP9T3VlkdOgVwr7Cwc6+GzPkMOeYYc8gw55BlyHO2X\/DjZ5lY4bGxs496JvlLTrVMR+x6jb+p934EQtzaxWp8hhzxDDnmGHPIMOSr3Sxa6NeZ+6719\/UO3OqI+fsV939zw+zGWMusz5JBnyCHPkEOeIUdlf1ihvkrMWOvVydGNJ\/oa4AqPQBi778ljjJ0BoD8M+IE8Qw55hhzyDDkqz5fss95AV7hVra\/kc1LhGzvZcKyWtr5v3\/mSJ3\/fvs2VhazPkEOeIYc8Qw55hhyV\/WFv7lwbG7B+UmkrrOF9vDkS7OTF6\/euPdId2Ld90voMOeQZcsgz5JBnyNG4X\/JWoWKslvYrRq9tOSn4rS\/1UbhfsrBIefKQj7A+Qw55hhzyDDnkGXI8Oj9s7aRMdVLjuTUxv6+rqW8nZuEGyVunKWxdqvBfT1ifIYc8Qw55hhzyDDl+ZT3so29n4taN1i\/esnXlwp6nsSpO3w7QR848sF8SOCXPkEOeIYc8Q47GetjYdsJHmpxObrTWN4t\/bM771q7GQoUVvr6fSmG1zPoMOeQZcsgz5JBnyFFZD7s1b2msLnXize2EfaXEseliW2799U++qy3WZ8ghz5BDniGHPEOOv28OhQf+B9ZnyCHPkEOeIYc8Qw55hhzyDDnkGXLIM+SQZ8ghz5BDniGHPEMOeYYc8gw55BlyyDPkkGfIIc+QQ54hx38AGpVQ5g+KqO4AAAAASUVORK5CYII=",
// 				"status": 10000
// 			}
// 		}
// 	*/
// 	//
// 	$result = $vendor->SecurePayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/*
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Inquiry trx
//  *
//  */
// try {
// 	$result = $vendor->Inquiry((object) [
// 		'reference_num' => '1678599545',
// 		'payment_method' => 'qris',
// 	]);
// 	extract($result);
// 	print_r($response);
// 	/* // Success
// 		{
// 			"status": 10000,
// 			"data": {
// 				"amount": 10000,
// 				"fee": 1150,
// 				"transaction_id": 420,
// 				"transaction_no": "20230312640D657B62493",
// 				"merchant_reference_no": "1678599545",
// 				"status": 10000
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Testing payment
//  *
//  */
// try {
// 	$result = $vendor->TestPayment((object) [
// 		'transaction_id' => '378',
// 	]);
// 	extract($result);
// 	print_r($response);
// 	/*
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }
