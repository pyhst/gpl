<?php

require_once __DIR__ . '/../includes.php';

$init = new \PyGPaymentLib\Init(
	$_ENV['IPAY_MERCHANT_CODE'],
	$_ENV['IPAY_MERCHANT_KEY']
);
$init->setRequestURL($_ENV['IPAY_HOST_URL']);

$vendor = new \PyGPaymentLib\Vendors\Ipay88($init);

try {
	$result = $vendor->Inquiry((object) [
		'order_id' => 'H78S2021102660644514',
		'amount' => '2003900',
	]);
	// extract($result);
	// print_r($response);
	/* Statuses
	00	= Pembayaran sukses.
	Invalid parameters = Parameter yang dikirimkan merchant tidak tepat.
	Record not found = Data tidak ditemukan.
	Incorrect amount = Total yang tidak tepat (berbeda).
	Payment fail = Pembayaran gagal.
	Payment Pending = Pembayaran tertunda dan pelanggan harus membayar di mesin ATM.
	Haven’t Paid (0) = Tagihan belum dibayar atau berhenti di laman pembayaran iPay88.
	Haven’t Paid (1) = Tagihan belum dibayar atau berhenti di laman bank.
	M88Admin = Status pembayaran diubah oleh iPay88 Admin (gagal).
	*/
	print_r($result);
} catch (\Throwable $e) {
	echo 'Inquiry failed: ' . $e->getMessage();
}
