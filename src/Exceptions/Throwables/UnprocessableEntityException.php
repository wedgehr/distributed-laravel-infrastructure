<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Throwables;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UnprocessableEntityException extends UnprocessableEntityHttpException
{
	public function __construct($msg = 'Unable to process request.')
	{
		parent::__construct($msg);
	}

	public static function throw(string $msg = 'Unable to process request.')
	{
		throw new static($msg);
	}
}
