<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Throwables;

use Throwable;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException as BadSymfony;

class BadRequestException extends BadSymfony
{
	final public function __construct(string $message = '', \Throwable $previous = null, int $code = 0, array $headers = [])
	{
		parent::__construct($message, $previous, $code, $headers);
	}

	public static function throw($msg = 'Bad Request.', Throwable $e = null)
	{
		$e = $e ?? new \Exception($msg);

		throw new static($msg, $e);
	}
}
