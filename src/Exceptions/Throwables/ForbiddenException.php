<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Throwables;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ForbiddenException extends HttpException
{
	final public function __construct(\Throwable $previous = null, $code = 0)
	{
		parent::__construct(403, $previous ? $previous->getMessage() : 'Forbidden.', $previous, [], $code);
	}

	public static function throw($msg = 'Forbidden.')
	{
		$e = new \Exception($msg);

		throw new static($e);
	}
}
