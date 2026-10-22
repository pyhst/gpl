<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../vendor/autoload.php';

// Dotenv
$env = __DIR__ .'/../.env';
if (file_exists($env)) {
	$dotenv = new Symfony\Component\Dotenv\Dotenv();
	$dotenv->load($env);
}
