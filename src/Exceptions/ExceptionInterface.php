<?php

namespace PyGPaymentLib\Exceptions;

interface ExceptionInterface extends \Throwable
{

	public function getErrorCode();
	public function getErrorMessage();

}
