<?php

require_once __DIR__ . '/../includes.php';

$init = new \Growinc\Payment\Init(null, null);
$init->setMerchantKey($_ENV['CHRONOS_KEY']);
$init->setMerchantToken($_ENV['CHRONOS_TOKEN']);
$init->setPaymentURL($_ENV['CHRONOS_HOST_URL']);
$init->setRequestURL($_ENV['CHRONOS_HOST_URL']);
$init->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$init->setReturnURL('http://sb.tf2us.com/secure/callback/demo');

$vendor = new \Growinc\Payment\Vendors\Chronos($init);



// /**
//  *
//  * Create billing
//  *
//  */
// try {
// 	$transaction = new \Growinc\Payment\Transaction();
// 	$transaction->setCustomerName('Testing');
// 	$transaction->setCustomerPhone('081212121314');
// 	$transaction->setAmount(10000);
// 	$transaction->setReferenceNumber(time());
// 	$transaction->setExpireIn(10);
// 	//
// 	/*
// 		Bank Code	Bank	Capability
// 		008	Mandiri	One-off / Persistent - Open Amount, Closed Amount
// 		014	BCA	One-off / Persistent - Open Amount, Closed Amount
// 		002	BRI	One-off / Persistent - Open Amount, Closed Amount
// 		009	BNI	One-off - Closed Amount
// 		013	Permata	One-off / Persistent - Open Amount, Closed Amount
// 		011	Danamon	Persistent - Open Amount
// 		022	CIMB	One-off - Closed Amount
// 		153	Sahabat Sampoerna	One-off / Persistent - Open Amount, Closed Amount
// 	*/
// 	// $transaction->setPaymentMethod('va');
// 	// $transaction->setPaymentChannel('014');
// 	//
// 	// $transaction->setPaymentMethod('qris');
// 	//
// 	// $transaction->setPaymentMethod('ewallet');
// 	// $transaction->setPaymentChannel('ovo'); // ovo, dana, shopeepay, linkaja
// 	// $transaction->setPaymentChannel('shopeepay');
// 	//
// 	$transaction->setPaymentMethod('cstore'); // alfamart, indomaret
// 	$transaction->setPaymentChannel('alfamart');
// 	//
// 	// $transaction->setPaymentMethod('cc');
// 	//
// 	//
// 	$result = $vendor->SecurePayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success QRIS
// 		{
// 			"status": "000",
// 			"data": {
// 				"id": "eaf82ca6-f5e3-4b8a-930d-8c23b4890612",
// 				"merchantRef": "1695014617",
// 				"status": "pending",
// 				"feePayer": "customer",
// 				"amount": 10000,
// 				"fee": 100,
// 				"totalAmount": 10100,
// 				"expiredDate": "2023-09-18T12:33:38+07:00",
// 				"additionalInfo": {
// 					"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo"
// 				},
// 				"qris": {
// 					"content": "Nesciunt sint cumque fugit omnis totam. Asperiores blanditiis magni aliquid magni facilis quidem beatae. Voluptas magnam dolorem et ut et ipsa.",
// 					"image": "data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAAe8AAAHvCAYAAAB9iVfNAAAACXBIWXMAAA7EAAAOxAGVKw4bAAANyUlEQVR4nO3cQXLjSBYFweGY7n9lzHJqIWtDtRLKHwn3A5AEmGQYNu9zXdd\/AICO\/+7+AADA3xFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCI+XrqhT+fz\/XUa5dd1\/VZ+Xq77vPq61jt7n3Z9X3sun+rP98pr7faruu965T7PN2Tv3NP3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABDz2MLaXdOXuu5avTBkGepn77vr9d52nqdf767zN\/33set7m35e7pqwKOfJGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGK2L6zdNX1xbNf7Tl9UWv35dt2XCYtKE+26L7uW8Vafl+m\/313e9n\/\/b3jyBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgJjMwtrb7FoI27U0ddfbXm+66UuAu77f6ct9lt36PHkDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjIW1oVYvG+1aSpq+wLXa2xbWLKfNej3ew5M3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxGQW1qYva612yqLS3e\/tlOWqUxblpi+OuS9ne9v1\/huevAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBm+8LaKUtiu5yyTDZ9uWr6OX3bUtcp5+WU17tr+u+oxJM3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxHyuy+ANf2\/1UtL0palddi2ivW2Ba9d1nPK+\/D5P3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABDz9dQLT1+uumv6Utf0paTVn++U6717Xqb\/jnYter1teW6XXYuGp7zekzx5A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQMznuvYOOE1fkFq9pON6f\/a+01\/vrtXn4JTFsem\/j9Wmn6u7JiyO\/aYJS2yevAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASDmsYW16UtYb1sicv9mOeX7WO1ty3jTve383TXhf8iTNwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMQ8trC22vQFn11LRBOWfn7T9GWyXYtep5yD6ct905cj75p+rqb\/zu968v558gaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoCYxxbWdi1XTV9iu8tC2M9eb5dd9++uU87VatPv8\/Sls7umn4MST94AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQ87X7A5yyuHPKdaz2tkWv1YtZ0+\/f9MW76efqlEW+6XYtPT55\/zx5A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQMxjC2ulpZoV7zt92Wj6ktNd069j17mffk7f9vu4a\/pC3V3Tr+OUHv3JkzcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEfK7rmWGc6YtKpyxN3TV9Cct9+dn7Tlh8WmH6\/8Zdu7636Utn05V+l568ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIOZr9wdYbdfC0CkLYatfb\/p9OeV9d32\/u0y\/jumLd7uW3abfl+nn\/k+evAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBm+8La6sWdXYtju1iQ+t7q73f6uZq+tDf9vNx1yjLZ6v+N6feltJx2lydvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiNm+sLba25Z5TrmO6Z9vtenXu\/rzTf9dTl+Ae9t5mf59TPh8nrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEg5nNdo4d7Xmv1ItWu951+HbvsWmiavnQ23fQFuF2\/t7tOOS8W1gCAvybeABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQ87X7A6x2yoLPatOXl+465fudfh27FsJW23WfVy+iWcb73tuWGf\/kyRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBijltYW70wdMoy1PQlol3fx9ve967pn2+66efqrunnb7VSPzx5A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQMxjC2urF2h2Le7cNWFxZ4Xp17FrQcr5+5np\/we73nf6ubrrlEW5uyb8j3vyBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgJjHFtZWm76ENWFxZ6LVy0unnINdC2G7TL8vpyzA3TX98003YdHQkzcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEfK5ryxDV\/z\/A8EWlU+xaaJr+vs7fz0xf1pqwhPVPpv+OTrFrae9JnrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEg5rGFtV1LWKcsep2y+DT99VY7ZbnqlO\/jrlOu422mLyQ+eV48eQNAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0DM1+4PsMuu5a+7di11rV4Emr50Nn1Rafo5nf6+q1\/P9\/E773v39ab\/3p7kyRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBiPtf1zJDX25aIpr\/eaqcsk931tu9j9fveNf17O+XcTz8vp\/yOnlxi8+QNADHiDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMV+7P8Bd05ecdi0CrfbkItAKFp+8b8Hb7t8p71v6H\/fkDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMeINADGf62oMyuxa1tqltPTzm3xvv2P18tz011tt+jk9xfTFxSfPgSdvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiPl66oWnLxtN\/3zT33f6ktiuBaRd93n6uZp+XnZdx9sWwqaf5+mf70+evAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASDmsYW1XaYv5OxaVNpl13LV9Pdd7W0LZqt\/57uW005ZMJt+\/k7kyRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBiPtf1zDDO9AWk6ctGpyyOvc30xbu7pv\/eVjvl9+Y+v4cnbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIjJLKyd4pT7sus6di3ZvW2hbvr9m\/59nPL7veuU651+HX\/y5A0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAxjy2sTfe2Jafpy1\/Tl42mL4ntMn2p621LgHedck6n\/+8+yZM3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxHzt\/gDTl7\/u2rWUNH1BarXpi093WaT63tvO1fQltrtO+R+\/a8L\/kCdvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiHlsYW36YtFqq9\/3lIWwU76P1aZ\/vrt2ndNd\/xsTlrVWvK\/z973SffHkDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMeINADGf68oMyrzK9MW21UtT05erVpt+vaecv118bz8z\/Tom\/H49eQNAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0DM11MvPH0BaZdTFsJWm35fpp\/nXYtPqxf0Vr\/vKZy\/33m9Cctpd3nyBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgJjHFtbumrBUs8KuBanV77vr9VZf765zZXHse7uWsFbbdU6n379d52\/68tyTPHkDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAzPaFtbumLyqttmtJ7JSls1OWl952HdMXx6af+9VOOX+r\/9cmnANP3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABCTWVjje6uXfqa\/3q5lrV1LdqutXszadf+mL39NWOCa+L6rv7dTFiH\/DU\/eABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AEGNh7SWmL4ntWnZbbfri2PRlrelLdqec07umL8C9+ffmyRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBiMgtru5Z+plu9RLRrQWr6602\/jl2mL4Tddcr3u\/r7eNs5vXsdE67XkzcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEbF9YO2WhaboJi0C\/ade5mr7Udcrvbdf1Tv9+V5t+HbsW6ib8n3ryBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgJjPdR0xuAQAr+HJGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBi\/gdUJWI+Mape9AAAAABJRU5ErkJggg==",
// 					"url": ""
// 				}
// 			}
// 		}
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
// 		'id' => 'eaf82ca6-f5e3-4b8a-930d-8c23b4890612',
// 		'resend_callback' => false,
// 	]);
// 	extract($result);
// 	print_r($response);
// 	/* // Success inquiry QRIS
// 		{
// 			"status": "000",
// 			"data": {
// 				"id": "eaf82ca6-f5e3-4b8a-930d-8c23b4890612",
// 				"merchantRef": "1695014617",
// 				"status": "pending",
// 				"feePayer": "customer",
// 				"amount": 10000,
// 				"fee": 100,
// 				"totalAmount": 10100,
// 				"expiredDate": "2023-09-18T12:33:38+07:00",
// 				"additionalInfo": {
// 					"callback": "http:\/\/sb.tf2us.com\/secure\/callback\/demo"
// 				},
// 				"qris": {
// 					"content": "Nesciunt sint cumque fugit omnis totam. Asperiores blanditiis magni aliquid magni facilis quidem beatae. Voluptas magnam dolorem et ut et ipsa.",
// 					"image": "data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAAe8AAAHvCAYAAAB9iVfNAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAN1klEQVR4nO3cy24jSRYFweFA\/\/\/LnGUXGkJ1THVQcT3SbC2Q+aIcuTmv9\/v9HwCg47+nDwAA+P+INwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxHx96oNfr9f7U59d9n6\/Xyt\/t\/v6rX7vbtPPY\/X4pn\/vqfNYder\/wanr97Tn3v\/7733y9+bNGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGI+trC26tTi025PW5Ba9bRFr1XTl79ueQ52m\/5cTV\/a8xzs480bAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYo4vrK2avmB26nt3X5fdn3dq+Wv3905\/\/m5ZYpu+AHfqOj\/N9N\/bBN68ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBICazsMbPOLVMdmqpa9Uti1m33I\/p37vq1KLXqeeAfbx5A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQIyFtaF2LyCdWkSbvnC126n7sdupBa5Tnzf9OZ1+fPw8b94AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQk1lY2728xM84dd9uWdY6df2etuw2nfPl77x5A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQMzxhbXpC1dPs3uZzOf9jOnHt2r6\/bjl+Tt1\/fy\/38ebNwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMS83m+DNxPtXiKy\/PW9U8tVq265LrudWuqa\/hzc8vzxz7x5A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQMzXpz54+gLSqumLRaeWl3Y7tXQ2\/fpNv2+rpp\/HqSWxU8\/zqqfdt+n\/73\/lzRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBiPrawNp3Fou+dWhy7ZXlu+mLWqtXje9rvaPf3Tl8WXDX9+G5ZXPyVN28AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIySysnVoEWjVhced3pi+O7WY57WecOo9bnufpzylzefMGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAmNf7\/Zmhnd1LU6c+b9X0ZaNTy1DTl8RuOY\/dpl+X6QthT3teVlm828ebNwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMR8nT6A3U4tQ01fgFs1\/fqtWj2+6fdj1fTzmL7Ytmr6dV516jrvvn63PFd\/wps3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxLze79FDQB9zatHrlFMLcKc+b7dTz4vvnfV5uznf7z3t\/+mf8OYNADHiDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMV+nD2DV9MWnU0tJu5363t1LRBMWkHaY\/ryccur+Tr\/O0xf02MebNwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMQcX1g7tUw2fbFt9\/eumr5cdWrJafrS2fTrsvvzdl\/npy0kPm1h8sYFOG\/eABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AEPOxhbWnLe5MXwjbbff5Tl\/W2u2Whb\/dph\/fbrvv2y1Le9NNOF9v3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABDzer\/vGjSavsR2yqnFu1NLdqtuWfhbdWrB7NRzNX2ZbLfp12\/672j68f3KmzcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEfGxh7dSS025PW3K6ZaHplvs2fVFu+vO82\/SFulW3LCTuVvp9ePMGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAmK\/TB3DLctD0JaLpx7fbqeOb\/vztvi7TF+B2f+\/0Ba7pv8vp93fCctoqb94AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQ83q\/zw7yTF8Emu6WhbpbFuVOLUidcmq56mn345bzOGX6\/4M\/4c0bAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYr4+9cGWuv6d1fPYfZ2n34\/dph\/f9Odv1fTltKc996umP3\/Tn4NP8uYNADHiDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMR9bWFt1y\/LNqfN48sIQf5m+JHZq4e\/U87z7dzn9e\/1\/+XnevAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASDmYwtrlo1+xu5lo93XZdXu43va4tP039tu0+\/v9OPb7ZbrfOr\/35\/w5g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAxr\/c7MyizVWlJh8+5cXmpbPf92L385TmY5ZZltz\/hzRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBivk4fwKrdy0anlpxu+bzdTi0lTTf9vq2avpy26tRim+\/9mc8r8eYNADHiDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMR9bWDu1iHbK05Z+di8gTV\/QO2X67+jU9XNd7v7e6b\/LCbx5A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQMzHFtZOLRadWjDbvei1+nmnlsSmL6LdstA0\/Xe0+\/hO\/Y743i2\/oxt58waAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoCYjy2s3bLMc2rxafqC2anlr1XTr\/NuT7tvq6b\/H7rlOb3l+Vs1YbnPmzcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEfGxhbdWphaHdTi3uTL9+t9y31fO4ZQlrt93nMX358JZFw9XvnX6+u004D2\/eABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AEHN8YW3VLYs7q25ZTrvlvp26H9MX0aaf7\/Rlt92m34\/d3\/u0ZbdfefMGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAmI8trJ1aIjq1kLP7fG+5LqtuOd9T33tqCWu3py2YTTd9wezJS2zevAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASDmYwtrpxazbllAOrXYdmqJaPoi36nnavpi2\/Tlqqc9L6tOnUdpwex3JpyHN28AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCI+djC2vSFnOnLVbvtPt9T12\/6AtfTnFqaOrXsdurzVk3\/XU4\/3+nd+pU3bwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIh5vd\/PHJiavrw03fTrN305bfpS3PTn9NTv7Wm\/81W3\/D8o\/b\/35g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAxX6cPYPoS1qpTy1+nlpymLxHdsnA1fXluwtLU7+w+vunP\/S3\/T1dN\/318kjdvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiHm9358ZlLllYejUotL05aBb7tuq0vISn3PL7\/dpbllc\/JU3bwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIj5+tQHn1q0edr3rpp+fLc4tcC12\/Slwt2mL6fdsqS4avd5TL9vf8KbNwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMR8bGHt1FLSdKuLO9MXqU5xXb43YfGJv5y6H6d+H9Ofvxufe2\/eABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AEPOxhbVVtyzf3LJYtPt7py9N3cJy1c\/Y\/dxPXyZbNf34VpXuhzdvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiHm931uHwf764ENLXbtNXxJbder6nXJqeY7v3XI\/Tv0\/8Px9b8LS2SnevAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASDm6\/QB8DOmLzmdOr7dn3dqSeyW5S\/P3\/emP1e7WZT7Z968ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIMbC2lDTl79OLVKdOo\/dpi9cnXJqSWzV9KXC3W45j1Wl36U3bwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIjJLKxNWLQp272UdMvy0vRFpVsWwk49L0\/7v7H7eb7l97F6fKXnxZs3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxBxfWLtlqeuUU4tZu5eITh3fLUtTp0xf1tr9edPP99T\/g91uOb5PPi\/evAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASDm9X6PHrIBAP7GmzcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxPwPTLZURvfwY2AAAAAASUVORK5CYII=",
// 					"url": ""
// 				}
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	$vendor->ThrowErrorException($e);
// }
