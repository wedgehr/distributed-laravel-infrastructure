<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Throwables;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException as BadSymfony;
use Throwable;

class BadRequestException extends BadSymfony
{
	public static function throw($msg = 'Bad Request.', Throwable $e = null)
	{
		$e = $e ?? new \Exception($msg);

		throw new static($msg, $e);
	}
}
